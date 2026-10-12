<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Menampilkan daftar user.
     */
    public function index(Request $request)
    {
        $users = User::with('roles')
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('nip', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Form tambah user.
     */
    public function create()
    {
        if (auth()->user()->hasRole('super-admin')) {

            $roles = Role::where('guard_name', 'web')
                ->orderBy('name')
                ->get();
        } else {

            $roles = Role::where('guard_name', 'web')
                ->where('name', '!=', 'super-admin')
                ->orderBy('name')
                ->get();
        }

        return view('admin.users.create', compact('roles'));
    }

    /**
     * Simpan user baru.
     */
    public function store(Request $request)
    {
        $allowedRoles = auth()->user()->hasRole('super-admin')
            ? Role::where('guard_name', 'web')->pluck('name')->toArray()
            : Role::where('guard_name', 'web')
            ->where('name', '!=', 'super-admin')
            ->pluck('name')
            ->toArray();

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'nip' => [
                'required',
                'string',
                'max:30',
                'unique:users,nip',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],

            'role' => [
                'required',
                Rule::in($allowedRoles),
            ],

            'status' => [
                'required',
                'boolean',
            ],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'nip' => $validated['nip'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'status' => $validated['status'],
        ]);

        $user->assignRole($validated['role']);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * Form edit user.
     */
    public function edit(User $user)
    {
        /*
        |--------------------------------------------------------------------------
        | Admin tidak boleh mengedit Super Admin
        |--------------------------------------------------------------------------
        */

        if (
            $user->hasRole('super-admin') &&
            !auth()->user()->hasRole('super-admin')
        ) {
            abort(403, 'Anda tidak memiliki izin untuk mengedit Super Admin.');
        }


        if (auth()->user()->hasRole('super-admin')) {

            $roles = Role::where('guard_name', 'web')
                ->orderBy('name')
                ->get();
        } else {

            $roles = Role::where('guard_name', 'web')
                ->where('name', '!=', 'super-admin')
                ->orderBy('name')
                ->get();
        }

        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update user.
     */
    public function update(Request $request, User $user)
    {
        if (
            $user->hasRole('super-admin') &&
            !auth()->user()->hasRole('super-admin')
        ) {
            abort(403, 'Anda tidak memiliki izin mengubah Super Admin.');
        }

        $allowedRoles = auth()->user()->hasRole('super-admin')
            ? Role::where('guard_name', 'web')->pluck('name')->toArray()
            : Role::where('guard_name', 'web')
            ->where('name', '!=', 'super-admin')
            ->pluck('name')
            ->toArray();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],

            'nip' => [
                'required',
                'string',
                'max:30',
                Rule::unique('users', 'nip')->ignore($user->id),
            ],

            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($user->id),
            ],

            'role' => [
                'required',
                Rule::in($allowedRoles),
            ],

            'status' => ['required', 'boolean'],
        ]);

        $user->update([
            'name' => $validated['name'],
            'nip' => $validated['nip'],
            'email' => $validated['email'],
            'status' => $validated['status'],
        ]);

        $user->syncRoles([
            $validated['role'],
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    /**
     * Hapus user.
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with(
                'error',
                'Anda tidak dapat menghapus akun sendiri.'
            );
        }

        if (
            $user->hasRole('super-admin') &&
            !auth()->user()->hasRole('super-admin')
        ) {
            return back()->with(
                'error',
                'Akun super-admin tidak dapat dihapus.'
            );
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User berhasil dihapus.');
    }

    /**
     * Reset password user.
     */
    public function resetPassword(User $user)
    {
        if (
            $user->hasRole('super-admin') &&
            !auth()->user()->hasRole('super-admin')
        ) {
            return back()->with(
                'error',
                'Password super-admin tidak dapat direset oleh admin.'
            );
        }

        $user->update([
            'password' => Hash::make('password123'),
        ]);

        return back()->with(
            'success',
            'Password berhasil direset menjadi password123.'
        );
    }

    /**
     * Toggle status user.
     */
    public function toggleStatus(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with(
                'error',
                'Status akun sendiri tidak dapat dinonaktifkan.'
            );
        }

        if (
            $user->hasRole('super-admin') &&
            !auth()->user()->hasRole('super-admin')
        ) {
            return back()->with(
                'error',
                'Status akun super-admin tidak dapat diubah.'
            );
        }

        $user->update([
            'status' => !$user->status,
        ]);

        $status = $user->status
            ? 'diaktifkan'
            : 'dinonaktifkan';

        return back()->with(
            'success',
            "User berhasil {$status}."
        );
    }
}

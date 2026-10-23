<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\FonnteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
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
     * Password dibuat otomatis dan dikirim melalui WhatsApp Fonnte.
     */
    public function store(Request $request, FonnteService $fonnte)
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

            'no_hp' => [
                'required',
                'string',
                'max:20',
                'regex:/^[0-9+\-\s()]+$/',
            ],

            'role' => [
                'required',
                Rule::in($allowedRoles),
            ],

            'status' => [
                'required',
                'boolean',
            ],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'nip.required' => 'NIP wajib diisi.',
            'nip.unique' => 'NIP sudah terdaftar.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'no_hp.required' => 'Nomor HP/WhatsApp wajib diisi.',
            'no_hp.regex' => 'Format nomor HP tidak valid.',
            'role.required' => 'Role wajib dipilih.',
            'role.in' => 'Role yang dipilih tidak diizinkan.',
            'status.required' => 'Status akun wajib dipilih.',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Normalisasi nomor HP
        |--------------------------------------------------------------------------
        | Contoh:
        | 081234567890  menjadi 6281234567890
        | +6281234567890 menjadi 6281234567890
        */
        $noHp = preg_replace('/[\s\-()]+/', '', $validated['no_hp']);

        if (str_starts_with($noHp, '+')) {
            $noHp = substr($noHp, 1);
        }

        if (str_starts_with($noHp, '0')) {
            $noHp = '62' . substr($noHp, 1);
        }

        if (!preg_match('/^62[0-9]{8,13}$/', $noHp)) {
            return back()
                ->withInput($request->except([
                    'password',
                    'password_confirmation',
                ]))
                ->withErrors([
                    'no_hp' => 'Gunakan nomor WhatsApp Indonesia yang valid, misalnya 081234567890.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Generate password otomatis
        |--------------------------------------------------------------------------
        */
        $password = Str::password(14);

        /*
        |--------------------------------------------------------------------------
        | Simpan user
        |--------------------------------------------------------------------------
        */
        $user = User::create([
            'name' => $validated['name'],
            'nip' => $validated['nip'],
            'email' => $validated['email'],
            'no_hp' => $noHp,
            'password' => Hash::make($password),
            'must_change_password' => true,
            'status' => $validated['status'],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Tetapkan role sesuai pilihan admin
        |--------------------------------------------------------------------------
        */
        $user->assignRole($validated['role']);

        /*
        |--------------------------------------------------------------------------
        | Kirim informasi akun melalui WhatsApp
        |--------------------------------------------------------------------------
        */
        $message = implode("\n", [
            '*SSO PBJ DEMAK*',
            '',
            'Yth. ' . $user->name . ',',
            'Akun SSO Anda telah dibuat oleh administrator.',
            '',
            'Nama: ' . $user->name,
            'NIP: ' . $user->nip,
            'Email: ' . $user->email,
            'Password awal: ' . $password,
            '',
            'Silakan gunakan informasi tersebut untuk login ke SSO PBJ Demak.',
            'Demi keamanan, segera ganti password setelah login.',
            '',
            'Jangan membagikan password ini kepada orang lain.',
            'Tim IT PBJ Demak',
        ]);

        try {
            $fonnte->sendMessage($noHp, $message);

            return redirect()
                ->route('admin.users.index')
                ->with(
                    'success',
                    'User berhasil dibuat. Notifikasi WhatsApp telah diterima Fonnte untuk diproses.'
                );
        } catch (\Throwable $e) {
            Log::error('Gagal mengirim password awal melalui Fonnte.', [
                'user_id' => $user->id,
                'exception' => $e->getMessage(),
            ]);

            return redirect()
                ->route('admin.users.index')
                ->with(
                    'warning',
                    'User berhasil dibuat, tetapi pengiriman WhatsApp gagal. Gunakan fitur reset password untuk membuat password baru dan mengirimkannya kembali.'
                );
        }
    }

    /**
     * Form edit user.
     */
    public function edit(User $user)
    {
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
     * Reset password user dan kirim password baru melalui WhatsApp.
     */
    public function resetPassword(User $user, FonnteService $fonnte)
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

        if (empty($user->no_hp)) {
            return back()->with(
                'error',
                'User belum memiliki nomor WhatsApp. Silakan lengkapi nomor HP terlebih dahulu.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Generate password baru
        |--------------------------------------------------------------------------
        */
        $password = Str::password(14);

        $user->update([
            'password' => Hash::make($password),
            'must_change_password' => true,
        ]);

        $message = implode("\n", [
            '*RESET PASSWORD SSO PBJ DEMAK*',
            '',
            'Yth. ' . $user->name . ',',
            'Password akun SSO Anda telah direset oleh administrator.',
            '',
            'NIP: ' . $user->nip,
            'Password baru: ' . $password,
            '',
            'Silakan login menggunakan password baru.',
            'Demi keamanan, segera ganti password setelah login.',
            '',
            'Jangan membagikan password ini kepada orang lain.',
            'Tim IT PBJ Demak',
        ]);

        try {
            $fonnte->sendMessage($user->no_hp, $message);

            return back()->with(
                'success',
                'Password berhasil direset dan notifikasi telah diterima Fonnte untuk diproses.'
            );
        } catch (\Throwable $e) {
            Log::error('Gagal mengirim password reset melalui Fonnte.', [
                'user_id' => $user->id,
                'exception' => $e->getMessage(),
            ]);

            return back()->with(
                'warning',
                'Password sudah berubah, tetapi WhatsApp gagal dikirim. Lakukan reset password kembali setelah memperbaiki koneksi Fonnte.'
            );
        }
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
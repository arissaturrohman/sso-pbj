<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::withCount('users')
            ->where('guard_name', 'web')
            ->orderBy('name')
            ->paginate(10);

        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::where('guard_name', 'web')
            ->orderBy('name')
            ->get();

        return view('admin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:roles,name',
            ],
            'permissions' => [
                'nullable',
                'array',
            ],
            'permissions.*' => [
                'string',
                'exists:permissions,name',
            ],
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'guard_name' => 'web',
        ]);

        $role->syncPermissions($validated['permissions'] ?? []);

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Role berhasil ditambahkan.');
    }

    public function edit(Role $role)
    {
        if ($role->guard_name !== 'web') {
            abort(404);
        }

        $permissions = Permission::where('guard_name', 'web')
            ->orderBy('name')
            ->get();

        $rolePermissions = $role->permissions
            ->pluck('name')
            ->toArray();

        return view('admin.roles.edit', compact(
            'role',
            'permissions',
            'rolePermissions'
        ));
    }

    public function update(Request $request, Role $role)
    {
        if ($role->guard_name !== 'web') {
            abort(404);
        }

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'name')->ignore($role->id),
            ],
            'permissions' => [
                'nullable',
                'array',
            ],
            'permissions.*' => [
                'string',
                'exists:permissions,name',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Proteksi Super Admin
        |--------------------------------------------------------------------------
        */

        if ($role->name === 'super-admin') {

            if ($validated['name'] !== 'super-admin') {
                return back()
                    ->withInput()
                    ->with('error', 'Role super-admin tidak dapat diubah namanya.');
            }

            /*
            | Super-admin selalu memiliki seluruh permission.
            */
            $role->syncPermissions(
                Permission::where('guard_name', 'web')->get()
            );

            return redirect()
                ->route('admin.roles.index')
                ->with(
                    'success',
                    'Permission super-admin disinkronkan.'
                );
        }

        $role->update([
            'name' => $validated['name'],
        ]);

        $role->syncPermissions($validated['permissions'] ?? []);

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Role berhasil diperbarui.');
    }

    public function destroy(Role $role)
    {
        if ($role->guard_name !== 'web') {
            abort(404);
        }

        /*
        |--------------------------------------------------------------------------
        | Super Admin tidak boleh dihapus
        |--------------------------------------------------------------------------
        */

        if ($role->name === 'super-admin') {
            return back()->with(
                'error',
                'Role super-admin tidak dapat dihapus.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Role yang masih digunakan user tidak boleh dihapus
        |--------------------------------------------------------------------------
        */

        if ($role->users()->exists()) {
            return back()->with(
                'error',
                'Role tidak dapat dihapus karena masih digunakan oleh user.'
            );
        }

        $role->delete();

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Role berhasil dihapus.');
    }
}
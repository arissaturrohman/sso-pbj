<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [

            // User
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            'users.reset-password',
            'users.toggle-status',

            // Role
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',

            // Application
            'applications.view',
            'applications.create',
            'applications.edit',
            'applications.delete',

        ];

        foreach ($permissions as $permission) {

            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);

        }


        /*
        |--------------------------------------------------------------------------
        | Super Admin
        |--------------------------------------------------------------------------
        */

        $superAdmin = Role::findByName(
            'super-admin',
            'web'
        );

        $superAdmin->syncPermissions(
            Permission::all()
        );


        /*
        |--------------------------------------------------------------------------
        | Admin
        |--------------------------------------------------------------------------
        */

        $admin = Role::findByName(
            'admin',
            'web'
        );

        $admin->syncPermissions([

            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            'users.reset-password',
            'users.toggle-status',

            'roles.view',

            'applications.view',

        ]);


        /*
        |--------------------------------------------------------------------------
        | User
        |--------------------------------------------------------------------------
        */

        $user = Role::findByName(
            'user',
            'web'
        );

        $user->syncPermissions([]);

    }
}
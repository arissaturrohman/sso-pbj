<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::updateOrCreate(
            [
                'email' => '[email protected]',
            ],
            [
                'name' => 'Super Administrator',
                'nip' => '199303022024211009',
                'password' => Hash::make('password'),
                'status' => true,
            ]
        );

        $user->assignRole('super-admin');
    }
}
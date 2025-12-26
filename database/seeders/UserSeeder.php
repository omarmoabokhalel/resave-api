<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
    'name' => 'Admin',
    'email' => 'admin@resave.com',
    'password' => Hash::make('password'),
    'role' => 'admin'
]);
User::create([
    'name' => 'Omar User',
    'email' => 'user@resave.com',
    'password' => Hash::make('password'),
    'role' => 'user'
]);

        // User::create([
        //     'name' => 'Omar User',
        //     'email' => 'user@resave.com',
        //     'password' => Hash::make('password'),
        // ]);

        // User::create([
        //     'name' => 'Admin',
        //     'email' => 'admin@resave.com',
        //     'password' => Hash::make('password'),
        // ]);
    }
}

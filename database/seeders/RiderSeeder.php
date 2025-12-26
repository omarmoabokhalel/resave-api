<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rider;
use Illuminate\Support\Facades\Hash;

class RiderSeeder extends Seeder
{
    public function run(): void
    {
        // Rider::create([
        //     'name' => 'Ahmed Rider',
        //     'email' => 'rider@resave.com',
        //     'password' => Hash::make('password'),
        //     'phone' => '01000000000',
        // ]);
    }
}

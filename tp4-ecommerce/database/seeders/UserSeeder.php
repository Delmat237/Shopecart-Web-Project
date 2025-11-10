<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@shopcart.com',
            'password' => Hash::make('password123'),
            'role' => 'ADMIN'
        ]);

        User::create([
            'name' => 'Client User',
            'email' => 'client@shopcart.com',
            'password' => Hash::make('password123'),
            'role' => 'USER'
        ]);
    }
}
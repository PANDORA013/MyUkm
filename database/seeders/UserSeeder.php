<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Create admin user
        User::create([
            'name' => 'Admin User',
            'nim' => 'admin123',
            'password' => Hash::make('password'),
            'is_admin' => true,
        ]);

        // Create regular users
        User::factory(10)->create();
    }
}

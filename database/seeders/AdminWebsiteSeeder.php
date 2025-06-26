<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;


class AdminWebsiteSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->updateOrInsert(
            ['nim' => 'TH.17004'],
            [
                'name' => 'Admin Website',
                'email' => 'admin@myukm.com',
                'nim' => 'TH.17004',
                'password' => Hash::make('AR/171004'),
                'role' => 'admin_website'
            ]
        );
    }
}

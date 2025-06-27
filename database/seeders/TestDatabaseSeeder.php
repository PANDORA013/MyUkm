<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TestDatabaseSeeder extends Seeder
{
    public function run()
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Clear existing data
        $this->truncateTables();

        // Seed test data
        $this->call([
            UserSeeder::class,
            // Add other seeders as needed
        ]);

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    protected function truncateTables()
    {
        $tables = [
            'users',
            'password_reset_tokens',
            'sessions',
            // Add other tables as needed
        ];

        foreach ($tables as $table) {
            DB::table($table)->truncate();
        }
    }
}

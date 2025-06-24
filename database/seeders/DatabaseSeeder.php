<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;



class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Seed essential data and ensure UKM ↔ group sync
        $this->call([
            UKMSeeder::class,
            AdminWebsiteSeeder::class,
            SyncUkmGroupsSeeder::class,
        ]);
    }
}

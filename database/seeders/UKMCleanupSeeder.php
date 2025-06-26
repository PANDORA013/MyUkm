<?php

namespace Database\Seeders;

use App\Models\UKM;
use App\Models\User;
use Illuminate\Database\Seeder;

class UKMCleanupSeeder extends Seeder
{
    public function run(): void
    {
        // Reset all user UKM associations
        User::whereNotNull('ukm_id')
            ->update(['ukm_id' => null, 'role' => 'anggota']);
            
        // Delete all UKM records
        UKM::truncate();
        
        $this->command->info('All UKM data has been cleaned up successfully');
    }
}

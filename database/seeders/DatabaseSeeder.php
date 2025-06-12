<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Group;
use Illuminate\Support\Facades\Log;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        try {
            // Create groups if they don't exist
            $groups = [
                ['name' => 'SIMS', 'referral_code' => '0812'],
                ['name' => 'PSM', 'referral_code' => '0813'],
                ['name' => 'PSHT', 'referral_code' => '0814']
            ];

            foreach ($groups as $groupData) {
                $group = Group::firstOrCreate(
                    ['referral_code' => $groupData['referral_code']],
                    ['name' => $groupData['name']]
                );

                Log::info('Group created/updated:', [
                    'name' => $group->name,
                    'referral_code' => $group->referral_code
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error seeding groups:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}

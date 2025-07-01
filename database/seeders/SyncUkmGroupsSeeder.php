<?php

namespace Database\Seeders;

use App\Models\UKM;
use App\Models\Group;
use Illuminate\Database\Seeder;

class SyncUkmGroupsSeeder extends Seeder
{
    /**
     * Ensure every UKM has a corresponding entry in `groups` table.
     */
    public function run(): void
    {
        UKM::chunk(100, function ($ukms) {
            foreach ($ukms as $ukm) {
                Group::updateOrCreate(
                    ['referral_code' => $ukm->code],
                    ['name' => $ukm->name]
                );
            }
        });
    }
}

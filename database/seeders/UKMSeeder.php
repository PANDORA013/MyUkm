<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UKM;

class UKMSeeder extends Seeder
{
    public function run(): void
    {
        $ukms = [
            ['name' => 'Unit Kesenian', 'code' => 'UKS'],
            ['name' => 'Basket',        'code' => 'BSK'],
            ['name' => 'Mapala',        'code' => 'MPL'],
            ['name' => 'Paduan Suara',  'code' => 'PSM'],
        ];

        foreach ($ukms as $data) {
            UKM::updateOrCreate(
                ['code' => $data['code']],
                ['name' => $data['name']]
            );
        }
    }
}

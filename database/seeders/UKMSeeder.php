<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UKM;

class UKMSeeder extends Seeder
{
    public function run(): void
    {
        $ukms = [
            ['nama' => 'Unit Kesenian', 'kode' => 'UKS'],
            ['nama' => 'Basket',        'kode' => 'BSK'],
            ['nama' => 'Mapala',        'kode' => 'MPL'],
            ['nama' => 'Paduan Suara',  'kode' => 'PSM'],
        ];

        foreach ($ukms as $data) {
            UKM::updateOrCreate(
                ['kode' => $data['kode']],
                ['nama' => $data['nama']]
            );
        }
    }
}

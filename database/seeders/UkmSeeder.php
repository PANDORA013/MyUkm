<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ukm;

class UkmSeeder extends Seeder
{
    public function run()
    {
        $ukmList = [
            'SIMS UISI',
            'Perisai Diri',
            'OIA (Office Of International Affairs)',
            'PSHT',
            'PSM',
            'Silo Theater',
            'STIMBARA',
            'UISI Media',
            'Komunitas Olahraga',
        ];

        $startToken = 1001;

        foreach ($ukmList as $index => $name) {
            $token = (string) ($startToken + $index);
            Ukm::updateOrCreate(['token' => $token], ['name' => $name]);
        }
    }
}

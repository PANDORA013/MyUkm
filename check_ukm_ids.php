<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\UKM;

$ukms = UKM::with('groups')->get();
foreach ($ukms as $ukm) {
    echo "UKM: " . $ukm->nama . " (ID: " . $ukm->id . ")\n";
    if ($ukm->groups->count() > 0) {
        foreach ($ukm->groups as $group) {
            echo "  Grup: " . $group->name . " (Code: " . $group->referral_code . ")\n";
        }
    }
    echo "\n";
}

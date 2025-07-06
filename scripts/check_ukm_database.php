<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\UKM;

echo "=== UKM Database Check ===" . PHP_EOL;

try {
    $ukms = UKM::all();
    echo "Total UKMs: " . $ukms->count() . PHP_EOL;
    
    if ($ukms->count() > 0) {
        echo "\nUKMs in database:" . PHP_EOL;
        foreach ($ukms as $ukm) {
            echo "- ID: {$ukm->id}, Name: {$ukm->name}, Code: {$ukm->code}" . PHP_EOL;
        }
    } else {
        echo "No UKMs found in database." . PHP_EOL;
    }
    
    // Check for soft deleted UKMs
    $deletedUkms = UKM::onlyTrashed()->get();
    if ($deletedUkms->count() > 0) {
        echo "\nSoft deleted UKMs:" . PHP_EOL;
        foreach ($deletedUkms as $ukm) {
            echo "- ID: {$ukm->id}, Name: {$ukm->name}, Code: {$ukm->code}, Deleted: {$ukm->deleted_at}" . PHP_EOL;
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}

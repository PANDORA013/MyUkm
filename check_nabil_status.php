<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

$nabil = User::where('name', 'LIKE', '%nabil%')
    ->orWhere('name', 'LIKE', '%Nabil%')
    ->first();

if ($nabil) {
    echo "User: " . $nabil->name . " (ID: " . $nabil->id . ")\n";
    echo "Role global: " . $nabil->role . "\n";
    echo "Grup yang diikuti:\n";
    
    foreach ($nabil->groups()->withPivot(['is_admin'])->get() as $group) {
        echo "- " . $group->name . " (admin: " . ($group->pivot->is_admin ? 'YES' : 'NO') . ")\n";
    }
} else {
    echo "User Nabil tidak ditemukan\n";
}

<?php

require_once __DIR__ . '/../../vendor/autoload.php';
$app = require __DIR__ . '/../../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Group;

// Find admin user and group
$admin = User::where('nim', 'admin002')->first();
$group = Group::where('referral_code', '0810')->first();

if ($admin && $group) {
    // Make admin user admin of the group
    $admin->groups()->syncWithoutDetaching([
        $group->id => [
            'is_admin' => true,
            'is_muted' => false
        ]
    ]);
    
    echo "✅ Admin user {$admin->name} is now admin of group {$group->name}\n";
    
    // Verify
    $adminGroups = $admin->adminGroups;
    echo "✅ Admin groups count: " . $adminGroups->count() . "\n";
} else {
    echo "❌ Admin or group not found\n";
}

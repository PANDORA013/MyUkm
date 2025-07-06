<?php

/**
 * Quick database query script
 */

// Bootstrap Laravel
require_once __DIR__ . '/../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../bootstrap/app.php';

// Get the Illuminate application instance
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Group;

echo "=== Quick Admin Grup Setup ===\n\n";

// Find admin_grup users
$adminUsers = User::where('role', 'admin_grup')->get();
echo "Admin Grup Users:\n";
foreach ($adminUsers as $user) {
    echo "- {$user->name} (NIM: {$user->nim}, ID: {$user->id})\n";
}

// Find groups
$groups = Group::all();
echo "\nGroups:\n";
foreach ($groups as $group) {
    echo "- {$group->name} (Code: {$group->referral_code}, ID: {$group->id})\n";
}

// Make admin002 admin of the SIMS group
$adminUser = User::where('nim', 'admin002')->first();
$group = Group::where('referral_code', '0810')->first();

if ($adminUser && $group) {
    echo "\nMaking {$adminUser->name} admin of {$group->name}...\n";
    
    // Check if already exists
    $existing = $adminUser->groups()->where('group_id', $group->id)->first();
    
    if ($existing) {
        // Update to admin
        $adminUser->groups()->updateExistingPivot($group->id, ['is_admin' => true]);
        echo "✅ Updated existing membership to admin\n";
    } else {
        // Create new admin membership
        $adminUser->groups()->attach($group->id, [
            'is_admin' => true,
            'is_muted' => false
        ]);
        echo "✅ Created new admin membership\n";
    }
    
    // Verify
    $adminGroups = $adminUser->adminGroups;
    echo "Admin groups for {$adminUser->name}: {$adminGroups->count()}\n";
    foreach ($adminGroups as $adminGroup) {
        echo "- {$adminGroup->name}\n";
    }
} else {
    echo "❌ Could not find admin user or group\n";
}

echo "\nDone!\n";

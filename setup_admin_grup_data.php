<?php
/**
 * Setup test data untuk admin_grup
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

use App\Models\User;
use App\Models\Group;
use App\Models\GroupUser;

echo "=== Setup Test Data Admin Grup ===\n\n";

try {
    // Find admin_grup user
    $adminUser = User::where('role', 'admin_grup')->first();
    if (!$adminUser) {
        echo "❌ Tidak ada user dengan role admin_grup\n";
        return;
    }
    
    echo "✅ Admin user: {$adminUser->name} (ID: {$adminUser->id})\n";
    
    // Find or create group
    $group = Group::first();
    if (!$group) {
        echo "⚠️  Tidak ada grup, membuat grup test...\n";
        $group = Group::create([
            'name' => 'UKM Test',
            'referral_code' => 'TEST_' . rand(1000, 9999),
            'description' => 'UKM untuk testing admin grup',
            'created_by' => $adminUser->id,
            'is_active' => true
        ]);
        echo "✅ Grup dibuat: {$group->name}\n";
    } else {
        echo "✅ Grup existing: {$group->name} (ID: {$group->id})\n";
    }
    
    // Check if user is already admin of this group
    $existingPivot = $adminUser->groups()->where('group_id', $group->id)->first();
    
    if ($existingPivot) {
        // Update existing relationship to make admin
        $adminUser->groups()->updateExistingPivot($group->id, [
            'is_admin' => true,
            'is_muted' => false
        ]);
        echo "✅ User sudah ada di grup, diupdate menjadi admin\n";
    } else {
        // Create new relationship as admin
        $adminUser->groups()->attach($group->id, [
            'is_admin' => true,
            'is_muted' => false
        ]);
        echo "✅ User ditambahkan ke grup sebagai admin\n";
    }
    
    // Verify the relationship works
    $adminGroups = $adminUser->adminGroups;
    echo "\n📊 Hasil verifikasi:\n";
    echo "   Jumlah admin groups: " . $adminGroups->count() . "\n";
    
    if ($adminGroups->count() > 0) {
        foreach ($adminGroups as $adminGroup) {
            echo "   - {$adminGroup->name} (ID: {$adminGroup->id})\n";
        }
    }
    
    echo "\n✅ Setup selesai! Sekarang admin_grup user memiliki grup untuk dikelola.\n";
    echo "🌐 Akses http://127.0.0.1:8000 untuk test layout.\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

<?php
/**
 * Script untuk testing sinkronisasi privilege admin per grup
 * 
 * Menguji apakah badge, statistik, dan tombol di halaman admin website
 * sudah benar-benar sinkron dengan data pivot is_admin di database.
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Group;
use App\Models\UKM;

echo "=== TEST SINKRONISASI PRIVILEGE ADMIN PER GRUP ===\n\n";

// 1. Test data Nabil
echo "1. STATUS NABIL:\n";
$nabil = User::where('name', 'LIKE', '%nabil%')->first();
if ($nabil) {
    echo "   User: {$nabil->name} (ID: {$nabil->id})\n";
    echo "   Role global: {$nabil->role}\n";
    echo "   Grup yang diikuti:\n";
    
    foreach ($nabil->groups()->withPivot(['is_admin'])->get() as $group) {
        $isAdmin = $group->pivot->is_admin ? 'YES' : 'NO';
        echo "   - {$group->name} (admin: {$isAdmin})\n";
    }
    echo "\n";
}

// 2. Test badge logic
echo "2. TEST BADGE LOGIC:\n";
if ($nabil) {
    $groups = $nabil->groups()->withPivot(['is_admin'])->get();
    
    foreach ($groups as $group) {
        $isAdminInGroup = $group->pivot && $group->pivot->is_admin;
        
        if ($nabil->role === 'admin_website') {
            $badge = ['Admin Website', 'danger'];
        } elseif ($isAdminInGroup) {
            $badge = ['Admin Grup', 'warning'];
        } else {
            $badge = ['Anggota', 'primary'];
        }
        
        echo "   {$group->name}: Badge = '{$badge[0]}' (class: {$badge[1]})\n";
        
        if ($nabil->role === 'admin_grup' && !$isAdminInGroup) {
            echo "     + Extra: Global: Admin Grup\n";
        }
    }
    echo "\n";
}

// 3. Test statistik per grup
echo "3. TEST STATISTIK PER GRUP:\n";
$groups = Group::with(['users' => function($query) {
    $query->withPivot(['is_admin']);
}])->get();

foreach ($groups as $group) {
    $totalMembers = $group->users->count();
    $adminCount = $group->users->where('pivot.is_admin', true)->count();
    $memberCount = $totalMembers - $adminCount;
    
    echo "   {$group->name}:\n";
    echo "     - Total anggota: {$totalMembers}\n";
    echo "     - Admin grup: {$adminCount}\n";
    echo "     - Anggota biasa: {$memberCount}\n";
    
    // List admin
    $admins = $group->users->where('pivot.is_admin', true);
    if ($admins->count() > 0) {
        echo "     - Admin: " . $admins->pluck('name')->join(', ') . "\n";
    }
    echo "\n";
}

// 4. Test button promosi/demosi
echo "4. TEST BUTTON PROMOSI/DEMOSI:\n";
if ($nabil) {
    foreach ($nabil->groups()->withPivot(['is_admin'])->get() as $group) {
        $isAdminInThisGroup = $group->pivot && $group->pivot->is_admin;
        
        echo "   {$group->name} - {$nabil->name}:\n";
        
        if (!$isAdminInThisGroup) {
            echo "     - Tombol: 'Jadikan Admin di Grup Ini' (success)\n";
            echo "     - Action: promoteToAdminInGroup\n";
        } else {
            echo "     - Tombol: 'Hapus Admin dari Grup Ini' (warning)\n";
            echo "     - Action: demoteFromAdminInGroup\n";
        }
        echo "\n";
    }
}

// 5. Test method helper
echo "5. TEST HELPER METHODS:\n";
if ($nabil) {
    foreach ($nabil->groups as $group) {
        $isAdminHelper = $nabil->isAdminInGroup($group);
        $pivotAdmin = $group->users()->where('user_id', $nabil->id)->first()->pivot->is_admin ?? false;
        
        echo "   {$group->name}:\n";
        echo "     - isAdminInGroup(): " . ($isAdminHelper ? 'true' : 'false') . "\n";
        echo "     - pivot->is_admin: " . ($pivotAdmin ? 'true' : 'false') . "\n";
        echo "     - Match: " . ($isAdminHelper === $pivotAdmin ? 'YES ✓' : 'NO ✗') . "\n";
        echo "\n";
    }
}

echo "=== KESIMPULAN ===\n";
echo "✓ Jika semua data di atas sudah benar, maka:\n";
echo "  - Badge admin hanya muncul jika user admin di grup tersebut\n";
echo "  - Statistik admin grup menghitung berdasarkan pivot.is_admin\n";
echo "  - Tombol promosi/demosi berdasarkan status admin per grup\n";
echo "  - Helper method konsisten dengan data pivot\n\n";

echo "PANDUAN TESTING MANUAL:\n";
echo "1. Buka halaman: /admin/ukm/{ukm_id}/anggota\n";
echo "2. Cek badge 'Admin Grup' hanya muncul untuk admin di grup tersebut\n";
echo "3. Cek statistik 'Admin Grup' sesuai dengan jumlah admin di grup\n";
echo "4. Cek tombol promosi/demosi sesuai status admin per grup\n";
echo "5. Test promosi/demosi admin dan lihat perubahan real-time\n";

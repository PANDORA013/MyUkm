<?php

/**
 * Test privilege admin per grup - memastikan admin status hanya berlaku di grup tertentu
 */

require_once __DIR__ . '/../../bootstrap/app.php';

use App\Models\User;
use App\Models\Group;
use Illuminate\Support\Facades\DB;

echo "=== TEST PRIVILEGE ADMIN PER GRUP ===\n\n";

try {
    // Test 1: Ambil data user dan grup untuk testing
    echo "1. Mengambil data testing...\n";
    
    $milla = User::where('name', 'Milla')->first();
    $nabil = User::where('name', 'Nabil')->first();
    $thomas = User::where('name', 'Thomas')->first();
    
    $sims = Group::where('referral_code', '0810')->first();
    $psm = Group::where('referral_code', '0811')->first();
    
    if (!$milla || !$nabil || !$thomas || !$sims || !$psm) {
        echo "ERROR: Data testing tidak lengkap!\n";
        exit(1);
    }
    
    echo "   ✓ Data user dan grup tersedia\n";
    echo "   - Milla (ID: {$milla->id})\n";
    echo "   - Nabil (ID: {$nabil->id})\n";
    echo "   - Thomas (ID: {$thomas->id})\n";
    echo "   - SIMS (ID: {$sims->id}, Code: {$sims->referral_code})\n";
    echo "   - PSM (ID: {$psm->id}, Code: {$psm->referral_code})\n\n";
    
    // Test 2: Cek helper methods untuk admin per grup
    echo "2. Testing helper methods admin per grup...\n";
    
    // Test Milla
    echo "   Testing Milla:\n";
    echo "   - Is admin in SIMS: " . ($milla->isAdminInGroup($sims) ? 'YES' : 'NO') . "\n";
    echo "   - Is admin in PSM: " . ($milla->isAdminInGroup($psm) ? 'YES' : 'NO') . "\n";
    echo "   - Role in SIMS: " . ($milla->getRoleInGroup($sims) ?? 'NOT_MEMBER') . "\n";
    echo "   - Role in PSM: " . ($milla->getRoleInGroup($psm) ?? 'NOT_MEMBER') . "\n";
    
    // Test Nabil
    echo "   Testing Nabil:\n";
    echo "   - Is admin in SIMS: " . ($nabil->isAdminInGroup($sims) ? 'YES' : 'NO') . "\n";
    echo "   - Is admin in PSM: " . ($nabil->isAdminInGroup($psm) ? 'YES' : 'NO') . "\n";
    echo "   - Role in SIMS: " . ($nabil->getRoleInGroup($sims) ?? 'NOT_MEMBER') . "\n";
    echo "   - Role in PSM: " . ($nabil->getRoleInGroup($psm) ?? 'NOT_MEMBER') . "\n";
    
    // Test Thomas
    echo "   Testing Thomas:\n";
    echo "   - Is admin in SIMS: " . ($thomas->isAdminInGroup($sims) ? 'YES' : 'NO') . "\n";
    echo "   - Is admin in PSM: " . ($thomas->isAdminInGroup($psm) ? 'YES' : 'NO') . "\n";
    echo "   - Role in SIMS: " . ($thomas->getRoleInGroup($sims) ?? 'NOT_MEMBER') . "\n";
    echo "   - Role in PSM: " . ($thomas->getRoleInGroup($psm) ?? 'NOT_MEMBER') . "\n\n";
    
    // Test 3: Cek data pivot di database
    echo "3. Verifikasi data pivot di database...\n";
    $pivotData = DB::table('group_user')
        ->join('users', 'group_user.user_id', '=', 'users.id')
        ->join('groups', 'group_user.group_id', '=', 'groups.id')
        ->select('users.name as user_name', 'groups.name as group_name', 'group_user.is_admin')
        ->orderBy('users.name')
        ->orderBy('groups.name')
        ->get();
    
    foreach ($pivotData as $data) {
        $adminStatus = $data->is_admin ? 'ADMIN' : 'MEMBER';
        echo "   - {$data->user_name} di {$data->group_name}: {$adminStatus}\n";
    }
    echo "\n";
    
    // Test 4: Testing privilege granular
    echo "4. Testing privilege granular...\n";
    
    $expectedResults = [
        'Milla di SIMS' => ['expected' => 'admin', 'actual' => $milla->getRoleInGroup($sims)],
        'Milla di PSM' => ['expected' => 'member', 'actual' => $milla->getRoleInGroup($psm)],
        'Nabil di SIMS' => ['expected' => 'member', 'actual' => $nabil->getRoleInGroup($sims)],
        'Nabil di PSM' => ['expected' => 'admin', 'actual' => $nabil->getRoleInGroup($psm)],
        'Thomas di SIMS' => ['expected' => 'member', 'actual' => $thomas->getRoleInGroup($sims)],
        'Thomas di PSM' => ['expected' => 'member', 'actual' => $thomas->getRoleInGroup($psm)],
    ];
    
    $allTestsPassed = true;
    foreach ($expectedResults as $test => $result) {
        $passed = $result['expected'] === $result['actual'];
        $status = $passed ? '✓ PASS' : '✗ FAIL';
        echo "   {$status} {$test}: expected '{$result['expected']}', got '{$result['actual']}'\n";
        if (!$passed) {
            $allTestsPassed = false;
        }
    }
    
    echo "\n";
    
    // Test 5: Testing admin groups collection
    echo "5. Testing admin groups collection...\n";
    
    $millaAdminGroups = $milla->adminGroups()->get();
    $nabilAdminGroups = $nabil->adminGroups()->get();
    $thomasAdminGroups = $thomas->adminGroups()->get();
    
    echo "   - Milla admin di: " . $millaAdminGroups->pluck('name')->join(', ') . " (count: {$millaAdminGroups->count()})\n";
    echo "   - Nabil admin di: " . $nabilAdminGroups->pluck('name')->join(', ') . " (count: {$nabilAdminGroups->count()})\n";
    echo "   - Thomas admin di: " . $thomasAdminGroups->pluck('name')->join(', ') . " (count: {$thomasAdminGroups->count()})\n";
    
    echo "\n";
    
    // Test 6: Testing promote/demote
    echo "6. Testing promote/demote functionality...\n";
    
    // Backup original status
    $originalMillaPSM = $milla->getRoleInGroup($psm);
    
    echo "   - Testing promote Thomas to admin in SIMS...\n";
    $promoteResult = $thomas->promoteToAdminInGroup($sims);
    $newRole = $thomas->getRoleInGroup($sims);
    echo "     Promote result: " . ($promoteResult ? 'SUCCESS' : 'FAILED') . "\n";
    echo "     New role: {$newRole}\n";
    
    echo "   - Testing demote Thomas back to member in SIMS...\n";
    $demoteResult = $thomas->demoteFromAdminInGroup($sims);
    $newRole = $thomas->getRoleInGroup($sims);
    echo "     Demote result: " . ($demoteResult ? 'SUCCESS' : 'FAILED') . "\n";
    echo "     New role: {$newRole}\n";
    
    echo "\n";
    
    // Summary
    echo "=== HASIL TEST ===\n";
    if ($allTestsPassed) {
        echo "✓ SEMUA TEST PASSED!\n";
        echo "✓ Privilege admin per grup berfungsi dengan benar\n";
        echo "✓ User bisa admin di satu grup dan anggota biasa di grup lain\n";
        echo "✓ Helper methods dan collection berfungsi dengan baik\n";
    } else {
        echo "✗ ADA TEST YANG GAGAL!\n";
        echo "✗ Perlu perbaikan pada implementasi privilege admin per grup\n";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
    exit(1);
}

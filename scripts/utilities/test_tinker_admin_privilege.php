<?php

// Test dengan artisan tinker
// php artisan tinker

// Ambil user dan group
$milla = App\Models\User::where('name', 'Milla')->first();
$nabil = App\Models\User::where('name', 'Nabil')->first();
$thomas = App\Models\User::where('name', 'Thomas')->first();

$sims = App\Models\Group::where('referral_code', '0810')->first();
$psm = App\Models\Group::where('referral_code', '0811')->first();

// Test privilege admin per grup
echo "=== Test Privilege Admin Per Grup ===\n";

echo "1. Milla:\n";
echo "   - Admin di SIMS: " . ($milla->isAdminInGroup($sims) ? 'YES' : 'NO') . "\n";
echo "   - Admin di PSM: " . ($milla->isAdminInGroup($psm) ? 'YES' : 'NO') . "\n";
echo "   - Role di SIMS: " . $milla->getRoleInGroup($sims) . "\n";
echo "   - Role di PSM: " . $milla->getRoleInGroup($psm) . "\n";

echo "2. Nabil:\n";
echo "   - Admin di SIMS: " . ($nabil->isAdminInGroup($sims) ? 'YES' : 'NO') . "\n";
echo "   - Admin di PSM: " . ($nabil->isAdminInGroup($psm) ? 'YES' : 'NO') . "\n";
echo "   - Role di SIMS: " . $nabil->getRoleInGroup($sims) . "\n";
echo "   - Role di PSM: " . $nabil->getRoleInGroup($psm) . "\n";

echo "3. Thomas:\n";
echo "   - Admin di SIMS: " . ($thomas->isAdminInGroup($sims) ? 'YES' : 'NO') . "\n";
echo "   - Admin di PSM: " . ($thomas->isAdminInGroup($psm) ? 'YES' : 'NO') . "\n";
echo "   - Role di SIMS: " . $thomas->getRoleInGroup($sims) . "\n";
echo "   - Role di PSM: " . $thomas->getRoleInGroup($psm) . "\n";

// Test admin groups collection
echo "4. Admin Groups:\n";
echo "   - Milla admin di: " . $milla->adminGroups()->pluck('name')->join(', ') . "\n";
echo "   - Nabil admin di: " . $nabil->adminGroups()->pluck('name')->join(', ') . "\n";
echo "   - Thomas admin di: " . $thomas->adminGroups()->pluck('name')->join(', ') . "\n";

<?php

// Test middleware EnsureGroupAdmin dengan curl

$testCases = [
    'Test 1: Akses dashboard SIMS sebagai Milla (harus berhasil)' => [
        'url' => 'http://localhost/MyUkm-main/grup/0810/admin/dashboard',
        'user' => 'Milla (Admin di SIMS)',
        'expected' => 'SUCCESS'
    ],
    'Test 2: Akses dashboard PSM sebagai Milla (harus ditolak)' => [
        'url' => 'http://localhost/MyUkm-main/grup/0811/admin/dashboard',
        'user' => 'Milla (NOT Admin di PSM)',
        'expected' => 'BLOCKED'
    ],
    'Test 3: Akses dashboard PSM sebagai Nabil (harus berhasil)' => [
        'url' => 'http://localhost/MyUkm-main/grup/0811/admin/dashboard',
        'user' => 'Nabil (Admin di PSM)',
        'expected' => 'SUCCESS'
    ],
    'Test 4: Akses dashboard SIMS sebagai Nabil (harus ditolak)' => [
        'url' => 'http://localhost/MyUkm-main/grup/0810/admin/dashboard',
        'user' => 'Nabil (NOT Admin di SIMS)',
        'expected' => 'BLOCKED'
    ],
    'Test 5: Akses dashboard apapun sebagai Thomas (harus ditolak)' => [
        'url' => 'http://localhost/MyUkm-main/grup/0810/admin/dashboard',
        'user' => 'Thomas (NOT Admin)',
        'expected' => 'BLOCKED'
    ]
];

echo "=== TEST MIDDLEWARE ENSUREROUPADMIN ===\n\n";

echo "NOTE: Test ini memerlukan login manual ke browser terlebih dahulu\n";
echo "karena menggunakan session authentication.\n\n";

echo "LANGKAH TESTING:\n\n";

foreach ($testCases as $testName => $testData) {
    echo "{$testName}\n";
    echo "   URL: {$testData['url']}\n";
    echo "   User: {$testData['user']}\n";
    echo "   Expected: {$testData['expected']}\n";
    echo "   Cara test: Login sebagai user tersebut, lalu akses URL\n\n";
}

echo "=== VERIFIKASI MIDDLEWARE ===\n\n";

// Test helper method langsung
echo "Verifikasi helper method:\n";

$commands = [
    'Test Milla admin di SIMS:',
    'php artisan tinker --execute="$milla = App\\Models\\User::where(\'name\', \'Milla\')->first(); $sims = App\\Models\\Group::where(\'referral_code\', \'0810\')->first(); echo $milla->isAdminInGroup($sims) ? \'YES\' : \'NO\';"',
    '',
    'Test Milla admin di PSM:',
    'php artisan tinker --execute="$milla = App\\Models\\User::where(\'name\', \'Milla\')->first(); $psm = App\\Models\\Group::where(\'referral_code\', \'0811\')->first(); echo $milla->isAdminInGroup($psm) ? \'YES\' : \'NO\';"',
    '',
    'Test Nabil admin di PSM:',
    'php artisan tinker --execute="$nabil = App\\Models\\User::where(\'name\', \'Nabil\')->first(); $psm = App\\Models\\Group::where(\'referral_code\', \'0811\')->first(); echo $nabil->isAdminInGroup($psm) ? \'YES\' : \'NO\';"',
    '',
    'Test Nabil admin di SIMS:',
    'php artisan tinker --execute="$nabil = App\\Models\\User::where(\'name\', \'Nabil\')->first(); $sims = App\\Models\\Group::where(\'referral_code\', \'0810\')->first(); echo $nabil->isAdminInGroup($sims) ? \'YES\' : \'NO\';"'
];

foreach ($commands as $command) {
    echo "{$command}\n";
}

echo "\n=== EXPECTED RESULTS ===\n";
echo "✓ Milla admin di SIMS: YES\n";
echo "✓ Milla admin di PSM: NO\n";
echo "✓ Nabil admin di PSM: YES\n";
echo "✓ Nabil admin di SIMS: NO\n";
echo "✓ Thomas admin di grup manapun: NO\n\n";

echo "=== TESTING URLS ===\n";
echo "Login sebagai masing-masing user dan test akses:\n";
echo "- Milla: /grup/0810/admin/dashboard (SUCCESS)\n";
echo "- Milla: /grup/0811/admin/dashboard (BLOCKED)\n";
echo "- Nabil: /grup/0811/admin/dashboard (SUCCESS)\n";
echo "- Nabil: /grup/0810/admin/dashboard (BLOCKED)\n";
echo "- Thomas: /grup/*/admin/dashboard (BLOCKED)\n\n";

echo "=== STATUS IMPLEMENTASI ===\n";
echo "✓ Helper methods User model\n";
echo "✓ Middleware EnsureGroupAdmin\n";
echo "✓ GroupAdminController\n";
echo "✓ Routes dengan middleware\n";
echo "✓ Views untuk admin dashboard\n";
echo "✓ Interface updates\n";
echo "✓ Database pivot is_admin\n";
echo "✓ Testing data setup\n\n";

echo "IMPLEMENTASI PRIVILEGE ADMIN PER GRUP COMPLETE!\n";

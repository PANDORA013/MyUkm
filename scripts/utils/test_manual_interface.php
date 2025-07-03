<?php

/**
 * Test login dan verifikasi tampilan interface untuk privilege admin per grup
 */

echo "=== TEST LOGIN DAN INTERFACE PRIVILEGE ADMIN PER GRUP ===\n\n";

// Test URLs untuk akses
$testUrls = [
    'Login Milla' => [
        'url' => 'http://localhost/MyUkm-main/login',
        'credentials' => ['nim' => '3012210046', 'password' => 'password123'],
        'expected_redirect' => '/ukm'
    ],
    'UKM Index' => [
        'url' => 'http://localhost/MyUkm-main/ukm',
        'should_show' => ['link admin untuk SIMS', 'tidak ada link admin untuk PSM']
    ],
    'UKM SIMS Detail' => [
        'url' => 'http://localhost/MyUkm-main/ukm/0810',
        'should_show' => ['button Kelola Grup', 'button Kelola Anggota', 'Status: Admin Grup']
    ],
    'UKM PSM Detail' => [
        'url' => 'http://localhost/MyUkm-main/ukm/0811',
        'should_show' => ['Status: Anggota', 'tidak ada button admin']
    ],
    'Admin Dashboard SIMS (harus bisa akses)' => [
        'url' => 'http://localhost/MyUkm-main/grup/0810/admin/dashboard',
        'should_work' => true
    ],
    'Admin Dashboard PSM (harus ditolak)' => [
        'url' => 'http://localhost/MyUkm-main/grup/0811/admin/dashboard',
        'should_work' => false
    ]
];

echo "Test URLs yang perlu dicoba manual:\n\n";

foreach ($testUrls as $testName => $testData) {
    echo "### {$testName}\n";
    echo "URL: {$testData['url']}\n";
    
    if (isset($testData['credentials'])) {
        echo "Credentials: NIM={$testData['credentials']['nim']}, Password={$testData['credentials']['password']}\n";
    }
    
    if (isset($testData['should_show'])) {
        echo "Harus menampilkan:\n";
        foreach ($testData['should_show'] as $item) {
            echo "  - {$item}\n";
        }
    }
    
    if (isset($testData['should_work'])) {
        echo "Expected: " . ($testData['should_work'] ? 'BERHASIL AKSES' : 'DITOLAK/ERROR') . "\n";
    }
    
    echo "\n";
}

echo "=== LANGKAH-LANGKAH TESTING MANUAL ===\n\n";

echo "1. Buka browser ke: http://localhost/MyUkm-main/login\n";
echo "2. Login sebagai Milla (NIM: 3012210046, Password: password123)\n";
echo "3. Pergi ke halaman UKM (/ukm) dan verifikasi:\n";
echo "   - SIMS menampilkan badge 'Admin Grup'\n";
echo "   - PSM menampilkan badge 'Anggota'\n";
echo "   - SIMS memiliki button 'Kelola'\n";
echo "   - PSM tidak memiliki button 'Kelola'\n\n";

echo "4. Klik detail SIMS (/ukm/0810) dan verifikasi:\n";
echo "   - Status Anda: Admin Grup\n";
echo "   - Ada button 'Kelola Grup' dan 'Kelola Anggota'\n\n";

echo "5. Klik detail PSM (/ukm/0811) dan verifikasi:\n";
echo "   - Status Anda: Anggota\n";
echo "   - Tidak ada button admin\n\n";

echo "6. Test akses admin dashboard:\n";
echo "   - /grup/0810/admin/dashboard harus BISA diakses\n";
echo "   - /grup/0811/admin/dashboard harus DITOLAK\n\n";

echo "7. Logout dan login sebagai Nabil (NIM: 3012210045, Password: password123)\n";
echo "8. Verifikasi kebalikannya:\n";
echo "   - PSM: Admin Grup, ada button admin\n";
echo "   - SIMS: Anggota, tidak ada button admin\n\n";

echo "9. Test akses admin dashboard Nabil:\n";
echo "   - /grup/0811/admin/dashboard harus BISA diakses\n";
echo "   - /grup/0810/admin/dashboard harus DITOLAK\n\n";

echo "10. Login sebagai Thomas (NIM: 3012210044, Password: password123)\n";
echo "11. Verifikasi Thomas adalah anggota biasa di kedua grup:\n";
echo "    - Tidak ada button admin di grup manapun\n";
echo "    - Tidak bisa akses dashboard admin manapun\n\n";

echo "=== HASIL YANG DIHARAPKAN ===\n";
echo "✓ Privilege admin hanya berlaku di grup tertentu\n";
echo "✓ Interface menampilkan role per grup dengan benar\n";
echo "✓ Button admin hanya muncul di grup tempat user adalah admin\n";
echo "✓ Akses admin dashboard dibatasi sesuai privilege per grup\n";
echo "✓ User bisa admin di satu grup dan anggota biasa di grup lain\n";

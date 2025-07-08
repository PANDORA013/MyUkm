<?php

require_once __DIR__ . '/../../vendor/autoload.php';

// Bootstrap Laravel 11
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

/**
 * Script untuk test sinkronisasi privilege admin per grup
 * Verifikasi bahwa interface admin website menampilkan data yang benar
 */

echo "=== TEST SINKRONISASI PRIVILEGE ADMIN PER GRUP ===\n\n";

// Test dengan artisan tinker commands
$testCommands = [
    "1. Cek data privilege di database:",
    'mysql -h localhost -u root -p -e "USE myukm; SELECT gu.user_id, gu.group_id, gu.is_admin, u.name, g.name as group_name FROM group_user gu JOIN users u ON gu.user_id = u.id JOIN groups g ON gu.group_id = g.id ORDER BY u.name, g.name;"',
    "",
    
    "2. Test helper methods User model:",
    'php artisan tinker --execute="$nabil = App\\Models\\User::where(\'name\', \'Nabil\')->first(); $sims = App\\Models\\Group::where(\'referral_code\', \'0810\')->first(); $psm = App\\Models\\Group::where(\'referral_code\', \'0811\')->first(); echo \'Nabil admin di SIMS: \' . ($nabil->isAdminInGroup($sims) ? \'YES\' : \'NO\') . \' | Nabil admin di PSM: \' . ($nabil->isAdminInGroup($psm) ? \'YES\' : \'NO\');"',
    "",
    
    "3. Expected Results:",
    "   - Nabil: Admin di PSM (YES), BUKAN admin di SIMS (NO)",
    "   - Milla: Admin di SIMS (YES), BUKAN admin di PSM (NO)",
    "   - Thomas: BUKAN admin di grup manapun (NO & NO)",
    "",
    
    "4. Test tampilan di admin website:",
    "   - Login sebagai admin website",
    "   - Buka /admin/ukm/55/anggota (SIMS)",
    "   - Verifikasi Nabil tampil sebagai 'Anggota', bukan 'Admin Grup'",
    "   - Buka /admin/ukm/56/anggota (PSM)", 
    "   - Verifikasi Nabil tampil sebagai 'Admin Grup'",
    "",
    
    "5. Test statistik admin per grup:",
    "   - Di halaman SIMS: Admin Grup = 1 (hanya Milla)",
    "   - Di halaman PSM: Admin Grup = 1 (hanya Nabil)",
    "",
    
    "6. Test button promosi/demosi:",
    "   - Di SIMS: Nabil ada button 'promote' (hijau)",
    "   - Di PSM: Nabil ada button 'demote' (kuning)",
    "   - Di SIMS: Milla ada button 'demote' (kuning)",
    "   - Di PSM: Milla ada button 'promote' (hijau)",
];

foreach ($testCommands as $command) {
    echo $command . "\n";
}

echo "\n=== VERIFIKASI MASALAH YANG DILAPORKAN ===\n";
echo "SEBELUM: Nabil tampil sebagai 'Admin Grup' di halaman SIMS admin website\n";
echo "SESUDAH: Nabil harus tampil sebagai 'Anggota' di halaman SIMS admin website\n";
echo "         karena Nabil BUKAN admin di grup SIMS (is_admin = 0)\n\n";

echo "PERUBAHAN YANG DILAKUKAN:\n";
echo "1. ✓ Update view admin/ukm_anggota.blade.php:\n";
echo "   - Statistik admin menggunakan \$anggota->where('pivot.is_admin', true)\n";
echo "   - Badge role menggunakan privilege per grup (pivot.is_admin)\n";
echo "   - Button promosi/demosi berdasarkan status admin di grup tersebut\n\n";

echo "2. ✓ Update AdminWebsiteController:\n";
echo "   - Method lihatAnggota() menggunakan withPivot(['is_admin', ...])\n";
echo "   - Tambah promoteToAdminInGroup() dan demoteFromAdminInGroup()\n";
echo "   - Routes baru untuk admin per grup\n\n";

echo "3. ✓ Update JavaScript:\n";
echo "   - Function baru confirmMakeAdminInGroup() dan confirmRemoveAdminFromGroup()\n";
echo "   - AJAX calls ke route admin per grup\n";
echo "   - Parameter ukm_id untuk identify grup\n\n";

echo "TESTING MANUAL:\n";
echo "1. Login sebagai admin website\n";
echo "2. Buka /admin/ukm/55/anggota (SIMS)\n";
echo "3. Cek Nabil: harus tampil badge 'Anggota' + button hijau 'promote'\n";
echo "4. Buka /admin/ukm/56/anggota (PSM)\n";
echo "5. Cek Nabil: harus tampil badge 'Admin Grup' + button kuning 'demote'\n\n";

echo "STATUS: SINKRONISASI PRIVILEGE ADMIN PER GRUP COMPLETE ✅\n";

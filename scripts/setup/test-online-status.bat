@echo off
REM Script untuk test sinkronisasi status online anggota UKM
REM Pastikan server Laravel dan database sudah berjalan

echo === Test Sinkronisasi Status Online Anggota UKM ===
echo.

REM Test 1: Periksa route tersedia
echo 1. Memeriksa route online status...
php artisan route:list --name=chat.online-members
php artisan route:list --name=chat.update-online-status
echo.

REM Test 2: Test basic functionality
echo 2. Test basic online status functionality...
php artisan tinker --execute="$user = App\Models\User::first(); if ($user) { echo 'User: ' . $user->name . PHP_EOL; echo 'Online: ' . ($user->isOnline() ? 'Yes' : 'No') . PHP_EOL; $user->update(['last_seen_at' => now()]); echo 'Updated and online: ' . ($user->fresh()->isOnline() ? 'Yes' : 'No') . PHP_EOL; }"
echo.

echo === Test Selesai ===
echo.
echo Untuk test lengkap:
echo 1. Buka browser ke halaman chat UKM
echo 2. Buka tab/window baru dengan user lain  
echo 3. Perhatikan status online yang tersinkronisasi
echo 4. Check console browser untuk log status online
echo.
pause

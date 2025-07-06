@echo off
SETLOCAL EnableDelayedExpansion
echo ===============================================
echo =          MYUKM PROJECT STARTER           =
echo ===============================================
echo.
echo Memulai persiapan proyek MyUkm...

:: Set lokasi XAMPP (sesuaikan jika berbeda)
set XAMPP_PATH=C:\xampp
set PROJECT_PATH=%~dp0

:: Masuk ke direktori proyek
cd /d %PROJECT_PATH%

:: 1. Cek dan jalankan XAMPP
echo.
echo [1/9] Memeriksa XAMPP...
tasklist | findstr /i "httpd.exe mysqld.exe" > nul
if %errorlevel% equ 0 (
    echo ✓ XAMPP (Apache & MySQL) sudah berjalan
) else (
    echo ! Menjalankan XAMPP Control Panel...
    start "" "%XAMPP_PATH%\xampp-control.exe"
    echo ! Harap pastikan Apache dan MySQL sudah berjalan di XAMPP Control Panel
    timeout /t 10 /nobreak
)

:: 2. Install Composer Dependencies
echo.
echo [2/9] Menginstall dependensi Composer...
if exist composer.phar (
    php composer.phar install
) else (
    composer install
)
if %errorlevel% neq 0 (
    echo x Gagal menginstall dependensi Composer
    pause
    exit /b 1
)

:: 3. Install NPM Dependencies
echo.
echo [3/9] Menginstall dependensi NPM...
call npm install
if %errorlevel% neq 0 (
    echo x Gagal menginstall dependensi NPM
    pause
    exit /b 1
)

:: 4. Setup .env
echo.
echo [4/9] Memeriksa file .env...
if not exist ".env" (
    echo ! Membuat file .env dari .env.example
    copy ".env.example" ".env"
    
    echo ! Generate application key
    call php artisan key:generate
) else (
    echo ✓ File .env sudah ada
)

:: 5. Jalankan Vite di terminal terpisah
echo.
echo [5/9] Menjalankan Vite dev server...
start "Vite Dev Server" cmd /k "npm run dev"

echo ! Menunggu Vite siap...
timeout /t 5 /nobreak

:: 6. Jalankan migrasi
echo.
echo [6/9] Menjalankan migrasi database...
call php artisan migrate --force
if %errorlevel% neq 0 (
    echo ! Ada masalah dengan migrasi database
    echo ! Pastikan database 'myukm' sudah dibuat di MySQL
)

:: 7. Jalankan seeder (jika ada)
echo.
echo [7/9] Menjalankan database seeder...
call php artisan db:seed --force

:: 8. Jalankan Laravel server di terminal terpisah
echo.
echo [8/9] Menjalankan Laravel server...
start "Laravel Server" cmd /k "php artisan serve"

:: 9. Buka browser
echo.
echo [9/9] Membuka browser...
start http://localhost:8000

echo.
echo ===============================================
echo =            SELESAI!                           =
echo ===============================================
echo.
echo Aplikasi MyUkm berhasil dijalankan!
echo.
echo 1. Vite Dev Server: http://localhost:5173
echo 2. Laravel Server: http://localhost:8000
echo.
echo Tekan tombol apa saja untuk menutup jendela ini...
pause > nul

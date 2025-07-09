@echo off
title MyUKM - Universal Launcher
color 0A

:MAIN_MENU
cls
echo.
echo ===============================================
echo            MyUKM Universal Launcher
echo ===============================================
echo.
echo Select an option:
echo.
echo [1] Development Server (with hot reload)
echo [2] Production-like Server
echo [3] Queue Worker Only
echo [4] Realtime Development (Laravel + Queue + Vite)
echo [5] Full Development Stack
echo [6] Quick Start (artisan serve only)
echo [7] Laravel + Queue Worker (Chat Ready) ⭐ RECOMMENDED
echo [8] Complete Realtime Stack (All Services)
echo [9] Demo Real-time Features
echo.
echo [T] Test Menu
echo [U] Utilities Menu
echo [Q] Quit
echo.
set /p choice="Enter your choice: "

if /i "%choice%"=="1" goto DEV_SERVER
if /i "%choice%"=="2" goto PRODUCTION_SERVER
if /i "%choice%"=="3" goto QUEUE_WORKER
if /i "%choice%"=="4" goto REALTIME_DEV
if /i "%choice%"=="5" goto FULL_DEV
if /i "%choice%"=="6" goto QUICK_START
if /i "%choice%"=="7" goto LARAVEL_QUEUE
if /i "%choice%"=="8" goto COMPLETE_REALTIME
if /i "%choice%"=="9" goto DEMO_REALTIME
if /i "%choice%"=="T" goto TEST_MENU
if /i "%choice%"=="U" goto UTILITIES_MENU
if /i "%choice%"=="Q" goto END

echo Invalid choice. Please try again.
pause
goto MAIN_MENU

:DEV_SERVER
echo.
echo ===============================================
echo        Starting Development Server
echo ===============================================
echo.
echo [1/4] Checking environment...
if not exist ".env" (
    echo Creating .env from template...
    copy ".env.example" ".env" >nul
    php artisan key:generate
)

echo [2/4] Installing dependencies...
if not exist "vendor\autoload.php" (
    composer install
)
if not exist "node_modules" (
    npm install
)

echo [3/4] Running migrations...
php artisan migrate --force

echo [4/4] Starting servers...
start "Vite Dev Server" cmd /k "npm run dev"
timeout /t 3 >nul
start "Laravel Server" cmd /k "php artisan serve --host=0.0.0.0 --port=8000"

echo.
echo ✓ Development server started!
echo ✓ Laravel: http://localhost:8000
echo ✓ Vite: http://localhost:5173
echo.
pause
goto MAIN_MENU

:PRODUCTION_SERVER
echo.
echo ===============================================
echo        Starting Production-like Server
echo ===============================================
echo.
echo [1/6] Checking environment...
if not exist ".env" (
    copy ".env.example" ".env" >nul
    php artisan key:generate --force
)

echo [2/6] Installing production dependencies...
composer install --no-dev --optimize-autoloader

echo [3/6] Building assets...
npm ci --production
npm run build

echo [4/6] Optimizing application...
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo [5/6] Running migrations...
php artisan migrate --force

echo [6/6] Starting production server...
php artisan serve --host=0.0.0.0 --port=8000

goto MAIN_MENU

:QUEUE_WORKER
echo.
echo ===============================================
echo            Starting Queue Worker Only
echo ===============================================
echo.
echo Starting Laravel queue worker for background jobs...
echo This handles chat messages, notifications, and other async tasks
php artisan queue:work --verbose --tries=3 --timeout=90

goto MAIN_MENU

:LARAVEL_QUEUE
echo.
echo ===============================================
echo       Laravel + Queue Worker (Chat Ready)
echo ===============================================
echo.
echo [1/3] Checking environment...
if not exist ".env" (
    echo Creating .env from template...
    copy ".env.example" ".env" >nul
    php artisan key:generate
)

echo [2/3] Starting Laravel server...
start "Laravel Server" cmd /k "php artisan serve --host=0.0.0.0 --port=8000"

echo [3/3] Starting queue worker for real-time features...
start "Queue Worker" cmd /k "php artisan queue:work --queue=realtime,default --verbose --tries=3 --timeout=90"

echo.
echo ✓ Laravel + Queue Worker started!
echo ✓ Laravel: http://localhost:8000
echo ✓ Queue Worker: Running in background
echo ✓ Chat and notifications are now ready!
echo.
pause
goto MAIN_MENU

:COMPLETE_REALTIME
echo.
echo ===============================================
echo      Complete Realtime Stack (All Services)
echo ===============================================
echo.
echo [1/5] Checking environment...
if not exist ".env" (
    echo Creating .env from template...
    copy ".env.example" ".env" >nul
    php artisan key:generate
)

echo [2/5] Installing dependencies...
if not exist "vendor\autoload.php" (
    composer install
)
if not exist "node_modules" (
    npm install
)

echo [3/5] Running migrations...
php artisan migrate --force

echo [4/5] Starting all realtime services...
start "Queue Worker (High Priority)" cmd /k "php artisan queue:work --queue=realtime,default --verbose --tries=3 --timeout=90"
timeout /t 2 >nul
start "Laravel Server" cmd /k "php artisan serve --host=0.0.0.0 --port=8000"
timeout /t 2 >nul
start "Vite Dev Server" cmd /k "npm run dev"

echo [5/5] Services started successfully!

echo.
echo ✓ Complete Realtime Stack is running!
echo ✓ Laravel: http://localhost:8000
echo ✓ Vite HMR: http://localhost:5173
echo ✓ Queue Worker: Processing realtime tasks
echo ✓ Chat, notifications, and live updates ready!
echo.
echo All services are running in separate windows for optimal performance.
echo Close individual windows to stop specific services.
echo.
pause
goto MAIN_MENU

:REALTIME_DEV
echo.
echo ===============================================
echo        Starting Realtime Development
echo ===============================================
echo.
echo [1/3] Starting queue worker...
start "Queue Worker" cmd /k "php artisan queue:work --queue=realtime,default --verbose"

echo [2/3] Starting Laravel server...
start "Laravel Server" cmd /k "php artisan serve --host=0.0.0.0 --port=8000"

echo [3/3] Starting Vite dev server...
start "Vite Dev Server" cmd /k "npm run dev"

echo.
echo ✓ Realtime development environment started!
echo ✓ All services are running in separate windows
echo.
pause
goto MAIN_MENU

:FULL_DEV
echo [0/5] Memastikan admin website default tersedia...
php artisan db:seed --class=AdminWebsiteSeeder
if %errorlevel% neq 0 (
    echo Gagal menjalankan seeder AdminWebsiteSeeder. Pastikan database terkoneksi!
    pause
    goto MAIN_MENU
)
echo Membersihkan cache Laravel...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
echo ✓ Admin website default (NIM: TH.171004, Password: AR.171004) sudah tersedia!
echo.
echo ===============================================
echo        Starting Full Development Stack
echo ===============================================
echo.

echo [1/5] Checking dependencies...
if not exist "vendor\autoload.php" (
    echo Composer dependencies belum terinstall, menjalankan composer install...
    composer install
)
if not exist "node_modules" (
    echo Node.js dependencies belum terinstall, menjalankan npm install...
    npm install
)


echo [2/5] Setting up environment...
if not exist ".env" (
    copy ".env.example" ".env" >nul
    php artisan key:generate
)

echo [3/5] Database setup...
php artisan migrate
php artisan db:seed

echo [4/5] Starting all services...
start "Queue Worker" cmd /k "php artisan queue:work --verbose"
start "Laravel Server" cmd /k "php artisan serve --host=0.0.0.0 --port=8000"
start "Vite Dev Server" cmd /k "npm run dev"

echo [5/5] Opening browser...
timeout /t 5 >nul
start http://localhost:8000

echo.
echo ✓ Full development stack started!
echo.
pause
goto MAIN_MENU

:QUICK_START
echo.
echo ===============================================
echo            Quick Start - Laravel Only
echo ===============================================
echo.
php artisan serve --host=0.0.0.0 --port=8000

goto MAIN_MENU

:TEST_MENU
cls
echo.
echo ===============================================
echo                Test Menu
echo ===============================================
echo.
echo [1] Run All Tests
echo [2] Run Feature Tests Only
echo [3] Run Unit Tests Only
echo [4] Run Specific Test File
echo [5] Run Tests with Coverage
echo [6] Test Database Connection
echo [7] Test Broadcasting
echo [8] Performance Tests
echo.
echo [B] Back to Main Menu
echo.
set /p test_choice="Enter your choice: "

if /i "%test_choice%"=="1" (
    php artisan test
    pause
    goto TEST_MENU
)
if /i "%test_choice%"=="2" (
    php artisan test tests/Feature
    pause
    goto TEST_MENU
)
if /i "%test_choice%"=="3" (
    php artisan test tests/Unit
    pause
    goto TEST_MENU
)
if /i "%test_choice%"=="4" (
    set /p test_file="Enter test file path (e.g., tests/Feature/Auth/LoginTest.php): "
    php artisan test %test_file%
    pause
    goto TEST_MENU
)
if /i "%test_choice%"=="5" (
    vendor\bin\phpunit --coverage-html coverage
    echo Coverage report generated in ./coverage/index.html
    pause
    goto TEST_MENU
)
if /i "%test_choice%"=="6" (
    php artisan tinker --execute="DB::connection()->getPdo(); echo 'Database connection successful!';"
    pause
    goto TEST_MENU
)
if /i "%test_choice%"=="7" (
    php scripts\test\test-realtime-complete.php
    pause
    goto TEST_MENU
)
if /i "%test_choice%"=="8" (
    echo Running performance tests...
    php artisan test --group=performance
    pause
    goto TEST_MENU
)
if /i "%test_choice%"=="B" goto MAIN_MENU

echo Invalid choice. Please try again.
pause
goto TEST_MENU

:UTILITIES_MENU
cls
echo.
echo ===============================================
echo              Utilities Menu
echo ===============================================
echo.
echo [1] Clear All Caches
echo [2] Reset Database
echo [3] Generate App Key
echo [4] Check System Status
echo [5] Fix File Permissions
echo [6] Optimize Application
echo [7] Check Group URLs
echo [8] Organize Files
echo.
echo [B] Back to Main Menu
echo.
set /p util_choice="Enter your choice: "

if /i "%util_choice%"=="1" (
    echo Clearing all caches...
    php artisan cache:clear
    php artisan config:clear
    php artisan route:clear
    php artisan view:clear
    echo ✓ All caches cleared!
    pause
    goto UTILITIES_MENU
)
if /i "%util_choice%"=="2" (
    echo Resetting database...
    php artisan migrate:fresh --seed
    echo ✓ Database reset complete!
    pause
    goto UTILITIES_MENU
)
if /i "%util_choice%"=="3" (
    php artisan key:generate
    echo ✓ App key generated!
    pause
    goto UTILITIES_MENU
)
if /i "%util_choice%"=="4" (
    .\scripts\utilities\check-status.bat
    pause
    goto UTILITIES_MENU
)
if /i "%util_choice%"=="5" (
    echo Fixing file permissions...
    attrib -r storage\* /s
    echo ✓ Permissions fixed!
    pause
    goto UTILITIES_MENU
)
if /i "%util_choice%"=="6" (
    echo Optimizing application...
    php artisan optimize
    composer dump-autoload --optimize
    echo ✓ Application optimized!
    pause
    goto UTILITIES_MENU
)
if /i "%util_choice%"=="7" (
    php scripts\utilities\check-group-urls.php
    pause
    goto UTILITIES_MENU
)
if /i "%util_choice%"=="8" (
    php scripts\utilities\organize-files.php
    pause
    goto UTILITIES_MENU
)
if /i "%util_choice%"=="B" goto MAIN_MENU

echo Invalid choice. Please try again.
pause
goto UTILITIES_MENU

:DEMO_REALTIME
echo.
echo ===============================================
echo         MyUKM Real-time Features Demo
echo ===============================================
echo.
echo This will start a guided demo of MyUKM's real-time features:
echo   ✓ Laravel Server (http://localhost:8000)
echo   ✓ Queue Worker (for instant chat and notifications)
echo   ✓ Auto-opens browser with instructions
echo.
echo Perfect for testing:
echo   - Real-time chat messaging
echo   - Live notifications
echo   - User status updates
echo   - Group activity broadcasts
echo.
set /p demo_confirm="Start real-time demo? (Y/N): "
if /i "%demo_confirm%" NEQ "Y" goto MAIN_MENU

echo.
echo Starting demo...
start demo-realtime.bat
echo.
echo ✓ Demo launcher started!
echo ✓ Follow the instructions in the demo window
echo.
pause
goto MAIN_MENU

:END
echo.
echo Thank you for using MyUKM Universal Launcher!
echo.
pause
exit

REM Error handling
:ERROR
echo.
echo An error occurred. Please check the output above.
echo.
pause
goto MAIN_MENU

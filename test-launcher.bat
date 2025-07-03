@echo off
title Test MyUKM Launch Scripts
color 0B

echo =========================================
echo       MyUKM Launch Scripts Test
echo =========================================
echo.
echo Testing all available launch options...
echo.

echo 📋 Available Launch Scripts:
echo   1. launch-myukm.bat     - Complete setup (first time)
echo   2. instant-launch.bat   - Quick launch (daily use)
echo   3. quick-start.bat      - Legacy quick start
echo   4. server-menu.bat      - Server menu interface
echo.

:menu
echo =========================================
echo Select test option:
echo.
echo   [1] Test Complete Launch (launch-myukm.bat)
echo   [2] Test Instant Launch (instant-launch.bat)
echo   [3] Test Queue Worker Only
echo   [4] Test Environment Setup
echo   [5] Create Desktop Shortcuts
echo   [6] View Project URLs
echo   [0] Exit
echo.
set /p choice="Enter your choice (0-6): "

if "%choice%"=="1" goto test_complete
if "%choice%"=="2" goto test_instant
if "%choice%"=="3" goto test_queue
if "%choice%"=="4" goto test_env
if "%choice%"=="5" goto create_shortcuts
if "%choice%"=="6" goto show_urls
if "%choice%"=="0" goto end
goto menu

:test_complete
echo.
echo =========================================
echo   Testing Complete Launch Script
echo =========================================
echo.
echo 🧪 This will run the full setup process...
echo 💡 Press Ctrl+C in the server window to stop
echo.
pause
call launch-myukm.bat
echo.
echo ✅ Complete launch test finished
echo.
pause
goto menu

:test_instant
echo.
echo =========================================
echo   Testing Instant Launch Script
echo =========================================
echo.
echo 🧪 This will run the quick launch...
echo 💡 Press Ctrl+C in the server window to stop
echo.
pause
call instant-launch.bat
echo.
echo ✅ Instant launch test finished
echo.
pause
goto menu

:test_queue
echo.
echo =========================================
echo   Testing Queue Worker Only
echo =========================================
echo.
echo 🧪 Starting queue worker for testing...
echo 💡 Press Ctrl+C to stop the worker
echo.
php artisan queue:work --timeout=60 --sleep=3 --tries=3 --verbose
echo.
echo ✅ Queue worker test finished
echo.
pause
goto menu

:test_env
echo.
echo =========================================
echo   Testing Environment Setup
echo =========================================
echo.
echo 🧪 Checking environment configuration...
echo.

if exist ".env" (
    echo ✅ .env file exists
) else (
    echo ❌ .env file missing
    if exist ".env.example" (
        echo 🔧 Creating .env from example...
        copy ".env.example" ".env"
        echo ✅ .env file created
    )
)

echo.
echo 🔑 Checking application key...
php artisan key:generate --show
echo.

echo 🗄️ Checking database connection...
php artisan migrate:status
echo.

echo ✅ Environment test completed
echo.
pause
goto menu

:create_shortcuts
echo.
echo =========================================
echo   Creating Desktop Shortcuts
echo =========================================
echo.
call create-shortcuts.bat
pause
goto menu

:show_urls
echo.
echo =========================================
echo       MyUKM Application URLs
echo =========================================
echo.
echo 🌐 Main Application:
echo   • Homepage:    http://localhost:8000/
echo   • Login:       http://localhost:8000/login
echo   • Register:    http://localhost:8000/register
echo   • Dashboard:   http://localhost:8000/dashboard
echo.
echo 💬 Chat & Real-time:
echo   • Chat:        http://localhost:8000/chat
echo   • Groups:      http://localhost:8000/groups
echo.
echo 👤 User Management:
echo   • Profile:     http://localhost:8000/profile
echo   • Settings:    http://localhost:8000/settings
echo.
echo 🔧 Admin Panel:
echo   • Admin:       http://localhost:8000/admin
echo   • Users:       http://localhost:8000/admin/users
echo   • Groups:      http://localhost:8000/admin/groups
echo.
echo 💡 Development Tools:
echo   • Logs:        storage/logs/laravel.log
echo   • Queue Jobs:  Check running queue worker
echo   • Database:    database/database.sqlite
echo.
pause
goto menu

:end
echo.
echo =========================================
echo   Thank you for testing MyUKM!
echo =========================================
echo.
echo 💡 Quick Start Tips:
echo   • First time: Run launch-myukm.bat
echo   • Daily use: Run instant-launch.bat
echo   • Create shortcuts for easy access
echo.
echo 🌐 Access: http://localhost:8000
echo.
pause

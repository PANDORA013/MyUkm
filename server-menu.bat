@echo off
title MyUKM Server Launcher Menu
color 0A

:menu
cls
echo.
echo ================================================================
echo                  MyUKM Development Server Menu
echo ================================================================
echo.
echo   🚀 Choose your startup mode:
echo.
echo   [1] Quick Start          - Fast startup, minimal setup
echo   [2] Full Development     - All services + monitoring  
echo   [3] Production-like      - Optimized + database checks
echo   [4] Simple Server Only   - Just Laravel server
echo.
echo   [5] Database Setup       - Setup database and migrations
echo   [6] Clear All Caches     - Clear Laravel caches
echo   [7] View Server Status   - Check if server is running
echo.
echo   [0] Exit
echo.
echo ================================================================
echo.
set /p choice="Enter your choice (0-7): "

if "%choice%"=="1" goto quick_start
if "%choice%"=="2" goto full_dev
if "%choice%"=="3" goto production_like
if "%choice%"=="4" goto simple_server
if "%choice%"=="5" goto database_setup
if "%choice%"=="6" goto clear_caches
if "%choice%"=="7" goto server_status
if "%choice%"=="0" goto exit

echo Invalid choice! Please try again.
timeout /t 2 /nobreak >nul
goto menu

:quick_start
echo.
echo Starting Quick Start mode...
call quick-start.bat
goto menu

:full_dev
echo.
echo Starting Full Development mode...
call start-full-dev.bat
goto menu

:production_like
echo.
echo Starting Production-like mode...
call start-production-like.bat
goto menu

:simple_server
echo.
echo Starting simple Laravel server...
echo.
echo URL: http://localhost:8000
echo Press Ctrl+C to stop
echo.
start http://localhost:8000
php artisan serve
goto menu

:database_setup
echo.
echo ================================
echo      Database Setup
echo ================================
echo.
echo [1/4] Checking environment...
if not exist ".env" (
    echo Creating .env file...
    copy ".env.example" ".env" >nul
    php artisan key:generate
    echo.
    echo ⚠ Please configure your database settings in .env file
    echo Then run this option again.
    pause
    goto menu
)

echo [2/4] Testing database connection...
php artisan migrate:status >nul 2>&1
if errorlevel 1 (
    echo ❌ Database connection failed!
    echo Please check your database configuration in .env file.
    pause
    goto menu
)

echo [3/4] Running migrations...
php artisan migrate

echo [4/4] Seeding database (optional)...
set /p seed="Do you want to seed the database with test data? (y/n): "
if /i "%seed%"=="y" (
    php artisan db:seed
    echo ✓ Database seeded
)

echo.
echo ✅ Database setup completed!
pause
goto menu

:clear_caches
echo.
echo ================================
echo      Clearing Caches
echo ================================
echo.
echo Clearing configuration cache...
php artisan config:clear
echo Clearing route cache...
php artisan route:clear
echo Clearing view cache...
php artisan view:clear
echo Clearing application cache...
php artisan cache:clear
echo Clearing compiled services...
php artisan clear-compiled
echo.
echo ✅ All caches cleared!
pause
goto menu

:server_status
echo.
echo ================================
echo      Server Status Check
echo ================================
echo.
echo Checking if Laravel server is running on port 8000...
netstat -an | findstr ":8000" >nul 2>&1
if errorlevel 1 (
    echo ❌ Server is NOT running on port 8000
) else (
    echo ✅ Server appears to be running on port 8000
    echo    URL: http://localhost:8000
)
echo.
echo Checking Laravel application...
if exist "artisan" (
    echo ✅ Laravel application detected
    php artisan --version
) else (
    echo ❌ Laravel application not found in current directory
)
echo.
pause
goto menu

:exit
echo.
echo Thank you for using MyUKM Server Launcher!
echo.
exit /b 0

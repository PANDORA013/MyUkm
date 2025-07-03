@echo off
title MyUKM Production-like Server
color 0A

echo.
echo ===============================================
echo        MyUKM Production-like Environment
echo ===============================================
echo.

REM Check if we're in correct directory
if not exist "artisan" (
    echo [ERROR] Laravel project not found in current directory!
    echo Please navigate to your Laravel project root and try again.
    pause
    exit /b 1
)

echo [1/8] Checking PHP installation...
php --version >nul 2>&1
if errorlevel 1 (
    echo [ERROR] PHP not found! Please install PHP and add to PATH.
    pause
    exit /b 1
)
echo      ✓ PHP is available

echo [2/8] Checking Composer dependencies...
if not exist "vendor\autoload.php" (
    echo      Installing Composer dependencies...
    composer install --no-dev --optimize-autoloader
) else (
    echo      ✓ Dependencies already installed
)

echo [3/8] Checking environment configuration...
if not exist ".env" (
    echo      Creating .env from template...
    copy ".env.example" ".env" >nul
    php artisan key:generate --force
    echo      ⚠ Please configure database settings in .env file
) else (
    echo      ✓ Environment file exists
)

echo [4/8] Optimizing application...
php artisan config:cache --quiet
php artisan route:cache --quiet
php artisan view:cache --quiet
echo      ✓ Application optimized

echo [5/8] Checking database connection...
php artisan migrate:status >nul 2>&1
if errorlevel 1 (
    echo      ⚠ Database not configured or not accessible
    echo      ℹ Configure database in .env file for full functionality
    set DB_OK=false
) else (
    echo      ✓ Database connection successful
    set DB_OK=true
)

echo [6/8] Running database migrations...
if "%DB_OK%"=="true" (
    php artisan migrate --force --quiet
    echo      ✓ Database migrations completed
) else (
    echo      ⚠ Skipping migrations (database not available)
)

echo [7/8] Starting background services...
REM Start queue worker in background
start "Queue Worker" /MIN cmd /k "title Queue Worker && php artisan queue:work --daemon --tries=3 --timeout=90"
echo      ✓ Queue worker started

echo [8/8] Starting web server...
echo.
echo ===============================================
echo   🌟 MyUKM Server Started Successfully!
echo ===============================================
echo.
echo   🌐 Application URL: http://localhost:8000
echo   📊 Status: Production-like environment
echo   💾 Database: %DB_OK%
echo   ⚡ Queue: Active
echo   🔄 Optimized: Yes
echo.
echo   📋 Available Pages:
echo   - Homepage:     http://localhost:8000/
echo   - Login:        http://localhost:8000/login
echo   - Register:     http://localhost:8000/register
echo   - Dashboard:    http://localhost:8000/dashboard
echo   - Chat:         http://localhost:8000/chat
echo   - Admin:        http://localhost:8000/admin
echo.
echo   🎯 Ready for development and testing!
echo.
echo ===============================================
echo.

REM Auto-open browser
timeout /t 2 /nobreak >nul
start http://localhost:8000

REM Start the server
echo Server logs will appear below:
echo =============================
php artisan serve --host=0.0.0.0 --port=8000

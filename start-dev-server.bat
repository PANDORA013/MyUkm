@echo off
title MyUKM Development Server Launcher
color 0A

echo.
echo ============================================================
echo                MyUKM Development Server Launcher
echo ============================================================
echo.
echo Starting all required services for MyUKM development...
echo.

REM Check if PHP is available
php --version >nul 2>&1
if errorlevel 1 (
    echo [ERROR] PHP not found in PATH. Please install PHP or add it to PATH.
    echo Press any key to exit...
    pause >nul
    exit /b 1
)

REM Check if Composer is available  
composer --version >nul 2>&1
if errorlevel 1 (
    echo [ERROR] Composer not found in PATH. Please install Composer or add it to PATH.
    echo Press any key to exit...
    pause >nul
    exit /b 1
)

REM Check if we're in the right directory
if not exist "artisan" (
    echo [ERROR] Laravel artisan file not found. Please run this script from Laravel project root.
    echo Press any key to exit...
    pause >nul
    exit /b 1
)

echo [INFO] All prerequisites check passed!
echo.

REM Clear Laravel caches
echo [1/6] Clearing Laravel caches...
php artisan config:clear --quiet
php artisan route:clear --quiet  
php artisan view:clear --quiet
php artisan cache:clear --quiet
echo      ✓ Caches cleared

REM Install/Update dependencies if needed
if not exist "vendor" (
    echo [2/6] Installing Composer dependencies...
    composer install --quiet
    echo      ✓ Composer dependencies installed
) else (
    echo [2/6] Composer dependencies already installed
    echo      ✓ Skipping composer install
)

REM Check if .env exists
if not exist ".env" (
    echo [3/6] Creating .env file from .env.example...
    copy ".env.example" ".env" >nul
    php artisan key:generate --quiet
    echo      ✓ Environment file created and key generated
) else (
    echo [3/6] Environment file already exists
    echo      ✓ Using existing .env file
)

REM Run migrations if database is configured
echo [4/6] Checking database connection...
php artisan migrate:status >nul 2>&1
if errorlevel 1 (
    echo      ⚠ Database not configured or not accessible
    echo      ℹ You may need to configure database in .env file
) else (
    echo      ✓ Database connection OK
    echo [4.1/6] Running pending migrations...
    php artisan migrate --force --quiet
    echo      ✓ Database migrations completed
)

echo [5/6] Starting Laravel development server...
echo.
echo ============================================================
echo   🚀 MyUKM Development Environment Started Successfully!
echo ============================================================
echo.
echo   📱 Application URL: http://localhost:8000
echo   🔧 Admin Panel:     http://localhost:8000/admin  
echo   💬 Chat System:     http://localhost:8000/chat
echo   📊 Database:        Configured in .env
echo.
echo   💡 Tips:
echo   - Press Ctrl+C to stop the server
echo   - Open http://localhost:8000 in your browser
echo   - Check logs in storage/logs/ for debugging
echo.
echo ============================================================
echo.

REM Start the Laravel development server
echo [6/6] Laravel server starting on http://localhost:8000...
echo.
php artisan serve --host=0.0.0.0 --port=8000

REM This will only execute if the server stops
echo.
echo [INFO] Laravel development server stopped.
echo Press any key to exit...
pause >nul

@echo off
title MyUKM - System Status Check
color 0B

echo.
echo ===============================================
echo            MyUKM System Status Check
echo ===============================================
echo.

REM Check PHP
echo [1/8] Checking PHP installation...
php --version >nul 2>&1
if errorlevel 1 (
    echo ❌ PHP not found!
) else (
    echo ✅ PHP is available
    php --version | findstr /R "PHP"
)

REM Check Composer
echo [2/8] Checking Composer...
composer --version >nul 2>&1
if errorlevel 1 (
    echo ❌ Composer not found!
) else (
    echo ✅ Composer is available
    composer --version | findstr /R "Composer"
)

REM Check Node.js
echo [3/8] Checking Node.js...
node --version >nul 2>&1
if errorlevel 1 (
    echo ❌ Node.js not found!
) else (
    echo ✅ Node.js is available
    node --version
)

REM Check NPM
echo [4/8] Checking NPM...
npm --version >nul 2>&1
if errorlevel 1 (
    echo ❌ NPM not found!
) else (
    echo ✅ NPM is available
    npm --version
)

REM Check Laravel installation
echo [5/8] Checking Laravel project...
if exist "artisan" (
    echo ✅ Laravel project detected
) else (
    echo ❌ Laravel project not found!
)

REM Check dependencies
echo [6/8] Checking dependencies...
if exist "vendor\autoload.php" (
    echo ✅ Composer dependencies installed
) else (
    echo ❌ Composer dependencies missing!
    echo    Run: composer install
)

if exist "node_modules" (
    echo ✅ NPM dependencies installed
) else (
    echo ❌ NPM dependencies missing!
    echo    Run: npm install
)

REM Check environment
echo [7/8] Checking environment configuration...
if exist ".env" (
    echo ✅ Environment file exists
) else (
    echo ⚠️  Environment file missing!
    echo    Copy .env.example to .env and configure
)

REM Check database connection
echo [8/8] Checking database connection...
php artisan tinker --execute="try { DB::connection()->getPdo(); echo 'Database: OK'; } catch(Exception $e) { echo 'Database: FAILED - ' . $e->getMessage(); }" 2>nul

echo.
echo ===============================================
echo            Status Check Complete
echo ===============================================
echo.
pause

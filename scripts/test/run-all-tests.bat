@echo off
title MyUKM - Test Runner
color 0E

echo.
echo ===============================================
echo            MyUKM Test Suite Runner
echo ===============================================
echo.

REM Check if we're in correct directory
if not exist "artisan" (
    echo [ERROR] Laravel project not found in current directory!
    echo Please navigate to your Laravel project root and try again.
    pause
    exit /b 1
)

echo [1/3] Preparing test environment...
REM Ensure test database is ready
php artisan config:clear
php artisan cache:clear

echo [2/3] Running Laravel Tests...
echo.
echo =================== FEATURE TESTS ===================
php artisan test tests/Feature --verbose

echo.
echo =================== UNIT TESTS ===================
php artisan test tests/Unit --verbose

echo [3/3] Running Custom Tests...
echo.
echo =================== CUSTOM TESTS ===================

REM Test database models
if exist "scripts\test\test_models.php" (
    echo Testing Models...
    php scripts\test\test_models.php
)

REM Test realtime functionality
if exist "scripts\test\test-realtime-complete.php" (
    echo Testing Realtime Features...
    php scripts\test\test-realtime-complete.php
)

echo.
echo ===============================================
echo            Test Suite Complete
echo ===============================================
echo.
pause

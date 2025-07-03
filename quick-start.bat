@echo off
title MyUKM Quick Start
color 0A

echo.
echo ================================
echo      MyUKM Quick Start  
echo ================================
echo.

REM Quick checks
if not exist "artisan" (
    echo Error: Not in Laravel project directory!
    pause
    exit /b 1
)

REM Clear caches quickly
echo Preparing application...
php artisan config:clear --quiet
php artisan view:clear --quiet

REM Start server
echo.
echo 🚀 Starting Laravel server...
echo.
echo   URL: http://localhost:8000
echo   Press Ctrl+C to stop
echo.

REM Open browser automatically
start http://localhost:8000

REM Start Laravel server
php artisan serve

@echo off
title MyUKM Real-time Demo Launcher
color 0B

echo.
echo ===============================================
echo         MyUKM Real-time Features Demo
echo ===============================================
echo.
echo This script will start MyUKM with real-time features enabled.
echo.
echo What will be started:
echo   1. Laravel Server (http://localhost:8000)
echo   2. Queue Worker (for real-time chat and notifications)
echo.

set /p confirm="Ready to start? (Y/N): "
if /i "%confirm%" NEQ "Y" (
    echo Demo cancelled.
    pause
    exit
)

echo.
echo [1/3] Checking environment...
if not exist ".env" (
    echo Creating .env from template...
    copy ".env.example" ".env" >nul 2>&1
    php artisan key:generate >nul 2>&1
)

echo [2/3] Starting Laravel server...
start "Laravel Server" cmd /k "title Laravel Server && echo Laravel Server Starting... && php artisan serve --host=0.0.0.0 --port=8000"

echo [3/3] Starting queue worker for real-time features...
start "Queue Worker" cmd /k "title Queue Worker - Real-time && echo Queue Worker Starting... && php artisan queue:work --queue=realtime,default --verbose --tries=3 --timeout=90"

echo.
echo ========================================
echo   🚀 MyUKM Real-time Demo Started!
echo ========================================
echo.
echo ✓ Laravel Server: http://localhost:8000
echo ✓ Queue Worker: Running for real-time features
echo.
echo To test real-time features:
echo 1. Open multiple browser tabs to http://localhost:8000
echo 2. Login as different users
echo 3. Start a chat conversation
echo 4. Watch messages appear in real-time across all tabs!
echo.
echo Real-time features enabled:
echo   - Instant chat messages
echo   - Real-time notifications
echo   - Live user status updates
echo   - Group activity broadcasts
echo.

timeout /t 3 >nul
echo Opening browser...
start http://localhost:8000

echo.
echo Demo is now running! Close this window when done.
echo To stop: Close the "Laravel Server" and "Queue Worker" windows.
echo.
pause

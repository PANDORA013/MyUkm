@echo off
title MyUKM Real-time Features - Final Testing
color 0A

echo.
echo ===============================================
echo    🚀 MyUKM Real-time Features Final Test
echo ===============================================
echo.
echo This script will guide you through testing the real-time
echo chat and notification features that have been implemented.
echo.
echo FEATURES TO TEST:
echo ✓ Real-time chat messaging
echo ✓ Instant notifications
echo ✓ Live user status updates
echo ✓ Group activity broadcasts
echo ✓ Queue-based background processing
echo.

set /p ready="Ready to start testing? (Y/N): "
if /i "%ready%" NEQ "Y" (
    echo Testing cancelled.
    pause
    exit
)

echo.
echo ===============================================
echo       Starting Real-time Services
echo ===============================================
echo.

echo [1/4] Checking if services are already running...
netstat -an | findstr ":8000" >nul
if %errorlevel% equ 0 (
    echo ✓ Laravel server appears to be running
) else (
    echo ! Laravel server not detected, starting it...
    start "Laravel Server" cmd /k "title Laravel Server - Real-time Ready && cd /d %~dp0 && php artisan serve --host=0.0.0.0 --port=8000"
    timeout /t 3 >nul
)

echo [2/4] Starting queue worker for real-time features...
start "Queue Worker" cmd /k "title Queue Worker - Real-time Processing && cd /d %~dp0 && php artisan queue:work --queue=realtime,default --verbose --tries=3 --timeout=90"

echo [3/4] Waiting for services to initialize...
timeout /t 5 >nul

echo [4/4] Opening browser for testing...
start http://localhost:8000

echo.
echo ===============================================
echo          Services Started Successfully!
echo ===============================================
echo.
echo ✓ Laravel Server: http://localhost:8000
echo ✓ Queue Worker: Processing real-time jobs
echo ✓ Browser: Opening to application
echo.
echo NEXT STEPS:
echo 1. Follow the manual testing guide that will open
echo 2. Test real-time chat with multiple browser tabs
echo 3. Verify notifications work instantly
echo 4. Check that queue worker processes jobs
echo.

set /p guide="Open detailed testing guide? (Y/N): "
if /i "%guide%" EQU "Y" (
    start manual-testing-guide.bat
)

echo.
echo ===============================================
echo       🎯 QUICK TESTING CHECKLIST
echo ===============================================
echo.
echo □ Open 2-3 browser tabs to http://localhost:8000
echo □ Login as admin@myukm.com (password: password)
echo □ Create/join groups with different users
echo □ Send chat messages - they should appear INSTANTLY
echo □ Check notifications work in real-time
echo □ Verify Queue Worker window shows job processing
echo □ No errors in browser console (F12)
echo.
echo ===============================================
echo           Expected Results
echo ===============================================
echo.
echo ✅ CHAT REAL-TIME:
echo    - Messages appear instantly across all tabs
echo    - No page refresh needed
echo    - Multiple users can chat simultaneously
echo.
echo ✅ NOTIFICATIONS REAL-TIME:
echo    - Join/leave notifications appear immediately
echo    - Online status updates automatically
echo    - Admin actions trigger instant alerts
echo.
echo ✅ BACKEND PERFORMANCE:
echo    - Queue worker processes jobs smoothly
echo    - No errors in Laravel logs
echo    - Database updates correctly
echo.
echo.
echo 🚀 REAL-TIME FEATURES ARE NOW READY FOR TESTING!
echo.
echo When you're done testing, you can close the
echo Laravel Server and Queue Worker windows.
echo.
echo Happy testing! 🎉
echo.
pause

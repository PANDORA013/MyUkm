@echo off
echo ========================================
echo   Manual Test: Real-time Chat Fix
echo   Verify Messages Appear Instantly
echo ========================================
echo.

:: Check if in correct directory
if not exist "artisan" (
    echo ❌ Error: artisan file not found. Make sure you're in the Laravel project root.
    pause
    exit /b 1
)

echo 🧪 Manual Testing Instructions:
echo.

echo [Step 1] Starting queue worker...
start "Queue Worker" /min cmd /k "echo 🔄 Queue Worker Active && echo ⚡ Processing chat broadcasts... && echo Press Ctrl+C to stop && echo. && php artisan queue:work --timeout=60 --sleep=1 --tries=3"
timeout /t 2 >nul

echo [Step 2] Starting Laravel server...
start "Laravel Server" /min cmd /k "echo 🌐 Server Running && echo 📍 URL: http://localhost:8000 && echo Press Ctrl+C to stop && echo. && php artisan serve --host=localhost --port=8000"
timeout /t 3 >nul

echo [Step 3] Opening test chat room...
start http://localhost:8000/ukm/55/chat
timeout /t 2 >nul

echo ========================================
echo   🧪 REAL-TIME CHAT TEST PROTOCOL
echo ========================================
echo.
echo ✅ SETUP COMPLETE:
echo   • Queue worker: Running (minimized)
echo   • Laravel server: Running (minimized)
echo   • Chat room: Opening in browser
echo.
echo 📋 TESTING STEPS:
echo   1. Login as Thomas (admin_grup) in current browser
echo   2. Open INCOGNITO/PRIVATE window
echo   3. Go to: http://localhost:8000/ukm/55/chat
echo   4. Login as Andre (anggota) in incognito window
echo   5. Send message from Thomas
echo   6. ✅ VERIFY: Message appears instantly in Andre's window
echo   7. Send message from Andre  
echo   8. ✅ VERIFY: Message appears instantly in Thomas's window
echo.
echo 🔍 WHAT TO CHECK:
echo   • Messages appear WITHOUT refreshing the page
echo   • Response time is under 1 second
echo   • Browser console shows "✅ Subscribed to private channel"
echo   • Browser console shows "📨 Received chat message" when message arrives
echo.
echo 🚨 IF MESSAGES DON'T APPEAR INSTANTLY:
echo   • Check browser console for errors (F12)
echo   • Verify both queue worker and server are running
echo   • Check browser network tab for failed requests
echo   • Try refreshing both browsers and test again
echo.
echo 📊 SUCCESS CRITERIA:
echo   ✅ Real-time messages (no reload needed)
echo   ✅ Messages appear in under 1 second
echo   ✅ Console shows successful channel connection
echo   ✅ Console shows message reception events
echo.
echo Press any key when testing is complete...
pause >nul

echo.
echo 🔍 Need to check queue status?
echo   • Queue worker window should show processing messages
echo   • Check logs: storage/logs/laravel.log
echo.
echo Testing complete! 🎉

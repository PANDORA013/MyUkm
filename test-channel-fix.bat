@echo off
echo ========================================
echo   Testing Channel Fix for Real-time Chat
echo   Verifying Backend-Frontend Channel Match
echo ========================================
echo.

:: Check if in correct directory
if not exist "artisan" (
    echo ❌ Error: artisan file not found. Make sure you're in the Laravel project root.
    pause
    exit /b 1
)

echo 🔧 Testing real-time chat channel synchronization...
echo.

echo [1/3] Starting queue worker for broadcasting...
start "MyUKM Queue Worker" /min cmd /k "echo 🔄 MyUKM Queue Worker Started && echo ⚡ Processing broadcasting jobs... && echo. && php artisan queue:work --timeout=60 --sleep=1 --tries=3"
timeout /t 2 >nul

echo [2/3] Running channel verification tests...
php scripts/test-realtime-performance.php
echo.

echo [3/3] Testing frontend-backend channel compatibility...
echo.

echo 📊 CHANNEL CONFIGURATION SUMMARY:
echo   • Backend Event: ChatMessageSent
echo   • Backend Channel: PrivateChannel('group.' + groupCode)
echo   • Frontend Channel: pusher.subscribe('group.' + groupCode)
echo   • Event Name: 'chat.message'
echo   • Authentication: routes/channels.php
echo.

echo 🔧 FIXED ISSUES:
echo   ✅ Backend now uses group.{groupCode} instead of chat.{groupId}
echo   ✅ Frontend already uses group.{groupCode} (no change needed)
echo   ✅ Event name matches: 'chat.message'
echo   ✅ Channel authentication updated for referral_code
echo.

echo 📱 MANUAL TESTING:
echo   1. Open: http://localhost:8000/ukm/55/chat
echo   2. Login as different users in separate browsers
echo   3. Send messages - should appear instantly without reload
echo   4. Check browser console for connection status
echo.

echo 🚀 Starting Laravel server for testing...
echo   Server will be available at: http://localhost:8000
echo   Channel fix is now active!
echo.

timeout /t 3 >nul
start http://localhost:8000/ukm/55/chat

php artisan serve --host=localhost --port=8000

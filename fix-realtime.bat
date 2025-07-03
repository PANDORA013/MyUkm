@echo off
echo ========================================
echo   MyUKM Real-time Troubleshooting
echo ========================================
echo.

echo 🔍 Checking real-time configuration...
echo.

echo [1/5] Checking Pusher configuration...
php artisan tinker --execute="echo 'PUSHER_APP_KEY: ' . env('PUSHER_APP_KEY') . PHP_EOL; echo 'PUSHER_APP_CLUSTER: ' . env('PUSHER_APP_CLUSTER') . PHP_EOL; echo 'BROADCAST_DRIVER: ' . env('BROADCAST_DRIVER') . PHP_EOL;"

echo.
echo [2/5] Checking queue worker status...
tasklist /FI "IMAGENAME eq cmd.exe" /FI "WINDOWTITLE eq MyUKM Queue Worker*" 2>nul | find "cmd.exe" >nul
if %errorlevel% == 0 (
    echo ✅ Queue worker is running
) else (
    echo ❌ Queue worker is NOT running
    echo Starting queue worker...
    start "MyUKM Queue Worker" /min cmd /k "php artisan queue:work --timeout=60 --sleep=3 --tries=3"
    timeout /t 2 >nul
    echo ✅ Queue worker started
)

echo.
echo [3/5] Testing broadcasting...
php artisan tinker --execute="broadcast(new App\Events\TestEvent(['message' => 'Test broadcast at ' . now()])); echo 'Test broadcast sent!' . PHP_EOL;"

echo.
echo [4/5] Checking database tables...
php artisan tinker --execute="echo 'Groups: ' . App\Models\Group::count() . PHP_EOL; echo 'Users: ' . App\Models\User::count() . PHP_EOL; echo 'Chat messages: ' . App\Models\Chat::count() . PHP_EOL;"

echo.
echo [5/5] Testing group channels...
php artisan tinker --execute="foreach(App\Models\Group::all() as \$g) { echo 'Group: ' . \$g->name . ' - Channel: group.' . \$g->referral_code . PHP_EOL; }"

echo.
echo ========================================
echo   💡 Troubleshooting Tips:
echo ========================================
echo 1. Make sure queue worker is running
echo 2. Check browser console for errors
echo 3. Verify Pusher credentials in .env
echo 4. Test with: http://localhost:8000/ukm/0810/chat
echo 5. Open browser dev tools and check Network tab
echo.
echo ========================================
echo   🚀 Quick Fix Commands:
echo ========================================
echo • Restart queue: taskkill /F /FI "WINDOWTITLE eq MyUKM Queue Worker*" ^&^& start "MyUKM Queue Worker" /min cmd /k "php artisan queue:work"
echo • Clear cache: php artisan config:clear ^&^& php artisan route:clear ^&^& php artisan view:clear
echo • Test broadcast: php artisan tinker --execute="broadcast(new App\Events\TestEvent(['test' => true]));"
echo.
pause

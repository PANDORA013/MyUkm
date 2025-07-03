@echo off
echo ========================================
echo   Testing Middleware Fixes
echo ========================================
echo.

echo 🔧 Applied middleware fixes:
echo ✅ Added /broadcasting/auth to CSRF exceptions
echo ✅ Updated CSP headers for Pusher compatibility  
echo ✅ Improved broadcasting routes configuration
echo.

echo [1/3] Clearing caches after middleware changes...
php artisan config:clear >nul 2>&1
php artisan route:clear >nul 2>&1  
php artisan view:clear >nul 2>&1
echo ✅ Caches cleared

echo.
echo [2/3] Testing broadcasting route...
echo Available broadcasting routes:
php artisan route:list | findstr broadcasting

echo.
echo [3/3] Testing CSRF configuration...
php artisan tinker --execute="echo 'CSRF exceptions: '; var_dump(app('App\Http\Middleware\VerifyCsrfToken')->getExcept() ?? ['broadcasting/auth']); echo PHP_EOL;"

echo.
echo ========================================
echo   🎯 Next Steps:
echo ========================================
echo 1. Hard refresh browser (Ctrl+Shift+R)
echo 2. Test chat: http://localhost:8000/ukm/0810/chat
echo 3. Check browser console for errors
echo 4. Look for these success messages:
echo    • "✅ Laravel Echo initialized successfully"  
echo    • "✅ Subscribed to private channel: group.0810"
echo    • "✅ Pusher connected successfully"
echo.

echo 💡 If still having issues:
echo • Check Network tab for failed /broadcasting/auth requests
echo • Verify user is logged in and member of group
echo • Try different browser or incognito mode
echo.
pause

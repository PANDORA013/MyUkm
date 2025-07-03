@echo off
echo ========================================
echo   Quick Fix untuk Echo Errors
echo ========================================
echo.

echo 🔧 Applying quick fix for console errors...
echo.

echo [1/2] Clearing browser cache recommended...
echo   - Press Ctrl+Shift+R untuk hard refresh
echo   - Atau clear browser cache manually
echo.

echo [2/2] Restarting server components...
php artisan config:clear >nul 2>&1
php artisan view:clear >nul 2>&1

echo ✅ Quick fix applied!
echo.
echo 💡 Next steps:
echo   1. Hard refresh browser (Ctrl+Shift+R)
echo   2. Check console untuk error baru
echo   3. Test kirim pesan di chat
echo.
echo 🔗 Test chat: http://localhost:8000/ukm/0810/chat
echo.
pause

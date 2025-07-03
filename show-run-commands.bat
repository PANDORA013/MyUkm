@echo off
REM MyUKM - All-in-One CMD Commands

echo ========================================
echo   Copy and paste ONE of these commands:
echo ========================================
echo.

echo [OPTION 1] Complete Setup + Launch (First time):
echo composer install ^&^& npm install ^&^& php artisan key:generate --force ^&^& php artisan migrate --force ^&^& php artisan db:seed --force ^&^& php artisan config:clear ^&^& php artisan route:clear ^&^& php artisan view:clear ^&^& php artisan cache:clear ^&^& start /min cmd /k "php artisan queue:work --timeout=60 --sleep=3 --tries=3" ^&^& timeout /t 2 ^&^& start http://localhost:8000 ^&^& php artisan serve --host=localhost --port=8000
echo.

echo [OPTION 2] Quick Launch (Already setup):
echo php artisan config:clear ^&^& php artisan route:clear ^&^& php artisan view:clear ^&^& php artisan cache:clear ^&^& start /min cmd /k "php artisan queue:work --timeout=60 --sleep=3 --tries=3" ^&^& timeout /t 2 ^&^& start http://localhost:8000 ^&^& php artisan serve --host=localhost --port=8000
echo.

echo [OPTION 3] Development Mode (with NPM):
echo php artisan config:clear ^&^& php artisan route:clear ^&^& start /min cmd /k "npm run dev" ^&^& start /min cmd /k "php artisan queue:work --timeout=60 --sleep=3 --tries=3" ^&^& timeout /t 2 ^&^& start http://localhost:8000 ^&^& php artisan serve --host=localhost --port=8000
echo.

echo [OPTION 4] Test Chat URLs:
echo start http://localhost:8000/login ^&^& start http://localhost:8000/ukm/0810/chat
echo.

echo ========================================
echo   NOTE: Use ^&^& for CMD or ; for PowerShell
echo ========================================
pause

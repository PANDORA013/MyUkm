@echo off
echo ⚡ Starting MyUKM All-in-One...
php artisan config:clear >nul 2>&1
php artisan route:clear >nul 2>&1
php artisan view:clear >nul 2>&1
php artisan cache:clear >nul 2>&1
start "Queue Worker" /min cmd /k "php artisan queue:work --timeout=60 --sleep=3 --tries=3"
timeout /t 3 >nul
start http://localhost:8000
echo ✅ MyUKM Started! Browser opening...
php artisan serve --host=localhost --port=8000

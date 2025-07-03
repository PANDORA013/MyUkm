@echo off
echo ⚡ Starting MyUKM Server...
start /min cmd /k "php artisan queue:work --timeout=60 --sleep=3 --tries=3"
timeout /t 2 >nul
start http://localhost:8000
php artisan serve --host=localhost --port=8000

@echo off
echo ⚡ MyUKM Quick Launch...

:: Silent setup
if not exist ".env" copy ".env.example" ".env" >nul 2>&1
php artisan key:generate --force --no-interaction >nul 2>&1
composer install --no-interaction >nul 2>&1
npm install --silent >nul 2>&1
npm run build --silent >nul 2>&1
php artisan migrate --force --no-interaction >nul 2>&1
php artisan db:seed --force --no-interaction >nul 2>&1
php artisan config:clear >nul 2>&1

::  Start ULTRA queue worker (maximum real-time responsiveness)
start "ULTRA Queue Worker - Real-Time" /min cmd /c "php artisan queue:work --queue=realtime,high,default --timeout=10 --sleep=0 --tries=2 --memory=256 --max-jobs=1000"

:: Auto-open browser
start http://localhost:8000

:: Start server
echo 🌐 Server: http://localhost:8000
echo 🚀 Ready! Browser opening...
php artisan serve --host=localhost --port=8000 --quiet

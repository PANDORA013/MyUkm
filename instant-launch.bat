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

::  Start queue worker (optimized for real-time responsiveness)
start "Queue Worker - Real-Time" /min cmd /c "php artisan queue:work --queue=realtime,high,default --timeout=30 --sleep=1 --tries=3 --memory=128"

:: Auto-open browser
start http://localhost:8000

:: Start server
echo 🌐 Server: http://localhost:8000
echo 🚀 Ready! Browser opening...
php artisan serve --host=localhost --port=8000 --quiet

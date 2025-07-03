@echo off
start /min cmd /k "php artisan queue:work"
start http://localhost:8000
php artisan serve

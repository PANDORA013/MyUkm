# MyUKM - All-in-One PowerShell Commands

# Command 1: Complete Setup & Launch (Recommended for first time)
composer install; npm install; php artisan key:generate --force; php artisan migrate --force; php artisan db:seed --force; php artisan config:clear; php artisan route:clear; php artisan view:clear; php artisan cache:clear; Start-Process -WindowStyle Minimized powershell -ArgumentList "-Command", "php artisan queue:work --timeout=60 --sleep=3 --tries=3"; Start-Sleep 2; Start-Process "http://localhost:8000"; php artisan serve --host=localhost --port=8000

# Command 2: Quick Launch (if already setup before)  
php artisan config:clear; php artisan route:clear; php artisan view:clear; php artisan cache:clear; Start-Process -WindowStyle Minimized powershell -ArgumentList "-Command", "php artisan queue:work --timeout=60 --sleep=3 --tries=3"; Start-Sleep 2; Start-Process "http://localhost:8000"; php artisan serve --host=localhost --port=8000

# Command 3: Development Mode (with file watching)
php artisan config:clear; php artisan route:clear; php artisan view:clear; php artisan cache:clear; Start-Process -WindowStyle Minimized powershell -ArgumentList "-Command", "php artisan queue:work --timeout=60 --sleep=3 --tries=3"; Start-Process -WindowStyle Minimized powershell -ArgumentList "-Command", "npm run dev"; Start-Sleep 2; Start-Process "http://localhost:8000"; php artisan serve --host=localhost --port=8000

# Command 4: Test Real-time Chat After Launch
Start-Process "http://localhost:8000/login"; Start-Process "http://localhost:8000/ukm/0810/chat"

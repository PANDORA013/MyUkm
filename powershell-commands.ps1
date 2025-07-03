# MyUKM PowerShell Commands - Copy Paste Ready

# Command 1: All-in-One (Recommended)
Start-Process -WindowStyle Minimized cmd -ArgumentList "/k", "php artisan queue:work --timeout=60 --sleep=3 --tries=3"; Start-Sleep 2; Start-Process "http://localhost:8000"; php artisan serve --host=localhost --port=8000

# Command 2: Simple Server Only
Start-Process "http://localhost:8000"; php artisan serve --host=localhost --port=8000

# Command 3: With Background Jobs
Start-Job -ScriptBlock { php artisan queue:work }; Start-Process "http://localhost:8000"; php artisan serve --host=localhost --port=8000

# Command 4: Clear Cache + Start
php artisan config:clear; php artisan route:clear; php artisan view:clear; php artisan cache:clear; Start-Process -WindowStyle Minimized cmd -ArgumentList "/k", "php artisan queue:work"; Start-Sleep 2; Start-Process "http://localhost:8000"; php artisan serve

# Command 5: Open Multiple URLs
Start-Process "http://localhost:8000"; Start-Process "http://localhost:8000/login"; Start-Process "http://localhost:8000/ukm/0810/chat"; php artisan serve

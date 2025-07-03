@echo off
echo ========================================
echo   MyUKM Real-time Development Server
echo   with Queue Worker for Performance
echo ========================================
echo.

:: Check if in correct directory
if not exist "artisan" (
    echo Error: artisan file not found. Make sure you're in the Laravel project root.
    pause
    exit /b 1
)

:: Check database connection
echo [1/4] Checking database connection...
php artisan migrate:status >nul 2>&1
if errorlevel 1 (
    echo Warning: Database connection issue. Please check your .env file.
    echo.
    echo Starting server anyway...
) else (
    echo ✓ Database connection OK
)

:: Clear cache for fresh start
echo [2/4] Clearing cache...
php artisan config:clear >nul 2>&1
php artisan route:clear >nul 2>&1
php artisan view:clear >nul 2>&1
echo ✓ Cache cleared

:: Create log files if they don't exist
if not exist "storage\logs" mkdir "storage\logs"
if not exist "storage\logs\laravel.log" type nul > "storage\logs\laravel.log"

:: Start queue worker in background
echo [3/4] Starting queue worker for real-time features...
start "MyUKM Queue Worker" cmd /k "echo MyUKM Queue Worker Started && echo Processing jobs for better real-time performance... && echo. && php artisan queue:work --timeout=60 --sleep=3 --tries=3"

:: Wait a moment for queue worker to start
timeout /t 2 >nul

:: Start development server
echo [4/4] Starting development server...
echo.
echo ========================================
echo   🚀 MyUKM Development Environment
echo ========================================
echo   📱 Web App: http://localhost:8000
echo   ⚡ Queue Worker: Running (separate window)
echo   📊 Real-time Features: Enabled
echo   📝 Logs: storage\logs\laravel.log
echo ========================================
echo.
echo 💡 Tips:
echo   - Chat messages are now processed via queue
echo   - Online status updates use background jobs
echo   - This improves response time and user experience
echo   - Keep queue worker window open for optimal performance
echo.
echo Press Ctrl+C to stop the server
echo.

:: Start the Laravel development server
php artisan serve --host=localhost --port=8000

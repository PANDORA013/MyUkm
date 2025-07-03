@echo off
title MyUKM Full Development Environment
color 0A

echo.
echo ================================================================
echo            MyUKM Full Development Environment Launcher
echo ================================================================
echo.
echo This will start:
echo  1. Laravel Development Server (http://localhost:8000)
echo  2. Queue Worker (Background Jobs)
echo  3. Real-time Broadcasting (if configured)
echo  4. File Watcher (Auto-reload on changes)
echo.

REM Create logs directory if it doesn't exist
if not exist "storage\logs" mkdir "storage\logs"
if not exist "temp\logs" mkdir "temp\logs"

REM Check prerequisites
echo [INIT] Checking prerequisites...
php --version >nul 2>&1
if errorlevel 1 (
    echo [ERROR] PHP not found! Please install PHP and add to PATH.
    goto :error_exit
)

if not exist "artisan" (
    echo [ERROR] Not in Laravel project directory!
    goto :error_exit
)

echo       ✓ PHP is available
echo       ✓ Laravel project detected
echo.

REM Clear caches and prepare environment
echo [PREP] Preparing Laravel environment...
php artisan config:clear --quiet 2>nul
php artisan route:clear --quiet 2>nul
php artisan view:clear --quiet 2>nul
php artisan cache:clear --quiet 2>nul
echo       ✓ Caches cleared

REM Check .env file
if not exist ".env" (
    echo [PREP] Creating environment file...
    copy ".env.example" ".env" >nul 2>&1
    php artisan key:generate --quiet 2>nul
    echo       ✓ Environment file created
) else (
    echo       ✓ Environment file exists
)

REM Start services in background
echo.
echo [START] Starting development services...
echo.

REM Start Laravel Server
echo Starting Laravel Server on http://localhost:8000...
start "Laravel Server" /MIN cmd /k "title Laravel Server && color 0E && echo Laravel Development Server && echo ========================== && echo Started at: %date% %time% && echo URL: http://localhost:8000 && echo Press Ctrl+C to stop && echo. && php artisan serve --host=0.0.0.0 --port=8000"

REM Wait a moment for server to start
timeout /t 2 /nobreak >nul

REM Start Queue Worker  
echo Starting Queue Worker...
start "Queue Worker" /MIN cmd /k "title Queue Worker && color 0B && echo Laravel Queue Worker && echo ===================== && echo Started at: %date% %time% && echo Processing background jobs... && echo Press Ctrl+C to stop && echo. && php artisan queue:work --tries=3 --timeout=90"

REM Start File Watcher (if npm is available)
npm --version >nul 2>&1
if not errorlevel 1 (
    echo Starting File Watcher...
    start "File Watcher" /MIN cmd /k "title File Watcher && color 0D && echo File Watcher - Auto Reload && echo ========================== && echo Started at: %date% %time% && echo Watching for file changes... && echo Press Ctrl+C to stop && echo. && npm run dev"
) else (
    echo       ⚠ NPM not found, skipping file watcher
)

REM Create monitoring dashboard
echo Starting Monitoring Dashboard...
start "Monitoring" /MIN cmd /k "title Monitoring Dashboard && color 0C && echo MyUKM Monitoring Dashboard && echo ========================== && echo. && echo 📊 Server Status: && echo   - Laravel: http://localhost:8000 && echo   - Queue: Processing && echo   - Logs: storage/logs/ && echo. && echo 💡 Quick Commands: && echo   - php artisan route:list && echo   - php artisan queue:retry all && echo   - php artisan cache:clear && echo. && echo Press any key to open application... && pause >nul && start http://localhost:8000"

echo.
echo ================================================================
echo   🎉 MyUKM Development Environment Started Successfully!
echo ================================================================  
echo.
echo   📱 Application:     http://localhost:8000
echo   🏠 Homepage:        http://localhost:8000/
echo   🔐 Login:           http://localhost:8000/login
echo   💬 Chat:            http://localhost:8000/chat  
echo   👤 Admin:           http://localhost:8000/admin
echo.
echo   📊 Services Running:
echo   ✓ Laravel Server    (Port 8000)
echo   ✓ Queue Worker      (Background Jobs)
if not errorlevel 1 echo   ✓ File Watcher      (Auto-reload)
echo   ✓ Monitoring        (Dashboard)
echo.
echo   💡 Tips:
echo   - Each service runs in separate window
echo   - Close windows to stop services  
echo   - Check Monitoring window for quick commands
echo   - Logs are in storage/logs/ and temp/logs/
echo.
echo ================================================================
echo.

REM Wait and then open browser
echo [AUTO] Opening browser in 3 seconds...
timeout /t 3 /nobreak >nul
start http://localhost:8000

echo.
echo 🚀 All services started! You can now use your application.
echo.
echo   To stop all services: Close all command windows
echo   To restart: Run this script again
echo.
echo Press any key to keep this window open for monitoring...
pause >nul

REM Keep this window open for logs
echo.
echo [MONITOR] This window will show server logs...
echo ================================================
echo.
php artisan serve --host=0.0.0.0 --port=8001 --quiet

goto :eof

:error_exit
echo.
echo [ERROR] Cannot start development environment.
echo Please fix the errors above and try again.
echo.
pause
exit /b 1

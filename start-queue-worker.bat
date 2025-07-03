@echo off
echo.
echo ====================================================
echo           MyUKM Queue Worker Monitor
echo ====================================================
echo.

cd /d "%~dp0"

echo [%date% %time%] Starting Queue Worker with monitoring...
echo.

:start_queue
echo Starting PHP Artisan Queue Worker...
echo Press Ctrl+C to stop the queue worker
echo.

REM Start queue worker with detailed output
php artisan queue:work database --verbose --tries=3 --timeout=60 --sleep=3 --max-jobs=1000 --max-time=3600

echo.
echo [%date% %time%] Queue worker stopped
echo.

choice /c YN /m "Restart queue worker? (Y/N)"
if errorlevel 2 goto end
if errorlevel 1 goto start_queue

:end
echo.
echo Queue worker monitoring ended.
pause

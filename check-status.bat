@echo off
echo Checking MyUKM application status...
echo.

echo Checking if Laravel server is running on port 8000...
netstat -an | findstr ":8000" >nul
if %errorlevel% == 0 (
    echo [OK] Laravel server is running on port 8000
) else (
    echo [WAITING] Laravel server not yet running on port 8000
)

echo.
echo Checking if queue worker is running...
tasklist /FI "IMAGENAME eq php.exe" /FO CSV | findstr "queue:work" >nul
if %errorlevel% == 0 (
    echo [OK] Queue worker is running
) else (
    echo [WAITING] Queue worker not yet running
)

echo.
echo If both services show [OK], the application is ready to test.
echo If not, wait for launch-myukm.bat to complete, then run this script again.
echo.
echo To test real-time chat:
echo 1. Open http://localhost:8000 in two browser windows
echo 2. Join the same group in both windows  
echo 3. Send a message from one window
echo 4. Verify it appears instantly in the other window
echo.
pause

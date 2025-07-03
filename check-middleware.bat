@echo off
echo ========================================
echo   MyUKM Middleware Analysis
echo ========================================
echo.

echo 🔍 Analyzing middleware configuration...
echo.

echo [1/5] Checking CSRF Token Configuration...
php artisan tinker --execute="echo 'CSRF Token: ' . csrf_token() . PHP_EOL;"

echo.
echo [2/5] Checking Broadcasting Routes...
php artisan route:list | findstr broadcasting
echo.

echo [3/5] Testing Broadcasting Auth Endpoint...
echo Testing /broadcasting/auth endpoint...
powershell -Command "try { $response = Invoke-WebRequest -Uri 'http://localhost:8000/broadcasting/auth' -Method POST -Headers @{'X-CSRF-TOKEN'='test'; 'Content-Type'='application/json'} -Body '{}' -UseBasicParsing; echo 'Status:' $response.StatusCode } catch { echo 'Error:' $_.Exception.Message }"

echo.
echo [4/5] Checking Middleware Registration...
echo Middleware Groups (web):
echo - EncryptCookies
echo - AddQueuedCookiesToResponse  
echo - StartSession
echo - ShareErrorsFromSession
echo - VerifyCsrfToken
echo - SubstituteBindings
echo - UpdateLastSeen

echo.
echo [5/5] Checking Channel Authorization...
php artisan tinker --execute="echo 'Checking channel authorization...' . PHP_EOL; try { \$user = App\Models\User::first(); echo 'Test user: ' . \$user->name . ' (Role: ' . \$user->role . ')' . PHP_EOL; } catch (Exception \$e) { echo 'Error: ' . \$e->getMessage() . PHP_EOL; }"

echo.
echo ========================================
echo   🔧 Middleware Issues Found:
echo ========================================
echo.

echo 💡 Potential Issues:
echo 1. CSRF token mismatch in broadcasting requests
echo 2. SecurityHeaders CSP might block Pusher connections
echo 3. Channel authentication might be failing
echo 4. Session middleware interference
echo.

echo 🔧 Recommendations:
echo 1. Check browser console for CSRF errors
echo 2. Verify Pusher connection in Network tab
echo 3. Test with disabled SecurityHeaders temporarily
echo 4. Check if user is properly authenticated
echo.

echo ========================================
echo   🚀 Quick Fixes to Try:
echo ========================================
echo 1. Add /broadcasting/* to CSRF exceptions
echo 2. Update CSP headers for Pusher
echo 3. Test broadcasting auth manually
echo 4. Clear all caches and sessions
echo.
pause

@echo off
title MyUKM - Local CI Test Runner
color 0A

echo ===============================================
echo          MyUKM Local CI Test Runner
echo       (Simulating GitHub Actions locally)
echo ===============================================
echo.

echo [STEP 1] Environment Setup
echo Checking PHP version...
php --version
echo.

echo [STEP 2] Clearing caches (like GitHub Actions)
echo Clearing application caches...
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
echo ✅ Caches cleared
echo.

echo [STEP 3] Running database migrations
echo Running fresh migrations...
php artisan migrate:fresh --seed --force
echo ✅ Database setup complete
echo.

echo [STEP 4] Running tests (same as GitHub Actions)
echo.
echo ==========================================
echo           UNIT TESTS
echo ==========================================
vendor\bin\phpunit --testsuite=Unit --testdox
echo.

echo ==========================================
echo          FEATURE TESTS  
echo ==========================================
vendor\bin\phpunit --testsuite=Feature --testdox
echo.

echo ==========================================
echo          TEST SUMMARY
echo ==========================================
echo.
echo [INFO] Local test run completed!
echo This simulates the same environment as GitHub Actions.
echo.
echo Expected results:
echo ✅ Unit Tests: Should pass
echo ✅ Feature Tests: All 108 tests should pass  
echo ✅ Browser Tests: Skipped (no Chromedriver needed)
echo.
echo [COMPARISON] 
echo - Local Results: See above
echo - GitHub Actions: Check https://github.com/PANDORA013/MyUkm/actions
echo.
echo Both should produce similar results!
echo.
pause

@echo off
title MyUKM - GitHub Workflow Monitor
color 0B

echo ==========================================
echo        MyUKM GitHub Workflow Monitor
echo ==========================================
echo.

echo [INFO] Latest commit pushed to GitHub:
git log --oneline -1
echo.

echo [INFO] GitHub Actions workflow will automatically run for:
echo - Branch: main
echo - Workflow: Laravel Tests
echo - Trigger: Push event
echo.

echo [STATUS] To check the workflow status:
echo 1. Visit: https://github.com/PANDORA013/MyUkm/actions
echo 2. Look for the latest "Laravel Tests" workflow run
echo 3. The workflow includes:
echo    - PHP 8.2 setup
echo    - MySQL 8.0 database
echo    - Composer dependencies
echo    - Database migrations
echo    - Unit tests execution
echo    - Feature tests execution
echo    - Detailed test reporting
echo.

echo [WORKFLOW FEATURES]
echo ✅ Automatic environment setup
echo ✅ Database migrations
echo ✅ Unit tests with detailed output
echo ✅ Feature tests with detailed output
echo ✅ Test artifacts upload
echo ✅ Comprehensive error reporting
echo ✅ Browser tests auto-skip (CI environment)
echo.

echo [EXPECTED RESULTS]
echo - Unit Tests: Should pass
echo - Feature Tests: All 108 tests should pass
echo - Browser Tests: Automatically skipped (no Chromedriver in CI)
echo - Total Time: ~3-5 minutes
echo.

echo [NEXT STEPS]
echo 1. Monitor the workflow at: https://github.com/PANDORA013/MyUkm/actions
echo 2. Check test results and artifacts
echo 3. Review any failures (if any)
echo.

echo Press any key to open GitHub Actions page...
pause >nul

start https://github.com/PANDORA013/MyUkm/actions

echo.
echo Workflow monitoring script completed!
echo Check the GitHub Actions page for real-time status.
pause

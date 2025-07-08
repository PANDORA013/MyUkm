@echo off
echo =========================================
echo    MyUKM Project File Organization
echo =========================================
echo.

REM Create directory structure
echo Creating directory structure...
mkdir docs\reports 2>nul
mkdir docs\implementation 2>nul
mkdir docs\testing 2>nul
mkdir scripts\database 2>nul
mkdir scripts\setup 2>nul
mkdir scripts\testing 2>nul
mkdir scripts\monitoring 2>nul
mkdir scripts\utilities 2>nul
mkdir temp\cache 2>nul
mkdir temp\logs 2>nul

echo.
echo Moving documentation files...
REM Documentation and Reports
if exist ACCESSIBILITY_BUTTON_FIXES.txt move ACCESSIBILITY_BUTTON_FIXES.txt docs\reports\ 2>nul
if exist ACCESSIBILITY_IMPROVEMENTS.txt move ACCESSIBILITY_IMPROVEMENTS.txt docs\reports\ 2>nul
if exist ADMIN_PER_GRUP_IMPLEMENTATION.md move ADMIN_PER_GRUP_IMPLEMENTATION.md docs\implementation\ 2>nul
if exist CHAT_MIDDLEWARE_FIX_REPORT.md move CHAT_MIDDLEWARE_FIX_REPORT.md docs\reports\ 2>nul
if exist COMPLETE_JAVASCRIPT_FIX_SUMMARY.md move COMPLETE_JAVASCRIPT_FIX_SUMMARY.md docs\reports\ 2>nul
if exist ERROR_FIX_SUMMARY.md move ERROR_FIX_SUMMARY.md docs\reports\ 2>nul
if exist IMPLEMENTATION_SUMMARY.md move IMPLEMENTATION_SUMMARY.md docs\implementation\ 2>nul
if exist JAVASCRIPT_SYNTAX_FIX_REPORT.md move JAVASCRIPT_SYNTAX_FIX_REPORT.md docs\reports\ 2>nul
if exist LAYOUT_ADMIN_GRUP_SUMMARY.md move LAYOUT_ADMIN_GRUP_SUMMARY.md docs\implementation\ 2>nul
if exist MYSQL_SYNCHRONIZATION_REPORT.txt move MYSQL_SYNCHRONIZATION_REPORT.txt docs\reports\ 2>nul
if exist PRODUCTION_AUTH_OPTIMIZATION.txt move PRODUCTION_AUTH_OPTIMIZATION.txt docs\reports\ 2>nul
if exist TESTING_MANUAL_ADMIN_PRIVILEGE.txt move TESTING_MANUAL_ADMIN_PRIVILEGE.txt docs\testing\ 2>nul

echo Moving database scripts...
REM Database Scripts
if exist check_db.php move check_db.php scripts\database\ 2>nul
if exist check_nabil_status.php move check_nabil_status.php scripts\database\ 2>nul
if exist check_ukm_ids.php move check_ukm_ids.php scripts\database\ 2>nul
if exist create_deletion_history.php move create_deletion_history.php scripts\database\ 2>nul
if exist delete_admin_account.php move delete_admin_account.php scripts\database\ 2>nul
if exist quick_db_setup.php move quick_db_setup.php scripts\database\ 2>nul
if exist setup_admin_grup_data.php move setup_admin_grup_data.php scripts\database\ 2>nul
if exist setup_admin.php move setup_admin.php scripts\database\ 2>nul

echo Moving setup scripts...
REM Setup Scripts
if exist quick_setup.php move quick_setup.php scripts\setup\ 2>nul
if exist setup_test_login.php move setup_test_login.php scripts\setup\ 2>nul

echo Moving testing scripts...
REM Testing Scripts
if exist test_admin_grup_layout.php move test_admin_grup_layout.php scripts\testing\ 2>nul
if exist test_admin_sync.php move test_admin_sync.php scripts\testing\ 2>nul
if exist test_chat_login.php move test_chat_login.php scripts\testing\ 2>nul
if exist test_chat_monitor.php move test_chat_monitor.php scripts\testing\ 2>nul
if exist test_chat_realtime.php move test_chat_realtime.php scripts\testing\ 2>nul
if exist test_chat_simple.php move test_chat_simple.php scripts\testing\ 2>nul
if exist test_layout.php move test_layout.php scripts\testing\ 2>nul
if exist test_new_user_complete.php move test_new_user_complete.php scripts\testing\ 2>nul
if exist final_verification.php move final_verification.php scripts\testing\ 2>nul

echo Moving monitoring scripts...
REM Monitoring Scripts
if exist debug_routes.php move debug_routes.php scripts\monitoring\ 2>nul

echo Moving utility scripts...
REM Utility Scripts
if exist trigger-workflow.sh move trigger-workflow.sh scripts\utilities\ 2>nul

echo Moving temporary files...
REM Temporary Files
if exist cookies_user.txt move cookies_user.txt temp\ 2>nul
if exist test_output.txt move test_output.txt temp\logs\ 2>nul
if exist .phpunit.result.cache move .phpunit.result.cache temp\cache\ 2>nul

echo.
echo Creating .gitkeep files...
echo. > docs\.gitkeep
echo. > temp\.gitkeep
echo. > temp\cache\.gitkeep
echo. > temp\logs\.gitkeep

echo.
echo =========================================
echo   File Organization Complete!
echo =========================================
echo.
echo New Structure:
echo ├── docs/
echo │   ├── reports/           # Bug fixes and reports
echo │   ├── implementation/    # Feature implementations
echo │   └── testing/          # Testing documentation
echo ├── scripts/
echo │   ├── database/         # Database utilities
echo │   ├── setup/            # Setup scripts
echo │   ├── testing/          # Test scripts
echo │   ├── monitoring/       # Monitoring tools
echo │   └── utilities/        # General utilities
echo └── temp/
echo     ├── cache/            # Temporary cache files
echo     └── logs/             # Temporary log files
echo.
echo Core Laravel files remain in root:
echo ├── composer.json
echo ├── package.json
echo ├── artisan
echo ├── phpunit.xml
echo ├── .editorconfig
echo ├── .env / .env.example
echo ├── .gitignore / .gitattributes
echo └── vite.config.js / postcss.config.js
echo.
pause

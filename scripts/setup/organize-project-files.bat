@echo off
echo =========================================
echo    MyUKM Project File Organization
echo =========================================
echo.
echo Creating directory structure...

REM Create directory structure
mkdir "docs\reports" 2>nul
mkdir "docs\implementation" 2>nul
mkdir "docs\testing" 2>nul
mkdir "scripts\database" 2>nul
mkdir "scripts\setup" 2>nul
mkdir "scripts\testing" 2>nul
mkdir "scripts\monitoring" 2>nul
mkdir "scripts\utilities" 2>nul
mkdir "temp\cache" 2>nul
mkdir "temp\logs" 2>nul

echo.
echo Moving documentation files...
REM Documentation and Reports
move "ACCESSIBILITY_BUTTON_FIXES.txt" "docs\reports\" 2>nul
move "ACCESSIBILITY_IMPROVEMENTS.txt" "docs\reports\" 2>nul
move "CHAT_MIDDLEWARE_FIX_REPORT.md" "docs\reports\" 2>nul
move "COMPLETE_JAVASCRIPT_FIX_SUMMARY.md" "docs\reports\" 2>nul
move "ERROR_FIX_SUMMARY.md" "docs\reports\" 2>nul
move "JAVASCRIPT_SYNTAX_FIX_REPORT.md" "docs\reports\" 2>nul
move "MYSQL_SYNCHRONIZATION_REPORT.txt" "docs\reports\" 2>nul
move "PRODUCTION_AUTH_OPTIMIZATION.txt" "docs\reports\" 2>nul

REM Implementation Documentation
move "ADMIN_PER_GRUP_IMPLEMENTATION.md" "docs\implementation\" 2>nul
move "IMPLEMENTATION_SUMMARY.md" "docs\implementation\" 2>nul
move "LAYOUT_ADMIN_GRUP_SUMMARY.md" "docs\implementation\" 2>nul

REM Testing Documentation
move "TESTING_MANUAL_ADMIN_PRIVILEGE.txt" "docs\testing\" 2>nul

echo Moving database scripts...
REM Database Scripts
move "check_db.php" "scripts\database\" 2>nul
move "check_nabil_status.php" "scripts\database\" 2>nul
move "check_ukm_ids.php" "scripts\database\" 2>nul
move "create_deletion_history.php" "scripts\database\" 2>nul
move "delete_admin_account.php" "scripts\database\" 2>nul
move "quick_db_setup.php" "scripts\database\" 2>nul
move "setup_admin_grup_data.php" "scripts\database\" 2>nul
move "setup_admin.php" "scripts\database\" 2>nul

echo Moving setup scripts...
REM Setup Scripts
move "quick_setup.php" "scripts\setup\" 2>nul
move "setup_test_login.php" "scripts\setup\" 2>nul

echo Moving testing scripts...
REM Testing Scripts
move "test_admin_grup_layout.php" "scripts\testing\" 2>nul
move "test_admin_sync.php" "scripts\testing\" 2>nul
move "test_chat_login.php" "scripts\testing\" 2>nul
move "test_chat_monitor.php" "scripts\testing\" 2>nul
move "test_chat_realtime.php" "scripts\testing\" 2>nul
move "test_chat_simple.php" "scripts\testing\" 2>nul
move "test_layout.php" "scripts\testing\" 2>nul
move "test_new_user_complete.php" "scripts\testing\" 2>nul
move "final_verification.php" "scripts\testing\" 2>nul

echo Moving monitoring scripts...
REM Monitoring Scripts
move "debug_routes.php" "scripts\monitoring\" 2>nul

echo Moving utility scripts...
REM Utility Scripts
move "trigger-workflow.sh" "scripts\utilities\" 2>nul

echo Moving temporary files...
REM Temporary Files
move "cookies_user.txt" "temp\" 2>nul
move "test_output.txt" "temp\logs\" 2>nul
move ".phpunit.result.cache" "temp\cache\" 2>nul

echo.
echo =========================================
echo   File Organization Complete!
echo =========================================
echo.
echo New Structure:
echo ├── docs/
echo │   ├── reports/           # Bug fixes ^& reports
echo │   ├── implementation/    # Feature implementations
echo │   └── testing/          # Testing documentation
echo ├── scripts/
echo │   ├── database/         # Database utilities
echo │   ├── setup/            # Setup scripts
echo │   ├── testing/          # Test scripts
echo │   ├── monitoring/       # Monitoring tools
echo │   └── utilities/        # General utilities
echo ├── temp/
echo │   ├── cache/            # Cache files
echo │   └── logs/             # Log files
echo.
echo Core Laravel files remain in root:
echo ├── .env, .editorconfig, .gitignore
echo ├── artisan, composer.json, package.json
echo ├── phpunit.xml, vite.config.js
echo └── README.md
echo.
echo Organization complete! Your project is now clean and organized.
pause

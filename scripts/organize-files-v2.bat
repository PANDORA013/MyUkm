@echo off
echo ========================================
echo   MyUKM Project File Organization v2.0
echo   Cleaning up root directory
echo ========================================
echo.

:: Check if in correct directory
if not exist "artisan" (
    echo Error: artisan file not found. Make sure you're in the Laravel project root.
    pause
    exit /b 1
)

:: Create directories if they don't exist
echo [1/5] Creating organization directories...
if not exist "docs" mkdir "docs"
if not exist "scripts" mkdir "scripts"
if not exist "temp" mkdir "temp"
if not exist "archive" mkdir "archive"
if not exist "testing" mkdir "testing"
echo ✓ Directories created

:: Move documentation files
echo [2/5] Moving documentation files...
if exist "ACCESSIBILITY_BUTTON_FIXES.txt" move "ACCESSIBILITY_BUTTON_FIXES.txt" "docs\"
if exist "ACCESSIBILITY_IMPROVEMENTS.txt" move "ACCESSIBILITY_IMPROVEMENTS.txt" "docs\"
if exist "ADMIN_PER_GRUP_IMPLEMENTATION.md" move "ADMIN_PER_GRUP_IMPLEMENTATION.md" "docs\"
if exist "CHAT_MIDDLEWARE_FIX_REPORT.md" move "CHAT_MIDDLEWARE_FIX_REPORT.md" "docs\"
if exist "COMPLETE_JAVASCRIPT_FIX_SUMMARY.md" move "COMPLETE_JAVASCRIPT_FIX_SUMMARY.md" "docs\"
if exist "ERROR_FIX_SUMMARY.md" move "ERROR_FIX_SUMMARY.md" "docs\"
if exist "IMPLEMENTATION_SUMMARY.md" move "IMPLEMENTATION_SUMMARY.md" "docs\"
if exist "JAVASCRIPT_SYNTAX_FIX_REPORT.md" move "JAVASCRIPT_SYNTAX_FIX_REPORT.md" "docs\"
if exist "LAYOUT_ADMIN_GRUP_SUMMARY.md" move "LAYOUT_ADMIN_GRUP_SUMMARY.md" "docs\"
if exist "MYSQL_SYNCHRONIZATION_REPORT.txt" move "MYSQL_SYNCHRONIZATION_REPORT.txt" "docs\"
if exist "PRODUCTION_AUTH_OPTIMIZATION.txt" move "PRODUCTION_AUTH_OPTIMIZATION.txt" "docs\"
if exist "PROJECT_STRUCTURE.md" move "PROJECT_STRUCTURE.md" "docs\"
if exist "TESTING_MANUAL_ADMIN_PRIVILEGE.txt" move "TESTING_MANUAL_ADMIN_PRIVILEGE.txt" "docs\"
echo ✓ Documentation files moved

:: Move script files
echo [3/5] Moving script files...
if exist "check_db.php" move "check_db.php" "scripts\"
if exist "check_nabil_status.php" move "check_nabil_status.php" "scripts\"
if exist "check_ukm_ids.php" move "check_ukm_ids.php" "scripts\"
if exist "create_deletion_history.php" move "create_deletion_history.php" "scripts\"
if exist "debug_routes.php" move "debug_routes.php" "scripts\"
if exist "delete_admin_account.php" move "delete_admin_account.php" "scripts\"
if exist "final_verification.php" move "final_verification.php" "scripts\"
if exist "organize-files.bat" move "organize-files.bat" "scripts\"
if exist "quick_db_setup.php" move "quick_db_setup.php" "scripts\"
if exist "quick_setup.php" move "quick_setup.php" "scripts\"
if exist "setup_admin_grup_data.php" move "setup_admin_grup_data.php" "scripts\"
if exist "setup_admin.php" move "setup_admin.php" "scripts\"
if exist "setup_test_login.php" move "setup_test_login.php" "scripts\"
echo ✓ Script files moved

:: Move testing files
echo [4/5] Moving testing files...
if exist "test_admin_grup_layout.php" move "test_admin_grup_layout.php" "testing\"
if exist "test_admin_sync.php" move "test_admin_sync.php" "testing\"
if exist "test_chat_login.php" move "test_chat_login.php" "testing\"
if exist "test_chat_monitor.php" move "test_chat_monitor.php" "testing\"
if exist "test_chat_realtime.php" move "test_chat_realtime.php" "testing\"
if exist "test_chat_simple.php" move "test_chat_simple.php" "testing\"
if exist "test_layout.php" move "test_layout.php" "testing\"
if exist "test_new_user_complete.php" move "test_new_user_complete.php" "testing\"
echo ✓ Testing files moved

:: Update .gitignore
echo [5/5] Updating .gitignore...
echo. >> .gitignore
echo # Organized directories >> .gitignore
echo temp/ >> .gitignore
echo archive/ >> .gitignore
echo testing/temp_* >> .gitignore
echo *.tmp >> .gitignore
echo ✓ .gitignore updated

echo.
echo ========================================
echo   File Organization Complete!
echo ========================================
echo.
echo Files organized into:
echo   📂 docs/     - Documentation and reports
echo   📂 scripts/  - Setup and utility scripts  
echo   📂 testing/  - Test files and debugging
echo   📂 temp/     - Temporary files
echo   📂 archive/  - Old/backup files
echo.
echo Root directory is now much cleaner!
echo.
pause

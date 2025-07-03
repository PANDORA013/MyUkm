@echo off
title Create MyUKM Desktop Shortcuts
color 0A

echo.
echo ================================
echo   Creating MyUKM Shortcuts
echo ================================
echo.

REM Get current directory
set "CURRENT_DIR=%~dp0"

REM Try Desktop first, fallback to project shortcuts folder
set "DESKTOP=%USERPROFILE%\Desktop"
set "SHORTCUTS_DIR=%CURRENT_DIR%shortcuts"

REM Check if Desktop exists, if not create shortcuts folder
if not exist "%DESKTOP%" (
    echo Desktop folder not found, creating shortcuts in project folder...
    if not exist "%SHORTCUTS_DIR%" mkdir "%SHORTCUTS_DIR%"
    set "TARGET_DIR=%SHORTCUTS_DIR%"
) else (
    set "TARGET_DIR=%DESKTOP%"
)

echo Creating shortcuts in: %TARGET_DIR%
echo.

REM Create Launch MyUKM shortcut (Complete Setup) - Primary option
echo Creating "Launch MyUKM" shortcut...
powershell -Command "$WshShell = New-Object -comObject WScript.Shell; $Shortcut = $WshShell.CreateShortcut('%TARGET_DIR%\Launch MyUKM.lnk'); $Shortcut.TargetPath = '%CURRENT_DIR%launch-myukm.bat'; $Shortcut.WorkingDirectory = '%CURRENT_DIR%'; $Shortcut.Description = 'MyUKM Complete Setup and Launch'; $Shortcut.Save()" 2>nul
if %errorlevel% equ 0 (echo   ✓ Launch MyUKM shortcut created) else (echo   ✗ Failed to create Launch MyUKM shortcut)

REM Create Instant Launch shortcut (Quick Start) - Primary option
echo Creating "MyUKM Instant Launch" shortcut...
powershell -Command "$WshShell = New-Object -comObject WScript.Shell; $Shortcut = $WshShell.CreateShortcut('%TARGET_DIR%\MyUKM Instant Launch.lnk'); $Shortcut.TargetPath = '%CURRENT_DIR%instant-launch.bat'; $Shortcut.WorkingDirectory = '%CURRENT_DIR%'; $Shortcut.Description = 'MyUKM Quick Server Launch'; $Shortcut.Save()" 2>nul
if %errorlevel% equ 0 (echo   ✓ MyUKM Instant Launch shortcut created) else (echo   ✗ Failed to create Instant Launch shortcut)

REM Create Server Menu shortcut (Advanced options)
echo Creating "MyUKM Server Menu" shortcut...
powershell -Command "$WshShell = New-Object -comObject WScript.Shell; $Shortcut = $WshShell.CreateShortcut('%TARGET_DIR%\MyUKM Server Menu.lnk'); $Shortcut.TargetPath = '%CURRENT_DIR%server-menu.bat'; $Shortcut.WorkingDirectory = '%CURRENT_DIR%'; $Shortcut.Description = 'MyUKM Server Launcher Menu'; $Shortcut.Save()" 2>nul
if %errorlevel% equ 0 (echo   ✓ MyUKM Server Menu shortcut created) else (echo   ✗ Failed to create Server Menu shortcut)

REM Create Test Launcher shortcut
echo Creating "Test MyUKM" shortcut...
powershell -Command "$WshShell = New-Object -comObject WScript.Shell; $Shortcut = $WshShell.CreateShortcut('%TARGET_DIR%\Test MyUKM.lnk'); $Shortcut.TargetPath = '%CURRENT_DIR%test-launcher.bat'; $Shortcut.WorkingDirectory = '%CURRENT_DIR%'; $Shortcut.Description = 'MyUKM Testing Interface'; $Shortcut.Save()" 2>nul
if %errorlevel% equ 0 (echo   ✓ Test MyUKM shortcut created) else (echo   ✗ Failed to create Test launcher shortcut)

echo.
echo ================================
echo   Shortcuts Created Successfully!
echo ================================
echo.
echo   Main Launch Options:
echo   1. Launch MyUKM.lnk             (Complete setup + launch)
echo   2. MyUKM Instant Launch.lnk     (Quick daily launch)
echo.
echo   Additional Options:
echo   3. MyUKM Server Menu.lnk        (Advanced server menu)
echo   4. Test MyUKM.lnk               (Testing interface)
echo.
echo   Location: %TARGET_DIR%
echo.
echo ================================
echo   Quick Start Guide:
echo ================================
echo   First Time Setup:
echo   - Double-click "Launch MyUKM"
echo   - Wait for complete setup
echo   - Browser opens automatically to http://localhost:8000
echo.
echo   Daily Development:
echo   - Double-click "MyUKM Instant Launch"
echo   - Quick start for daily use
echo   - Browser opens automatically
echo.
echo   Both options start queue worker for real-time features!
echo.
pause

@echo off
title Create MyUKM Desktop Shortcuts
color 0A

echo.
echo ================================
echo   Creating Desktop Shortcuts
echo ================================
echo.

REM Get current directory
set "CURRENT_DIR=%~dp0"

REM Get desktop path
set "DESKTOP=%USERPROFILE%\Desktop"

echo Creating shortcuts on Desktop...
echo.

REM Create Quick Start shortcut
echo Creating "MyUKM Quick Start" shortcut...
echo Set oWS = WScript.CreateObject("WScript.Shell") > "%TEMP%\CreateShortcut.vbs"
echo sLinkFile = "%DESKTOP%\MyUKM Quick Start.lnk" >> "%TEMP%\CreateShortcut.vbs"
echo Set oLink = oWS.CreateShortcut(sLinkFile) >> "%TEMP%\CreateShortcut.vbs"
echo oLink.TargetPath = "%CURRENT_DIR%quick-start.bat" >> "%TEMP%\CreateShortcut.vbs"
echo oLink.WorkingDirectory = "%CURRENT_DIR%" >> "%TEMP%\CreateShortcut.vbs"
echo oLink.Description = "MyUKM Quick Start Server" >> "%TEMP%\CreateShortcut.vbs"
echo oLink.Save >> "%TEMP%\CreateShortcut.vbs"
cscript //nologo "%TEMP%\CreateShortcut.vbs"

REM Create Server Menu shortcut
echo Creating "MyUKM Server Menu" shortcut...
echo Set oWS = WScript.CreateObject("WScript.Shell") > "%TEMP%\CreateShortcut2.vbs"
echo sLinkFile = "%DESKTOP%\MyUKM Server Menu.lnk" >> "%TEMP%\CreateShortcut2.vbs"
echo Set oLink = oWS.CreateShortcut(sLinkFile) >> "%TEMP%\CreateShortcut2.vbs"
echo oLink.TargetPath = "%CURRENT_DIR%server-menu.bat" >> "%TEMP%\CreateShortcut2.vbs"
echo oLink.WorkingDirectory = "%CURRENT_DIR%" >> "%TEMP%\CreateShortcut2.vbs"
echo oLink.Description = "MyUKM Server Launcher Menu" >> "%TEMP%\CreateShortcut2.vbs"
echo oLink.Save >> "%TEMP%\CreateShortcut2.vbs"
cscript //nologo "%TEMP%\CreateShortcut2.vbs"

REM Create Full Development shortcut
echo Creating "MyUKM Full Dev" shortcut...
echo Set oWS = WScript.CreateObject("WScript.Shell") > "%TEMP%\CreateShortcut3.vbs"
echo sLinkFile = "%DESKTOP%\MyUKM Full Dev.lnk" >> "%TEMP%\CreateShortcut3.vbs"
echo Set oLink = oWS.CreateShortcut(sLinkFile) >> "%TEMP%\CreateShortcut3.vbs"
echo oLink.TargetPath = "%CURRENT_DIR%start-full-dev.bat" >> "%TEMP%\CreateShortcut3.vbs"
echo oLink.WorkingDirectory = "%CURRENT_DIR%" >> "%TEMP%\CreateShortcut3.vbs"
echo oLink.Description = "MyUKM Full Development Environment" >> "%TEMP%\CreateShortcut3.vbs"
echo oLink.Save >> "%TEMP%\CreateShortcut3.vbs"
cscript //nologo "%TEMP%\CreateShortcut3.vbs"

REM Cleanup
del "%TEMP%\CreateShortcut.vbs" 2>nul
del "%TEMP%\CreateShortcut2.vbs" 2>nul
del "%TEMP%\CreateShortcut3.vbs" 2>nul

echo.
echo ✅ Desktop shortcuts created successfully!
echo.
echo   📁 Created shortcuts:
echo   - MyUKM Quick Start.lnk     (Fast startup)
echo   - MyUKM Server Menu.lnk     (All options)
echo   - MyUKM Full Dev.lnk        (Full development)
echo.
echo   💡 You can now start MyUKM directly from Desktop!
echo.
pause

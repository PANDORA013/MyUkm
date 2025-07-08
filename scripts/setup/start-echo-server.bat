@echo off
echo ===============================================
echo =         MYUKM WEBSOCKET SERVER             =
echo ===============================================
echo.
echo Starting Laravel Echo Server for real-time communications...
echo.

:: Set directory to project root
cd /d %~dp0\..

:: Check if Node.js is installed
where node >nul 2>nul
if %errorlevel% neq 0 (
    echo Error: Node.js is not installed or not in PATH
    echo Please install Node.js first from https://nodejs.org/
    pause
    exit /b 1
)

:: Check if laravel-echo-server is installed globally
where laravel-echo-server >nul 2>nul
if %errorlevel% neq 0 (
    echo Laravel Echo Server is not installed globally. Installing now...
    call npm install -g laravel-echo-server
    if %errorlevel% neq 0 (
        echo Failed to install Laravel Echo Server. Please install it manually.
        pause
        exit /b 1
    )
)

:: Start laravel-echo-server
echo Starting Laravel Echo Server...
laravel-echo-server start --config=config/server/laravel-echo-server.json

pause

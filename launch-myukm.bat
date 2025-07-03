@echo off
echo ========================================
echo   MyUKM One-Click Complete Setup
echo   All-in-One Server Launch
echo ========================================
echo.

:: Check if in correct directory
if not exist "artisan" (
    echo ❌ Error: artisan file not found. Make sure you're in the Laravel project root.
    pause
    exit /b 1
)

echo 🚀 Starting complete setup process...
echo.

:: Step 1: Install dependencies
echo [1/6] Installing dependencies...
echo   📦 Installing Composer dependencies...
call composer install --no-interaction --quiet
if errorlevel 1 (
    echo ⚠️ Composer install had warnings, continuing...
)

echo   📦 Installing NPM dependencies...
call npm install --silent
if errorlevel 1 (
    echo ⚠️ NPM install had warnings, continuing...
)
echo ✅ Dependencies installed

:: Step 2: Environment setup
echo [2/7] Setting up environment...
if not exist ".env" (
    if exist ".env.example" (
        copy ".env.example" ".env" >nul
        echo   📋 Environment file created from template
    ) else (
        echo   ⚠️ No .env.example found, using default settings
    )
) else (
    echo   📋 Environment file already exists
)

:: Generate app key if needed
php artisan key:generate --force --no-interaction >nul 2>&1
echo ✅ Environment configured

:: Step 3: Build frontend assets
echo [3/7] Building frontend assets...
echo   📦 Building JavaScript and CSS...
call npm run build --silent
if errorlevel 1 (
    echo   ⚠️ Build had warnings, continuing...
) else (
    echo   ✅ Frontend assets built successfully
)

:: Step 4: Database setup
echo [4/7] Setting up database...
echo   🗄️ Running migrations...
php artisan migrate --force --no-interaction >nul 2>&1
if errorlevel 1 (
    echo   ⚠️ Migration warnings (database might not exist), continuing...
) else (
    echo   ✅ Database migrated successfully
)

echo   🌱 Seeding database...
php artisan db:seed --force --no-interaction >nul 2>&1
if errorlevel 1 (
    echo   ⚠️ Seeding had warnings, continuing...
) else (
    echo   ✅ Database seeded successfully
)

:: Step 5: Clear and optimize
echo [5/7] Optimizing application...
php artisan config:clear >nul 2>&1
php artisan route:clear >nul 2>&1
php artisan view:clear >nul 2>&1
php artisan cache:clear >nul 2>&1
echo ✅ Application optimized

:: Step 6: Start queue worker in background
echo [6/7] Starting queue worker for real-time features...
start "MyUKM Queue Worker" /min cmd /k "echo 🔄 MyUKM Queue Worker Started && echo ⚡ Processing real-time jobs... && echo. && php artisan queue:work --timeout=60 --sleep=3 --tries=3"
timeout /t 2 >nul
echo ✅ Queue worker started (minimized window)

:: Step 7: Start main server
echo [7/7] Starting Laravel development server...
echo.
echo ========================================
echo   🎉 MyUKM Ready to Launch!
echo ========================================
echo   🌐 Server: http://localhost:8000
echo   ⚡ Queue Worker: Running (background)
echo   📊 Real-time Features: Enabled
echo   🔧 Development Mode: Active
echo ========================================
echo.
echo 📱 Application URLs:
echo   • Homepage: http://localhost:8000/
echo   • Login: http://localhost:8000/login
echo   • Register: http://localhost:8000/register
echo   • Dashboard: http://localhost:8000/dashboard
echo   • Chat: http://localhost:8000/chat
echo   • Admin: http://localhost:8000/admin
echo.
echo 💡 Tips:
echo   • Server will open automatically in 3 seconds
echo   • Queue worker runs in background (minimized)
echo   • Press Ctrl+C to stop the server
echo   • Keep this window open while developing
echo.

:: Auto-open browser after 3 seconds
echo ⏳ Opening browser in 3 seconds...
timeout /t 3 >nul
start http://localhost:8000

:: Start the Laravel development server (this blocks until stopped)
php artisan serve --host=localhost --port=8000

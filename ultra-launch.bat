@echo off
title MyUKM Ultra Launch - Maximum Responsiveness
color 0A

echo.
echo ⚡⚡⚡ MyUKM ULTRA LAUNCH - MAXIMUM REAL-TIME RESPONSIVENESS ⚡⚡⚡
echo ================================================================
echo.

:: Enhanced environment check and setup
echo 🔧 Setting up environment...
if not exist ".env" (
    echo   📄 Creating .env file...
    copy ".env.example" ".env" >nul 2>&1
)

:: Generate app key if needed
echo   🔑 Checking application key...
php artisan key:generate --force --no-interaction >nul 2>&1

:: Install dependencies with optimizations
echo   📦 Installing dependencies (optimized)...
composer install --no-interaction --optimize-autoloader --classmap-authoritative >nul 2>&1
npm install --silent --no-optional >nul 2>&1

:: Build assets with optimization
echo   🎨 Building optimized assets...
npm run build --silent >nul 2>&1

:: Database setup with optimizations
echo   🗄️  Setting up database...
php artisan migrate --force --no-interaction >nul 2>&1
php artisan db:seed --force --no-interaction --class=DatabaseSeeder >nul 2>&1

:: Clear all caches for maximum performance
echo   🧹 Optimizing cache...
php artisan config:clear >nul 2>&1
php artisan route:clear >nul 2>&1
php artisan view:clear >nul 2>&1
php artisan cache:clear >nul 2>&1

:: Cache configurations for production-level performance
echo   ⚡ Caching for maximum speed...
php artisan config:cache >nul 2>&1
php artisan route:cache >nul 2>&1
php artisan view:cache >nul 2>&1

:: Start ULTRA-OPTIMIZED queue worker for maximum real-time responsiveness
echo   🚀 Starting ULTRA queue worker (real-time optimized)...
start "ULTRA Queue Worker - Real-Time" /min cmd /c "php artisan queue:work --queue=realtime,high,default --timeout=10 --sleep=0 --tries=2 --memory=256 --max-jobs=1000"

:: Start secondary queue worker for backup
echo   🔄 Starting backup queue worker...
start "Backup Queue Worker" /min cmd /c "php artisan queue:work --queue=default,low --timeout=30 --sleep=1 --tries=3 --memory=128"

:: Auto-open browser after small delay
echo   🌐 Opening browser...
timeout /t 2 /nobreak >nul 2>&1
start http://localhost:8000

echo.
echo ✅ ULTRA LAUNCH COMPLETE!
echo ================================================================
echo 🌐 Server: http://localhost:8000
echo ⚡ Queue Worker: Ultra-optimized for instant messaging
echo 🔄 Backup Worker: Running for reliability
echo 📊 Caching: Production-level optimization enabled
echo 🚀 Ready for MAXIMUM real-time responsiveness!
echo ================================================================
echo.

:: Start server with optimized settings
php artisan serve --host=localhost --port=8000 --quiet

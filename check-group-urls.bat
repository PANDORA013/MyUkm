@echo off
echo ========================================
echo   MyUKM - Check Group Chat URLs
echo ========================================
echo.

echo 🔍 Checking available groups and their chat URLs...
echo.

echo Groups and their chat URLs:
php artisan tinker --execute="foreach(App\Models\Group::all() as \$g) { echo \$g->name.' (ID: '.\$g->id.') - Code: '.\$g->referral_code.' - URL: http://localhost:8000/ukm/'.\$g->referral_code.'/chat'.PHP_EOL; }"

echo.
echo ========================================
echo   💡 How to Access Chat:
echo ========================================
echo 1. Make sure you're logged in
echo 2. Make sure you're a member of the group  
echo 3. Use the correct referral code in URL
echo 4. Example: http://localhost:8000/ukm/0810/chat
echo.
echo ========================================
echo   🔧 Quick Actions:
echo ========================================
echo • Login: http://localhost:8000/login
echo • UKM List: http://localhost:8000/ukm
echo • Dashboard: http://localhost:8000/dashboard
echo.
pause

@echo off
title MyUKM Real-time Manual Testing Guide
color 0E

echo.
echo ===============================================
echo      MyUKM Real-time Manual Testing Guide
echo ===============================================
echo.
echo This guide will help you test real-time chat and notifications manually.
echo.
echo CURRENT STATUS:
echo ✓ Laravel Server: Should be running on http://localhost:8000
echo ✓ Queue Worker: Should be processing background jobs
echo ✓ Database: Fresh data with admin user ready
echo.
echo.
echo ===============================================
echo           STEP 1: Open Browser Windows
echo ===============================================
echo.
echo 1. Open 2-3 browser tabs or windows
echo 2. Navigate all tabs to: http://localhost:8000
echo.
echo Press any key to continue...
pause >nul

echo.
echo ===============================================
echo            STEP 2: Login as Users
echo ===============================================
echo.
echo DEFAULT LOGIN CREDENTIALS:
echo.
echo Admin User:
echo   Email: admin@myukm.com
echo   Password: password
echo.
echo Note: If password doesn't work, try: admin123, myukm123, or 12345678
echo.
echo LOGIN INSTRUCTIONS:
echo 1. Tab 1: Login as admin@myukm.com
echo 2. Tab 2: Create new user account or use existing
echo 3. Tab 3: Create another user account (optional)
echo.
echo Press any key to continue...
pause >nul

echo.
echo ===============================================
echo          STEP 3: Create/Join Groups
echo ===============================================
echo.
echo GROUP TESTING:
echo 1. In Tab 1 (Admin): Create a new group
echo 2. Note the 4-digit group join code
echo 3. In Tab 2: Join the group using the code
echo 4. In Tab 3: Join the same group (if using 3 tabs)
echo.
echo EXPECTED RESULT:
echo ✓ All users should be in the same group
echo ✓ Group membership should update in real-time
echo.
echo Press any key to continue...
pause >nul

echo.
echo ===============================================
echo           STEP 4: Test Real-time Chat
echo ===============================================
echo.
echo CHAT TESTING:
echo 1. Go to chat section in all tabs
echo 2. Send a message from Tab 1
echo 3. Message should appear INSTANTLY in Tab 2 and Tab 3
echo 4. Send messages from different tabs
echo 5. All messages should appear immediately across all tabs
echo.
echo WHAT TO LOOK FOR:
echo ✓ Messages appear instantly (no page refresh needed)
echo ✓ No delays in message delivery
echo ✓ Messages show correct user names and timestamps
echo ✓ Chat scrolls automatically to new messages
echo.
echo Press any key to continue...
pause >nul

echo.
echo ===============================================
echo        STEP 5: Test Notifications
echo ===============================================
echo.
echo NOTIFICATION TESTING:
echo 1. User joins/leaves group → Check for notifications
echo 2. Admin performs actions → Check for alerts
echo 3. New messages → Check for message notifications
echo 4. Online status changes → Check for status updates
echo.
echo EXPECTED BEHAVIOR:
echo ✓ Real-time notifications without page refresh
echo ✓ Online status indicators update automatically
echo ✓ Group activity notifications appear instantly
echo.
echo Press any key to continue...
pause >nul

echo.
echo ===============================================
echo           STEP 6: Verify Backend
echo ===============================================
echo.
echo CHECK QUEUE WORKER WINDOW:
echo ✓ Should show job processing messages
echo ✓ Look for "Processing" messages when you send chat
echo ✓ No error messages should appear
echo.
echo CHECK BROWSER DEVELOPER CONSOLE:
echo ✓ Press F12 in browser
echo ✓ Check Console tab for JavaScript errors
echo ✓ Should see WebSocket/EventSource connections (if using)
echo.
echo CHECK LARAVEL SERVER WINDOW:
echo ✓ Should show HTTP requests when you interact
echo ✓ No 500 errors should appear
echo.
echo Press any key to continue...
pause >nul

echo.
echo ===============================================
echo              TESTING RESULTS
echo ===============================================
echo.
echo If everything works correctly, you should see:
echo.
echo ✓ REAL-TIME CHAT:
echo   - Messages appear instantly across all browser tabs
echo   - No page refresh required
echo   - Chat works smoothly with multiple users
echo.
echo ✓ REAL-TIME NOTIFICATIONS:
echo   - Join/leave notifications appear immediately
echo   - Online status updates in real-time
echo   - Admin actions trigger instant notifications
echo.
echo ✓ BACKEND PROCESSING:
echo   - Queue worker processes jobs successfully
echo   - No errors in Laravel server logs
echo   - Database updates happen correctly
echo.
echo.
echo ===============================================
echo               TROUBLESHOOTING
echo ===============================================
echo.
echo IF REAL-TIME FEATURES DON'T WORK:
echo.
echo 1. CHECK SERVICES:
echo    - Laravel Server running on port 8000?
echo    - Queue Worker processing jobs?
echo    - Both windows still open and active?
echo.
echo 2. CHECK BROWSER:
echo    - JavaScript enabled?
echo    - No errors in Developer Console (F12)?
echo    - Try refreshing the page
echo.
echo 3. CHECK DATABASE:
echo    - Are users in the same group?
echo    - Do messages appear in database?
echo    - Are jobs being created in jobs table?
echo.
echo 4. RESTART SERVICES:
echo    - Close Laravel Server and Queue Worker windows
echo    - Run start.bat again
echo    - Choose option [7] Laravel + Queue Worker (Chat Ready)
echo.
echo.
echo ===============================================
echo               TESTING COMPLETE
echo ===============================================
echo.
echo Real-time chat and notification testing guide complete!
echo.
echo If all tests pass:
echo ✓ MyUKM real-time features are working perfectly!
echo ✓ Chat system is ready for production use
echo ✓ Notification system is functioning correctly
echo.
echo Thank you for testing MyUKM! 🚀
echo.
pause

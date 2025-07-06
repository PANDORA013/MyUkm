<?php
/**
 * Manual Real-time Testing Script
 * Verifies chat and notification features are working properly
 */

echo "=== MyUKM Real-time Features Manual Test ===\n\n";

// Test 1: Database connectivity
echo "1. Testing database connection...\n";
try {
    $pdo = new PDO('sqlite:' . __DIR__ . '/database/database.sqlite');
    echo "✓ Database connection successful\n\n";
} catch (Exception $e) {
    echo "✗ Database connection failed: " . $e->getMessage() . "\n\n";
}

// Test 2: Check if required tables exist
echo "2. Checking required tables...\n";
$requiredTables = ['users', 'groups', 'chats', 'jobs', 'group_user'];
foreach ($requiredTables as $table) {
    try {
        $stmt = $pdo->query("SELECT COUNT(*) FROM $table");
        $count = $stmt->fetchColumn();
        echo "✓ Table '$table' exists with $count records\n";
    } catch (Exception $e) {
        echo "✗ Table '$table' missing or error: " . $e->getMessage() . "\n";
    }
}
echo "\n";

// Test 3: Check if queue jobs table is ready
echo "3. Checking queue configuration...\n";
try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM jobs");
    $jobCount = $stmt->fetchColumn();
    echo "✓ Queue jobs table ready (current jobs: $jobCount)\n\n";
} catch (Exception $e) {
    echo "✗ Queue configuration issue: " . $e->getMessage() . "\n\n";
}

// Test 4: Check user and group data
echo "4. Checking test data...\n";
try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE email LIKE '%admin%'");
    $adminCount = $stmt->fetchColumn();
    echo "✓ Found $adminCount admin users\n";
    
    $stmt = $pdo->query("SELECT COUNT(*) FROM groups WHERE is_active = 1");
    $groupCount = $stmt->fetchColumn();
    echo "✓ Found $groupCount active groups\n";
    
    $stmt = $pdo->query("SELECT COUNT(*) FROM group_user");
    $membershipCount = $stmt->fetchColumn();
    echo "✓ Found $membershipCount group memberships\n\n";
} catch (Exception $e) {
    echo "✗ Test data issue: " . $e->getMessage() . "\n\n";
}

// Test 5: Simulate chat message creation
echo "5. Testing chat message simulation...\n";
try {
    // Get a test user and group
    $stmt = $pdo->query("SELECT id FROM users LIMIT 1");
    $userId = $stmt->fetchColumn();
    
    $stmt = $pdo->query("SELECT id FROM groups LIMIT 1");
    $groupId = $stmt->fetchColumn();
    
    if ($userId && $groupId) {
        // Insert test chat message
        $testMessage = "Test message for real-time verification - " . date('H:i:s');
        $stmt = $pdo->prepare("INSERT INTO chats (user_id, group_id, message, created_at, updated_at) VALUES (?, ?, ?, datetime('now'), datetime('now'))");
        $stmt->execute([$userId, $groupId, $testMessage]);
        
        echo "✓ Test chat message created successfully\n";
        echo "  Message: '$testMessage'\n";
        echo "  User ID: $userId\n";
        echo "  Group ID: $groupId\n\n";
    } else {
        echo "✗ No test users or groups found\n\n";
    }
} catch (Exception $e) {
    echo "✗ Chat message test failed: " . $e->getMessage() . "\n\n";
}

// Test 6: Verify server endpoints
echo "6. Testing server endpoints...\n";
$endpoints = [
    'http://localhost:8000' => 'Main page',
    'http://localhost:8000/login' => 'Login page',
    'http://localhost:8000/register' => 'Register page'
];

foreach ($endpoints as $url => $description) {
    $context = stream_context_create([
        'http' => [
            'timeout' => 5,
            'method' => 'GET'
        ]
    ]);
    
    $response = @file_get_contents($url, false, $context);
    if ($response !== false) {
        echo "✓ $description accessible\n";
    } else {
        echo "✗ $description not accessible (check if Laravel server is running)\n";
    }
}
echo "\n";

// Manual testing instructions
echo "=== MANUAL TESTING INSTRUCTIONS ===\n\n";
echo "Now test the real-time features manually:\n\n";

echo "STEP 1: Open Multiple Browser Windows\n";
echo "- Open 2-3 browser tabs to http://localhost:8000\n";
echo "- This simulates multiple users\n\n";

echo "STEP 2: Login as Different Users\n";
echo "- Tab 1: Login as admin user (check database for admin credentials)\n";
echo "- Tab 2: Login as regular user\n";
echo "- Tab 3: Login as another user (if available)\n\n";

echo "STEP 3: Join the Same Group\n";
echo "- All users should join the same group for testing\n";
echo "- Use the group join code from the groups table\n\n";

echo "STEP 4: Test Real-time Chat\n";
echo "- Send messages from different tabs\n";
echo "- Messages should appear INSTANTLY in all tabs\n";
echo "- Check browser developer console for any errors\n\n";

echo "STEP 5: Test Notifications\n";
echo "- Join/leave groups from different tabs\n";
echo "- Admin actions should trigger notifications\n";
echo "- Online status should update in real-time\n\n";

echo "EXPECTED RESULTS:\n";
echo "✓ Messages appear instantly across all browser tabs\n";
echo "✓ No delays in message delivery\n";
echo "✓ Online status updates in real-time\n";
echo "✓ Notifications work properly\n";
echo "✓ No JavaScript errors in browser console\n";
echo "✓ Queue worker processes jobs (check Queue Worker window)\n\n";

echo "TROUBLESHOOTING:\n";
echo "- If messages don't appear instantly, check Queue Worker window\n";
echo "- If errors occur, check Laravel logs in storage/logs/\n";
echo "- Ensure both Laravel Server and Queue Worker are running\n";
echo "- Check browser console for JavaScript errors\n\n";

echo "=== TEST COMPLETED ===\n";
echo "Real-time chat and notification system is ready for manual testing!\n";
echo "Follow the instructions above to verify all features work correctly.\n";

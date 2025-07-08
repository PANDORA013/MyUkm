<?php

require_once __DIR__ . '/scripts/utils/check_db.php';

function testChatRealtime() {
    echo "=== TESTING CHAT REALTIME FUNCTIONALITY ===\n\n";
    
    // Test URLs
    $baseUrl = 'http://127.0.0.1:8000';
    $loginUrl = $baseUrl . '/login';
    $chatUrl = $baseUrl . '/ukm/55/chat'; // SIMS group
    $sendMessageUrl = $baseUrl . '/ukm/55/messages';
    
    echo "1. Testing Admin Grup (Thomas) access to chat...\n";
    
    // Simulate login for admin_grup (Thomas)
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $loginUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, '/tmp/thomas_cookies.txt');
    curl_setopt($ch, CURLOPT_COOKIEFILE, '/tmp/thomas_cookies.txt');
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    echo "   - Login page access: HTTP $httpCode\n";
    
    // Get CSRF token from login page
    preg_match('/<input type="hidden" name="_token" value="([^"]+)"/', $response, $matches);
    $csrfToken = $matches[1] ?? null;
    
    if ($csrfToken) {
        echo "   - CSRF token obtained: " . substr($csrfToken, 0, 10) . "...\n";
        
        // Attempt login
        $loginData = [
            '_token' => $csrfToken,
            'email' => 'thomas@test.com', // We need to create proper email
            'password' => 'password123'
        ];
        
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($loginData));
        curl_setopt($ch, CURLOPT_POST, true);
        
        $loginResponse = curl_exec($ch);
        $loginHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        echo "   - Login attempt: HTTP $loginHttpCode\n";
    }
    
    curl_close($ch);
    
    echo "\n2. Testing chat page access...\n";
    
    // Test chat page access
    $ch2 = curl_init();
    curl_setopt($ch2, CURLOPT_URL, $chatUrl);
    curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch2, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch2, CURLOPT_COOKIEFILE, '/tmp/thomas_cookies.txt');
    
    $chatResponse = curl_exec($ch2);
    $chatHttpCode = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
    
    echo "   - Chat page access: HTTP $chatHttpCode\n";
    
    if (strpos($chatResponse, 'chat-container') !== false) {
        echo "   - Chat container found in response ✓\n";
    } else {
        echo "   - Chat container NOT found in response ✗\n";
    }
    
    if (strpos($chatResponse, 'Pusher') !== false) {
        echo "   - Pusher script found in response ✓\n";
    } else {
        echo "   - Pusher script NOT found in response ✗\n";
    }
    
    curl_close($ch2);
    
    echo "\n3. Testing database chat messages...\n";
    
    // Check existing messages in database
    try {
        $pdo = new PDO('mysql:host=127.0.0.1;dbname=myukm', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM chats WHERE group_id = 55");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo "   - Existing messages in SIMS group: " . $result['count'] . "\n";
        
        // Check chat table structure
        $stmt = $pdo->prepare("DESCRIBE chats");
        $stmt->execute();
        $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        echo "   - Chat table columns: " . implode(', ', $columns) . "\n";
        
    } catch (PDOException $e) {
        echo "   - Database error: " . $e->getMessage() . "\n";
    }
    
    echo "\n4. Testing route accessibility...\n";
    
    // Test if routes are accessible without auth
    $testRoutes = [
        '/ukm' => 'UKM Index',
        '/ukm/55' => 'UKM Detail',
        '/ukm/55/chat' => 'Chat Page'
    ];
    
    foreach ($testRoutes as $route => $description) {
        $ch3 = curl_init();
        curl_setopt($ch3, CURLOPT_URL, $baseUrl . $route);
        curl_setopt($ch3, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch3, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($ch3, CURLOPT_NOBODY, true);
        
        curl_exec($ch3);
        $httpCode = curl_getinfo($ch3, CURLINFO_HTTP_CODE);
        
        echo "   - $description ($route): HTTP $httpCode\n";
        curl_close($ch3);
    }
    
    echo "\n=== CHAT REALTIME TEST COMPLETED ===\n";
}

// Run the test
testChatRealtime();
?>

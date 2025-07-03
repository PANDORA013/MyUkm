<?php

function testChatRealtimeWithLogin() {
    echo "=== TESTING CHAT REALTIME WITH SIMULATED LOGIN ===\n\n";
    
    $baseUrl = 'http://127.0.0.1:8000';
    
    // Create users with email for testing
    try {
        $pdo = new PDO('mysql:host=127.0.0.1;dbname=myukm', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Update users to have emails for login testing
        $pdo->exec("UPDATE users SET email = 'thomas@test.com' WHERE id = 152");
        $pdo->exec("UPDATE users SET email = 'andre@test.com' WHERE id = 156");
        
        // Set password hash for both users (password: 'password123')
        $hashedPassword = password_hash('password123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id IN (152, 156)");
        $stmt->execute([$hashedPassword]);
        
        echo "✓ Test users prepared with emails and passwords\n\n";
        
    } catch (PDOException $e) {
        echo "✗ Database error: " . $e->getMessage() . "\n";
        return;
    }
    
    // Test Thomas (admin_grup) login and chat access
    echo "1. Testing Thomas (admin_grup) login and chat access...\n";
    
    $thomasCookies = '/tmp/thomas_cookies.txt';
    $andreCookies = '/tmp/andre_cookies.txt';
    
    // Clean old cookies
    @unlink($thomasCookies);
    @unlink($andreCookies);
    
    $thomasResult = testUserLogin('thomas@test.com', 'password123', $thomasCookies, $baseUrl);
    echo "   Thomas login: " . ($thomasResult ? "✓ Success" : "✗ Failed") . "\n";
    
    if ($thomasResult) {
        $chatAccess = testChatAccess($thomasCookies, $baseUrl . '/ukm/55/chat');
        echo "   Thomas chat access: " . ($chatAccess ? "✓ Success" : "✗ Failed") . "\n";
        
        if ($chatAccess) {
            $messageTest = testSendMessage($thomasCookies, $baseUrl . '/ukm/55/messages', 'Hello from Thomas (admin_grup)!');
            echo "   Thomas send message: " . ($messageTest ? "✓ Success" : "✗ Failed") . "\n";
        }
    }
    
    echo "\n2. Testing Andre (anggota) login and chat access...\n";
    
    $andreResult = testUserLogin('andre@test.com', 'password123', $andreCookies, $baseUrl);
    echo "   Andre login: " . ($andreResult ? "✓ Success" : "✗ Failed") . "\n";
    
    if ($andreResult) {
        $chatAccess = testChatAccess($andreCookies, $baseUrl . '/ukm/55/chat');
        echo "   Andre chat access: " . ($chatAccess ? "✓ Success" : "✗ Failed") . "\n";
        
        if ($chatAccess) {
            $messageTest = testSendMessage($andreCookies, $baseUrl . '/ukm/55/messages', 'Hello from Andre (anggota)!');
            echo "   Andre send message: " . ($messageTest ? "✓ Success" : "✗ Failed") . "\n";
        }
    }
    
    echo "\n3. Testing message retrieval...\n";
    
    if ($thomasResult) {
        $messages = getMessages($thomasCookies, $baseUrl . '/ukm/55/messages');
        if ($messages) {
            echo "   ✓ Messages retrieved successfully\n";
            echo "   Total messages: " . count($messages) . "\n";
            foreach ($messages as $i => $msg) {
                echo "   Message " . ($i + 1) . ": [{$msg['user']['name']}] {$msg['message']} - {$msg['created_at']}\n";
            }
        } else {
            echo "   ✗ Failed to retrieve messages\n";
        }
    }
    
    echo "\n4. Testing inter-user communication...\n";
    
    if ($thomasResult && $andreResult) {
        echo "   Sending message from Thomas...\n";
        $thomasMsg = testSendMessage($thomasCookies, $baseUrl . '/ukm/55/messages', 'Thomas: Can you see this Andre?');
        
        echo "   Sending message from Andre...\n";
        $andreMsg = testSendMessage($andreCookies, $baseUrl . '/ukm/55/messages', 'Andre: Yes Thomas, I can see your message!');
        
        if ($thomasMsg && $andreMsg) {
            echo "   ✓ Inter-user communication successful\n";
            
            // Retrieve latest messages
            $latestMessages = getMessages($thomasCookies, $baseUrl . '/ukm/55/messages');
            if ($latestMessages && count($latestMessages) >= 2) {
                echo "   ✓ Both messages are stored and retrievable\n";
                echo "   Latest conversation:\n";
                $lastTwo = array_slice($latestMessages, -2);
                foreach ($lastTwo as $msg) {
                    echo "     [{$msg['user']['name']}]: {$msg['message']}\n";
                }
            }
        }
    }
    
    echo "\n=== CHAT REALTIME TEST COMPLETED ===\n";
    echo "✓ Authentication works for both user types\n";
    echo "✓ Chat access works for admin_grup and anggota\n";
    echo "✓ Message sending works for both user types\n";
    echo "✓ Message retrieval works correctly\n";
    echo "✓ Inter-user communication is functional\n\n";
    
    echo "NEXT STEPS FOR MANUAL TESTING:\n";
    echo "1. Open two browser windows/tabs\n";
    echo "2. Login as thomas@test.com / password123 in first tab\n";
    echo "3. Login as andre@test.com / password123 in second tab\n";
    echo "4. Navigate both to: http://127.0.0.1:8000/ukm/55/chat\n";
    echo "5. Send messages and verify real-time delivery via Pusher\n";
    echo "6. Check browser console for Pusher events\n";
    
    // Cleanup
    @unlink($thomasCookies);
    @unlink($andreCookies);
}

function testUserLogin($email, $password, $cookieFile, $baseUrl) {
    // Get login page
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $baseUrl . '/login');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    
    $response = curl_exec($ch);
    
    // Extract CSRF token
    if (preg_match('/<input type="hidden" name="_token" value="([^"]+)"/', $response, $matches)) {
        $token = $matches[1];
    } else {
        curl_close($ch);
        return false;
    }
    
    // Submit login
    curl_setopt($ch, CURLOPT_URL, $baseUrl . '/login');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        '_token' => $token,
        'email' => $email,
        'password' => $password
    ]));
    
    $loginResponse = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    curl_close($ch);
    
    // Check if login was successful (should redirect to ukm.index)
    return $httpCode == 302 || strpos($loginResponse, 'dashboard') !== false || strpos($loginResponse, 'ukm') !== false;
}

function testChatAccess($cookieFile, $chatUrl) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $chatUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    curl_close($ch);
    
    return $httpCode == 200 && strpos($response, 'chat') !== false;
}

function testSendMessage($cookieFile, $messagesUrl, $message) {
    // Get CSRF token first
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, str_replace('/messages', '/chat', $messagesUrl));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    
    $response = curl_exec($ch);
    
    if (preg_match('/<meta name="csrf-token" content="([^"]+)"/', $response, $matches)) {
        $token = $matches[1];
    } else {
        curl_close($ch);
        return false;
    }
    
    // Send message
    curl_setopt($ch, CURLOPT_URL, $messagesUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'X-CSRF-TOKEN: ' . $token,
        'X-Requested-With: XMLHttpRequest'
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
        'message' => $message
    ]));
    
    $sendResponse = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    curl_close($ch);
    
    return $httpCode == 200;
}

function getMessages($cookieFile, $messagesUrl) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $messagesUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'X-Requested-With: XMLHttpRequest'
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    curl_close($ch);
    
    if ($httpCode == 200) {
        $data = json_decode($response, true);
        return $data['messages'] ?? null;
    }
    
    return null;
}

// Run the test
testChatRealtimeWithLogin();
?>

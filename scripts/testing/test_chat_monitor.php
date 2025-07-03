<?php

function monitorChatActivity() {
    echo "=== CHAT ACTIVITY MONITOR ===\n";
    echo "Monitoring chat messages in SIMS group (ID: 55)\n";
    echo "Press Ctrl+C to stop monitoring\n\n";
    
    try {
        $pdo = new PDO('mysql:host=127.0.0.1;dbname=myukm', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $lastMessageId = 0;
        
        // Get initial message count
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM chats WHERE group_id = 55");
        $stmt->execute();
        $initialCount = $stmt->fetch()['count'];
        
        echo "Initial message count: $initialCount\n";
        echo "Waiting for new messages...\n\n";
        
        while (true) {
            // Check for new messages
            $stmt = $pdo->prepare("
                SELECT c.id, c.message, c.created_at, u.name, u.role 
                FROM chats c 
                JOIN users u ON c.user_id = u.id 
                WHERE c.group_id = 55 AND c.id > ? 
                ORDER BY c.created_at ASC
            ");
            $stmt->execute([$lastMessageId]);
            $newMessages = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($newMessages as $msg) {
                $timestamp = date('H:i:s', strtotime($msg['created_at']));
                $role = strtoupper($msg['role']);
                echo "[$timestamp] [{$role}] {$msg['name']}: {$msg['message']}\n";
                $lastMessageId = $msg['id'];
            }
            
            // Check user activity
            $stmt = $pdo->prepare("
                SELECT u.name, u.role, u.last_seen_at
                FROM group_user gu 
                JOIN users u ON gu.user_id = u.id 
                WHERE gu.group_id = 55 AND u.last_seen_at >= ?
            ");
            $stmt->execute([date('Y-m-d H:i:s', strtotime('-5 minutes'))]);
            $activeUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (!empty($newMessages) && !empty($activeUsers)) {
                echo "   Active users: ";
                $userList = array_map(function($u) {
                    return $u['name'] . '(' . $u['role'] . ')';
                }, $activeUsers);
                echo implode(', ', $userList) . "\n";
            }
            
            sleep(2); // Check every 2 seconds
        }
        
    } catch (PDOException $e) {
        echo "Database error: " . $e->getMessage() . "\n";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}

function showChatStats() {
    echo "=== CHAT STATISTICS ===\n\n";
    
    try {
        $pdo = new PDO('mysql:host=127.0.0.1;dbname=myukm', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Total messages in SIMS group
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM chats WHERE group_id = 55");
        $stmt->execute();
        $totalMessages = $stmt->fetch()['count'];
        
        echo "Total messages in SIMS group: $totalMessages\n";
        
        // Messages by user
        $stmt = $pdo->prepare("
            SELECT u.name, u.role, COUNT(c.id) as message_count
            FROM chats c 
            JOIN users u ON c.user_id = u.id 
            WHERE c.group_id = 55 
            GROUP BY u.id, u.name, u.role 
            ORDER BY message_count DESC
        ");
        $stmt->execute();
        $userStats = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (!empty($userStats)) {
            echo "\nMessages by user:\n";
            foreach ($userStats as $stat) {
                echo "  {$stat['name']} ({$stat['role']}): {$stat['message_count']} messages\n";
            }
        }
        
        // Recent messages (last 10)
        $stmt = $pdo->prepare("
            SELECT c.message, c.created_at, u.name, u.role 
            FROM chats c 
            JOIN users u ON c.user_id = u.id 
            WHERE c.group_id = 55 
            ORDER BY c.created_at DESC 
            LIMIT 10
        ");
        $stmt->execute();
        $recentMessages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (!empty($recentMessages)) {
            echo "\nRecent messages (latest 10):\n";
            foreach (array_reverse($recentMessages) as $msg) {
                $time = date('H:i:s', strtotime($msg['created_at']));
                echo "  [$time] {$msg['name']}: {$msg['message']}\n";
            }
        }
        
        // Group members
        echo "\nSIMS group members:\n";
        $stmt = $pdo->prepare("
            SELECT u.name, u.role, u.email, u.last_seen_at
            FROM group_user gu 
            JOIN users u ON gu.user_id = u.id 
            WHERE gu.group_id = 55
        ");
        $stmt->execute();
        $members = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($members as $member) {
            $lastSeen = $member['last_seen_at'] ? date('Y-m-d H:i:s', strtotime($member['last_seen_at'])) : 'Never';
            echo "  {$member['name']} ({$member['role']}) - {$member['email']} - Last seen: $lastSeen\n";
        }
        
    } catch (PDOException $e) {
        echo "Database error: " . $e->getMessage() . "\n";
    }
}

if ($argc > 1 && $argv[1] === 'monitor') {
    monitorChatActivity();
} else {
    showChatStats();
    echo "\n";
    echo "=== USAGE ===\n";
    echo "php test_chat_monitor.php          - Show current statistics\n";
    echo "php test_chat_monitor.php monitor  - Start real-time monitoring\n\n";
    
    echo "=== MANUAL TESTING INSTRUCTIONS ===\n";
    echo "1. Open browser and login as Thomas: http://127.0.0.1:8000/login\n";
    echo "   Email: thomas@test.com / Password: password123\n\n";
    echo "2. Open another browser/tab and login as Andre: http://127.0.0.1:8000/login\n";
    echo "   Email: andre@test.com / Password: password123\n\n";
    echo "3. Both navigate to SIMS chat: http://127.0.0.1:8000/ukm/55/chat\n\n";
    echo "4. Start monitoring: php test_chat_monitor.php monitor\n\n";
    echo "5. Send messages and verify:\n";
    echo "   - Messages appear in monitoring console\n";
    echo "   - Real-time delivery between users\n";
    echo "   - Pusher events in browser console\n";
    echo "   - No session timeout issues\n\n";
    
    echo "=== TESTING SCENARIOS ===\n";
    echo "A. Admin Grup → Anggota Communication:\n";
    echo "   - Thomas (admin_grup) sends message\n";
    echo "   - Andre (anggota) receives immediately\n\n";
    echo "B. Anggota → Admin Grup Communication:\n";
    echo "   - Andre (anggota) sends message\n";
    echo "   - Thomas (admin_grup) receives immediately\n\n";
    echo "C. Session Management:\n";
    echo "   - Keep chat open for >10 minutes\n";
    echo "   - Verify CSRF auto-refresh works\n";
    echo "   - No session expired errors\n\n";
}
?>

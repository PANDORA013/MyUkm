<?php

// Simple login test via Laravel artisan
echo "=== CREATING TEST LOGIN CREDENTIALS ===\n\n";

try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=myukm', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create simple password hash using Laravel's Hash::make equivalent
    $password = 'password123';
    $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
    
    // Update users with proper Laravel-compatible password hash
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = 152");
    $stmt->execute([$hash]);
    
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = 156");
    $stmt->execute([$hash]);
    
    echo "✓ Updated passwords for Thomas and Andre\n";
    echo "  Email: thomas@test.com / Password: password123\n";
    echo "  Email: andre@test.com / Password: password123\n\n";
    
    // Test credentials created
    echo "=== TEST CREDENTIALS READY ===\n";
    echo "Now you can manually test:\n";
    echo "1. Open: http://127.0.0.1:8000/login\n";
    echo "2. Login as Thomas (admin_grup): thomas@test.com / password123\n";
    echo "3. Open another tab and login as Andre (anggota): andre@test.com / password123\n";
    echo "4. Both users navigate to: http://127.0.0.1:8000/ukm/55/chat\n";
    echo "5. Send messages between users and verify real-time delivery\n";
    echo "6. Check browser console for Pusher connection and events\n\n";
    
    // Check current messages in chat
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM chats WHERE group_id = 55");
    $stmt->execute();
    $messageCount = $stmt->fetch()['count'];
    
    echo "Current messages in SIMS group: $messageCount\n\n";
    
    // Show group info
    $stmt = $pdo->prepare("SELECT g.*, COUNT(gu.user_id) as member_count FROM groups g LEFT JOIN group_user gu ON g.id = gu.group_id WHERE g.id = 55 GROUP BY g.id");
    $stmt->execute();
    $group = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($group) {
        echo "SIMS Group Info:\n";
        echo "- Name: {$group['name']}\n";
        echo "- Referral Code: {$group['referral_code']}\n";
        echo "- Members: {$group['member_count']}\n";
        echo "- Status: " . ($group['is_active'] ? 'Active' : 'Inactive') . "\n\n";
    }
    
    echo "=== MANUAL TESTING CHECKLIST ===\n";
    echo "[ ] Login Thomas (admin_grup) - Check access to chat\n";
    echo "[ ] Login Andre (anggota) - Check access to chat\n";
    echo "[ ] Send message from Thomas to group\n";
    echo "[ ] Verify Andre receives message in real-time\n";
    echo "[ ] Send message from Andre to group\n";
    echo "[ ] Verify Thomas receives message in real-time\n";
    echo "[ ] Check browser console for Pusher events\n";
    echo "[ ] Verify CSRF token refresh works\n";
    echo "[ ] Test session management (no timeout during chat)\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>

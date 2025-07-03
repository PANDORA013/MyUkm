<?php

function testNewUserRegistrationAndUKMAccess() {
    echo "=== TESTING NEW USER REGISTRATION & UKM ACCESS ===\n\n";
    
    try {
        $pdo = new PDO('mysql:host=127.0.0.1;dbname=myukm', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Check current users
        $stmt = $pdo->prepare("SELECT id, name, email, role FROM users ORDER BY created_at DESC");
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "Current users in database:\n";
        foreach ($users as $user) {
            $email = $user['email'] ?: 'NULL';
            echo "  ID: {$user['id']} | {$user['name']} | {$email} | {$user['role']}\n";
        }
        echo "\n";
        
        // Get latest user (Milla)
        $latestUser = $users[0];
        echo "Latest registered user: {$latestUser['name']} (ID: {$latestUser['id']})\n";
        
        // Check if Milla can be added to a group (test group membership capability)
        echo "\nTesting group membership for new user...\n";
        
        // Check available groups
        $stmt = $pdo->prepare("SELECT id, name, referral_code FROM groups WHERE is_active = 1");
        $stmt->execute();
        $groups = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "Available groups:\n";
        foreach ($groups as $group) {
            echo "  ID: {$group['id']} | {$group['name']} | Code: {$group['referral_code']}\n";
        }
        
        // Add Milla to SIMS group for testing
        $simsGroupId = 55;
        $millaId = $latestUser['id'];
        
        // Check if already member
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM group_user WHERE user_id = ? AND group_id = ?");
        $stmt->execute([$millaId, $simsGroupId]);
        $isMember = $stmt->fetch()['count'] > 0;
        
        if (!$isMember) {
            $stmt = $pdo->prepare("INSERT INTO group_user (user_id, group_id, created_at, updated_at) VALUES (?, ?, NOW(), NOW())");
            $stmt->execute([$millaId, $simsGroupId]);
            echo "\n✓ Added Milla to SIMS group for testing\n";
        } else {
            echo "\n✓ Milla is already a member of SIMS group\n";
        }
        
        // Update Milla's email for login testing
        $stmt = $pdo->prepare("UPDATE users SET email = ? WHERE id = ?");
        $stmt->execute(['milla@test.com', $millaId]);
        
        // Set password for Milla
        $hashedPassword = password_hash('password123', PASSWORD_BCRYPT, ['cost' => 10]);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$hashedPassword, $millaId]);
        
        echo "✓ Set email and password for Milla: milla@test.com / password123\n";
        
        // Check SIMS group members now
        echo "\nSIMS group members after update:\n";
        $stmt = $pdo->prepare("
            SELECT u.name, u.role, u.email
            FROM group_user gu 
            JOIN users u ON gu.user_id = u.id 
            WHERE gu.group_id = 55
            ORDER BY u.name
        ");
        $stmt->execute();
        $simsMembers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($simsMembers as $member) {
            echo "  {$member['name']} ({$member['role']}) - {$member['email']}\n";
        }
        
        echo "\n=== VITE ERROR VERIFICATION ===\n";
        
        // Test if Vite manifest error is fixed
        $manifestPath = __DIR__ . '/public/build/manifest.json';
        if (file_exists($manifestPath)) {
            $manifest = json_decode(file_get_contents($manifestPath), true);
            echo "✓ Vite manifest exists\n";
            
            if (isset($manifest['resources/js/app.js'])) {
                echo "✓ resources/js/app.js found in manifest\n";
                
                $appJs = $manifest['resources/js/app.js'];
                if (isset($appJs['css']) && !empty($appJs['css'])) {
                    echo "✓ CSS bundled with JS: " . implode(', ', $appJs['css']) . "\n";
                }
            }
            
            // Check if resources/css/app.css is NOT directly in manifest (this was the problem)
            if (!isset($manifest['resources/css/app.css'])) {
                echo "✓ resources/css/app.css NOT in manifest (correct - it's bundled with JS)\n";
            }
        }
        
        echo "\n=== TESTING INSTRUCTIONS ===\n";
        echo "Now you can test the full flow:\n\n";
        
        echo "1. REGISTRATION TEST:\n";
        echo "   - Go to: http://127.0.0.1:8000/register\n";
        echo "   - Register a new user\n";
        echo "   - Should complete without Vite errors\n\n";
        
        echo "2. THREE-USER CHAT TEST:\n";
        echo "   a) Login as Thomas: thomas@test.com / password123\n";
        echo "   b) Login as Andre: andre@test.com / password123\n";
        echo "   c) Login as Milla: milla@test.com / password123\n\n";
        
        echo "3. CHAT TESTING:\n";
        echo "   - All three navigate to: http://127.0.0.1:8000/ukm/55/chat\n";
        echo "   - Test three-way communication:\n";
        echo "     * Thomas (admin_grup) → Andre & Milla\n";
        echo "     * Andre (anggota) → Thomas & Milla\n";
        echo "     * Milla (anggota) → Thomas & Andre\n\n";
        
        echo "4. ROLE-BASED ACCESS TEST:\n";
        echo "   - Verify admin_grup (Thomas) can access grup/ URLs\n";
        echo "   - Verify anggota (Andre, Milla) redirected appropriately\n";
        echo "   - All should access UKM features equally\n\n";
        
        echo "5. START MONITORING:\n";
        echo "   php test_chat_monitor.php monitor\n\n";
        
        echo "=== VERIFICATION CHECKLIST ===\n";
        echo "[ ] Registration completes without Vite errors\n";
        echo "[ ] All three users can login successfully\n";
        echo "[ ] All three users can access SIMS chat\n";
        echo "[ ] Three-way real-time communication works\n";
        echo "[ ] No CSS/asset loading issues\n";
        echo "[ ] Role-based access control works properly\n";
        echo "[ ] Session management works (no timeouts)\n";
        echo "[ ] Pusher events work for all users\n";
        
    } catch (PDOException $e) {
        echo "Database error: " . $e->getMessage() . "\n";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}

// Run the test
testNewUserRegistrationAndUKMAccess();
?>

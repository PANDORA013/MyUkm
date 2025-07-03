<?php

require_once __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "========================================\n";
echo "   MyUKM Real-time Test\n";
echo "========================================\n\n";

echo "🔍 Testing Real-time Chat Setup...\n\n";

// 1. Check Pusher config
echo "[1/4] Pusher Configuration:\n";
echo "   • App Key: " . config('broadcasting.connections.pusher.key') . "\n";
echo "   • Cluster: " . config('broadcasting.connections.pusher.options.cluster') . "\n";
echo "   • Driver: " . config('broadcasting.default') . "\n\n";

// 2. Test group and users
echo "[2/4] Database Status:\n";
$sims = \App\Models\Group::where('referral_code', '0810')->first();
if ($sims) {
    echo "   • SIMS Group Found: ID {$sims->id}, Code: {$sims->referral_code}\n";
    echo "   • Members: " . $sims->users()->count() . "\n";
    echo "   • Chat Channel: group.{$sims->referral_code}\n";
} else {
    echo "   ❌ SIMS Group not found!\n";
}

// 3. Test message creation and broadcasting
echo "\n[3/4] Testing Message Broadcasting...\n";
try {
    if ($sims) {
        $testUser = \App\Models\User::first();
        if ($testUser) {
            // Create test message
            $testChat = \App\Models\Chat::create([
                'user_id' => $testUser->id,
                'group_id' => $sims->id,
                'message' => '🚀 Real-time Test Message - ' . date('H:i:s'),
            ]);
            
            $testChat->load(['user', 'group']);
            
            echo "   ✅ Test message created (ID: {$testChat->id})\n";
            echo "   📝 Message: \"{$testChat->message}\"\n";
            echo "   👤 User: {$testUser->name}\n";
            echo "   📡 Broadcasting to channel: group.{$sims->referral_code}\n";
            
            // Dispatch to queue
            dispatch(new \App\Jobs\BroadcastChatMessage($testChat, $sims->referral_code));
            echo "   ⚡ Dispatched to queue for broadcasting\n";
            
        } else {
            echo "   ❌ No users found in database\n";
        }
    }
} catch (\Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

// 4. Instructions
echo "\n[4/4] Testing Instructions:\n";
echo "   1. Open browser: http://localhost:8000/login\n";
echo "   2. Login with test user credentials\n";
echo "   3. Go to: http://localhost:8000/ukm/0810/chat\n";
echo "   4. Open browser console (F12)\n";
echo "   5. Look for these messages:\n";
echo "      • '✅ Subscribed to private channel: group.0810'\n";
echo "      • '✅ Pusher connected successfully'\n";
echo "      • 'Pusher connection state: connected'\n";
echo "   6. Send a test message\n";
echo "   7. Message should appear instantly without page reload\n\n";

echo "========================================\n";
echo "   🔧 If Real-time Doesn't Work:\n";
echo "========================================\n";
echo "1. Check browser console for errors\n";
echo "2. Verify user is logged in and member of group\n";
echo "3. Make sure queue worker is running\n";
echo "4. Check Network tab for failed requests\n";
echo "5. Try refreshing the page\n\n";

echo "Test completed! Check the instructions above.\n";

?>

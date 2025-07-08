<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel 11 application
$app = require_once __DIR__ . '/../bootstrap/app.php';

// Boot the application
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Import facades after bootstrap
use Illuminate\Support\Facades\DB;

try {
    echo "🔍 CHAT SYSTEM DEBUGGING - MyUKM Application\n";
    echo "============================================\n\n";

    // 1. Check if users exist and their roles
    echo "1. Checking Users and Roles...\n";
    $users = DB::table('users')->select('id', 'name', 'nim', 'role')->get();
    foreach ($users as $user) {
        echo "   👤 User: {$user->name} (NIM: {$user->nim}) - Role: {$user->role}\n";
    }
    echo "\n";

    // 2. Check groups and memberships
    echo "2. Checking Groups and Memberships...\n";
    $groups = DB::table('groups')
        ->select('id', 'name', 'referral_code', 'is_active')
        ->get();
    
    foreach ($groups as $group) {
        echo "   🏢 Group: {$group->name} (Code: {$group->referral_code}) - Active: " . ($group->is_active ? 'Yes' : 'No') . "\n";
        
        // Check members
        $members = DB::table('group_user')
            ->join('users', 'group_user.user_id', '=', 'users.id')
            ->where('group_user.group_id', $group->id)
            ->select('users.name', 'users.nim', 'users.role', 'group_user.is_admin', 'group_user.is_muted')
            ->get();
            
        foreach ($members as $member) {
            $adminStatus = $member->is_admin ? 'Admin' : 'Member';
            $muteStatus = $member->is_muted ? 'Muted' : 'Active';
            echo "     - {$member->name} ({$member->role}) - {$adminStatus}, {$muteStatus}\n";
        }
    }
    echo "\n";

    // 3. Check chat messages
    echo "3. Checking Chat Messages...\n";
    $chats = DB::table('chats')
        ->join('users', 'chats.user_id', '=', 'users.id')
        ->join('groups', 'chats.group_id', '=', 'groups.id')
        ->select('chats.*', 'users.name as sender', 'groups.name as group_name')
        ->orderBy('chats.created_at', 'desc')
        ->get();
    
    if ($chats->count() > 0) {
        foreach ($chats as $chat) {
            echo "   💬 [{$chat->group_name}] {$chat->sender}: {$chat->message} ({$chat->created_at})\n";
        }
    } else {
        echo "   ℹ️  No chat messages found\n";
    }
    echo "\n";

    // 4. Test middleware path checking
    echo "4. Testing Middleware Path Logic...\n";
    $testPaths = [
        'chat/send',
        'chat/messages',
        'chat/join',
        'ukm/0810/chat',
        'admin/dashboard',
        'grup/dashboard'
    ];
    
    foreach ($testPaths as $path) {
        echo "   🛣️  Path: /{$path}\n";
        
        // Check what middleware would do with this path
        if (str_starts_with($path, 'admin/') || $path === 'admin') {
            echo "      ❌ Blocked: Admin only\n";
        } elseif (str_starts_with($path, 'grup/') || $path === 'grup') {
            echo "      ❌ Blocked: Admin grup only\n";
        } elseif (str_starts_with($path, 'ukm/') || $path === 'ukm') {
            echo "      ✅ Allowed: All authenticated users\n";
        } else {
            echo "      ✅ Allowed: Default access\n";
        }
    }
    echo "\n";

    // 5. Check for missing events/classes
    echo "5. Checking Required Classes and Events...\n";
    
    $requiredClasses = [
        'App\Events\ChatMessageSent',
        'App\Events\MessageTyping',
        'App\Helpers\BroadcastHelper',
        'App\Http\Controllers\ChatController',
        'App\Models\Chat',
        'App\Models\Group',
        'App\Models\User'
    ];
    
    foreach ($requiredClasses as $class) {
        if (class_exists($class)) {
            echo "   ✅ Class exists: {$class}\n";
        } else {
            echo "   ❌ Missing class: {$class}\n";
        }
    }
    echo "\n";

    // 6. Check Pusher configuration
    echo "6. Checking Pusher Configuration...\n";
    $pusherKey = env('PUSHER_APP_KEY');
    $pusherSecret = env('PUSHER_APP_SECRET');
    $pusherCluster = env('PUSHER_APP_CLUSTER');
    
    echo "   🔑 Pusher Key: " . ($pusherKey ? "Set (***" . substr($pusherKey, -4) . ")" : "Not set") . "\n";
    echo "   🔐 Pusher Secret: " . ($pusherSecret ? "Set" : "Not set") . "\n";
    echo "   🌍 Pusher Cluster: " . ($pusherCluster ?: "Not set") . "\n";
    echo "\n";

    // 7. Simulate chat access for different user roles
    echo "7. Simulating Chat Access for Different Roles...\n";
    
    foreach ($users as $user) {
        echo "   👤 Testing access for: {$user->name} (Role: {$user->role})\n";
        
        // Check if user is member of any group
        $userGroups = DB::table('group_user')
            ->join('groups', 'group_user.group_id', '=', 'groups.id')
            ->where('group_user.user_id', $user->id)
            ->select('groups.name', 'groups.referral_code', 'group_user.is_muted')
            ->get();
            
        if ($userGroups->count() > 0) {
            foreach ($userGroups as $group) {
                $accessStatus = $group->is_muted ? "❌ Muted" : "✅ Can chat";
                echo "     - Group: {$group->name} ({$group->referral_code}) - {$accessStatus}\n";
            }
        } else {
            echo "     - ❌ Not member of any group\n";
        }
    }
    echo "\n";

    echo "🎯 DIAGNOSIS COMPLETE!\n";
    echo "====================\n";
    
    // Summary
    echo "\n🔍 POTENTIAL ISSUES FOUND:\n";
    
    $issues = [];
    
    // Check for users without groups
    $usersWithoutGroups = DB::table('users')
        ->whereNotExists(function ($query) {
            $query->select(DB::raw(1))
                  ->from('group_user')
                  ->whereRaw('group_user.user_id = users.id');
        })->count();
    
    if ($usersWithoutGroups > 0) {
        $issues[] = "❌ {$usersWithoutGroups} user(s) are not members of any group";
    }
    
    // Check for muted users
    $mutedUsers = DB::table('group_user')->where('is_muted', 1)->count();
    if ($mutedUsers > 0) {
        $issues[] = "⚠️  {$mutedUsers} user(s) are muted in groups";
    }
    
    // Check for inactive groups
    $inactiveGroups = DB::table('groups')->where('is_active', 0)->count();
    if ($inactiveGroups > 0) {
        $issues[] = "⚠️  {$inactiveGroups} group(s) are inactive";
    }
    
    if (empty($issues)) {
        echo "✅ No major issues found! Chat system should be working.\n";
        echo "\nℹ️  If chat is still not working, check:\n";
        echo "   1. Browser console for JavaScript errors\n";
        echo "   2. Network tab for failed AJAX requests\n";
        echo "   3. Laravel logs for server errors\n";
        echo "   4. Pusher connectivity\n";
    } else {
        foreach ($issues as $issue) {
            echo "{$issue}\n";
        }
    }

} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

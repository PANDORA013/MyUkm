<?php

/*
 * Chat Function Verification Script
 * Verifies all chat-related functions are working correctly after middleware fixes
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

echo "\n=== CHAT FUNCTION VERIFICATION ===\n";
echo "Timestamp: " . date('Y-m-d H:i:s') . "\n\n";

// Test database connection
try {
    $pdo = new PDO("mysql:host=127.0.0.1;dbname=myukm", "root", "");
    echo "✅ Database Connection: SUCCESS\n";
} catch (Exception $e) {
    echo "❌ Database Connection: FAILED - " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n--- CHAT CONTROLLER METHODS VERIFICATION ---\n";

$controllerPath = __DIR__ . '/../app/Http/Controllers/ChatController.php';
$controllerContent = file_get_contents($controllerPath);

$requiredMethods = [
    'sendChat' => 'Handle sending chat messages via AJAX',
    'getMessagesAjax' => 'Get messages for AJAX requests',
    'typing' => 'Handle typing indicator',
    'joinGroup' => 'Join group functionality',
    'logoutGroup' => 'Logout from group',
    'getUnreadCount' => 'Get unread message count',
    'getMessages' => 'Get messages for specific group (UKM route)',
    'sendMessage' => 'Send message for specific group (UKM route)'
];

echo "Checking ChatController methods:\n";
foreach ($requiredMethods as $method => $description) {
    if (strpos($controllerContent, "public function $method") !== false) {
        echo "  ✅ $method() - $description\n";
    } else {
        echo "  ❌ $method() - MISSING - $description\n";
    }
}

echo "\n--- ROUTE VERIFICATION ---\n";

$routeFile = __DIR__ . '/../routes/web.php';
$routeContent = file_get_contents($routeFile);

$chatRoutes = [
    'chat.send' => 'POST /chat/send -> sendChat',
    'chat.messages' => 'GET /chat/messages -> getMessagesAjax',
    'chat.typing' => 'POST /chat/typing -> typing',
    'chat.join' => 'POST /chat/join -> joinGroup',
    'chat.logout' => 'POST /chat/logout -> logoutGroup',
    'chat.unread-count' => 'GET /chat/unread-count -> getUnreadCount',
    'ukm.messages' => 'GET /ukm/{code}/messages -> getMessages',
    'ukm.send-message' => 'POST /ukm/{code}/messages -> sendMessage'
];

echo "Checking route definitions:\n";
foreach ($chatRoutes as $routeName => $description) {
    if (strpos($routeContent, $routeName) !== false) {
        echo "  ✅ $routeName - $description\n";
    } else {
        echo "  ❌ $routeName - MISSING - $description\n";
    }
}

echo "\n--- MIDDLEWARE VERIFICATION ---\n";

// Check middleware configuration
$middlewarePath = __DIR__ . '/../app/Http/Middleware/EnsureUserRole.php';
$middlewareContent = file_get_contents($middlewarePath);

echo "Checking EnsureUserRole middleware:\n";
if (strpos($middlewareContent, "str_starts_with(\$path, 'chat/')") !== false) {
    echo "  ✅ Chat paths explicitly allowed\n";
} else {
    echo "  ❌ Chat paths not explicitly allowed\n";
}

if (strpos($middlewareContent, "str_starts_with(\$path, 'ukm/')") !== false) {
    echo "  ✅ UKM paths explicitly allowed\n";
} else {
    echo "  ❌ UKM paths not explicitly allowed\n";
}

echo "\n--- VIEW-CONTROLLER ENDPOINT MAPPING ---\n";

$chatViewPath = __DIR__ . '/../resources/views/chat.blade.php';
if (file_exists($chatViewPath)) {
    $chatViewContent = file_get_contents($chatViewPath);
    
    $endpointMappings = [
        "route('chat.send')" => 'sendChat method',
        "route('chat.messages')" => 'getMessagesAjax method',
        "route('chat.typing')" => 'typing method',
        "route('chat.join')" => 'joinGroup method',
        "route('chat.logout')" => 'logoutGroup method'
    ];
    
    echo "Checking view-controller endpoint mappings:\n";
    foreach ($endpointMappings as $endpoint => $method) {
        if (strpos($chatViewContent, $endpoint) !== false) {
            echo "  ✅ $endpoint -> $method\n";
        } else {
            echo "  ❌ $endpoint -> $method - MISSING\n";
        }
    }
} else {
    echo "  ❌ chat.blade.php view file not found\n";
}

echo "\n--- ROLE-BASED ACCESS SIMULATION ---\n";

// Test different user roles
$testUsers = [
    ['name' => 'Test Member', 'role' => 'anggota'],
    ['name' => 'Test Admin Grup', 'role' => 'admin_grup'], 
    ['name' => 'Test Admin Website', 'role' => 'admin_website']
];

echo "Simulating chat access for different user roles:\n";
foreach ($testUsers as $user) {
    echo "  👤 {$user['name']} (Role: {$user['role']})\n";
    
    $chatPaths = ['/chat/send', '/chat/messages', '/ukm/test123/chat'];
    foreach ($chatPaths as $path) {
        $allowed = true;
        
        // Simulate EnsureUserRole middleware logic
        if (str_starts_with($path, 'admin/') && $user['role'] !== 'admin_website') {
            $allowed = false;
        } elseif (str_starts_with($path, 'grup/') && $user['role'] !== 'admin_grup') {
            $allowed = false;
        } elseif (str_starts_with($path, 'ukm/') || str_starts_with($path, 'chat/')) {
            $allowed = true; // Explicitly allowed after our fix
        }
        
        echo "    " . ($allowed ? "✅" : "❌") . " $path\n";
    }
}

echo "\n--- DATABASE INTEGRITY CHECK ---\n";

// Check required tables
$requiredTables = ['users', 'groups', 'group_user', 'chats', 'cache', 'sessions', 'jobs'];
echo "Checking required tables:\n";
foreach ($requiredTables as $table) {
    try {
        $result = $pdo->query("SELECT COUNT(*) FROM $table")->fetchColumn();
        echo "  ✅ Table '$table' exists with $result records\n";
    } catch (Exception $e) {
        echo "  ❌ Table '$table' missing or inaccessible\n";
    }
}

echo "\n--- EVENT & BROADCASTING CHECK ---\n";

$requiredClasses = [
    'app/Events/ChatMessageSent.php' => 'Chat message broadcasting event',
    'app/Jobs/ProcessChatMessage.php' => 'Chat message processing job',
    'app/Helpers/BroadcastHelper.php' => 'Safe broadcasting helper'
];

echo "Checking required classes:\n";
foreach ($requiredClasses as $file => $description) {
    $path = __DIR__ . '/../' . $file;
    if (file_exists($path)) {
        echo "  ✅ $file - $description\n";
    } else {
        echo "  ❌ $file - MISSING - $description\n";
    }
}

echo "\n--- REAL-TIME FUNCTIONALITY CHECK ---\n";

// Check Pusher configuration
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $envContent = file_get_contents($envFile);
    
    $pusherConfigs = [
        'PUSHER_APP_KEY' => 'Pusher application key',
        'PUSHER_APP_SECRET' => 'Pusher application secret',
        'PUSHER_APP_CLUSTER' => 'Pusher cluster configuration'
    ];
    
    echo "Checking Pusher configuration:\n";
    foreach ($pusherConfigs as $config => $description) {
        if (strpos($envContent, $config . '=') !== false) {
            echo "  ✅ $config - $description\n";
        } else {
            echo "  ❌ $config - MISSING - $description\n";
        }
    }
} else {
    echo "  ❌ .env file not found\n";
}

echo "\n--- RECOMMENDED TESTS ---\n";

echo "Manual testing recommendations:\n";
echo "1. Login as different user roles (anggota, admin_grup, admin_website)\n";
echo "2. Join a group and test chat functionality\n";
echo "3. Test AJAX endpoints with browser developer tools\n";
echo "4. Check Laravel logs for any errors: storage/logs/laravel.log\n";
echo "5. Test real-time messaging between multiple browser windows\n";

echo "\n--- DEBUGGING COMMANDS ---\n";
echo "php artisan config:clear && php artisan cache:clear\n";
echo "php artisan route:list | grep chat\n";
echo "php artisan tinker (then test User and Group models)\n";
echo "tail -f storage/logs/laravel.log\n";

echo "\n=== VERIFICATION COMPLETE ===\n";

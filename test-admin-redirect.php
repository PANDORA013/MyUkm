<?php
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\AuthService;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

echo "=== Test Admin Login Redirect ===\n";

// Get admin user
$adminUser = User::where('role', 'admin')->first();
if (!$adminUser) {
    echo "❌ Admin user tidak ditemukan!\n";
    exit(1);
}

echo "✅ Admin user ditemukan:\n";
echo "   ID: {$adminUser->id}\n";
echo "   NIM: {$adminUser->nim}\n";
echo "   Name: {$adminUser->name}\n";
echo "   Role: {$adminUser->role}\n\n";

// Test AuthService redirect
$authService = new AuthService();
$redirectRoute = $authService->getRedirectRoute($adminUser);

echo "🔄 Testing AuthService redirect:\n";
echo "   Expected redirect: {$redirectRoute}\n";

if ($redirectRoute === '/admin/dashboard') {
    echo "✅ AuthService redirect benar!\n";
} else {
    echo "❌ AuthService redirect salah! Expected: /admin/dashboard, Got: {$redirectRoute}\n";
}

echo "\n=== Test Complete ===\n";

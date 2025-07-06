<?php

require __DIR__ . '/../../vendor/autoload.php';

$app = require __DIR__ . '/../../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing login for admin user...\n";

$credentials = [
    'nim' => 'TH.171004',
    'password' => 'AR.171004'
];

echo "Attempting auth with credentials:\n";
echo "NIM: " . $credentials['nim'] . "\n";
echo "Password: " . $credentials['password'] . "\n\n";

// Try to manually authenticate
$user = App\Models\User::where('nim', $credentials['nim'])->first();

if ($user) {
    echo "User found in database:\n";
    echo "Name: " . $user->name . "\n";
    echo "NIM: " . $user->nim . "\n";
    echo "Role: " . $user->role . "\n";
    echo "Password hash: " . $user->password . "\n\n";
    
    // Test password hash
    $passwordCheck = \Illuminate\Support\Facades\Hash::check($credentials['password'], $user->password);
    echo "Password check result: " . ($passwordCheck ? 'PASS' : 'FAIL') . "\n\n";
    
    // Test Auth::attempt
    echo "Testing Auth::attempt...\n";
    $authResult = \Illuminate\Support\Facades\Auth::attempt($credentials);
    echo "Auth::attempt result: " . ($authResult ? 'SUCCESS' : 'FAILED') . "\n";
    
    if ($authResult) {
        $loggedInUser = \Illuminate\Support\Facades\Auth::user();
        echo "Logged in user: " . $loggedInUser->name . " (Role: " . $loggedInUser->role . ")\n";
    }
} else {
    echo "User not found!\n";
}

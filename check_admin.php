<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking admin user...\n";

$user = App\Models\User::where('nim', 'TH.171004')->first();

if ($user) {
    echo "✓ User found!\n";
    echo "Name: " . $user->name . "\n";
    echo "NIM: " . $user->nim . "\n";
    echo "Email: " . $user->email . "\n";
    echo "Role: " . $user->role . "\n";
    echo "Password hash: " . $user->password . "\n";
    
    // Test password verification
    $passwordCorrect = \Illuminate\Support\Facades\Hash::check('AR.171004', $user->password);
    echo "Password 'AR.171004' matches: " . ($passwordCorrect ? 'YES' : 'NO') . "\n";
} else {
    echo "✗ User with NIM 'TH.171004' not found!\n";
    
    // Check all users
    $allUsers = App\Models\User::all();
    echo "\nAll users in database:\n";
    foreach ($allUsers as $u) {
        echo "- " . $u->nim . " (" . $u->name . ") - Role: " . ($u->role ?? 'null') . "\n";
    }
}

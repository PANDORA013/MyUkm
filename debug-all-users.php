<?php
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

echo "=== All Users Debug ===\n";

$allUsers = User::all(['id', 'nim', 'name', 'role']);
foreach ($allUsers as $user) {
    echo "ID: {$user->id}, NIM: {$user->nim}, Name: {$user->name}, Role: '{$user->role}'\n";
}

<?php
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\DB;

echo "=== Debug Admin Users ===\n";

// Check admin_website users
$adminUsers = User::where('role', 'admin_website')->get(['id', 'nim', 'name', 'role']);
echo "Admin Website Users:\n";
foreach ($adminUsers as $user) {
    echo "ID: {$user->id}, NIM: {$user->nim}, Name: {$user->name}, Role: {$user->role}\n";
}

// Check admin_grup users
$adminGrupUsers = User::where('role', 'admin_grup')->get(['id', 'nim', 'name', 'role']);
echo "\nAdmin Grup Users:\n";
foreach ($adminGrupUsers as $user) {
    echo "ID: {$user->id}, NIM: {$user->nim}, Name: {$user->name}, Role: {$user->role}\n";
}

// Check all roles
$allRoles = User::select('role', DB::raw('count(*) as count'))->groupBy('role')->get();
echo "\nAll User Roles:\n";
foreach ($allRoles as $role) {
    echo "Role: {$role->role}, Count: {$role->count}\n";
}

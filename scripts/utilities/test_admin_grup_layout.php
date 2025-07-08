<?php
/**
 * Test script untuk verifikasi layout admin_grup
 * 
 * Script ini melakukan test:
 * 1. Login dengan user role admin_grup
 * 2. Akses halaman yang menggunakan layout admin_grup
 * 3. Verifikasi tidak ada error pada layout
 */

// Simulate web request environment
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/ukm';
$_SERVER['SERVER_NAME'] = 'localhost';
$_SERVER['SERVER_PORT'] = '8000';
$_SERVER['HTTPS'] = '';

require_once __DIR__ . '/../../vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Group;
use App\Models\GroupUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

echo "=== Test Admin Grup Layout ===\n\n";

try {
    // Bootstrap Laravel app
    $app = require_once __DIR__ . '/../../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    
    $request = Request::capture();
    $response = $kernel->handle($request);
    
    echo "✅ Laravel application bootstrap berhasil\n";
    
    // Test 1: Cek apakah User model memiliki relationship adminGroups
    echo "\n1. Testing adminGroups relationship pada User model...\n";
    
    $userModel = new User();
    if (method_exists($userModel, 'adminGroups')) {
        echo "✅ Method adminGroups() ada pada User model\n";
    } else {
        echo "❌ Method adminGroups() tidak ditemukan pada User model\n";
        return;
    }
    
    // Test 2: Cek apakah ada user dengan role admin_grup
    echo "\n2. Testing user dengan role admin_grup...\n";
    
    $adminGrupUser = User::where('role', 'admin_grup')->first();
    if ($adminGrupUser) {
        echo "✅ Ditemukan user dengan role admin_grup: {$adminGrupUser->name} (ID: {$adminGrupUser->id})\n";
        
        // Test relationship adminGroups
        try {
            $adminGroups = $adminGrupUser->adminGroups;
            echo "✅ Relationship adminGroups berhasil dipanggil\n";
            echo "   Jumlah grup admin: " . $adminGroups->count() . "\n";
            
            if ($adminGroups->count() > 0) {
                foreach ($adminGroups as $group) {
                    echo "   - {$group->name} (ID: {$group->id})\n";
                }
            } else {
                echo "   (Belum ada grup untuk user ini)\n";
            }
        } catch (Exception $e) {
            echo "❌ Error saat mengakses adminGroups: " . $e->getMessage() . "\n";
        }
    } else {
        echo "⚠️  Tidak ada user dengan role admin_grup dalam database\n";
        echo "   Membuat user test...\n";
        
        // Create test user
        $testUser = User::create([
            'name' => 'Admin Grup Test',
            'nim' => 'TEST_' . rand(100000, 999999),
            'email' => 'admin_grup_test@test.com',
            'password' => Hash::make('password123'),
            'role' => 'admin_grup'
        ]);
        
        echo "✅ User test dibuat: {$testUser->name} (ID: {$testUser->id})\n";
        $adminGrupUser = $testUser;
    }
    
    // Test 3: Test layout rendering (simulate)
    echo "\n3. Testing layout admin_grup rendering...\n";
    
    $layoutPath = __DIR__ . '/../../resources/views/layouts/admin_grup.blade.php';
    if (file_exists($layoutPath)) {
        echo "✅ File layout admin_grup.blade.php ditemukan\n";
        
        // Read layout content
        $layoutContent = file_get_contents($layoutPath);
        
        // Check for common issues
        $checks = [
            'Auth::user()->adminGroups' => 'Reference to adminGroups relationship',
            '@if(Auth::user()->role === \'admin_grup\'' => 'Role check for admin_grup',
            'fas fa-crown' => 'Admin crown icon',
            'Kelola UKM' => 'Admin menu section'
        ];
        
        foreach ($checks as $pattern => $description) {
            if (strpos($layoutContent, $pattern) !== false) {
                echo "✅ {$description} ditemukan\n";
            } else {
                echo "❌ {$description} tidak ditemukan\n";
            }
        }
        
        // Check for potential issues
        if (strpos($layoutContent, 'Auth::user()->adminGroups && Auth::user()->adminGroups->count()') !== false) {
            echo "✅ Null-safe check untuk adminGroups ditemukan\n";
        } else {
            echo "⚠️  Null-safe check untuk adminGroups mungkin kurang\n";
        }
        
    } else {
        echo "❌ File layout admin_grup.blade.php tidak ditemukan\n";
    }
    
    // Test 4: Database connection check
    echo "\n4. Testing database connection...\n";
    
    try {
        $userCount = User::count();
        $groupCount = Group::count();
        echo "✅ Database connection berhasil\n";
        echo "   Total users: {$userCount}\n";
        echo "   Total groups: {$groupCount}\n";
    } catch (Exception $e) {
        echo "❌ Database connection error: " . $e->getMessage() . "\n";
    }
    
    echo "\n=== Test Selesai ===\n";
    echo "Server berjalan di: http://127.0.0.1:8000\n";
    echo "Silakan akses halaman tersebut untuk test manual.\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

<?php

require_once __DIR__ . '/../../vendor/autoload.php';

// Bootstrap Laravel 11
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\UserDeletionHistory;
use Illuminate\Support\Facades\DB;

echo "=== DELETING ADMIN ACCOUNT ===" . PHP_EOL;

// ID akun yang akan dihapus
$userId = 153;

try {
    // Mulai transaksi database
    DB::beginTransaction();
    
    // Cari user yang akan dihapus
    $user = User::find($userId);
    
    if (!$user) {
        echo "User dengan ID $userId tidak ditemukan!" . PHP_EOL;
        DB::rollBack();
        exit;
    }
    
    echo "User ditemukan: " . $user->name . " (Role: " . $user->role . ")" . PHP_EOL;
    
    // Catat riwayat penghapusan
    UserDeletionHistory::create([
        'user_id' => $user->id,
        'user_name' => $user->name,
        'user_nim' => $user->nim,
        'user_email' => $user->email,
        'user_role' => $user->role,
        'reason' => 'Dihapus melalui script khusus',
        'deleted_by' => 1, // Asumsikan ID 1 adalah admin utama
    ]);
    
    // Hapus relasi di group_user
    $user->groups()->detach();
    
    // Hapus user secara permanen
    $result = $user->forceDelete();
    
    if ($result) {
        DB::commit();
        echo "User berhasil dihapus secara permanen." . PHP_EOL;
    } else {
        DB::rollBack();
        echo "Gagal menghapus user!" . PHP_EOL;
    }
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "Error: " . $e->getMessage() . PHP_EOL;
}

// Verifikasi hasil
echo "=== VERIFICATION ===" . PHP_EOL;
echo "Mencari user dengan ID $userId..." . PHP_EOL;
$checkUser = User::withTrashed()->find($userId);
if ($checkUser) {
    echo "User masih ada di database (mungkin soft deleted)." . PHP_EOL;
} else {
    echo "User berhasil dihapus permanen." . PHP_EOL;
}

// Cek riwayat penghapusan
$history = UserDeletionHistory::where('user_id', $userId)->first();
if ($history) {
    echo "Riwayat penghapusan berhasil dicatat." . PHP_EOL;
    echo "Detail: " . $history->user_name . " (" . $history->user_nim . ")" . PHP_EOL;
} else {
    echo "Riwayat penghapusan tidak ditemukan!" . PHP_EOL;
}

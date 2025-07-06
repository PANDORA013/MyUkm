<?php

/**
 * Template Bootstrap untuk Script Laravel 11
 * 
 * Gunakan template ini untuk membuat script PHP yang mengakses Laravel models dan services.
 * Copy-paste bagian bootstrap di bawah ini ke script Anda.
 */

require_once __DIR__ . '/../../vendor/autoload.php';

// Bootstrap Laravel 11
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Sekarang Anda bisa menggunakan semua fitur Laravel:
// use App\Models\User;
// use App\Models\Group;
// use Illuminate\Support\Facades\DB;
// dll.

echo "Laravel 11 bootstrap template berhasil!" . PHP_EOL;
echo "Anda bisa hapus file ini setelah memahami cara penggunaannya." . PHP_EOL;

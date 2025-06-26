<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UkmController;
use App\Http\Controllers\AdminWebsiteController;
use App\Http\Controllers\AdminGrupController;

// Root route
Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();
        if ($user->role === 'admin_website') {
            return redirect('/admin/dashboard');
        }
        if ($user->role === 'admin_grup') {
            return redirect('/grup/dashboard');
        }
        return redirect()->route('ukm.index');
    }
    return redirect()->route('login');
});

// Auth routes
Route::get('register', [AuthController::class, 'showRegister'])->name('register');
Route::post('register', [AuthController::class, 'register']);
Route::get('login', [AuthController::class, 'showLogin'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

// Protected routes
Route::middleware(['auth'])->group(function () {
    // Chat Functionality (with group membership check)
    Route::middleware(['auth', 'role:member'])->group(function () {
        Route::post('/chat/send', [ChatController::class, 'sendChat'])->name('chat.send');
        Route::post('/chat/logout', [ChatController::class, 'logoutGroup'])->name('chat.logout');
        Route::get('/chat/unread-count', [ChatController::class, 'getUnreadCount'])->name('chat.unread-count');
    });

    // Profile Management
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');
    Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.updatePhoto');

    // Home alias and UKM routes
    Route::get('/home', fn() => redirect()->route('ukm.index'))->name('home');
    Route::get('/ukm', [UkmController::class, 'index'])->name('ukm.index');
    Route::post('/ukm/join', [UkmController::class, 'join'])->name('ukm.join');
    Route::delete('/ukm/{code}/leave', [UkmController::class, 'leave'])->name('ukm.leave');
    Route::get('/ukm/{code}/chat', [UkmController::class, 'chat'])->name('ukm.chat');

    // Admin Website routes
    Route::prefix('admin')->middleware(['role:admin_website'])->group(function () {
        Route::get('/dashboard', [AdminWebsiteController::class, 'dashboard'])->name('admin.dashboard');
        Route::post('/ukm/tambah', [AdminWebsiteController::class, 'tambahUKM'])->name('admin.ukm.tambah');
        Route::delete('/ukm/hapus/{id}', [AdminWebsiteController::class, 'hapusUKM'])->name('admin.ukm.hapus');
        Route::get('/ukm/{id}/anggota', [AdminWebsiteController::class, 'lihatAnggota'])->name('admin.ukm.anggota');
        Route::get('/ukm/edit/{id}', [AdminWebsiteController::class, 'editUKM'])->name('admin.ukm.edit');
        Route::post('/ukm/update/{id}', [AdminWebsiteController::class, 'updateUKM'])->name('admin.ukm.update');
        Route::post('/user/jadikan-admin', [AdminWebsiteController::class, 'jadikanAdminGrup'])->name('admin.user.jadikan-admin');
        Route::post('/user/hapus-admin', [AdminWebsiteController::class, 'hapusAdminGrup'])->name('admin.user.hapus-admin');
        Route::post('/ukm/{ukmId}/keluarkan/{userId}', [AdminWebsiteController::class, 'keluarkanAnggota'])->name('admin.ukm.keluarkan');
        Route::get('/search-member', [AdminWebsiteController::class, 'searchMember'])->name('admin.search-member');
        Route::get('/member/{userId}/ukms', [AdminWebsiteController::class, 'showMemberUkms'])->name('admin.member-ukms');
    });

    // Admin Grup routes
    Route::prefix('grup')->middleware(['role:admin_grup'])->group(function () {
        Route::get('/dashboard', [AdminGrupController::class, 'dashboard'])->name('grup.dashboard');
        Route::get('/anggota', [AdminGrupController::class, 'lihatAnggota'])->name('grup.anggota');
        Route::post('/keluarkan/{id}', [AdminGrupController::class, 'keluarkanAnggota'])->name('grup.keluarkan');
        Route::post('/mute/{id}', [AdminGrupController::class, 'muteAnggota'])->name('grup.mute');
    });
});

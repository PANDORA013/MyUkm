<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UkmController;
use App\Http\Controllers\AdminWebsiteController;
use App\Http\Controllers\AdminGrupController;
use Illuminate\Support\Facades\Config;

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

    // Profile routes
    Route::delete('/profile/destroy', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admin Website routes
    Route::middleware(['auth', 'role:admin_website', 'verified'])->prefix('admin')->name('admin.')->group(function () {
        // Dashboard
        Route::get('/dashboard', [AdminWebsiteController::class, 'dashboard'])->name('dashboard');
        
        // Manajemen User
        Route::post('/users/{id}/make-admin', [AdminWebsiteController::class, 'jadikanAdminGrup'])
            ->name('users.make-admin')
            ->middleware('throttle:60,1');
            
        Route::post('/users/{id}/remove-admin', [AdminWebsiteController::class, 'hapusAdminGrup'])
            ->name('users.remove-admin')
            ->middleware('throttle:60,1');
            
        Route::delete('/users/{id}', [AdminWebsiteController::class, 'hapusAkun'])
            ->name('users.destroy')
            ->middleware('throttle:60,1');
        
        // Manajemen UKM
        Route::get('/ukm/{id}/anggota', [AdminWebsiteController::class, 'lihatAnggota'])
            ->name('ukm.anggota')
            ->whereNumber('id');
            
        Route::get('/ukm/edit/{id}', [AdminWebsiteController::class, 'editUKM'])
            ->name('ukm.edit')
            ->whereNumber('id');
            
        Route::post('/ukm/update/{id}', [AdminWebsiteController::class, 'updateUKM'])
            ->name('ukm.update')
            ->whereNumber('id');
            
        Route::post('/ukm/{ukmId}/keluarkan/{userId}', [AdminWebsiteController::class, 'keluarkanAnggota'])
            ->name('ukm.keluarkan')
            ->whereNumber(['ukmId', 'userId']);
        
        // Pencarian
        Route::get('/search-member', [AdminWebsiteController::class, 'searchMember'])
            ->name('search-member');
            
        Route::get('/member/{userId}/ukms', [AdminWebsiteController::class, 'showMemberUkms'])
            ->name('member-ukms')
            ->whereNumber('userId');
    });

    // Admin Website Routes
    Route::prefix('admin')->middleware(['auth', 'role:admin_website'])->group(function () {
        Route::get('/dashboard', [AdminWebsiteController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/members', [AdminWebsiteController::class, 'members'])->name('admin.members');
        Route::get('/ukms', [AdminWebsiteController::class, 'ukms'])->name('admin.ukms');
        Route::get('/ukm-members/{ukm}', [AdminWebsiteController::class, 'ukmMembers'])->name('admin.ukm.members');
        Route::get('/search-member', [AdminWebsiteController::class, 'searchMember'])->name('admin.member.search');
        
        // User Deletion History Routes
        Route::prefix('user-deletions')->name('admin.user-deletions.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\UserDeletionHistoryController::class, 'index'])->name('index');
            Route::get('/{userDeletionHistory}', [\App\Http\Controllers\Admin\UserDeletionHistoryController::class, 'show'])->name('show');
        });
    });

    // Admin Grup routes
    Route::prefix('grup')->middleware(['role:admin_grup'])->group(function () {
        Route::get('/dashboard', [AdminGrupController::class, 'dashboard'])->name('grup.dashboard');
        Route::get('/anggota', [AdminGrupController::class, 'lihatAnggota'])->name('grup.anggota');
        Route::post('/keluarkan/{id}', [AdminGrupController::class, 'keluarkanAnggota'])->name('grup.keluarkan');
        Route::post('/mute/{id}', [AdminGrupController::class, 'muteAnggota'])->name('grup.mute');
    });

    // Pusher Test Routes
    Route::get('/pusher-test', function () {
        return view('pusher-test');
    })->name('pusher.test');

    // Test broadcast route
    Route::post('/broadcasting/test', function () {
        event(new App\Events\TestEvent([
            'message' => 'Hello from the server!',
            'time' => now()->toDateTimeString()
        ]));
        return response()->json(['status' => 'Message sent!']);
    })->name('broadcast.test');
});

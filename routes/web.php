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
use App\Http\Controllers\GroupAdminController;

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
        // Untuk user biasa (role: member, anggota, dll), arahkan ke home/ukm.index
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
Route::get('logout', [AuthController::class, 'logout'])->name('logout.get');

// CSRF token refresh route - accessible without auth but requires web middleware for session
Route::middleware(['web'])->get('/csrf-refresh', function () {
    return response()->json(['token' => csrf_token()]);
})->name('csrf.refresh');

// Protected routes
Route::middleware(['auth', 'ensure.role'])->group(function () {
    // Home alias
    Route::get('/home', fn() => redirect()->route('ukm.index'))->name('home');
    
    // UKM routes - accessible by all authenticated users (member, admin_grup, admin_website)
    Route::get('/ukm', [UkmController::class, 'index'])->name('ukm.index');
    Route::get('/ukm/{code}', [UkmController::class, 'show'])->name('ukm.show');
    Route::post('/ukm/join', [UkmController::class, 'join'])->name('ukm.join');
    Route::delete('/ukm/{code}/leave', [UkmController::class, 'leave'])->name('ukm.leave');
    Route::get('/ukm/{code}/chat', [UkmController::class, 'chat'])->name('ukm.chat');
    Route::get('/ukm/{code}/messages', [ChatController::class, 'getMessages'])->name('ukm.messages');
    Route::post('/ukm/{code}/messages', [ChatController::class, 'sendMessage'])->name('ukm.send-message');
    
    // Chat Functionality (with group membership check) - accessible by all authenticated users
    Route::post('/chat/send', [ChatController::class, 'sendChat'])->name('chat.send');
    Route::post('/chat/logout', [ChatController::class, 'logoutGroup'])->name('chat.logout');
    Route::get('/chat/unread-count', [ChatController::class, 'getUnreadCount'])->name('chat.unread-count');
    Route::get('/chat/messages', [ChatController::class, 'getMessagesAjax'])->name('chat.messages');
    Route::post('/chat/typing', [ChatController::class, 'typing'])->name('chat.typing');
    Route::post('/chat/join', [ChatController::class, 'joinGroup'])->name('chat.join');
    
    // Online status routes - untuk sinkronisasi status online anggota UKM
    Route::get('/chat/online-members', [ChatController::class, 'getOnlineMembers'])->name('chat.online-members');
    Route::post('/chat/update-online-status', [ChatController::class, 'updateOnlineStatus'])->name('chat.update-online-status');
    
    // Profile Management for regular users - accessible by all authenticated users
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');
    Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.updatePhoto');
    Route::delete('/profile/destroy', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Group routes that tests are looking for
    Route::post('/group/join', [UkmController::class, 'join'])->name('group.join');
    Route::post('/group/leave', [UkmController::class, 'leave'])->name('group.leave');

    // Admin Website routes
    Route::middleware(['role:admin_website'])->prefix('admin')->name('admin.')->group(function () {
        // Dashboard
        Route::get('/dashboard', [AdminWebsiteController::class, 'dashboard'])->name('dashboard');
        
        // Manajemen User
        Route::post('/users/{id}/make-admin', [AdminWebsiteController::class, 'jadikanAdminGrup'])
            ->name('users.make-admin')
            ->middleware('throttle:60,1');
            
        Route::post('/users/{id}/remove-admin', [AdminWebsiteController::class, 'hapusAdminGrup'])
            ->name('users.remove-admin')
            ->middleware('throttle:60,1');
            
        // Promosi/demosi admin per grup
        Route::post('/users/{id}/promote-in-group', [AdminWebsiteController::class, 'promoteToAdminInGroup'])
            ->name('users.promote-in-group')
            ->middleware('throttle:60,1');
            
        Route::post('/users/{id}/demote-from-group', [AdminWebsiteController::class, 'demoteFromAdminInGroup'])
            ->name('users.demote-from-group')
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
        
        // Admin website profile management
        Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
        Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');
        Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.updatePhoto');
        
        // Members management
        Route::get('/members', [AdminWebsiteController::class, 'members'])->name('members');
        Route::get('/member/{id}', [AdminWebsiteController::class, 'showMember'])->name('member.show');
        Route::get('/member/{id}/edit', [AdminWebsiteController::class, 'editMember'])->name('member.edit');
        Route::put('/member/{id}', [AdminWebsiteController::class, 'updateMember'])->name('member.update');
        
        // UKM management
        Route::get('/ukms', [AdminWebsiteController::class, 'ukms'])->name('ukms');
        Route::post('/ukms', [AdminWebsiteController::class, 'tambahUKM'])->name('tambah-ukm');
        Route::delete('/ukm/{id}', [AdminWebsiteController::class, 'hapusUKM'])->name('hapus-ukm');
        Route::delete('/member/{id}', [AdminWebsiteController::class, 'hapusAkun'])->name('hapus-member');
        Route::get('/ukm', [AdminWebsiteController::class, 'ukms'])->name('ukm.index'); // Add this route for tests
        Route::get('/ukm-members/{ukm}', [AdminWebsiteController::class, 'ukmMembers'])->name('ukm.members');
        
        // Daftar Admin Grup UKM
        Route::get('/users/admins', [AdminWebsiteController::class, 'adminGroupUsers'])->name('users.admins');
        
        // Daftar Pengguna Aktif Bulan Ini
        Route::get('/users/active', [AdminWebsiteController::class, 'activeUsers'])->name('users.active');
        
        // Daftar Pengguna Baru Bulan Ini
        Route::get('/users/new', [AdminWebsiteController::class, 'newUsers'])->name('users.new');
        
        // Rata-rata anggota per UKM (grafik)
        Route::get('/ukms/average', [AdminWebsiteController::class, 'averageMembers'])->name('ukms.average');
        // Detail aktivitas UKM tertentu
        Route::get('/ukms/activity/{ukmId}', [AdminWebsiteController::class, 'ukmActivityDetail'])->name('ukms.activity');
        // Detail aktivitas UKM tertentu
        Route::get('/ukms/{ukm}/activity', [AdminWebsiteController::class, 'ukmActivityDetail'])->name('ukms.activity');
        
        // Additional admin functions for absolute control
        Route::get('/statistics', [AdminWebsiteController::class, 'getStatistics'])->name('statistics');
        Route::get('/riwayat-penghapusan', [AdminWebsiteController::class, 'riwayatPenghapusan'])->name('riwayat-penghapusan');
        Route::post('/users/{id}/make-global-admin', [AdminWebsiteController::class, 'makeGlobalAdmin'])->name('users.make-global-admin');
        Route::post('/users/{id}/remove-global-admin', [AdminWebsiteController::class, 'removeGlobalAdmin'])->name('users.remove-global-admin');
        Route::delete('/users/{id}/force-delete', [AdminWebsiteController::class, 'forceDeleteUser'])->name('users.force-delete');
        
        // Resourceful routes
        Route::resource('users', \App\Http\Controllers\Admin\UsersController::class, [
            'as' => 'admin',
            'parameters' => ['users' => 'id']
        ]);
        Route::resource('groups', \App\Http\Controllers\Admin\GroupsController::class, [
            'as' => 'admin',
            'parameters' => ['groups' => 'id']
        ]);
        
        // User Deletion History Routes
        Route::prefix('user-deletions')->name('user-deletions.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\UserDeletionHistoryController::class, 'index'])->name('index');
            Route::get('/{userDeletionHistory}', [\App\Http\Controllers\Admin\UserDeletionHistoryController::class, 'show'])->name('show');
        });
    });

    // Admin Grup routes
    Route::middleware(['role:admin_grup'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/groups/{id}/manage', [AdminGrupController::class, 'manageGroup'])->name('groups.manage');
        Route::post('/groups/{id}/remove-member/{userId}', [AdminGrupController::class, 'removeMember'])->name('groups.remove-member');
        Route::post('/groups/{id}/mute-member/{userId}', [AdminGrupController::class, 'muteMember'])->name('groups.mute-member');
    });
    
    Route::middleware(['role:admin_grup'])->prefix('grup')->name('grup.')->group(function () {
        // Dashboard and index routes
        Route::get('/', [AdminGrupController::class, 'index'])->name('index');
        Route::get('/dashboard', [AdminGrupController::class, 'dashboard'])->name('dashboard');
        Route::get('/anggota', [AdminGrupController::class, 'lihatAnggota'])->name('anggota');
        Route::post('/keluarkan/{id}', [AdminGrupController::class, 'keluarkanAnggota'])->name('keluarkan');
        Route::post('/mute/{id}', [AdminGrupController::class, 'muteAnggota'])->name('mute');
        Route::post('/update-description', [AdminGrupController::class, 'updateDescription'])->name('update-description');
        
        // Admin grup profile management
        Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
        Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');
        Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.updatePhoto');
    });

    // Group Admin routes - privilege admin per grup
    Route::middleware(['group.admin'])->prefix('grup/{code}/admin')->name('group.admin.')->group(function () {
        Route::get('/dashboard', [GroupAdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/members', [GroupAdminController::class, 'members'])->name('members');
        Route::post('/promote', [GroupAdminController::class, 'promoteToAdmin'])->name('promote');
        Route::post('/demote', [GroupAdminController::class, 'demoteToMember'])->name('demote');
        Route::delete('/remove-member', [GroupAdminController::class, 'removeMember'])->name('remove-member');
        Route::put('/settings', [GroupAdminController::class, 'updateSettings'])->name('settings');
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

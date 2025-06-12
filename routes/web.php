<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\UkmController;
use App\Http\Controllers\ProfileController;

// Auth routes
Route::get('register', [AuthController::class, 'showRegister'])->name('register');
Route::post('register', [AuthController::class, 'register']);
Route::get('login', [AuthController::class, 'showLogin'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

// Protected routes
Route::middleware(['auth'])->group(function () {
    // UKM Management
    Route::get('/', [UkmController::class, 'index'])->name('ukm.index');
    Route::post('/ukm/join', [UkmController::class, 'join'])->name('ukm.join');
    Route::delete('/ukm/{code}/leave', [UkmController::class, 'leave'])->name('ukm.leave');
    Route::get('/ukm/{code}/chat', [ChatController::class, 'showChat'])->name('ukm.chat');
    
    // Chat Functionality
    Route::post('/chat/send', [ChatController::class, 'sendChat'])->name('chat.send');
    Route::post('/chat/logout', [ChatController::class, 'logoutGroup'])->name('chat.logout');
    Route::post('/chat/mark-read', [ChatController::class, 'markRead'])->name('chat.mark-read');
    Route::get('/chat/unread-count', [ChatController::class, 'getUnreadCount'])->name('chat.unread-count');

    // Profile Management
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');
    Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.updatePhoto');
});

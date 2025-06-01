<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\UkmController;

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
});

<?php

use App\Http\Controllers\UkmController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/join-ukm', [UkmController::class, 'showJoinForm'])->name('ukms.join.form');
    Route::post('/join-ukm', [UkmController::class, 'join'])->name('ukms.join');

    Route::get('/admin/users', [App\Http\Controllers\AdminController::class, 'listUsers'])
        ->name('admin.users');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

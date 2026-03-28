<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;

// ─── Guest routes (redirect to dashboard if already logged in) ────────────────
Route::middleware(['guest_custom'])->group(function () {
    Route::get('/',         [AuthController::class, 'showLogin'])->name('login');
    Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',   [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register',[AuthController::class, 'register'])->name('register.post');
});

// ─── AJAX: check email ────────────────────────────────────────────────────────
Route::post('/check-email', [AuthController::class, 'checkEmail'])->name('check.email');

// ─── Auth routes (must be logged in) ─────────────────────────────────────────
Route::middleware(['auth_custom'])->group(function () {
    Route::get('/dashboard',          [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout',            [AuthController::class, 'logout'])->name('logout');

    // Users CRUD
    Route::get('/users',              [UserController::class, 'index'])->name('users.index');
    Route::get('/users/data',         [UserController::class, 'ajaxData'])->name('users.data');
    Route::post('/users',             [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{id}/edit',    [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{id}',         [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}',      [UserController::class, 'destroy'])->name('users.destroy');
});

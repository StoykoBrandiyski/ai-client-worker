<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;

// Guest Routes

    Route::get('/register', [UserController::class, 'create'])->name('register');
    Route::post('/users', [UserController::class, 'store'])->name('users');
    
    Route::get('/login', [UserController::class, 'login'])->name('login');
    Route::post('/users/authenticate', [UserController::class, 'authenticate']);

// Authenticated Routes 
Route::middleware('auth')->group(function () {
    Route::post('/logout', [UserController::class, 'logout'])->name('logout');
    
    Route::get('/dashboard', [DashboardController::class, 'show'])->name('show');

    Route::get('/editUser', [UserController::class, 'editPage'])->name('editUser');
    Route::post('/storeEditUser', [UserController::class, 'storeEditUser'])->name('storeEditUser');

    // Dashboard Home
    Route::get('/dashboard', function () {
        return view('welcome'); // Create a simple dashboard blade view
    })->name('dashboard');
});
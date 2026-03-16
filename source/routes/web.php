<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PromptTemplateController;

// Guest Routes

    Route::get('/register', [UserController::class, 'create'])->name('register');
    Route::post('/users', [UserController::class, 'store'])->name('users');
    
    Route::get('/login', [UserController::class, 'login'])->name('login');
    Route::post('/users/authenticate', [UserController::class, 'authenticate'])->name('authenticate');

// Authenticated Routes 
Route::middleware('auth')->group(function () {
    Route::post('/logout', [UserController::class, 'logout'])->name('logout');
    
    Route::get('/dashboard', [DashboardController::class, 'show'])->name('show');

    Route::get('/editUser', [UserController::class, 'editPage'])->name('editUser');
    Route::post('/storeEditUser', [UserController::class, 'storeEditUser'])->name('storeEditUser');
    
    // Group
    Route::get('/groups', [GroupController::class, 'getAll']);
    Route::get('/groups/{id}', [GroupController::class, 'getById'])->where('id', '[0-9]+');
    Route::get('/storeGroup', [GroupController::class, 'storeGroup']);
    Route::post('/groups', [GroupController::class, 'store']);
    Route::delete('/groups/{id}', [GroupController::class, 'destroy']);

    Route::get('/prompts/create', [PromptTemplateController::class, 'create']);
    Route::post('/prompts', [PromptTemplateController::class, 'store']);

    Route::get('/tasks', [TaskController::class, 'getList']);
    Route::get('/tasks/create', [TaskController::class, 'createTasks']);
    Route::post('/tasks', [TaskController::class, 'store']);
    Route::post('/tasks/child', [TaskController::class, 'storeChild']);
    Route::get('/tasks/{id}', [TaskController::class, 'editTaskId']);
    Route::get('/tasks/{id}/download', [TaskController::class, 'download']);
    Route::get('/groups/{id}/tasks', [GroupController::class, 'getListByGroupId']);
    
    // Dashboard Home
    Route::get('/dashboard', function () {
        return view('welcome'); // Create a simple dashboard blade view
    })->name('dashboard');
});
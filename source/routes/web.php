<?php

use App\Http\Controllers\EngineController;
use App\Http\Controllers\EngineModelController;
use App\Http\Controllers\ProcessController;
use App\Http\Controllers\ProcessLogController;
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

    Route::get('/process-logs', [ProcessLogController::class, 'index'])->name('process-logs.index');
    Route::get('/templates/{template}', function (\App\Models\PromptTemplate $template) {
        return response()->json([
            'content' => $template->content
        ]);
    });

    Route::get('/task-images/{filename}', function ($filename) {

        if (!Storage::disk('public')->exists('task-images/' . $filename)) {
            abort(404);
        }

        return Storage::disk('public')->response('task-images/' . $filename);
    })->name('image.show');

    Route::get('/tasks', [TaskController::class, 'getList']);
    Route::get('/tasks/create', [TaskController::class, 'createTasks'])->name('tasks.create');
    Route::post('/tasks', [TaskController::class, 'store']);
    Route::post('/tasks/child', [TaskController::class, 'storeChild']);
    Route::get('/tasks/{id}', [TaskController::class, 'editTaskId']);
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
    Route::get('/tasks/{id}/download', [TaskController::class, 'download']);
    Route::get('/groups/{id}/tasks', [GroupController::class, 'getListByGroupId']);

    // Engine
    Route::get('/engines', [EngineController::class, 'getAll']);
    Route::get('/engines/create', [EngineController::class, 'create']); // Form
    Route::post('/engines', [EngineController::class, 'store']);
    Route::get('/engines/{id}', [EngineController::class, 'getById']);
    Route::delete('/engines', [EngineController::class, 'destroy']);

    // Engine Models
    // Page Rendering Routes
    Route::get('/engine/models/create', [EngineModelController::class, 'create']);
    Route::get('/engine/models/edit/{id}', [EngineModelController::class, 'edit']);

    Route::get('/engine/models', [EngineModelController::class, 'getList']);
    Route::post('/engine/models', [EngineModelController::class, 'store']);
    Route::get('/engine/models/{id}', [EngineModelController::class, 'getById']);
    Route::delete('/engine/models', [EngineModelController::class, 'destroy']);

    // Processes
    Route::get('/processes', [ProcessController::class, 'getAll']);
    Route::get('/processes/create', [ProcessController::class, 'create']);
    Route::get('/processes/edit/{id}', [ProcessController::class, 'edit']);
    Route::post('/processes', [ProcessController::class, 'store']);
    Route::get('/processes/{id}', [ProcessController::class, 'getById']);
    Route::delete('/processes', [ProcessController::class, 'destroy']);

    // Dashboard Home
    Route::get('/dashboard', function () {
        return view('welcome'); // Create a simple dashboard blade view
    })->name('dashboard');
});

<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\UserController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->middleware('auth');

Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout']);
Route::get('/users/create', [UserController::class, 'create'])
    ->middleware(['auth', 'can:create,' . User::class])
    ->name('users.create');

Route::post('/users', [UserController::class, 'store'])
    ->middleware('auth')
    ->name('users.store');

    Route::middleware('auth')->group(function () {
    Route::get('/projects', [ProjectController::class, 'index'])
        ->name('projects.index');

    Route::post('/projects', [ProjectController::class, 'store'])
        ->name('projects.store');
});
Route::get('/projects/create', [ProjectController::class, 'create'])
    ->name('projects.create');
Route::get('/projects/{project}', [ProjectController::class, 'show'])
    ->name('projects.show');

    Route::get('/projects/{project}/edit', [ProjectController::class, 'edit'])
    ->name('projects.edit');

Route::put('/projects/{project}', [ProjectController::class, 'update'])
    ->name('projects.update');
    Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])
    ->name('projects.destroy');
    
Route::put('/projects/{project}/members', [ProjectController::class, 'updateMembers'])
    ->name('projects.members.update');
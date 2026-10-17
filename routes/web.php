<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\UserController;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/projects')->middleware('auth');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:5,1');
});
Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/users/create', [UserController::class, 'create'])
        ->middleware('can:create,'.User::class)
        ->name('users.create');

    Route::post('/users', [UserController::class, 'store'])
        ->name('users.store');

    Route::resource('projects', ProjectController::class)
        ->middlewareFor('index', 'can:viewAny,'.Project::class)
        ->middlewareFor('create', 'can:create,'.Project::class)
        ->middlewareFor('show', 'can:view,project')
        ->middlewareFor('edit', 'can:update,project')
        ->middlewareFor('destroy', 'can:delete,project');

    Route::put('/projects/{project}/members', [ProjectController::class, 'updateMembers'])
        ->name('projects.members.update');
    Route::get('/projects/{project}/tickets/create', [TicketController::class, 'create'])
        ->middleware('can:create,App\Models\Ticket,project')
        ->name('tickets.create');

    Route::post('/projects/{project}/tickets', [TicketController::class, 'store'])
        ->name('tickets.store');

    Route::get('/tickets/{ticket}/edit', [TicketController::class, 'edit'])
        ->middleware('can:update,ticket')
        ->name('tickets.edit');

    Route::put('/tickets/{ticket}', [TicketController::class, 'update'])
        ->name('tickets.update');

    Route::delete('/tickets/{ticket}', [TicketController::class, 'destroy'])
        ->middleware('can:delete,ticket')
        ->name('tickets.destroy');
    Route::patch('/tickets/{ticket}/status', [TicketController::class, 'updateStatus'])
        ->name('tickets.status.update');
    Route::get('/my-tickets', [TicketController::class, 'mine'])->name('tickets.mine');

    Route::post('/tickets/{ticket}/comments', [CommentController::class, 'store'])
        ->name('comments.store');

    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])
        ->middleware('can:delete,comment')
        ->name('comments.destroy');
    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])
        ->middleware('can:view,ticket')
        ->name('tickets.show');

    Route::get('/notifications', [NotificationController::class, 'index'])
        ->name('notifications.index');

    Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])
        ->name('notifications.read-all');

    Route::post('/notifications/{id}/read', [NotificationController::class, 'read'])
        ->name('notifications.read');
});

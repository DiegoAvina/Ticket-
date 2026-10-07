<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\PriorityController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\MicrosoftLoginController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('tickets')->name('tickets.')->group(function () {
        Route::get('/', [TicketController::class, 'index'])->name('index');
        Route::get('/crear', [TicketController::class, 'create'])->name('create');
        Route::post('/', [TicketController::class, 'store'])->name('store');
        Route::get('/{ticket}', [TicketController::class, 'show'])->name('show');
        Route::post('/{ticket}/asignar', [TicketController::class, 'assign'])->name('assign');
        Route::post('/{ticket}/estado', [TicketController::class, 'changeStatus'])->name('status');
        Route::post('/{ticket}/comentarios', [TicketController::class, 'addMessage'])->name('messages');
        Route::post('/{ticket}/resolver', [TicketController::class, 'resolve'])->name('resolve');
        Route::post('/{ticket}/cerrar', [TicketController::class, 'close'])->name('close');
        Route::post('/{ticket}/reabrir', [TicketController::class, 'reopen'])->name('reopen');
        Route::post('/{ticket}/escalar', [TicketController::class, 'escalate'])->name('escalate');
        Route::get('/{ticket}/adjuntos/{attachment}', [TicketController::class, 'downloadAttachment'])->name('attachments.download');
        Route::patch('/{ticket}', [TicketController::class, 'update'])->name('update');
        Route::delete('/{ticket}', [TicketController::class, 'destroy'])->name('destroy');
    });

    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', UserController::class)->only(['index', 'edit', 'update']);
        Route::resource('departments', DepartmentController::class)->except(['show']);
        Route::resource('services', ServiceController::class)->except(['show']);
        Route::resource('categories', CategoryController::class)->except(['show']);
        Route::resource('priorities', PriorityController::class)->except(['show']);
    });

    Route::get('/auth/microsoft/redirect', [
        MicrosoftLoginController::class,
        'redirect'
    ])->name('microsoft.redirect');

    Route::get('/auth/microsoft/callback', [
        MicrosoftLoginController::class,
        'callback'
    ])->name('microsoft.callback');
});

require __DIR__ . '/auth.php';

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\SubscriptionController;
use App\Http\Controllers\App\DashboardController;
use App\Http\Controllers\App\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes - App Panel (Default)
|--------------------------------------------------------------------------
|
| These routes are for the main application panel used by clients.
| This is the default panel that loads when users access the site.
|
*/

Route::middleware(['tenant'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('app.dashboard');

    Route::prefix('app')->name('app.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::resource('users', UserController::class);
    });
});

/*
|--------------------------------------------------------------------------
| Admin Panel Routes
|--------------------------------------------------------------------------
|
| These routes are for the admin panel used by super admins.
| All admin routes are prefixed with 'admin' and use public schema.
|
*/

Route::prefix('admin')->name('admin.')->middleware(['admin.auth'])->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

    // User Management
    Route::resource('users', AdminUserController::class);

    // Subscription Management
    Route::resource('subscriptions', SubscriptionController::class);

    // Client Schema Management
    Route::post('/clients/{client}/create-schema', [AdminController::class, 'createClientSchema'])->name('clients.create-schema');
});

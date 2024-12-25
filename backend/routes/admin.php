<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {
    // Guest routes
    Route::middleware('guest:admin')->group(function () {
        Route::get('login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('login', [AuthController::class, 'login'])->name('login.post');
    });

    // Protected routes
    Route::middleware('auth:admin')->group(function () {
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
        
        // Dashboard accessible by all admins
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // User management routes - accessible by super admin and admin
        Route::middleware('admin.roles:super_admin,admin')->group(function () {
            Route::get('users', [UserController::class, 'index'])->name('users.index');
            Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
            Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
            Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
            Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        });

        // Course management routes - accessible by super admin, admin, and tutor
        Route::middleware('admin.roles:super_admin,admin,tutor')->group(function () {
            // Add course management routes here
        });

        // Financial routes - accessible by super admin, admin, and finance
        Route::middleware('admin.roles:super_admin,admin,finance')->group(function () {
            // Add financial routes here
        });

        // Sales routes - accessible by super admin, admin, and sales
        Route::middleware('admin.roles:super_admin,admin,sales')->group(function () {
            // Add sales routes here
        });

        // Management routes - accessible by super admin, admin, and management
        Route::middleware('admin.roles:super_admin,admin,management')->group(function () {
            // Add management routes here
        });
    });
}); 
<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\BusinessController;
use App\Http\Controllers\Admin\ProfessionalController;
use App\Http\Controllers\Admin\CourseExamController;
use App\Http\Controllers\Admin\CourseExamParticipantController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductCategoryController;
use App\Http\Controllers\Admin\BrandController;
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
            Route::post('users/{user}/transactions', [UserController::class, 'createTransaction'])
                ->name('users.transactions.store');

            // Business management routes
            Route::resource('business-profiles', BusinessController::class)->except(['create', 'store', 'destroy'])->names([
                'index' => 'businesses.index',
                'show' => 'businesses.show',
                'update' => 'businesses.update',
            ])->parameters([
                'business-profiles' => 'business'
            ]);
            Route::post('business-profiles/{business}/toggle-status', [BusinessController::class, 'toggleStatus'])
                ->name('businesses.toggle-status');

            // Business Customers routes
            Route::get('business-profiles/{business}/customers', [BusinessController::class, 'customers'])
                ->name('businesses.customers.index');
            Route::get('business-profiles/{business}/customers/{customer}', [BusinessController::class, 'showCustomer'])
                ->name('businesses.customers.show');

            // Business Invoices routes
            Route::get('business-profiles/{business}/invoices', [BusinessController::class, 'invoices'])
                ->name('businesses.invoices.index');
            Route::get('business-profiles/{business}/invoices/{invoice}', [BusinessController::class, 'showInvoice'])
                ->name('businesses.invoices.show');
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

        // Professional routes
        Route::resource('professionals', ProfessionalController::class)->except(['create', 'store', 'destroy']);
        Route::post('professionals/{professional}/toggle-status', [ProfessionalController::class, 'toggleStatus'])
            ->name('professionals.toggle-status');

        // Course Exam routes
        Route::group(['prefix' => 'courses/{course}/exams', 'as' => 'courses.exams.'], function () {
            Route::get('/', [CourseExamController::class, 'index'])->name('index');
            Route::get('/create', [CourseExamController::class, 'create'])->name('create');
            Route::post('/', [CourseExamController::class, 'store'])->name('store');
            Route::get('/{exam}', [CourseExamController::class, 'show'])->name('show');
            Route::get('/{exam}/edit', [CourseExamController::class, 'edit'])->name('edit');
            Route::put('/{exam}', [CourseExamController::class, 'update'])->name('update');
            Route::delete('/{exam}', [CourseExamController::class, 'destroy'])->name('destroy');
            Route::post('/{exam}/status', [CourseExamController::class, 'updateStatus'])->name('update-status');

            // Add participants routes
            Route::group(['prefix' => '{exam}/participants', 'as' => 'participants.'], function () {
                Route::get('/', [CourseExamParticipantController::class, 'index'])->name('index');
                Route::post('/bulk-update', [CourseExamParticipantController::class, 'bulkUpdate'])->name('bulk-update');
                Route::post('/{participant}', [CourseExamParticipantController::class, 'update'])->name('update');
            });
        });

        // Product routes
        Route::resource('products', ProductController::class)->except(['destroy']);

        // Product Categories routes
        Route::resource('product-categories', ProductCategoryController::class)->except(['create', 'edit', 'destroy']);

        // Brand routes
        Route::resource('brands', BrandController::class)->except(['create', 'edit', 'destroy']);
    });
}); 
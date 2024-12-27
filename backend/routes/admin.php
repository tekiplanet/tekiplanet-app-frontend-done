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
use App\Http\Controllers\Admin\ProductFeatureController;
use App\Http\Controllers\Admin\ProductSpecificationController;
use App\Http\Controllers\Admin\ProductImageController;
use App\Http\Controllers\Admin\ShippingZoneController;
use App\Http\Controllers\Admin\ShippingMethodController;
use App\Http\Controllers\Admin\ShippingAddressController;
use App\Http\Controllers\Admin\ServiceCategoryController;
use App\Http\Controllers\Admin\ServiceController;
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

        // Product Features routes
        Route::post('products/{product}/features', [ProductFeatureController::class, 'store'])
            ->name('products.features.store');
        Route::delete('products/features/{feature}', [ProductFeatureController::class, 'destroy'])
            ->name('products.features.destroy');

        // Product Specifications routes
        Route::post('products/{product}/specifications', [ProductSpecificationController::class, 'store'])
            ->name('products.specifications.store');
        Route::delete('products/specifications/{specification}', [ProductSpecificationController::class, 'destroy'])
            ->name('products.specifications.destroy');

        // Product Images routes
        Route::post('products/{product}/images', [ProductImageController::class, 'store'])
            ->name('products.images.store');
        Route::put('products/images/{image}', [ProductImageController::class, 'update'])
            ->name('products.images.update');
        Route::post('products/images/{image}/set-primary', [ProductImageController::class, 'setPrimary'])
            ->name('products.images.set-primary');
        Route::delete('products/images/{image}', [ProductImageController::class, 'destroy'])
            ->name('products.images.destroy');

        // Product Categories routes
        Route::resource('product-categories', ProductCategoryController::class)->except(['create', 'edit', 'destroy'])->parameters([
            'product-categories' => 'category'
        ]);

        // Brand routes
        Route::resource('brands', BrandController::class)->except(['create', 'edit', 'destroy']);

        // Shipping Management
        Route::prefix('shipping')->name('shipping.')->group(function () {
            // Shipping Zones
            Route::get('/zones', [ShippingZoneController::class, 'index'])->name('zones.index');
            Route::post('/zones', [ShippingZoneController::class, 'store'])->name('zones.store');
            Route::put('/zones/{zone}', [ShippingZoneController::class, 'update'])->name('zones.update');
            Route::delete('/zones/{zone}', [ShippingZoneController::class, 'destroy'])->name('zones.destroy');

            // Shipping Methods
            Route::get('/methods', [ShippingMethodController::class, 'index'])->name('methods.index');
            Route::post('/methods', [ShippingMethodController::class, 'store'])->name('methods.store');
            Route::put('/methods/{method}', [ShippingMethodController::class, 'update'])->name('methods.update');
            Route::delete('/methods/{method}', [ShippingMethodController::class, 'destroy'])->name('methods.destroy');

            // Shipping Addresses
            Route::get('/addresses', [ShippingAddressController::class, 'index'])->name('addresses.index');
            Route::post('/addresses', [ShippingAddressController::class, 'store'])->name('addresses.store');
            Route::put('/addresses/{address}', [ShippingAddressController::class, 'update'])->name('addresses.update');
            Route::delete('/addresses/{address}', [ShippingAddressController::class, 'destroy'])->name('addresses.destroy');
        });

        // Service Categories
        Route::resource('service-categories', ServiceCategoryController::class);
        Route::post('service-categories/{serviceCategory}/toggle-featured', [ServiceCategoryController::class, 'toggleFeatured'])
            ->name('service-categories.toggle-featured');

        // Services
        Route::resource('services', ServiceController::class);
        Route::post('services/{service}/toggle-featured', [ServiceController::class, 'toggleFeatured'])
            ->name('services.toggle-featured');
    });
}); 
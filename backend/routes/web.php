<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\BusinessController;
use App\Http\Controllers\Admin\ProfessionalController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Broadcast::routes(['middleware' => ['auth:sanctum']]);

// Include admin routes
require __DIR__.'/admin.php';

Route::group(['prefix' => 'admin', 'middleware' => ['auth:admin']], function () {
    // Existing routes...
    Route::patch('/users/{user}/status', [UserController::class, 'updateStatus'])->name('admin.users.status');
    Route::post('/users/{user}/notify', [UserController::class, 'sendNotification'])->name('admin.users.notify');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('admin.users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');
    Route::resource('businesses', BusinessController::class)->except(['create', 'store', 'destroy']);
    Route::post('businesses/{business}/toggle-status', [BusinessController::class, 'toggleStatus'])
        ->name('businesses.toggle-status');
    Route::resource('professionals', ProfessionalController::class)->except(['create', 'store', 'destroy']);
    Route::post('professionals/{professional}/toggle-status', [ProfessionalController::class, 'toggleStatus'])
        ->name('professionals.toggle-status');
});

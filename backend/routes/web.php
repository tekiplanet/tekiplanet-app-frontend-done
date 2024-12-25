<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\BusinessController;
use App\Http\Controllers\Admin\ProfessionalController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\CourseModuleController;
use App\Http\Controllers\Admin\CourseLessonController;
use App\Http\Controllers\Admin\CourseTopicController;

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
    Route::resource('professionals', ProfessionalController::class)
        ->except(['create', 'store', 'destroy'])
        ->names([
            'index' => 'admin.professionals.index',
            'show' => 'admin.professionals.show',
            'edit' => 'admin.professionals.edit',
            'update' => 'admin.professionals.update',
        ]);
    Route::post('professionals/{professional}/toggle-status', [ProfessionalController::class, 'toggleStatus'])
        ->name('admin.professionals.toggle-status');
    Route::resource('courses', CourseController::class)
        ->names([
            'index' => 'admin.courses.index',
            'show' => 'admin.courses.show',
            'edit' => 'admin.courses.edit',
            'update' => 'admin.courses.update',
            'create' => 'admin.courses.create',
            'store' => 'admin.courses.store',
            'destroy' => 'admin.courses.destroy',
        ]);
    // Course Modules Routes
    Route::prefix('courses/{course}')->name('admin.courses.')->group(function () {
        Route::resource('modules', CourseModuleController::class)->except(['index', 'create', 'show']);
        
        // Lesson Routes - Flattened structure
        Route::post('modules/{module}/lessons', [CourseLessonController::class, 'store'])
            ->name('modules.lessons.store')
            ->where('course', '[0-9a-f-]+')
            ->where('module', '[0-9a-f-]+');

        Route::get('lessons/{lesson}/edit', [CourseLessonController::class, 'edit'])
            ->name('lessons.edit')
            ->where('course', '[0-9a-f-]+')
            ->where('lesson', '[0-9a-f-]+');

        Route::put('lessons/{lesson}', [CourseLessonController::class, 'update'])
            ->name('lessons.update')
            ->where('course', '[0-9a-f-]+')
            ->where('lesson', '[0-9a-f-]+');

        Route::delete('lessons/{lesson}', [CourseLessonController::class, 'destroy'])
            ->name('lessons.destroy')
            ->where('course', '[0-9a-f-]+')
            ->where('lesson', '[0-9a-f-]+');

        // Course Topics Routes
        Route::post('modules/{module}/topics', [CourseTopicController::class, 'store'])->name('modules.topics.store');
        Route::get('topics/{topic}/edit', [CourseTopicController::class, 'edit'])->name('topics.edit');
        Route::put('topics/{topic}', [CourseTopicController::class, 'update'])->name('topics.update');
        Route::delete('topics/{topic}', [CourseTopicController::class, 'destroy'])->name('topics.destroy');
    });
});

// Temporary debug route list
Route::get('/debug-routes', function () {
    $routes = collect(\Route::getRoutes())->map(function ($route) {
        return [
            'uri' => $route->uri(),
            'methods' => $route->methods(),
            'name' => $route->getName()
        ];
    });
    dd($routes->toArray());
});

// Temporary debug route
Route::get('/test-lesson-route/{course}/{module}', function ($course, $module) {
    return response()->json([
        'message' => 'Route is accessible',
        'course' => $course,
        'module' => $module
    ]);
})->where(['course' => '[0-9a-f-]+', 'module' => '[0-9a-f-]+']);

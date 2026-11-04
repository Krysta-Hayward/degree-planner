<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicProgramController;
use App\Http\Controllers\PublicCourseController;

// Home route
Route::get('/', function () {
    return view('welcome');
});

// Health Check
Route::get('/health', function () {
    DB::connection()->getPdo();

    return response()->json([
        'app' => 'up',
        'database' => 'up',
    ]);
});

// Auth routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Public routes
Route::get('/programs', [PublicProgramController::class, 'index'])
    ->name('public.programs');

Route::get('/courses', [PublicCourseController::class, 'index'])
    ->name('public.courses');

// Admin routes
require __DIR__.'/admin.php';

// Advisor routes
require __DIR__.'/advisor.php';

// Student Routes
require __DIR__.'/student.php';

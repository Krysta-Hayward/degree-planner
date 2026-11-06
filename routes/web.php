<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PublicCourseController;
use App\Http\Controllers\PublicPrereqController;
use App\Http\Controllers\PublicProgramController;
use App\Http\Controllers\PublicTermController;
use Illuminate\Support\Facades\Route;

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
Route::get('/programs/{program}', [PublicProgramController::class, 'show'])
    ->name('public.programs.show');

Route::get('/courses', [PublicCourseController::class, 'index'])
    ->name('public.courses');
Route::get('/courses/{course}', [PublicCourseController::class, 'show'])
    ->name('public.courses.show');

Route::get('/terms', [PublicTermController::class, 'index'])
    ->name('public.terms.index');
Route::get('/terms/{term}', [PublicTermController::class, 'show'])
    ->name('public.terms.show');

Route::get('/prerequisites', [PublicPrereqController::class, 'index'])
    ->name('public.prereqs');
Route::get('/prerequisites/{course}', [PublicPrereqController::class, 'show'])
    ->name('public.prereqs-details');

// Admin routes
require __DIR__.'/admin.php';

// Advisor routes
require __DIR__.'/advisor.php';

// Student Routes
require __DIR__.'/student.php';

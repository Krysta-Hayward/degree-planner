<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminDataController;
use App\Http\Middleware\AdvisorMiddleware;
use App\Http\Controllers\Advisor\DashboardController as AdvisorDashboardController;
use App\Http\Controllers\Advisor\StudentController as AdvisorStudentController;
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

// Admin routes
Route::get('/admin/data/{table?}', [AdminDataController::class, 'index'])->name('admin.data');
Route::get('/admin', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

// Advisor routes
Route::middleware(['auth', AdvisorMiddleware::class])->group(function () {
    Route::get('/advisor', [AdvisorDashboardController::class, 'index'])->name('advisor.dashboard');
    Route::get('/advisor/students', [AdvisorStudentController::class, 'index'])->name('advisor.students.index');
});

// Student routes



<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminDataController;
use App\Http\Controllers\AdvisorDashboardController;
use App\Http\Controllers\AdvisorStudentController;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\AdvisorMiddleware;
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

// Admin routes
Route::middleware(['auth', AdminMiddleware::class])->group(function () {
    Route::get('/admin/data/{table?}', [AdminDataController::class, 'index'])->name('admin.data');
    Route::get('/admin', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
});

// Advisor Routes
require __DIR__.'/advisor.php';

// Student Routes
require __DIR__.'/student.php';





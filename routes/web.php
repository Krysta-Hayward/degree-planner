<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminDataController;
use App\Http\Controllers\Admin\AdminStudentController;
use App\Http\Controllers\Advisor\DashboardController as AdvisorDashboardController;
use App\Http\Controllers\Advisor\StudentController as AdvisorStudentController;
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
require __DIR__ . '/student.php';

Route::middleware(['auth', AdminMiddleware::class])->group(function () {
    Route::get('/admin/data/{table?}', [AdminDataController::class, 'index'])->name('admin.data');
    Route::get('/admin', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/students', [AdminStudentController::class, 'index'])->name('admin.students');

    Route::put('/admin/students/{student}/advisor', [
        AdminStudentController::class,
        'updateAdvisor',
    ])->name('admin.students.advisor.update');
});

// Advisor routes
Route::middleware(['auth', AdvisorMiddleware::class])->group(function () {
    Route::get('/advisor', [AdvisorDashboardController::class, 'index'])->name('advisor.dashboard');
    Route::get('/advisor/students', [AdvisorStudentController::class, 'index'])->name('advisor.students.index');
});

// Student routes

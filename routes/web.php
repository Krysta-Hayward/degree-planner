<?php

use App\Http\Controllers\Admin\AdminAdvisorController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminDataController;
use App\Http\Controllers\Admin\AdminStudentController;
use App\Http\Controllers\Admin\AdminCourseController;
use App\Http\Controllers\Admin\AdminProgramController;
use App\Http\Controllers\Admin\AdminTermController;
use App\Http\Controllers\Admin\AdminPlanController;
use App\Http\Controllers\Admin\AdminPrereqController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\AdvisorDashboardController;
use App\Http\Controllers\AdvisorStudentController;
use App\Http\Controllers\AdvisorPlanReviewController;
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

    Route::get('/admin/advisors', [AdminAdvisorController::class, 'index'])->name('admin.advisors');

    Route::get('/admin/students', [AdminStudentController::class, 'index'])->name('admin.students');
    Route::put('/admin/students/{student}/advisor', [
        AdminStudentController::class,
        'updateAdvisor',
    ])->name('admin.students.advisor.update');

    Route::get('/admin/users', [AdminUserController::class, 'index'])
        ->name('admin.users');

    Route::get('/admin/courses', [AdminCourseController::class, 'index'])
        ->name('admin.courses');

    Route::get('/admin/programs', [AdminProgramController::class, 'index'])
        ->name('admin.programs');
    Route::get('/admin/programs/{programName}', [AdminProgramController::class, 'show'])
        ->name('admin.programs.show');

    Route::get('/admin/terms', [AdminTermController::class, 'index'])->name('admin.terms');

    Route::get('/admin/plans', [AdminPlanController::class, 'index'])
        ->name('admin.plans');

    Route::get('/admin/plans/{student}', [AdminPlanController::class, 'show'])
        ->name('admin.plans.show');
    
    Route::get('/admin/prereqs', [AdminPrereqController::class, 'index'])->name('admin.prereqs');
});

// Advisor routes
Route::middleware(['auth', AdvisorMiddleware::class])->group(function () {
    Route::get('/advisor', [AdvisorDashboardController::class, 'index'])
        ->name('advisor.dashboard');

    Route::get('/advisor/students', [AdvisorStudentController::class, 'index'])
        ->name('advisor.students.index');

    Route::get('/advisor/plans', [AdvisorPlanReviewController::class, 'index'])
        ->name('advisor.plans.index');
});

// Student Routes
require __DIR__.'/student.php';

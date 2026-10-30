<?php

use App\Http\Controllers\Admin\AdminAdvisorController;
use App\Http\Controllers\Admin\AdminCourseController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminDataController;
use App\Http\Controllers\Admin\AdminPlanController;
use App\Http\Controllers\Admin\AdminPrereqController;
use App\Http\Controllers\Admin\AdminProgramController;
use App\Http\Controllers\Admin\AdminStudentController;
use App\Http\Controllers\Admin\AdminTermController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Support\Facades\Route;

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





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

require __DIR__.'/student.php';

Route::middleware(['auth', AdminMiddleware::class])->group(function () {
    // Database view route
    Route::get('/admin/data/{table?}', [AdminDataController::class, 'index'])->name('admin.data');

    // Admin dashboard route
    Route::get('/admin', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

    // Advisor routes
    Route::get('/admin/advisors', [AdminAdvisorController::class, 'index'])->name('admin.advisors');

    // Student routes
    Route::get('/admin/students', [AdminStudentController::class, 'index'])->name('admin.students');
    Route::put('/admin/students/{student}/advisor', [
        AdminStudentController::class,
        'updateAdvisor',
    ])->name('admin.students.advisor.update');

    // User routes
    Route::get('/admin/users', [AdminUserController::class, 'index'])
        ->name('admin.users');

    // Courses routes
    Route::get('/admin/courses', [AdminCourseController::class, 'index'])
        ->name('admin.courses.index');
    Route::get('/admin/courses/create', [AdminCourseController::class, 'create'])
        ->name('admin.courses.create');
    Route::post('/admin/courses', [AdminCourseController::class, 'store'])
        ->name('admin.courses.store');
    Route::get('/admin/courses/{course}/edit', [AdminCourseController::class, 'edit'])
        ->name('admin.courses.edit');
    Route::put('/admin/courses/{course}', [AdminCourseController::class, 'update'])
        ->name('admin.courses.update');
    Route::delete('/admin/courses/{course}', [AdminCourseController::class, 'destroy'])
        ->name('admin.courses.destroy');

    // Programs routes
    Route::get('/admin/programs', [AdminProgramController::class, 'index'])
        ->name('admin.programs.index');
    Route::get('/admin/programs/create', [AdminProgramController::class, 'create'])
        ->name('admin.programs.create');
    Route::post('/admin/programs', [AdminProgramController::class, 'store'])
        ->name('admin.programs.store');
    Route::get('/admin/programs/{programName}/edit', [AdminProgramController::class, 'edit'])
        ->name('admin.programs.edit');
    Route::put('/admin/programs/{programName}', [AdminProgramController::class, 'update'])
        ->name('admin.programs.update');
    Route::get('/admin/programs/{programName}', [AdminProgramController::class, 'show'])
        ->name('admin.programs.details');

    // Terms routes
    Route::get('/admin/terms', [AdminTermController::class, 'index'])
        ->name('admin.terms.index');
    Route::get('/admin/terms/create', [AdminTermController::class, 'create'])
        ->name('admin.terms.create');
    Route::post('/admin/terms', [AdminTermController::class, 'store'])
        ->name('admin.terms.store');
    Route::get('/admin/terms/{term}/edit', [AdminTermController::class, 'edit'])
        ->name('admin.terms.edit');
    Route::put('/admin/terms/{term}', [AdminTermController::class, 'update'])
        ->name('admin.terms.update');
    Route::delete('/admin/terms/{term}', [AdminTermController::class, 'destroy'])
        ->name('admin.terms.destroy');

    // Plans routes
    Route::get('/admin/plans', [AdminPlanController::class, 'index'])
        ->name('admin.plans');
    Route::get('/admin/plans/{student}', [AdminPlanController::class, 'show'])
        ->name('admin.plans.show');

    // Prerequisites routes
    Route::get('/admin/prereqs', [AdminPrereqController::class, 'index'])->name('admin.prereqs');
    Route::get('/admin/prereqs/create', [AdminPrereqController::class, 'create'])->name('admin.prereqs.create');
    Route::post('/admin/prereqs', [AdminPrereqController::class, 'store'])->name('admin.prereqs.store');
    Route::get('/admin/prereqs/{prereq}/edit', [AdminPrereqController::class, 'edit'])->name('admin.prereqs.edit');
    Route::put('/admin/prereqs/{prereq}', [AdminPrereqController::class, 'update'])->name('admin.prereqs.update');
    Route::delete('/admin/prereqs/{prereq}', [AdminPrereqController::class, 'destroy'])->name('admin.prereqs.destroy');
});

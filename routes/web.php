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

    Route::get('/admin/advisors', [AdminAdvisorController::class, 'index'])->name('admin.advisors');

    Route::get('/admin/students', [AdminStudentController::class, 'index'])->name('admin.students');
    Route::put('/admin/students/{student}/advisor', [
        AdminStudentController::class,
        'updateAdvisor',
    ])->name('admin.students.advisor.update');

    Route::get('/admin/users', [AdminUserController::class, 'index'])
        ->name('admin.users');

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

    Route::get('/admin/programs', [AdminProgramController::class, 'index'])
        ->name('admin.programs');
    // create has to come before {programName} so "create" isn't read as a program name
    Route::get('/admin/programs/create', [AdminProgramController::class, 'create'])
        ->name('admin.programs.create');
    Route::post('/admin/programs', [AdminProgramController::class, 'store'])
        ->name('admin.programs.store');
    Route::get('/admin/programs/{programName}/edit', [AdminProgramController::class, 'edit'])
        ->name('admin.programs.edit');
    Route::put('/admin/programs/{programName}', [AdminProgramController::class, 'update'])
        ->name('admin.programs.update');
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
require __DIR__.'/advisor.php';

// Student Routes
require __DIR__.'/student.php';

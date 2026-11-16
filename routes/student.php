<?php

use App\Http\Controllers\StudentController;
use App\Http\Middleware\StudentMiddleware;
use Illuminate\Support\Facades\Route;

// Student pages. Every route only ever works on the logged in student's own data.
Route::middleware(['auth', StudentMiddleware::class])
    ->prefix('student')
    ->name('student.')
    ->group(function () {
        // Student dashboard
        Route::get('/', [StudentController::class, 'dashboard'])->name('dashboard');
        // Select student program (Drop Box)
        Route::post('/program', [StudentController::class, 'selectProgram'])->name('program.select');
        // Clear student program (Drop Box)
        Route::delete('/program', [StudentController::class, 'clearProgram'])->name('program.clear');
        // Add a course to program map
        Route::post('/plan', [StudentController::class, 'addToPlan'])->name('plan.add');
        // Removes course from program map
        Route::delete('/plan/{plan}', [StudentController::class, 'removeFromPlan'])->name('plan.remove');

        // Student enrollment
        Route::post('/enroll/{classroom}', [StudentController::class, 'enroll'])
            ->name('enroll');
        // Submits to program map
        Route::post('/plan/submit', [StudentController::class, 'submitPlan'])->name('plan.submit');
    });

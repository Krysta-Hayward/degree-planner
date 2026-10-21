<?php

use App\Http\Controllers\StudentController;
use App\Http\Middleware\StudentMiddleware;
use Illuminate\Support\Facades\Route;

// Student pages. Every route only ever works on the logged in student's own data.
Route::middleware(['auth', StudentMiddleware::class])
    ->prefix('student')
    ->name('student.')
    ->group(function () {
        Route::get('/', [StudentController::class, 'dashboard'])->name('dashboard');
        Route::post('/program', [StudentController::class, 'selectProgram'])->name('program.select');
        Route::post('/plan', [StudentController::class, 'addToPlan'])->name('plan.add');
        Route::delete('/plan/{plan}', [StudentController::class, 'removeFromPlan'])->name('plan.remove');
    });

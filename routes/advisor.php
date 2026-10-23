<?php

use App\Http\Controllers\AdvisorDashboardController;
use App\Http\Controllers\AdvisorStudentController;
use App\Http\Middleware\AdvisorMiddleware;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', AdvisorMiddleware::class])->group(function () {
    Route::get('/advisor', [AdvisorDashboardController::class, 'index'])->name('advisor.dashboard');
    Route::get('/advisor/students', [AdvisorStudentController::class, 'index'])->name('advisor.students.index');
});

<?php

use App\Http\Controllers\AdvisorDashboardController;
use App\Http\Controllers\AdvisorStudentController;
use App\Http\Controllers\AdvisorPlanReviewController;
use App\Http\Middleware\AdvisorMiddleware;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', AdvisorMiddleware::class])->group(function () {
    // Advisor dashboard
    Route::get('/advisor', [AdvisorDashboardController::class, 'index'])->name('advisor.dashboard');
    // Advisor list students
    Route::get('/advisor/students', [AdvisorStudentController::class, 'index'])->name('advisor.students.index');
    // Advisor students plans list
    Route::get('/advisor/plans', [AdvisorPlanReviewController::class, 'index'])->name('advisor.plans.index');
    // TODO: Advisor show one student plan
    // TODO: Advisor sets status to approve 
    // TODO: Advisor request-changes to student plan
   
});

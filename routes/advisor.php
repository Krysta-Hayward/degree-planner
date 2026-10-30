<?php

use App\Http\Controllers\AdvisorDashboardController;
use App\Http\Controllers\AdvisorPlanReviewController;
use App\Http\Controllers\AdvisorStudentController;
use App\Http\Middleware\AdvisorMiddleware;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', AdvisorMiddleware::class])->group(function () {
    // Advisor dashboard
    Route::get('/advisor', [AdvisorDashboardController::class, 'index'])->name('advisor.dashboard');

    // Advisor's list of students
    Route::get('/advisor/students', [AdvisorStudentController::class, 'index'])->name('advisor.students.index');

    // Program maps waiting for review
    Route::get('/advisor/plans', [AdvisorPlanReviewController::class, 'index'])->name('advisor.plans.index');

    // One student's program map
    Route::get('/advisor/plans/{programMap}', [AdvisorPlanReviewController::class, 'show'])->name('advisor.plans.show');

    // Approve a program map
    Route::post('/advisor/plans/{programMap}/approve', [AdvisorPlanReviewController::class, 'approve'])->name('advisor.plans.approve');

    // Send a program map back for changes
    Route::post('/advisor/plans/{programMap}/request-changes', [AdvisorPlanReviewController::class, 'requestChanges'])->name('advisor.plans.changes');
});
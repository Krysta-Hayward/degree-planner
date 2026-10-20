<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminDataController;
use App\Http\Controllers\Advisor;
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

// Admin routes
Route::get('/admin/data/{table?}', [AdminDataController::class, 'index'])->name('admin.data');
Route::get('/admin', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

// Advisor routes
Route::middleware(['auth', 'advisor'])
    ->prefix('advisor')
    ->name('advisor.')
    ->group(function (){
        Route::get('/', [Advisor\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/students', [Advisor\StudentController::class, 'index'])->name('students.index');
    });

// Student routes

// Temp route
if (app()->isLocal()) {
    Route::get('/dev/login-advisor', function () {
        auth()->login(\App\Models\User::where('email', 'advisor@example.com')->firstOrFail());
        return redirect()->route('advisor.dashboard');
    });
}

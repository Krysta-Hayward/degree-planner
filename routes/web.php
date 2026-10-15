<?php

use App\Http\Controllers\AdminDataController;
use App\Http\Controllers\AdminDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/health', function () {
    DB::connection()->getPdo();

    return response()->json([
        'app' => 'up',
        'database' => 'up',
    ]);
});

Route::get('/admin/data/{table?}', [AdminDataController::class, 'index'])->name('admin.data');

Route::get('/admin', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
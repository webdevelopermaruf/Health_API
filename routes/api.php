<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\SettingsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/departments', [DepartmentController::class, 'index']);
    Route::post('/add/department', [DepartmentController::class, 'store']);
    Route::post('/edit/department/{id}', [DepartmentController::class, 'update']);
    Route::post('/delete/department/{id}', [DepartmentController::class, 'destroy']);
});

Route::get('/settings', [SettingsController::class, 'index']);
Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

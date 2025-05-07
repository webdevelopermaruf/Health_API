<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\StaffController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/departments', [DepartmentController::class, 'index']);
    Route::get('/show/department/{id}', [DepartmentController::class, 'show']);
    Route::post('/add/department', [DepartmentController::class, 'store']);
    Route::post('/edit/department/{id}', [DepartmentController::class, 'update']);
    Route::post('/delete/department/{id}', [DepartmentController::class, 'destroy']);


    Route::get('/doctors', [DoctorController::class, 'index']);
    Route::get('/show/doctor/{id}', [DoctorController::class, 'show']);
    Route::post('/add/doctor', [DoctorController::class, 'store']);
    Route::post('/edit/doctor/{id}', [DoctorController::class, 'update']);
    Route::post('/delete/doctor/{id}', [DoctorController::class, 'destroy']);

    Route::get('/staffs', [StaffController::class, 'index']);
    Route::get('/show/staff/{id}', [StaffController::class, 'show']);
    Route::post('/add/staff', [StaffController::class, 'store']);
    Route::post('/edit/staff/{id}', [StaffController::class, 'update']);
    Route::post('/delete/staff/{id}', [StaffController::class, 'destroy']);
});

Route::get('/settings', [SettingsController::class, 'index']);
Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

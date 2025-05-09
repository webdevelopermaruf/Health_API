<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\SalaryStructureController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\StaffController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {

    // Department Route
    Route::get('/departments', [DepartmentController::class, 'index']);
    Route::get('/show/department/{id}', [DepartmentController::class, 'show']);
    Route::post('/add/department', [DepartmentController::class, 'store']);
    Route::post('/edit/department/{id}', [DepartmentController::class, 'update']);
    Route::post('/delete/department/{id}', [DepartmentController::class, 'destroy']);

    // Doctor Route
    Route::get('/doctors', [DoctorController::class, 'index']);
    Route::get('/show/doctor/{id}', [DoctorController::class, 'show']);
    Route::post('/add/doctor', [DoctorController::class, 'store']);
    Route::post('/edit/doctor/{id}', [DoctorController::class, 'update']);
    Route::post('/delete/doctor/{id}', [DoctorController::class, 'destroy']);

    // Staffs Route
    Route::get('/staffs', [StaffController::class, 'index']);
    Route::get('/show/staff/{id}', [StaffController::class, 'show']);
    Route::post('/add/staff', [StaffController::class, 'store']);
    Route::post('/edit/staff/{id}', [StaffController::class, 'update']);
    Route::post('/delete/staff/{id}', [StaffController::class, 'destroy']);


    Route::get('/services', [ServiceController::class, 'index']);
    Route::get('/show/service/{id}', [ServiceController::class, 'show']);
    Route::post('/add/service', [ServiceController::class, 'store']);
    Route::post('/edit/service/{id}', [ServiceController::class, 'update']);
    Route::post('/delete/service/{id}', [ServiceController::class, 'destroy']);


    // Salary-Structure Route
    Route::get('/salary-structures', [SalaryStructureController::class, 'index']);
    Route::post('/add/salary-structure', [SalaryStructureController::class, 'store']);
    Route::post('/edit/salary-structure/{id}', [SalaryStructureController::class, 'update']);
    Route::post('/delete/salary-structure/{id}', [SalaryStructureController::class, 'destroy']);

    // Payment-Method Route
    Route::get('/payment-methods', [PaymentMethodController::class, 'index']);
    Route::post('/add/payment-method', [PaymentMethodController::class, 'store']);
    Route::post('/edit/payment-method/{id}', [PaymentMethodController::class, 'update']);
    Route::post('/delete/payment-method/{id}', [PaymentMethodController::class, 'destroy']);


});

Route::get('/settings', [SettingsController::class, 'index']);
Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

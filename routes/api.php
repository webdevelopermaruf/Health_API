<?php

use App\Http\Controllers\AppointmentScheduleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BedController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\LabReportController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\SalaryStructureController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {

    // Department Route
    Route::get('/departments', [DepartmentController::class, 'index']);
    Route::get('/doctors/departments', [DepartmentController::class, 'doctorsDept']);
    Route::get('/show/department/{id}', [DepartmentController::class, 'show']);
    Route::post('/add/department', [DepartmentController::class, 'store']);
    Route::post('/edit/department/{id}', [DepartmentController::class, 'update']);
    Route::post('/delete/department/{id}', [DepartmentController::class, 'destroy']);

    // Users Route
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/update/permission/{id}', [UserController::class, 'updatePermission']);

    // Features Route
    Route::get('/features', [UserController::class, 'features']);

    // Doctors Route
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

    // Patients Route
    Route::get('/patients', [PatientController::class, 'index']);
    Route::get('/show/patient/{id}', [PatientController::class, 'show']);
    Route::get('/find/patient/{phone}', [PatientController::class, 'searchByPhone']);
    Route::post('/add/patient', [PatientController::class, 'store']);
    Route::post('/edit/patient/{id}', [PatientController::class, 'update']);
    Route::post('/delete/patient/{id}', [PatientController::class, 'destroy']);

    Route::get('/services', [ServiceController::class, 'index']);
    Route::get('/show/service/{id}', [ServiceController::class, 'show']);
    Route::post('/add/service', [ServiceController::class, 'store']);
    Route::post('/edit/service/{id}', [ServiceController::class, 'update']);
    Route::post('/delete/service/{id}', [ServiceController::class, 'destroy']);

    // Salary-Structure Route
    Route::get('/salary-structures', [SalaryStructureController::class, 'index']);
    Route::get('/show/salary-structure/{id}', [SalaryStructureController::class, 'show']);
    Route::post('/add/salary-structure', [SalaryStructureController::class, 'store']);
    Route::post('/edit/salary-structure/{id}', [SalaryStructureController::class, 'update']);
    Route::post('/delete/salary-structure/{id}', [SalaryStructureController::class, 'destroy']);

    // Payment-Method Route
    Route::get('/payment-methods', [PaymentMethodController::class, 'index']);
    Route::get('/show/payment-method/{id}', [PaymentMethodController::class, 'show']);
    Route::post('/add/payment-method', [PaymentMethodController::class, 'store']);
    Route::post('/edit/payment-method/{id}', [PaymentMethodController::class, 'update']);
    Route::post('/delete/payment-method/{id}', [PaymentMethodController::class, 'destroy']);

    // Appointment Scheduler
    Route::get('/appointment-scheduler', [AppointmentScheduleController::class, 'index']);
    Route::post('/update/appointment-scheduler', [AppointmentScheduleController::class, 'update']);

    // Billing Route
    Route::post('/create/bill', [BillingController::class, 'store']);
    Route::get('/billings', [BillingController::class, 'index']);
    Route::get('pending-appointments', [BillingController::class, 'pending_appointments']);


    // Lab Route
    Route::get('/lab-reports', [LabReportController::class, 'index']);
    Route::post('/upload/lab-report/{id}', [LabReportController::class, 'update']);
    Route::post('/publish/lab-report/{id}', [LabReportController::class, 'publish']);


    // Room Route
    Route::get('/floors', [RoomController::class, 'floors']);
    Route::get('/rooms', [RoomController::class, 'index']);
    Route::get('/rooms/types', [RoomController::class, 'types']);
    Route::get('/show/room/{id}', [RoomController::class, 'show']);
    Route::post('/add/room', [RoomController::class, 'store']);
    Route::post('/edit/room/{id}', [RoomController::class, 'update']);
    Route::post('/delete/room/{id}', [RoomController::class, 'destroy']);

    // bed Route
    Route::get('/beds', [BedController::class, 'index']);
    Route::get('/beds/types', [BedController::class, 'types']);
    Route::get('/show/bed/{id}', [BedController::class, 'show']);
    Route::post('/add/bed', [BedController::class, 'store']);
    Route::post('/edit/bed/{id}', [BedController::class, 'update']);
    Route::post('/delete/bed/{id}', [BedController::class, 'destroy']);


    Route::post('/update/app', [SettingsController::class, 'update']);
});

Route::get('/app', [SettingsController::class, 'index']);
Route::get('/billing/{billId}', [BillingController::class, 'show']);
Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

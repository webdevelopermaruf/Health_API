<?php

use App\Http\Controllers\AppointmentScheduleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BedController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\IndoorController;
use App\Http\Controllers\LabReportController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\PharmacyBillingController;
use App\Http\Controllers\PharmacyMedicinesController;
use App\Http\Controllers\PharmacySuppliersController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\SalaryStructureController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\UserController;
use App\Models\RequisitionController;
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
    Route::post('/create/pharmacy/bill', [PharmacyBillingController::class, 'store']);
    Route::get('/billings', [BillingController::class, 'index']);
    Route::get('/pharmacy_billings', [PharmacyBillingController::class, 'index']);
    Route::get('pending-appointments', [BillingController::class, 'pending_appointments']);


    // Lab Route
    Route::get('/lab-reports', [LabReportController::class, 'index']);
    Route::get('/lab-delivery', [LabReportController::class, 'delivery']);
    Route::post('/upload/lab-report/{id}', [LabReportController::class, 'update']);
    Route::post('/publish/lab-report/{id}', [LabReportController::class, 'publish']);
    Route::post('/delivered/lab-report/{id}', [LabReportController::class, 'delivered']);

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
    Route::get('/beds/available', [BedController::class, 'available']);
    Route::get('/show/bed/{id}', [BedController::class, 'show']);
    Route::post('/add/bed', [BedController::class, 'store']);
    Route::post('/edit/bed/{id}', [BedController::class, 'update']);
    Route::post('/delete/bed/{id}', [BedController::class, 'destroy']);


    // pharmacy suppliers
    Route::get('/suppliers', [PharmacySuppliersController::class, 'index']);
    Route::get('/show/supplier/{id}', [PharmacySuppliersController::class, 'show']);
    Route::post('/add/supplier', [PharmacySuppliersController::class, 'store']);
    Route::post('/edit/supplier/{id}', [PharmacySuppliersController::class, 'update']);
    Route::post('/delete/supplier/{id}', [PharmacySuppliersController::class, 'destroy']);

    // pharmacy medicines
    Route::get('/medicines', [PharmacyMedicinesController::class, 'index']);
    Route::get('/show/medicine/{id}', [PharmacyMedicinesController::class, 'show']);
    Route::post('/add/medicine', [PharmacyMedicinesController::class, 'store']);
    Route::post('/edit/medicine/{id}', [PharmacyMedicinesController::class, 'update']);
    Route::post('/delete/medicine/{id}', [PharmacyMedicinesController::class, 'destroy']);


    // pharmacy medicines
    Route::get('/requisition', [RequisitionController::class, 'index']);
    Route::post('/request/requisition', [RequisitionController::class, 'store']);
    Route::post('/edit/requisition/{id}', [RequisitionController::class, 'update']);
    Route::post('/delete/requisition/{id}', [RequisitionController::class, 'destroy']);



    Route::get('/medicine/units', [PharmacyMedicinesController::class, 'units']);
    Route::get('/search/medicine/{search}', [PharmacyMedicinesController::class, 'search']);
    Route::post('/indoor/admission', [IndoorController::class, 'store']);
    Route::get('/indoor/patients', [PatientController::class, 'index']);
    Route::get('/indoor/cases', [IndoorController::class, 'cases']);

    Route::post('/update/app', [SettingsController::class, 'update']);
});

Route::get('/app', [SettingsController::class, 'index']);
Route::get('/billing/{billId}', [BillingController::class, 'show']);
Route::get('/billing/pharmacy/{billId}', [PharmacyBillingController::class, 'showPharmacy']);
Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

<?php

use App\Http\Controllers\BillingController;
use App\Imports\PharmacyMedicineImport;
use App\Imports\PharmacySupplierImport;
use App\Models\PharmacyMedicines;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;


Route::get('/', function () {
    return redirect()->away(env("FRONT_APP_URL", "http://localhost:3000"));
});
Route::get('/get-report/{invoice}/{mobile_number}', [BillingController::class, 'report']);
Route::get('/storage', function () {
    Artisan::call('storage:link');
});

Route::get('/importer', function () {
    return view('index');
});
Route::post('/import/medicine', function(Request $request)
{
    $request->validate([
        'file' => 'required|mimes:xlsx,xls,csv'
    ]);
    Excel::import(new PharmacyMedicineImport, $request->file('file'));
    return back()->with('success', 'Medicines imported successfully!');
});
Route::post('/import/supplier', function(Request $request)
{
    $request->validate([
        'file' => 'required|mimes:xlsx,xls,csv'
    ]);
    Excel::import(new PharmacySupplierImport, $request->file('file'));
    return back()->with('success', 'Medicines imported successfully!');
});

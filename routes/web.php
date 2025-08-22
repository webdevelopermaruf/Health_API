<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/get-report/{invoice}/{mobile_number}', [\App\Http\Controllers\BillingController::class, 'report']);
Route::get('/storage', function () {
    Artisan::call('storage:link');
});

Route::get('/refresh-database', function () {
    Artisan::call('migrate:refresh --seed');
});

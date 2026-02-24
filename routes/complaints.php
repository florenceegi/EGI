<?php

use App\Http\Controllers\ComplaintController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| DSA Complaint Routes
|--------------------------------------------------------------------------
|
| Routes for the Digital Services Act (Reg. UE 2022/2065) complaint system.
| Art. 16 - Notice-and-Action mechanism
| Art. 20 - Internal complaint handling
|
*/

Route::middleware(['auth', 'verified'])->prefix('dashboard/complaints')->name('complaints.')->group(function () {

    // Index: show complaint form + list of user's complaints
    Route::get('/', [ComplaintController::class, 'index'])
        ->name('index');

    // Store: submit new complaint
    Route::post('/store', [ComplaintController::class, 'store'])
        ->name('store');

    // Show: detail of a specific complaint
    Route::get('/{complaint}', [ComplaintController::class, 'show'])
        ->name('show');
});

<?php

use App\Http\Controllers\Enquiries\AdminEnquiryController;
use App\Http\Controllers\Enquiries\EnquiryController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Conscious Growth — Website Enquiries API
|--------------------------------------------------------------------------
|
| Mounted by RouteServiceProvider at /api/enquiries. Contract:
| intern_2/Consious Growth Setup/Enquiries API Specification.md
|
| Public forms are rate limited per IP; admin endpoints need
| `Authorization: Bearer <ENQUIRIES_ADMIN_TOKEN>`.
|
*/

// --- Public form submissions ----------------------------------------------
Route::post('/general', [EnquiryController::class, 'general']);
Route::post('/pathway', [EnquiryController::class, 'pathway']);
Route::post('/speaking', [EnquiryController::class, 'speaking']);

// --- Admin ----------------------------------------------------------------
Route::middleware('enquiries.admin')->group(function () {
    // "summary" must be registered before "{id}".
    Route::get('/admin/enquiries/summary', [AdminEnquiryController::class, 'summary']);
    Route::get('/admin/enquiries', [AdminEnquiryController::class, 'index']);
    Route::get('/admin/enquiries/{id}', [AdminEnquiryController::class, 'show'])->where('id', '[0-9]+');
    Route::patch('/admin/enquiries/{id}/status', [AdminEnquiryController::class, 'updateStatus'])->where('id', '[0-9]+');
});

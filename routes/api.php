<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\FarmerController;
use App\Http\Controllers\Api\V1\CommonDataController;
use App\Http\Controllers\Api\V1\AgreementController;
use App\Http\Controllers\Api\V1\SeedsBookingController;
use App\Http\Controllers\Api\V1\SeedDistributionsController;
use App\Http\Controllers\Api\V1\PackagingDistributionsController;
use App\Http\Controllers\Api\V1\AdvancePaymentsController;
use App\Http\Controllers\Api\V1\StorageLoadingsController;
use App\Http\Controllers\Api\V1\StorageUnloadingsController;
use App\Http\Controllers\Api\V1\ChallanController;
// use App\Http\Controllers\Api\V1\ReportController;


// Public routes
Route::prefix('v1')->group(function () {
    // Auth routes
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});

// Protected routes
Route::prefix('v1')->middleware('auth:sanctum')->name('api.')->group(function () {
    Route::get('profile', [AuthController::class, 'profile']);
    Route::put('profile', [AuthController::class, 'updateProfile']);
    Route::put('change-password', [AuthController::class, 'changePassword']);

    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('reset-password', [AuthController::class, 'resetPassword']);

    // Farmers
    Route::apiResource('farmers', FarmerController::class);

    Route::get('companies', [CommonDataController::class, 'companies']);
    Route::get('seed-varieties', [CommonDataController::class, 'seedVarieties']);
    Route::get('vehicles', [CommonDataController::class, 'vehicles']);
    Route::get('cold-storages', [CommonDataController::class, 'coldStorages']);
    Route::get('transporters', [CommonDataController::class, 'transporters']);
    Route::get('unloading-companies', [CommonDataController::class, 'unloadingCompanies']);

    // Seeds Booking
    Route::apiResource('potato-bookings', AgreementController::class);

    // Seeds Booking
    Route::apiResource('seeds-booking', SeedsBookingController::class);

    // Seed Distributions
    Route::apiResource('seed-distributions', SeedDistributionsController::class);
    Route::post('download-seed-distribution-pdf', [SeedDistributionsController::class, 'downloadSeedDistributionPdf']);

    Route::apiResource('packaging-distributions', PackagingDistributionsController::class);
    Route::get('download-packaging-distributions-pdf', [PackagingDistributionsController::class, 'downloadPackagingDistributionPdf']);

    Route::apiResource('advance-payments', AdvancePaymentsController::class);
    Route::get('advance-payments-filter', [AdvancePaymentsController::class, 'filter']);
    Route::get('download-advance-payment-pdf', [AdvancePaymentsController::class, 'downloadAdvancePaymentPdf']);
    
    Route::apiResource('storage-loadings', StorageLoadingsController::class);
    Route::get('download-storage-loading-pdf', [StorageLoadingsController::class, 'downloadStorageLoadingPdf']);

    Route::apiResource('storage-unloadings', StorageUnloadingsController::class);
    Route::get('download-storage-unloading-pdf', [StorageUnloadingsController::class, 'downloadStorageUnloadingPdf']);

    Route::apiResource('challans', ChallanController::class);
    Route::get('download-challans-pdf', [ChallanController::class, 'downloadChallansPdf']);

    /*
    // Storage
    Route::prefix('storage')->group(function () {
        Route::apiResource('loading', StorageLoadingController::class);
        Route::apiResource('unloading', StorageUnloadingController::class);
    });
    
    // Reports
    Route::get('reports/farmer/{farmer}', [ReportController::class, 'farmerReport']);
    Route::get('reports/storage', [ReportController::class, 'storageReport']);
    Route::get('reports/financial', [ReportController::class, 'financialReport']);*/
}); 
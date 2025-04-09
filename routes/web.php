<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
    // return redirect('/administrator/dashboard');
});

Route::get('/privacy-policy', [App\Http\Controllers\FrontPagesController::class, 'privacy'])->name('privacy');
Route::get('/contact', [App\Http\Controllers\FrontPagesController::class, 'contact'])->name('contact');
Route::post('/submit-quote', [App\Http\Controllers\FrontPagesController::class, 'submitQuote'])->name('quote.submit');

Route::middleware(['auth', 'verified'])->prefix('administrator')->name('admin.')->group(function(){

    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/farmers', [\App\Http\Controllers\Admin\FarmerController::class, 'index'])->name('farmers.index');
    Route::get('/farmers/search', [\App\Http\Controllers\Admin\FarmerController::class, 'searchFarmer'])->name('farmers.search');

    Route::get('/seed-distributions', [\App\Http\Controllers\Admin\SeedDistributionController::class, 'index'])->name('seed-distributions.index');
    Route::get('/seed-distributions/search', [\App\Http\Controllers\Admin\SeedDistributionController::class, 'searchSeedDistributions'])->name('seed-distributions.search');

    Route::get('/seeds-booking', [\App\Http\Controllers\Admin\SeedsBookingController::class, 'index'])->name('seeds-booking.index');
    Route::get('/companies', [\App\Http\Controllers\Admin\CompanyController::class, 'index'])->name('companies.index');
    Route::get('/seed-varieties', [\App\Http\Controllers\Admin\SeedVarietyController::class, 'index'])->name('seed-varieties.index');
    Route::get('/agreements', [\App\Http\Controllers\Admin\AgreementController::class, 'index'])->name('agreements.index');

    Route::get('/packaging-distributions', [\App\Http\Controllers\Admin\PackagingDistributionController::class, 'index'])->name('packaging-distributions.index');
    Route::get('/packaging-distributions/search-farmer', [\App\Http\Controllers\Admin\PackagingDistributionController::class, 'searchPackagingDistributionFarmer'])->name('packaging-distributions.search');

    Route::get('/advance-payments', [\App\Http\Controllers\Admin\AdvancePaymentController::class, 'index'])->name('advance-payments.index');
    Route::get('/transporters', [\App\Http\Controllers\Admin\TransporterController::class, 'index'])->name('transporters.index');
    Route::get('/vehicles', [\App\Http\Controllers\Admin\VehicleController::class, 'index'])->name('vehicles.index');
    Route::get('/cold-storages', [\App\Http\Controllers\Admin\ColdStorageController::class, 'index'])->name('cold-storages.index');
    
    Route::get('/storage-loadings', [\App\Http\Controllers\Admin\StorageLoadingController::class, 'index'])->name('storage-loadings.index');
    Route::get('/storage-loadings/search-farmer', [\App\Http\Controllers\Admin\StorageLoadingController::class, 'searchFarmer'])->name('storage-loadings.searchFarmer');
    
    Route::get('/challans', [\App\Http\Controllers\Admin\ChallanController::class, 'index'])->name('challans.index');
    Route::get('/unloading-companies', [\App\Http\Controllers\Admin\UnloadingCompaniesController::class, 'index'])->name('unloading-companies.index');
    Route::get('/storage-unloadings', [\App\Http\Controllers\Admin\StorageUnloadingController::class, 'index'])->name('storage-unloadings.index');

    // User Management Routes
    Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');

    Route::get('/admin/roles', function () { return view('admin.roles.index'); } )->name('roles.index');

    // AJAX route for DataTables
    Route::get('/farmers-data', [\App\Http\Controllers\Admin\FarmerController::class, 'getFarmersData'])->name('farmers.data');
    Route::get('/companies-data', [\App\Http\Controllers\Admin\CompanyController::class, 'getCompaniesData'])->name('companies.data');
    Route::get('/agreements-data', [\App\Http\Controllers\Admin\AgreementController::class, 'getAgreementsData'])->name('agreements.data');


    Route::post('/sms/test/single',[\App\Http\Controllers\Admin\SmsTestController::class, 'testSingleSms']);
    Route::post('/sms/test/bulk',[\App\Http\Controllers\Admin\SmsTestController::class, 'testBulkSms']);

    Route::get('/sms/sender-ids', [\App\Http\Controllers\Admin\SmsTestController::class, 'getSenderIds'])
    ->name('sms.sender-ids');
    Route::get('/sms/templates', [\App\Http\Controllers\Admin\SmsTestController::class, 'getTemplates'])
    ->name('sms.templates');

    Route::get('/sms-test', [\App\Http\Controllers\Admin\SmsTestController::class, 'index'])->name('sms.test');


    // Reports Routes
    // Reports Routes
    Route::get('/reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/processing', [\App\Http\Controllers\Admin\ReportController::class, 'processingReport'])->name('reports.processing');
    Route::get('/reports/farmer', [\App\Http\Controllers\Admin\ReportController::class, 'farmerReport'])->name('reports.farmer');
    Route::get('/reports/farmer/{farmer}', [\App\Http\Controllers\Admin\ReportController::class, 'farmerReportDetail'])->name('reports.farmer.detail');

    // Farmer Detailed Reports
    Route::get('/reports/farmer/{farmer}/agreements', [\App\Http\Controllers\Admin\ReportController::class, 'farmerAgreements'])->name('reports.farmer.agreements');
    Route::get('/reports/farmer/{farmer}/payments', [\App\Http\Controllers\Admin\ReportController::class, 'farmerPayments'])->name('reports.farmer.payments');
    Route::get('/reports/farmer/{farmer}/challans', [\App\Http\Controllers\Admin\ReportController::class, 'farmerChallans'])->name('reports.farmer.challans');
    Route::get('/reports/farmer/{farmer}/loadings', [\App\Http\Controllers\Admin\ReportController::class, 'farmerLoadings'])->name('reports.farmer.loadings');




    Route::get('/reports/storage', [\App\Http\Controllers\Admin\ReportController::class, 'storageReport'])->name('reports.storage');
    Route::get('/reports/financial', [\App\Http\Controllers\Admin\ReportController::class, 'financialReport'])->name('reports.financial');
    Route::post('/reports/export/pdf', [\App\Http\Controllers\Admin\ReportController::class, 'exportPdf'])->name('reports.export.pdf');
    Route::post('/reports/export/excel', [\App\Http\Controllers\Admin\ReportController::class, 'exportToExcel'])->name('reports.export.excel');
});

require __DIR__.'/auth.php';

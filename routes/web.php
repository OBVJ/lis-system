<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\LabRequestController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\SampleController;
use App\Http\Controllers\TestResultController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\QueueController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/lang/{locale}', [LanguageController::class, 'switch'])->name('lang.switch');



Route::middleware(['auth', 'active.user'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('permission:view_dashboard');
    Route::get('/queue', [QueueController::class, 'index'])->name('queue'); // Open to users to see board

    // Clinical Resources
    Route::middleware('permission:manage_patients')->group(function () {
        Route::get('/patients/search', [PatientController::class, 'ajaxSearch'])->name('patients.ajaxSearch');
        Route::resource('patients', PatientController::class);
        Route::get('/patients/receipt/{requestId}', [PatientController::class, 'printReceipt'])->name('patients.receipt');
    });

    Route::middleware('permission:manage_tests')->group(function () {
        Route::get('/tests/search', [TestController::class, 'ajaxSearch'])->name('tests.ajaxSearch');
        Route::resource('tests', TestController::class);
    });

    Route::middleware('permission:manage_requests')->group(function () {
        Route::patch('/requests/{labRequest}/status', [LabRequestController::class, 'updateStatus'])->name('requests.update-status');
        Route::resource('requests', LabRequestController::class)->parameters(['requests' => 'labRequest']);
    });

    // Billing & Payments
    Route::middleware('permission:manage_requests')->group(function () {
        Route::get('/billing', [\App\Http\Controllers\BillingController::class, 'index'])->name('billing.index');
        Route::patch('/billing/{payment}/mark-paid', [\App\Http\Controllers\BillingController::class, 'markPaid'])->name('billing.mark-paid');
        Route::patch('/billing/{payment}/refund', [\App\Http\Controllers\BillingController::class, 'refund'])->name('billing.refund');
        Route::get('/billing/{payment}/invoice', [\App\Http\Controllers\BillingController::class, 'invoice'])->name('billing.invoice');
        Route::get('/billing/{payment}/receipt', [\App\Http\Controllers\BillingController::class, 'receipt'])->name('billing.receipt');
    });

    // Lab Workbench
    Route::middleware('permission:manage_results')->group(function () {
        Route::get('/results', [ResultController::class, 'index'])->name('results.index');
        Route::get('/results/{item}/entry', [TestResultController::class, 'entry'])->name('results.entry');
        Route::post('/results/store', [TestResultController::class, 'store'])->name('results.store');
        Route::get('/results/{labRequest}/bulk-entry', [TestResultController::class, 'bulkEntry'])->name('results.bulk.entry');
        Route::post('/results/{labRequest}/bulk-store', [TestResultController::class, 'bulkStore'])->name('results.bulk.store');
        Route::get('/results/{labRequest}/edit', [ResultController::class, 'edit'])->name('results.edit');
    });

    // Sample Logistics
    Route::middleware('permission:manage_samples')->group(function () {
        Route::get('/samples', [SampleController::class, 'index'])->name('samples.index');
        Route::get('/samples/{sample}', [SampleController::class, 'show'])->name('samples.show');
        Route::get('/samples/{sample}/print', [SampleController::class, 'print'])->name('samples.print');
        Route::post('/samples/collect', [SampleController::class, 'store'])->name('samples.store');
    });

    // Reporting
    Route::middleware('permission:view_reports')->group(function () {
        Route::get('/reports/operational', [ReportController::class, 'operational'])->name('reports.operational');
        Route::get('/reports/financial', [ReportController::class, 'financial'])->name('reports.financial');
        Route::get('/reports/medical', [ReportController::class, 'medical'])->name('reports.medical');
        Route::get('/reports/{labRequest}/pdf', [ReportController::class, 'generatePdf'])->name('reports.pdf');
    });

    // Inventory
    Route::middleware('permission:manage_inventory')->group(function () {
        Route::resource('inventory', \App\Http\Controllers\InventoryController::class);
        Route::get('/inventory/{inventory}/transaction', [\App\Http\Controllers\InventoryController::class, 'transaction'])->name('inventory.transaction');
        Route::post('/inventory/{inventory}/transaction', [\App\Http\Controllers\InventoryController::class, 'storeTransaction'])->name('inventory.storeTransaction');
    });

    // Users and Roles Management
    Route::middleware('permission:manage_users')->group(function () {
        Route::patch('/users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');
        Route::resource('users', UserController::class)->except(['show', 'destroy']);
    });
    
    Route::middleware('permission:manage_roles')->group(function () {
        Route::resource('roles', RoleController::class)->except(['show']);
    });

    // Settings
    Route::middleware('permission:manage_settings')->group(function () {
        // General Settings
        Route::get('/settings', [\App\Http\Controllers\SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [\App\Http\Controllers\SettingController::class, 'update'])->name('settings.update');

        // Test Categories
        Route::get('/settings/test-categories', [\App\Http\Controllers\Settings\TestCategoryController::class, 'index'])->name('settings.test-categories.index');
        Route::post('/settings/test-categories', [\App\Http\Controllers\Settings\TestCategoryController::class, 'store'])->name('settings.test-categories.store');
        Route::delete('/settings/test-categories/{category}', [\App\Http\Controllers\Settings\TestCategoryController::class, 'destroy'])->name('settings.test-categories.destroy');

        // Inventory Units
        Route::get('/settings/inventory-units', [\App\Http\Controllers\Settings\InventoryUnitController::class, 'index'])->name('settings.inventory-units.index');
        Route::post('/settings/inventory-units', [\App\Http\Controllers\Settings\InventoryUnitController::class, 'store'])->name('settings.inventory-units.store');
        Route::delete('/settings/inventory-units/{unit}', [\App\Http\Controllers\Settings\InventoryUnitController::class, 'destroy'])->name('settings.inventory-units.destroy');

        // Inventory Categories
        Route::get('/settings/inventory-categories', [\App\Http\Controllers\Settings\InventoryCategoryController::class, 'index'])->name('settings.inventory-categories.index');
        Route::post('/settings/inventory-categories', [\App\Http\Controllers\Settings\InventoryCategoryController::class, 'store'])->name('settings.inventory-categories.store');
        Route::delete('/settings/inventory-categories/{category}', [\App\Http\Controllers\Settings\InventoryCategoryController::class, 'destroy'])->name('settings.inventory-categories.destroy');

        // Specimen Types
        Route::get('/settings/specimen-types', [\App\Http\Controllers\Settings\SpecimenTypeController::class, 'index'])->name('settings.specimen-types.index');
        Route::post('/settings/specimen-types', [\App\Http\Controllers\Settings\SpecimenTypeController::class, 'store'])->name('settings.specimen-types.store');
        Route::delete('/settings/specimen-types/{type}', [\App\Http\Controllers\Settings\SpecimenTypeController::class, 'destroy'])->name('settings.specimen-types.destroy');

        // Lab Units
        Route::get('/settings/lab-units', [\App\Http\Controllers\Settings\LabUnitController::class, 'index'])->name('settings.lab-units.index');
        Route::post('/settings/lab-units', [\App\Http\Controllers\Settings\LabUnitController::class, 'store'])->name('settings.lab-units.store');
        Route::delete('/settings/lab-units/{unit}', [\App\Http\Controllers\Settings\LabUnitController::class, 'destroy'])->name('settings.lab-units.destroy');

        // Audit Logs
        Route::get('/audit', [\App\Http\Controllers\AuditController::class, 'index'])->name('audit.index');
    });

});

require __DIR__.'/auth.php';

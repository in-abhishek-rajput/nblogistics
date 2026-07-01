<?php

use App\Http\Controllers\Admin\BiltyController;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DriversController;
use App\Http\Controllers\Admin\ExpensesController;
use App\Http\Controllers\Admin\InvoicesController;
use App\Http\Controllers\Admin\PartiesController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\ReceiptController;
use App\Http\Controllers\Admin\ReportsController;
use App\Http\Controllers\Admin\TripsController;
use App\Http\Controllers\Admin\TrucksController;
use Illuminate\Support\Facades\Route;

// Authentication Routes (Public - Guest only)
Route::middleware('guest')->group(function () {
    Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login'])->name('validate');
});
// Logout Route (Authenticated users only)
Route::middleware('auth')->group(function () {
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');
});

// Protected Routes (Require Authentication)
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Resource Routes
    Route::resource('parties', PartiesController::class);
    Route::resource('trips', TripsController::class);
    Route::get('drivers-attendance', function () {
        return view('admin.driver.attendance');
    })->name('drivers.attendance');
    Route::get('drivers-salary', function () {
        return view('admin.driver.salary');
    })->name('drivers.salary');
    Route::get('drivers/{driver}/advances', function ($driver) {
        return view('admin.driver.advances', ['driverId' => $driver]);
    })->name('drivers.advances');
    Route::get('drivers/{driver}/salary-details', function ($driver) {
        return view('admin.driver.salary-details', ['driverId' => $driver]);
    })->name('drivers.salary-details');
    Route::resource('drivers', DriversController::class);
    Route::resource('trucks', TrucksController::class);
    Route::resource('expenses', ExpensesController::class);
    Route::resource('invoices', InvoicesController::class);
    Route::resource('builty', BiltyController::class);
    Route::resource('receipts', ReceiptController::class);
    Route::resource('profile', ProfileController::class);

    // Bilty spelling alias for backward compatibility and cleaner URLs
    Route::get('bilty', fn () => redirect()->route('builty.index'))->name('bilty.index');
    Route::get('bilty/{id}', fn ($id) => redirect()->route('builty.show', $id))->name('bilty.show');
    Route::get('bilty/{id}/print', fn ($id) => redirect()->route('builty.print', $id))->name('bilty.print');

    // Document print/download routes
    Route::get('invoices/{id}/print', [InvoicesController::class, 'print'])->name('invoices.print');
    Route::get('builty/{id}/print', [BiltyController::class, 'print'])->name('builty.print');
    Route::get('receipts/{id}/print', [ReceiptController::class, 'print'])->name('receipts.print');
    Route::get('trips/{id}/digital-invoice', [TripsController::class, 'digitalInvoice'])->name('trips.digital-invoice');
    Route::get('trips/{id}/share-whatsapp-invoice', [TripsController::class, 'shareWhatsappInvoice'])->name('trips.share-whatsapp-invoice');
    Route::get('trips/{id}/pod-print', [TripsController::class, 'podPrint'])->name('trips.pod-print');
    
    // Trip Document Wizard (Bilty → Invoice → Receipt)
    Route::get('trips/{tripId}/documents/{step?}', function ($tripId, $step = 1) {
        return view('admin.trip.documents', ['tripId' => (int) $tripId, 'step' => (int) $step]);
    })->name('trip.documents');
    
    // Reports Routes
    Route::get('reports/daily-freight', [ReportsController::class, 'dailyFreight'])->name('reports.daily-freight');
    Route::get('reports/daily-freight/{date}/print', [ReportsController::class, 'dailyFreightPrint'])->name('reports.daily-freight.print');
    Route::get('reports/trips', [ReportsController::class, 'trips'])->name('reports.trips');
    Route::get('reports/trucks', [ReportsController::class, 'trucks'])->name('reports.trucks');
    Route::get('reports/drivers', [ReportsController::class, 'drivers'])->name('reports.drivers');
    Route::get('reports/parties', [ReportsController::class, 'parties'])->name('reports.parties');

    // Monthly Profit & Loss Report PDF
    Route::get('trucks/{truck}/monthly-report-pdf/{month?}/{year?}', [TrucksController::class, 'monthlyReportPdf'])->name('trucks.monthly-report-pdf');

    // Trip Expenses
    Route::get('trip-expenses', function () {
        return view('admin.trip-expenses.list');
    })->name('trip-expenses');
});

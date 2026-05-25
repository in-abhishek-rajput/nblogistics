<?php

use App\Http\Controllers\admin\BiltyController;
use App\Http\Controllers\admin\Auth\LoginController;
use App\Http\Controllers\admin\DashboardController;
use App\Http\Controllers\admin\DriversController;
use App\Http\Controllers\admin\ExpensesController;
use App\Http\Controllers\admin\InvoicesController;
use App\Http\Controllers\admin\PartiesController;
use App\Http\Controllers\admin\ProfileController;
use App\Http\Controllers\admin\ReportsController;
use App\Http\Controllers\admin\TripsController;
use App\Http\Controllers\admin\TrucksController;
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
    Route::resource('drivers', DriversController::class);
    Route::resource('trucks', TrucksController::class);
    Route::resource('expenses', ExpensesController::class);
    Route::resource('invoices', InvoicesController::class);
    Route::resource('builty', BiltyController::class);
    Route::resource('reports', ReportsController::class);
    Route::resource('profile', ProfileController::class);

    // Trip Expenses
    Route::get('trip-expenses', function () {
        return view('admin.trip-expenses.list');
    })->name('trip-expenses');
});
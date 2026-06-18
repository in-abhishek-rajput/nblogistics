<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Truck;
use Illuminate\Http\Request;

class TrucksController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $total_trucks = Truck::all()->count();
        $total_self_trucks = Truck::where('ownership', 'self')->count();
        $total_market_trucks = Truck::where('ownership', 'market')->count();
        return view('admin.truck.list', compact('total_trucks', 'total_self_trucks', 'total_market_trucks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return view('admin.truck.view', ['truckId' => $id]);
    }

    /**
     * Generate Monthly Profit & Loss Report PDF/HTML.
     */
    public function monthlyReportPdf(string $truck, string $month = null, string $year = null)
    {
        $truckModel = \App\Models\Truck::with('driver')->findOrFail($truck);
        $monthValue = $month ?? now()->month;
        $yearValue = $year ?? now()->year;

        $months = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
        ];

        $types = config('truck.types');

        $totalTripsStarted = \App\Models\Trip::where('truck_id', $truckModel->id)
            ->whereYear('start_date', $yearValue)
            ->whereMonth('start_date', $monthValue)
            ->count();

        $tripEndKm = \App\Models\Trip::where('truck_id', $truckModel->id)
            ->whereYear('start_date', $yearValue)
            ->whereMonth('start_date', $monthValue)
            ->whereNotNull('end_km')
            ->max('end_km');

        $fuelKm = \App\Models\TruckFuelExpense::where('truck_id', $truckModel->id)
            ->whereYear('expense_date', $yearValue)
            ->whereMonth('expense_date', $monthValue)
            ->whereNotNull('current_km_reading')
            ->max('current_km_reading');

        $lastTripKmReading = max($tripEndKm ?: 0, $fuelKm ?: 0);

        $totalRefuelQuantity = (float) \App\Models\TruckFuelExpense::where('truck_id', $truckModel->id)
            ->whereYear('expense_date', $yearValue)
            ->whereMonth('expense_date', $monthValue)
            ->sum('fuel_quantity');

        $trips = \App\Models\Trip::where('truck_id', $truckModel->id)
            ->whereYear('start_date', $yearValue)
            ->whereMonth('start_date', $monthValue)
            ->where('status', 'completed')
            ->with(['charges'])
            ->orderBy('start_date')
            ->get();

        $revenueData = $trips->map(function ($trip) {
            $totalCharges = 0;
            foreach ($trip->charges as $charge) {
                if ($charge->charge_direction === 'add_to_bill') {
                    $totalCharges += $charge->amount;
                } elseif ($charge->charge_direction === 'reduce_from_bill') {
                    $totalCharges -= $charge->amount;
                }
            }
            return [
                'id' => $trip->id,
                'date' => $trip->start_date?->format('d M') ?? '-',
                'route' => ($trip->origin ?? '') . ' → ' . ($trip->destination ?? ''),
                'freight_amount' => (float) $trip->freight_amount,
                'charges' => (float) $totalCharges,
            ];
        })->toArray();

        $totalRevenue = (float) $trips->sum('freight_amount') + $trips->sum(fn ($trip) => $trip->charges->sum('amount'));

        $expensesData = [];
        $tripIds = \App\Models\Trip::where('truck_id', $truckModel->id)
            ->whereYear('start_date', $yearValue)
            ->whereMonth('start_date', $monthValue)
            ->pluck('id');

        $fuelExpenses = \App\Models\TruckFuelExpense::where('truck_id', $truckModel->id)
            ->whereYear('expense_date', $yearValue)
            ->whereMonth('expense_date', $monthValue)
            ->orderBy('expense_date')
            ->get();
        foreach ($fuelExpenses as $expense) {
            $expensesData['fuel'][] = [
                'date' => $expense->expense_date?->format('d M') ?? '-',
                'type' => 'Fuel Expense',
                'amount' => (float) $expense->expense_amount,
            ];
        }

        $emiExpenses = \App\Models\TruckEmiPayment::whereHas('emi', fn ($q) => $q->where('truck_id', $truckModel->id))
            ->whereYear('payment_date', $yearValue)
            ->whereMonth('payment_date', $monthValue)
            ->where('status', 'paid')
            ->orderBy('payment_date')
            ->get();
        foreach ($emiExpenses as $expense) {
            $expensesData['emi'][] = [
                'date' => $expense->payment_date?->format('d M') ?? '-',
                'type' => 'EMI Payment',
                'amount' => (float) $expense->amount,
            ];
        }

        $driverExpenses = \App\Models\TripExpense::where('truck_id', $truckModel->id)
            ->where('expense_category', 'truck')
            ->whereYear('expense_date', $yearValue)
            ->whereMonth('expense_date', $monthValue)
            ->orderBy('expense_date')
            ->get();
        foreach ($driverExpenses as $expense) {
            $expensesData['driver'][] = [
                'date' => $expense->expense_date?->format('d M') ?? '-',
                'type' => $expense->expense_type ?? 'Driver Expense',
                'amount' => (float) $expense->amount,
            ];
        }

        $maintenanceExpenses = \App\Models\TruckMaintenanceExpense::where('truck_id', $truckModel->id)
            ->where('status', 'completed')
            ->whereYear('expense_date', $yearValue)
            ->whereMonth('expense_date', $monthValue)
            ->orderBy('expense_date')
            ->get();
        foreach ($maintenanceExpenses as $expense) {
            $expensesData['maintenance'][] = [
                'date' => $expense->expense_date?->format('d M') ?? '-',
                'type' => $expense->expense_type ?? 'Maintenance',
                'amount' => (float) $expense->amount,
            ];
        }

        $documentExpenses = \App\Models\TruckDocument::where('truck_id', $truckModel->id)
            ->where('expense_amount', '>', 0)
            ->whereYear('expense_date', $yearValue)
            ->whereMonth('expense_date', $monthValue)
            ->orderBy('expense_date')
            ->get();
        foreach ($documentExpenses as $expense) {
            $expensesData['document'][] = [
                'date' => $expense->expense_date?->format('d M') ?? '-',
                'type' => $expense->document_name . ' Renewal',
                'amount' => (float) $expense->expense_amount,
            ];
        }

        $tripExpenses = \App\Models\TripExpense::whereIn('trip_id', $tripIds)
            ->where('expense_category', 'trip')
            ->whereYear('expense_date', $yearValue)
            ->whereMonth('expense_date', $monthValue)
            ->orderBy('expense_date')
            ->get();
        foreach ($tripExpenses as $expense) {
            $expensesData['trip_expense'][] = [
                'date' => $expense->expense_date?->format('d M') ?? '-',
                'type' => $expense->expense_type ?? 'Trip Expense',
                'amount' => (float) $expense->amount,
            ];
        }

        $totalExpenses = (float) (\App\Models\TruckFuelExpense::where('truck_id', $truckModel->id)
            ->whereYear('expense_date', $yearValue)
            ->whereMonth('expense_date', $monthValue)
            ->sum('expense_amount'))
            + (\App\Models\TruckEmiPayment::whereHas('emi', fn ($q) => $q->where('truck_id', $truckModel->id))
                ->whereYear('payment_date', $yearValue)
                ->whereMonth('payment_date', $monthValue)
                ->where('status', 'paid')
                ->sum('amount'))
            + (\App\Models\TripExpense::where('truck_id', $truckModel->id)
                ->where('expense_category', 'truck')
                ->whereYear('expense_date', $yearValue)
                ->whereMonth('expense_date', $monthValue)
                ->sum('amount'))
            + (\App\Models\TruckMaintenanceExpense::where('truck_id', $truckModel->id)
                ->where('status', 'completed')
                ->whereYear('expense_date', $yearValue)
                ->whereMonth('expense_date', $monthValue)
                ->sum('amount'))
            + (\App\Models\TruckDocument::where('truck_id', $truckModel->id)
                ->where('expense_amount', '>', 0)
                ->whereYear('expense_date', $yearValue)
                ->whereMonth('expense_date', $monthValue)
                ->sum('expense_amount'))
            + (\App\Models\TripExpense::whereIn('trip_id', $tripIds)
                ->where('expense_category', 'trip')
                ->whereYear('expense_date', $yearValue)
                ->whereMonth('expense_date', $monthValue)
                ->sum('amount'))
            + (\App\Models\TripAdvance::whereIn('trip_id', $tripIds)->sum('amount'));

        $profitLoss = $totalRevenue - $totalExpenses;

        return view('livewire.admin.truck.monthly-report-pdf', [
            'truck' => $truckModel,
            'month' => (int) $monthValue,
            'year' => (int) $yearValue,
            'monthLabel' => $months[(int) $monthValue] ?? 'Unknown',
            'types' => $types,
            'totalTripsStarted' => $totalTripsStarted,
            'lastTripKmReading' => $lastTripKmReading ?: 0,
            'totalRefuelQuantity' => $totalRefuelQuantity,
            'revenueData' => $revenueData,
            'totalRevenue' => $totalRevenue,
            'expensesData' => $expensesData,
            'totalExpenses' => $totalExpenses,
            'profitLoss' => $profitLoss,
            'profitLossLabel' => $profitLoss >= 0 ? 'Profit' : 'Loss',
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
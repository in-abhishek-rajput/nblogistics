<?php

namespace App\Livewire\Admin\Truck;

use App\Models\Trip;
use App\Models\Truck;
use App\Models\TruckEmiPayment;
use App\Models\TruckFuelExpense;
use App\Models\TruckDocument;
use App\Models\TruckMaintenanceExpense;
use App\Models\TripExpense;
use App\Models\TripAdvance;
use Illuminate\Support\Carbon;
use Livewire\Component;
use Livewire\Attributes\Computed;

class MonthlyReport extends Component
{
    public int $truckId;
    public Truck $truck;

    public int $month;
    public int $year;

    public bool $revenueExpanded = true;
    public bool $expensesExpanded = true;

    protected $listeners = [
        'openMonthlyReportOffcanvas' => 'openPanel',
    ];

    public function mount(int $truckId)
    {
        $this->truckId = $truckId;
        $this->truck = Truck::with('driver')->findOrFail($this->truckId);
        $today = Carbon::today();
        $this->month = $today->month;
        $this->year = $today->year;
    }

    #[Computed]
    public function months()
    {
        return [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
        ];
    }

    #[Computed]
    public function years()
    {
        $currentYear = Carbon::now()->year;
        return range($currentYear - 5, $currentYear + 5);
    }

    #[Computed]
    public function selectedMonthLabel()
    {
        $months = $this->months;
        return $months[$this->month] ?? 'Unknown';
    }

    #[Computed]
    public function totalTripsStarted()
    {
        return Trip::where('truck_id', $this->truckId)
            ->whereYear('start_date', $this->year)
            ->whereMonth('start_date', $this->month)
            ->count();
    }

    #[Computed]
    public function lastTripKmReading()
    {
        $lastKm = Trip::where('truck_id', $this->truckId)
            ->whereYear('start_date', $this->year)
            ->whereMonth('start_date', $this->month)
            ->whereNotNull('end_km')
            ->max('end_km');

        $fuelKm = TruckFuelExpense::where('truck_id', $this->truckId)
            ->whereYear('expense_date', $this->year)
            ->whereMonth('expense_date', $this->month)
            ->whereNotNull('current_km_reading')
            ->max('current_km_reading');

        return max($lastKm ?: 0, $fuelKm ?: 0);
    }

    #[Computed]
    public function totalRefuelQuantity()
    {
        return (float) TruckFuelExpense::where('truck_id', $this->truckId)
            ->whereYear('expense_date', $this->year)
            ->whereMonth('expense_date', $this->month)
            ->sum('fuel_quantity');
    }

    // ─────────────────────────────────────────────
    // REVENUE
    // ─────────────────────────────────────────────

    #[Computed]
    public function revenueData()
    {
        $trips = Trip::where('truck_id', $this->truckId)
            ->whereYear('start_date', $this->year)
            ->whereMonth('start_date', $this->month)
            ->with(['charges'])
            ->orderBy('start_date')
            ->get();

        return $trips->map(function ($trip) {
            $freight = (float) $trip->freight_amount;
            $chargesTotal = 0;

            foreach ($trip->charges as $charge) {
                if ($charge->charge_direction === 'add_to_bill') {
                    $chargesTotal += $charge->amount;
                } elseif ($charge->charge_direction === 'reduce_from_bill') {
                    $chargesTotal -= $charge->amount;
                }
            }

            return [
                'id'              => $trip->id,
                'date'            => $trip->start_date?->format('d M') ?? '-',
                'route'           => ($trip->origin ?? '') . ' → ' . ($trip->destination ?? ''),
                'freight_amount'  => $freight,
                'charges'         => $chargesTotal,
                'total'           => $freight + $chargesTotal,
            ];
        })->toArray();
    }

    #[Computed]
    public function totalRevenue()
    {
        $trips = Trip::where('truck_id', $this->truckId)
            ->whereYear('start_date', $this->year)
            ->whereMonth('start_date', $this->month)
            ->with(['charges'])
            ->get();

        $total = 0;
        foreach ($trips as $trip) {
            $total += (float) $trip->freight_amount;
            foreach ($trip->charges as $charge) {
                if ($charge->charge_direction === 'add_to_bill') {
                    $total += $charge->amount;
                } elseif ($charge->charge_direction === 'reduce_from_bill') {
                    $total -= $charge->amount;
                }
            }
        }

        return $total;
    }

    // ─────────────────────────────────────────────
    // EXPENSES
    // ─────────────────────────────────────────────

    /**
     * Trip IDs for the selected month — reused across expense computed props.
     * Filter by start_date (same as ViewTruck) so all trip-linked expenses
     * belong to trips that STARTED in this month.
     */
    protected function tripIdsForMonth()
    {
        return Trip::where('truck_id', $this->truckId)
            ->whereYear('start_date', $this->year)
            ->whereMonth('start_date', $this->month)
            ->pluck('id');
    }

    #[Computed]
    public function expensesData()
    {
        $expenses = [];
        $tripIds  = $this->tripIdsForMonth();

        // ── Fuel Expenses ──────────────────────────────────────────────────
        $fuelExpenses = TruckFuelExpense::where('truck_id', $this->truckId)
            ->whereYear('expense_date', $this->year)
            ->whereMonth('expense_date', $this->month)
            ->orderBy('expense_date')
            ->get();

        foreach ($fuelExpenses as $expense) {
            $expenses['fuel'][] = [
                'date'      => $expense->expense_date?->format('d M') ?? '-',
                'type'      => 'Fuel Expense',
                'shop_name' => $expense->shop_name ?? $expense->diesel_pump_name ?? '-',
                'amount'    => (float) $expense->expense_amount,
                'quantity'  => $expense->fuel_quantity
                    ? number_format($expense->fuel_quantity, 2) . ' L'
                    : null,
            ];
        }

        // ── EMI Payments (paid only) ───────────────────────────────────────
        $emiExpenses = TruckEmiPayment::whereHas('emi', fn ($q) => $q->where('truck_id', $this->truckId))
            ->whereYear('payment_date', $this->year)
            ->whereMonth('payment_date', $this->month)
            ->where('status', 'paid')
            ->orderBy('payment_date')
            ->get();

        foreach ($emiExpenses as $expense) {
            $expenses['emi'][] = [
                'date'      => $expense->payment_date?->format('d M') ?? '-',
                'type'      => 'EMI Payment',
                'shop_name' => $expense->emi->finance_company ?? '-',
                'amount'    => (float) $expense->amount,
            ];
        }

        // ── Driver & Other Expenses ────────────────────────────────────────
        // TripExpense table se aate hain jahan expense_category = 'truck'
        // Same as ViewTruck's truckDriverExpenses() relationship
        $driverExpenses = TripExpense::where('truck_id', $this->truckId)
            ->where('expense_category', 'truck')
            ->whereYear('expense_date', $this->year)
            ->whereMonth('expense_date', $this->month)
            ->orderBy('expense_date')
            ->get();

        foreach ($driverExpenses as $expense) {
            $expenses['driver'][] = [
                'date'      => $expense->expense_date?->format('d M') ?? '-',
                'type'      => $expense->expense_type ?? 'Driver Expense',
                'shop_name' => $expense->shop_name ?? '-',
                'amount'    => (float) $expense->amount,
            ];
        }

        // ── Maintenance Expenses ───────────────────────────────────────────
        $maintenanceExpenses = TruckMaintenanceExpense::where('truck_id', $this->truckId)
            ->where('status', 'completed')
            ->whereYear('expense_date', $this->year)
            ->whereMonth('expense_date', $this->month)
            ->orderBy('expense_date')
            ->get();

        foreach ($maintenanceExpenses as $expense) {
            $expenses['maintenance'][] = [
                'date'      => $expense->expense_date?->format('d M') ?? '-',
                'type'      => $expense->expense_type ?? 'Maintenance',
                'shop_name' => $expense->shop_name ?? '-',
                'amount'    => (float) $expense->amount,
            ];
        }

        // ── Document Expenses ──────────────────────────────────────────────
        $documentExpenses = TruckDocument::where('truck_id', $this->truckId)
            ->where('expense_amount', '>', 0)
            ->whereYear('expense_date', $this->year)
            ->whereMonth('expense_date', $this->month)
            ->orderBy('expense_date')
            ->get();

        foreach ($documentExpenses as $expense) {
            $expenses['document'][] = [
                'date'      => $expense->expense_date?->format('d M') ?? '-',
                'type'      => $expense->document_name . ' Renewal',
                'shop_name' => '-',
                'amount'    => (float) $expense->expense_amount,
            ];
        }

        // ── Trip Expenses ──────────────────────────────────────────────────
        // Filtered by trip_id (trips that STARTED this month) — no
        // expense_category filter, same as ViewTruck's trip->expenses->sum().
        if ($tripIds->isNotEmpty()) {
            $tripExpenses = TripExpense::whereIn('trip_id', $tripIds)
                ->orderBy('expense_date')
                ->get();

            foreach ($tripExpenses as $expense) {
                $expenses['trip_expense'][] = [
                    'date'      => $expense->expense_date?->format('d M') ?? '-',
                    'type'      => $expense->expense_type ?? 'Trip Expense',
                    'shop_name' => $expense->shop_name ?? '-',
                    'amount'    => (float) $expense->amount,
                ];
            }

            // ── Trip Advances ──────────────────────────────────────────────
            $advances = TripAdvance::whereIn('trip_id', $tripIds)->get();

            foreach ($advances as $advance) {
                $expenses['advance'][] = [
                    'date'      => $advance->advance_date?->format('d M') ?? '-',
                    'type'      => 'Trip Advance',
                    'shop_name' => $advance->given_to ?? '-',
                    'amount'    => (float) $advance->amount,
                ];
            }
        }

        return $expenses;
    }

    #[Computed]
    public function totalExpenses()
    {
        $tripIds = $this->tripIdsForMonth();

        // Fuel
        $fuel = (float) TruckFuelExpense::where('truck_id', $this->truckId)
            ->whereYear('expense_date', $this->year)
            ->whereMonth('expense_date', $this->month)
            ->sum('expense_amount');

        // EMI (paid only)
        $emi = (float) TruckEmiPayment::whereHas('emi', fn ($q) => $q->where('truck_id', $this->truckId))
            ->whereYear('payment_date', $this->year)
            ->whereMonth('payment_date', $this->month)
            ->where('status', 'paid')
            ->sum('amount');

        // Driver & Other Expenses — TripExpense where expense_category = 'truck'
        $driver = (float) TripExpense::where('truck_id', $this->truckId)
            ->where('expense_category', 'truck')
            ->whereYear('expense_date', $this->year)
            ->whereMonth('expense_date', $this->month)
            ->sum('amount');

        // Maintenance
        $maintenance = (float) TruckMaintenanceExpense::where('truck_id', $this->truckId)
            ->where('status', 'completed')
            ->whereYear('expense_date', $this->year)
            ->whereMonth('expense_date', $this->month)
            ->sum('amount');

        // Documents
        $document = (float) TruckDocument::where('truck_id', $this->truckId)
            ->where('expense_amount', '>', 0)
            ->whereYear('expense_date', $this->year)
            ->whereMonth('expense_date', $this->month)
            ->sum('expense_amount');

        // Trip Expenses + Advances (trips filtered by start_date, no category filter)
        $tripExpenses = 0;
        $advances     = 0;

        if ($tripIds->isNotEmpty()) {
            $tripExpenses = (float) TripExpense::whereIn('trip_id', $tripIds)
                ->sum('amount');

            $advances = (float) TripAdvance::whereIn('trip_id', $tripIds)
                ->sum('amount');
        }

        return $fuel + $emi + $driver + $maintenance + $document + $tripExpenses + $advances;
    }

    // ─────────────────────────────────────────────
    // PROFIT / LOSS
    // ─────────────────────────────────────────────

    #[Computed]
    public function profitLoss()
    {
        return $this->totalRevenue - $this->totalExpenses;
    }

    #[Computed]
    public function profitLossLabel()
    {
        return $this->profitLoss >= 0 ? 'Profit' : 'Loss';
    }

    #[Computed]
    public function profitLossClass()
    {
        return $this->profitLoss >= 0 ? 'text-success' : 'text-danger';
    }

    // ─────────────────────────────────────────────
    // ACTIONS
    // ─────────────────────────────────────────────

    public function openPanel(): void
    {
        $this->truck = Truck::with('driver')->findOrFail($this->truckId);
        $this->dispatch('openMonthlyReportOffcanvas');
    }

    public function viewPdf(): void
    {
        $this->dispatch('showViewReportPdfModal');
    }

    public function downloadPdf()
    {
        return redirect()->route('trucks.monthly-report-pdf', [
            'truck' => $this->truckId,
            'month' => $this->month,
            'year'  => $this->year,
        ]);
    }

    public function getTypesProperty()
    {
        return config('truck.types');
    }

    public function render()
    {
        return view('livewire.admin.truck.monthly-report', [
            'months'              => $this->months,
            'years'               => $this->years,
            'totalTripsStarted'   => $this->totalTripsStarted,
            'lastTripKmReading'   => $this->lastTripKmReading,
            'totalRefuelQuantity' => $this->totalRefuelQuantity,
            'revenueData'         => $this->revenueData,
            'totalRevenue'        => $this->totalRevenue,
            'expensesData'        => $this->expensesData,
            'totalExpenses'       => $this->totalExpenses,
            'profitLoss'          => $this->profitLoss,
            'profitLossLabel'     => $this->profitLossLabel,
            'profitLossClass'     => $this->profitLossClass,
            'types'               => $this->types,
        ]);
    }
}
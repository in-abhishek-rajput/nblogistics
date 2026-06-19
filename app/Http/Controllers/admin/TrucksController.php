<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Truck;
use App\Models\Trip;
use App\Models\TruckEmiPayment;
use App\Models\TruckFuelExpense;
use App\Models\TruckDocument;
use App\Models\TruckMaintenanceExpense;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class TrucksController extends Controller
{
    public function index()
    {
        $total_trucks        = Truck::all()->count();
        $total_self_trucks   = Truck::where('ownership', 'self')->count();
        $total_market_trucks = Truck::where('ownership', 'market')->count();

        return view('admin.truck.list', compact('total_trucks', 'total_self_trucks', 'total_market_trucks'));
    }

    public function create() {}

    public function store(Request $request) {}

    public function show(string $id)
    {
        return view('admin.truck.view', ['truckId' => $id]);
    }

    /**
     * Monthly P&L PDF Download.
     *
     * Route: GET /trucks/{truck}/monthly-report-pdf?month=6&year=2025
     *
     * Calculation logic — ViewTruck.php se exact match:
     *
     * REVENUE:
     *   getTotalRevenueProperty() → trips->freight_amount + charges (add/reduce)
     *
     * EXPENSES:
     *   getTotalExpensesProperty() →
     *     emi         : truckEmiPayments() status=paid
     *     fuel        : truckFuelExpenses()
     *     document    : truckDocuments() expense_amount > 0
     *     driver      : truckDriverExpenses()          ← alag relationship, koi category filter nahi
     *     maintenance : truckMaintenanceExpenses() status=completed
     *     trip exp    : trip->expenses->sum('amount')  ← ALL expenses, koi category filter nahi
     *     advances    : trip->advances->sum('amount')
     */
    public function monthlyReportPdf(Request $request, int $truck)
    {
        // month/year query string se (MonthlyReport livewire downloadPdf() se aate hain)
        $monthValue = (int) ($request->query('month') ?: now()->month);
        $yearValue  = (int) ($request->query('year')  ?: now()->year);

        $truckModel = Truck::with('driver')->findOrFail($truck);

        $months = [
            1 => 'January',   2 => 'February', 3 => 'March',    4 => 'April',
            5 => 'May',       6 => 'June',      7 => 'July',     8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
        ];

        $types = config('truck.types');

        // Date range — ViewTruck ke getDateRange() jaisa (startOfMonth → endOfMonth)
        $startDate = \Illuminate\Support\Carbon::createFromDate($yearValue, $monthValue, 1)->startOfMonth();
        $endDate   = $startDate->copy()->endOfMonth();
        $range     = [$startDate, $endDate];

        // ── Summary Stats ──────────────────────────────────────────────────

        $totalTripsStarted = Trip::where('truck_id', $truckModel->id)
            ->whereBetween('start_date', $range)
            ->count();

        $tripEndKm = Trip::where('truck_id', $truckModel->id)
            ->whereBetween('start_date', $range)
            ->whereNotNull('end_km')
            ->max('end_km');

        $fuelKm = TruckFuelExpense::where('truck_id', $truckModel->id)
            ->whereBetween('expense_date', $range)
            ->whereNotNull('current_km_reading')
            ->max('current_km_reading');

        $lastTripKmReading = max($tripEndKm ?: 0, $fuelKm ?: 0);

        $totalRefuelQuantity = (float) TruckFuelExpense::where('truck_id', $truckModel->id)
            ->whereBetween('expense_date', $range)
            ->sum('fuel_quantity');

        // ── Revenue ────────────────────────────────────────────────────────
        // ViewTruck: getTotalRevenueProperty()
        //   $this->truck->trips() → whereBetween start_date → with charges
        //   totalFreight + charges (add_to_bill / reduce_from_bill)

        $trips = $truckModel->trips()
            ->whereBetween('start_date', $range)
            ->with(['charges'])
            ->orderBy('start_date')
            ->get();

        $revenueData = $trips->map(function ($trip) {
            $freight      = (float) $trip->freight_amount;
            $chargesTotal = 0;

            foreach ($trip->charges as $charge) {
                if ($charge->charge_direction === 'add_to_bill') {
                    $chargesTotal += $charge->amount;
                } elseif ($charge->charge_direction === 'reduce_from_bill') {
                    $chargesTotal -= $charge->amount;
                }
            }

            return [
                'id'             => $trip->id,
                'date'           => $trip->start_date?->format('d M') ?? '-',
                'route'          => ($trip->origin ?? '') . ' → ' . ($trip->destination ?? ''),
                'freight_amount' => $freight,
                'charges'        => $chargesTotal,
                'total'          => $freight + $chargesTotal,
            ];
        })->toArray();

        // totalRevenue — same trips collection reuse
        $totalFreight = (float) $trips->sum('freight_amount');
        $totalCharges = 0;
        foreach ($trips as $trip) {
            foreach ($trip->charges as $charge) {
                if ($charge->charge_direction === 'add_to_bill') {
                    $totalCharges += $charge->amount;
                } elseif ($charge->charge_direction === 'reduce_from_bill') {
                    $totalCharges -= $charge->amount;
                }
            }
        }
        $totalRevenue = $totalFreight + $totalCharges;

        // ── Expenses ───────────────────────────────────────────────────────
        // ViewTruck: getTotalExpensesProperty()
        // Sabhi queries model relationships se — same as ViewTruck

        // EMI — truckEmiPayments() status=paid, payment_date range
        $emiQuery = $truckModel->truckEmiPayments()
            ->where('truck_emi_payments.status', 'paid')
            ->whereBetween('payment_date', $range);

        $emiTotal    = (float) $emiQuery->sum('amount');
        $emiRows     = $emiQuery->orderBy('payment_date')->get();

        // Fuel — truckFuelExpenses(), expense_date range
        $fuelQuery   = $truckModel->truckFuelExpenses()
            ->whereBetween('expense_date', $range);

        $fuelTotal   = (float) $fuelQuery->sum('expense_amount');
        $fuelRows    = $fuelQuery->orderBy('expense_date')->get();

        // Documents — truckDocuments() expense_amount > 0, expense_date range
        $documentQuery = $truckModel->truckDocuments()
            ->where('expense_amount', '>', 0)
            ->whereBetween('expense_date', $range);

        $documentTotal = (float) $documentQuery->sum('expense_amount');
        $documentRows  = $documentQuery->orderBy('expense_date')->get();

        // Driver & Other — truckDriverExpenses(), expense_date range
        // ViewTruck mein koi category filter nahi — relationship jo bhi return kare
        $driverQuery = $truckModel->truckDriverExpenses()
            ->whereBetween('expense_date', $range);

        $driverTotal = (float) $driverQuery->sum('amount');
        $driverRows  = $driverQuery->orderBy('expense_date')->get();

        // Maintenance — truckMaintenanceExpenses() status=completed, expense_date range
        $maintenanceQuery = $truckModel->truckMaintenanceExpenses()
            ->where('status', 'completed')
            ->whereBetween('expense_date', $range);

        $maintenanceTotal = (float) $maintenanceQuery->sum('amount');
        $maintenanceRows  = $maintenanceQuery->orderBy('expense_date')->get();

        // Trip Expenses + Advances
        // ViewTruck: $trip->expenses->sum('amount') — ALL expenses, koi category filter nahi
        // ViewTruck: $trip->advances->sum('amount')
        $tripRows = $truckModel->trips()
            ->whereBetween('start_date', $range)
            ->with(['expenses', 'advances'])
            ->get();

        $tripExpensesTotal = 0;
        $advancesTotal     = 0;
        $tripExpenseRows   = collect();
        $advanceRows       = collect();

        foreach ($tripRows as $trip) {
            $tripExpensesTotal += $trip->expenses->sum('amount');
            $advancesTotal     += $trip->advances->sum('amount');
            $tripExpenseRows    = $tripExpenseRows->concat($trip->expenses);
            $advanceRows        = $advanceRows->concat($trip->advances);
        }

        // ── Total Expenses — ViewTruck jaisa exact ─────────────────────────
        $totalExpenses = $emiTotal + $fuelTotal + $documentTotal + $driverTotal
            + $maintenanceTotal + $tripExpensesTotal + $advancesTotal;

        // ── Expenses Display Data (PDF ke liye) ────────────────────────────
        $expensesData = [];

        foreach ($fuelRows as $e) {
            $expensesData['fuel'][] = [
                'date'      => $e->expense_date?->format('d M') ?? '-',
                'type'      => 'Fuel Expense',
                'shop_name' => $e->shop_name ?? $e->diesel_pump_name ?? '-',
                'amount'    => (float) $e->expense_amount,
                'quantity'  => $e->fuel_quantity
                    ? number_format($e->fuel_quantity, 2) . ' L'
                    : null,
            ];
        }

        foreach ($emiRows as $e) {
            $expensesData['emi'][] = [
                'date'      => $e->payment_date?->format('d M') ?? '-',
                'type'      => 'EMI Payment',
                'shop_name' => $e->emi->finance_company ?? '-',
                'amount'    => (float) $e->amount,
            ];
        }

        foreach ($driverRows as $e) {
            $expensesData['driver'][] = [
                'date'      => $e->expense_date?->format('d M') ?? '-',
                'type'      => $e->expense_type ?? 'Driver Expense',
                'shop_name' => $e->shop_name ?? '-',
                'amount'    => (float) $e->amount,
            ];
        }

        foreach ($maintenanceRows as $e) {
            $expensesData['maintenance'][] = [
                'date'      => $e->expense_date?->format('d M') ?? '-',
                'type'      => $e->expense_type ?? 'Maintenance',
                'shop_name' => $e->shop_name ?? '-',
                'amount'    => (float) $e->amount,
            ];
        }

        foreach ($documentRows as $e) {
            $expensesData['document'][] = [
                'date'      => $e->expense_date?->format('d M') ?? '-',
                'type'      => $e->document_name . ' Renewal',
                'shop_name' => '-',
                'amount'    => (float) $e->expense_amount,
            ];
        }

        foreach ($tripExpenseRows->sortBy('expense_date') as $e) {
            $expensesData['trip_expense'][] = [
                'date'      => $e->expense_date?->format('d M') ?? '-',
                'type'      => $e->expense_type ?? 'Trip Expense',
                'shop_name' => $e->shop_name ?? '-',
                'amount'    => (float) $e->amount,
            ];
        }

        foreach ($advanceRows as $e) {
            $expensesData['advance'][] = [
                'date'      => $e->advance_date?->format('d M') ?? '-',
                'type'      => 'Trip Advance',
                'shop_name' => $e->given_to ?? '-',
                'amount'    => (float) $e->amount,
            ];
        }

        // ── Profit / Loss ──────────────────────────────────────────────────
        $profitLoss = $totalRevenue - $totalExpenses;

        // ── PDF ────────────────────────────────────────────────────────────
        $data = [
            'truck'               => $truckModel,
            'month'               => $monthValue,
            'year'                => $yearValue,
            'monthLabel'          => $months[$monthValue] ?? 'Unknown',
            'types'               => $types,
            'totalTripsStarted'   => $totalTripsStarted,
            'lastTripKmReading'   => $lastTripKmReading ?: 0,
            'totalRefuelQuantity' => $totalRefuelQuantity,
            'revenueData'         => $revenueData,
            'totalRevenue'        => $totalRevenue,
            'expensesData'        => $expensesData,
            'totalExpenses'       => $totalExpenses,
            'profitLoss'          => $profitLoss,
            'profitLossLabel'     => $profitLoss >= 0 ? 'Profit' : 'Loss',
        ];

        $pdf = Pdf::loadView('livewire.admin.truck.monthly-report-pdf', $data)
            ->setPaper('a4', 'portrait');

        $filename = 'monthly-report-'
            . str_replace('/', '-', $truckModel->truck_number) . '-'
            . ($months[$monthValue] ?? $monthValue) . '-'
            . $yearValue . '.pdf';

        return $pdf->download($filename);
    }

    public function edit(string $id) {}

    public function update(Request $request, string $id) {}

    public function destroy(string $id) {}
}
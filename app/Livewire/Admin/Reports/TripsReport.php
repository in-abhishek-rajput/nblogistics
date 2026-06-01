<?php

namespace App\Livewire\Admin\Reports;

use App\Models\Trip;
use App\Models\TripExpense;
use Carbon\Carbon;
use Livewire\Component;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;

class TripsReport extends Component
{
    public int $month;
    public int $year;

    public array $summary = [];

    public function mount(): void
    {
        $this->month = (int) Carbon::now()->month;
        $this->year = (int) Carbon::now()->year;
        $this->fetchData();
    }

    public function updatedMonth(): void
    {
        $this->fetchData();
    }

    public function updatedYear(): void
    {
        $this->fetchData();
    }

    public function fetchData(): void
    {
        $trips = Trip::with(['party', 'truck', 'driver', 'expenses', 'advances', 'charges', 'payments'])
            ->whereMonth('start_date', $this->month)
            ->whereYear('start_date', $this->year)
            ->orderBy('start_date', 'desc')
            ->get();

        $totalTrips = $trips->count();
        $completedTrips = $trips->whereIn('status', ['completed', 'pod_received', 'pod_submitted', 'settled'])->count();
        $ongoingTrips = $trips->whereIn('status', ['pending', 'start'])->count();
        $cancelledTrips = $trips->where('status', 'cancelled')->count();

        $totalFreight = $trips->sum('freight_amount');
        $totalAdvances = $trips->sum(fn($trip) => $trip->advances->sum('amount'));
        $totalPayments = $trips->sum(fn($trip) => $trip->payments->sum('amount'));
        $totalAdditionalCharges = $trips->sum(fn($trip) => $trip->charges->where('charge_direction', 'add_to_bill')->sum('amount'));
        $totalDeductions = $trips->sum(fn($trip) => $trip->charges->where('charge_direction', 'reduce_from_bill')->sum('amount'));
        $totalPendingFreight = $trips->sum('pending_freight_amount');
        $totalExpenses = 0;
        $expenseBreakdown = [];

        $tripIds = $trips->pluck('id')->toArray();

        if (!empty($tripIds)) {
            $expenses = TripExpense::whereIn('trip_id', $tripIds)->get();
            $totalExpenses = $expenses->sum('amount');

            $expenseBreakdown = $expenses
                ->filter(fn($expense) => !empty($expense->expense_type))
                ->groupBy('expense_type')
                ->mapWithKeys(fn($group) => [
                    $group->first()->expense_type => $group->sum('amount'),
                ])
                ->toArray();
        }

        $profitLoss = $totalFreight - $totalExpenses;
        $averageTripProfit = $totalTrips > 0 ? round($profitLoss / $totalTrips, 2) : 0;

        $topRoute = $trips
            ->filter(fn($trip) => $trip->origin && $trip->destination)
            ->groupBy(fn($trip) => $trip->origin . '|' . $trip->destination)
            ->map(fn($group) => [
                'origin' => $group->first()->origin,
                'destination' => $group->first()->destination,
                'trip_count' => $group->count(),
            ])
            ->sortByDesc('trip_count')
            ->first();

        $tripRows = $trips->map(function ($trip) {
            $tripExpenses = $trip->expenses->sum('amount');
            $additionalCharges = $trip->charges->where('charge_direction', 'add_to_bill')->sum('amount');
            $deductions = $trip->charges->where('charge_direction', 'reduce_from_bill')->sum('amount');
            $netCharges = $additionalCharges - $deductions;
            $totalKm = ($trip->start_km !== null && $trip->end_km !== null)
                ? max(0, (int) $trip->end_km - (int) $trip->start_km)
                : null;

            return [
                'lr_number' => $trip->lr_number ?? '-',
                'date' => $trip->start_date?->format('d M Y') ?? '-',
                'party' => $trip->party?->name ?? $trip->party_name ?? '-',
                'truck' => $trip->truck?->truck_number ?? $trip->truck_name ?? '-',
                'driver' => $trip->driver?->name ?? $trip->driver_name ?? '-',
                'origin' => $trip->origin ?? '-',
                'destination' => $trip->destination ?? '-',
                'material_name' => $trip->material_name ?? '-',
                'billing_type' => ucwords(str_replace('_', ' ', $trip->billing_type ?? '-')),
                'unit' => $trip->unit,
                'per_unit_amount' => $trip->per_unit_amount,
                'freight_amount' => $trip->freight_amount,
                'advances' => $trip->advances->sum('amount'),
                'payments' => $trip->payments->sum('amount'),
                'additional_charges' => $additionalCharges,
                'deductions' => $deductions,
                'net_charges' => $netCharges,
                'expenses' => $tripExpenses,
                'profit' => $trip->freight_amount - $tripExpenses,
                'pending_freight_amount' => $trip->pending_freight_amount ?? 0,
                'start_km' => $trip->start_km,
                'end_km' => $trip->end_km,
                'total_km' => $totalKm,
                'status' => $trip->status,
            ];
        })->values()->toArray();

        $this->summary = [
            'total_trips' => $totalTrips,
            'completed_trips' => $completedTrips,
            'ongoing_trips' => $ongoingTrips,
            'cancelled_trips' => $cancelledTrips,
            'total_freight' => $totalFreight,
            'total_advances' => $totalAdvances,
            'total_payments' => $totalPayments,
            'total_additional_charges' => $totalAdditionalCharges,
            'total_deductions' => $totalDeductions,
            'total_net_charges' => $totalAdditionalCharges - $totalDeductions,
            'total_pending_freight' => $totalPendingFreight,
            'total_expenses' => $totalExpenses,
            'profit_loss' => $profitLoss,
            'average_trip_profit' => $averageTripProfit,
            'expense_breakdown' => $expenseBreakdown,
            'top_route' => $topRoute,
            'trip_rows' => $tripRows,
        ];
    }

    public function printReport(): void
    {
        $this->dispatch('printReport');
    }

    public function exportReport()
    {
        $data = [];
        foreach ($this->summary['trip_rows'] as $index => $trip) {
            $data[] = [
                $index + 1,
                $trip['lr_number'],
                $trip['date'],
                $trip['party'],
                $trip['truck'],
                $trip['driver'],
                $trip['origin'],
                $trip['destination'],
                $trip['material_name'],
                $trip['billing_type'],
                $trip['unit'],
                $trip['per_unit_amount'],
                $trip['freight_amount'],
                $trip['advances'],
                $trip['payments'],
                $trip['additional_charges'],
                $trip['deductions'],
                $trip['net_charges'],
                $trip['expenses'],
                $trip['profit'],
                $trip['pending_freight_amount'],
                $trip['start_km'],
                $trip['end_km'],
                $trip['total_km'],
                ucwords(str_replace('_', ' ', $trip['status'])),
            ];
        }

        return \Maatwebsite\Excel\Facades\Excel::download(new class($data) implements FromCollection, WithHeadings, WithStyles {
            protected $data;

            public function __construct($data)
            {
                $this->data = $data;
            }

            public function collection()
            {
                return collect($this->data);
            }

            public function headings(): array
            {
                return [
                    '#', 'LR Number', 'Date', 'Party', 'Truck', 'Driver', 'Origin', 'Destination',
                    'Material', 'Billing Type', 'Unit', 'Per Unit Amount',
                    'Freight (Rs)', 'Advances (Rs)', 'Payments (Rs)', 'Add Charges (Rs)',
                    'Deductions (Rs)', 'Net Charges (Rs)', 'Expenses (Rs)', 'Profit (Rs)',
                    'Pending Freight (Rs)', 'Start KM', 'End KM', 'Total KM', 'Status',
                ];
            }

            public function styles($sheet)
            {
                $sheet->getStyle('1:1')->getFont()->setBold(true);
                foreach ($sheet->getColumnIterator() as $column) {
                    $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
                }
            }
        }, 'trips-report.xlsx');
    }

    public function render()
    {
        $monthNames = [
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December',
        ];

        $currentYear = Carbon::now()->year;

        return view('livewire.admin.reports.trips-report', [
            'monthNames' => $monthNames,
            'years' => range($currentYear - 5, $currentYear + 5),
            'selectedMonth' => $this->month,
            'selectedYear' => $this->year,
            'summary' => $this->summary,
        ]);
    }
}

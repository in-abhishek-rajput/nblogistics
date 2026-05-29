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
    // ── Filter Properties (bound to blade selects via wire:model.live) ──
    public int $month;
    public int $year;

    // ── Report Data ──
    public array $summary = [];

    // ── On component load: set defaults and fetch data ──
    public function mount(): void
    {
        $this->month = (int) Carbon::now()->month;
        $this->year = (int) Carbon::now()->year;
        $this->fetchData();
    }

    // ── Re-fetch when month filter changes ──
    public function updatedMonth(): void
    {
        $this->fetchData();
    }

    // ── Re-fetch when year filter changes ──
    public function updatedYear(): void
    {
        $this->fetchData();
    }

    // ── Fetch trips from DB and calculate summary ──
    public function fetchData(): void
    {
        // Single query with eager-loaded relationships to avoid N+1
        $trips = Trip::with(['party', 'truck', 'driver', 'expenses'])
            ->whereMonth('start_date', $this->month)
            ->whereYear('start_date', $this->year)
            ->orderBy('start_date', 'desc')
            ->get();

        // ── Status counts ──
        $totalTrips = $trips->count();
        $completedTrips = $trips->whereIn('status', ['completed', 'pod_received', 'pod_submitted', 'settled'])->count();
        $ongoingTrips = $trips->whereIn('status', ['pending', 'start'])->count();
        $cancelledTrips = $trips->where('status', 'cancelled')->count();

        // ── Financial totals ──
        $totalFreight = $trips->sum('freight_amount');
        $totalExpenses = 0;
        $expenseBreakdown = [];

        $tripIds = $trips->pluck('id')->toArray();

        if (!empty($tripIds)) {
            $expenses = TripExpense::whereIn('trip_id', $tripIds)
                ->get();

            $totalExpenses = $expenses->sum('amount');

            // Group expenses by type for breakdown table
            $expenseBreakdown = $expenses
                ->filter(fn($e) => !empty($e->expense_type))
                ->groupBy('expense_type')
                ->mapWithKeys(fn($group) => [
                    $group->first()->expense_type => $group->sum('amount')
                ])
                ->toArray();
        }

        $profitLoss = $totalFreight - $totalExpenses;
        $averageTripProfit = $totalTrips > 0 ? round($profitLoss / $totalTrips, 2) : 0;

        // ── Top route by trip count ──
        $topRoute = $trips
            ->filter(fn($t) => $t->origin && $t->destination)
            ->groupBy(fn($t) => $t->origin . '|' . $t->destination)
            ->map(fn($group) => [
                'origin' => $group->first()->origin,
                'destination' => $group->first()->destination,
                'trip_count' => $group->count(),
            ])
            ->sortByDesc('trip_count')
            ->first();

        // ── Per-trip rows for overview table ──
        $tripRows = $trips->map(function ($trip) {
            $tripExpenses = $trip->expenses->sum('amount');

            return [
                'date' => $trip->start_date->format('d M Y'),
                'party' => $trip->party?->name ?? $trip->party_name ?? '—',
                'truck' => $trip->truck?->truck_number ?? $trip->truck_name ?? '—',
                'driver' => $trip->driver?->name ?? $trip->driver_name ?? '—',
                'origin' => $trip->origin ?? '—',
                'destination' => $trip->destination ?? '—',
                'freight_amount' => $trip->freight_amount,
                'expenses' => $tripExpenses,
                'profit' => $trip->freight_amount - $tripExpenses,
                'status' => $trip->status,
            ];
        })->values()->toArray();

        // ── Store everything in summary array ──
        $this->summary = [
            'total_trips' => $totalTrips,
            'completed_trips' => $completedTrips,
            'ongoing_trips' => $ongoingTrips,
            'cancelled_trips' => $cancelledTrips,
            'total_freight' => $totalFreight,
            'total_expenses' => $totalExpenses,
            'profit_loss' => $profitLoss,
            'average_trip_profit' => $averageTripProfit,
            'expense_breakdown' => $expenseBreakdown,
            'top_route' => $topRoute,
            'trip_rows' => $tripRows,
        ];
    }

    // ── Dispatch browser event to trigger window.print() ──
    public function printReport(): void
    {
        $this->dispatch('printReport');
    }

    // ── Export report data ──
    public function exportReport()
    {
        $data = [];
        foreach ($this->summary['trip_rows'] as $index => $trip) {
            $data[] = [
                $index + 1,
                $trip['date'],
                $trip['party'],
                $trip['truck'],
                $trip['driver'],
                $trip['origin'],
                $trip['destination'],
                $trip['freight_amount'],
                $trip['expenses'],
                $trip['profit'],
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
                    '#', 'Date', 'Party', 'Truck', 'Driver', 'Origin', 'Destination',
                    'Freight (₹)', 'Expenses (₹)', 'Profit (₹)', 'Status'
                ];
            }

            public function styles($sheet)
            {
                // Make heading bold
                $sheet->getStyle('1:1')->getFont()->setBold(true);
                // Auto size columns
                foreach ($sheet->getColumnIterator() as $column) {
                    $sheet->getColumnDimension($column->getColumnIndex())
                        ->setAutoSize(true);
                }
            }
        }, 'trips-report.xlsx');
    }

    // ── Pass ALL required variables explicitly to blade ──
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
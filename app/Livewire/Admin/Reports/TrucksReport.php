<?php

namespace App\Livewire\Admin\Reports;

use App\Models\Trip;
use App\Models\Truck;
use App\Models\TripExpense;
use Carbon\Carbon;
use Livewire\Component;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;

class TrucksReport extends Component
{
    public int $month;
    public int $year;

    public $trucks;
    public $summary = [];

    public function mount()
    {
        $this->month = Carbon::now()->month;
        $this->year = Carbon::now()->year;
        $this->fetchData();
    }

    public function updatedMonth()
    {
        $this->fetchData();
    }

    public function updatedYear()
    {
        $this->fetchData();
    }

    public function fetchData()
    {
        $this->trucks = Truck::with(['driver'])
            ->withCount(['trips' => function ($query) {
                $query->whereMonth('start_date', $this->month)
                    ->whereYear('start_date', $this->year);
            }])
            ->withSum(['trips' => function ($query) {
                $query->whereMonth('start_date', $this->month)
                    ->whereYear('start_date', $this->year);
            }], 'freight_amount')
            ->orderBy('truck_number')
            ->get();

        $this->calculateSummary();
    }

    public function calculateSummary()
    {
        $trucks = $this->trucks;

        $tripsForMonth = Trip::whereMonth('start_date', $this->month)
            ->whereYear('start_date', $this->year)
            ->get();

        $totalTrucks = $trucks->count();
        $activeTrucks = $trucks->where('status', 'available')->count();
        $trucksUsedInTrips = $trucks->where('trips_count', '>', 0)->count();
        $idleTrucks = $totalTrucks - $trucksUsedInTrips;

        $truckPerformance = $trucks->map(function ($truck) use ($tripsForMonth) {
            $truckTrips = $tripsForMonth->where('truck_id', $truck->id);
            $tripIds = $truckTrips->pluck('id')->toArray();

            $income = $truckTrips->sum('freight_amount');

            $expenses = !empty($tripIds)
                ? TripExpense::where('truck_id', $truck->id)->whereIn('trip_id', $tripIds)->sum('amount')
                : 0;

            $maintenanceExpenses = TripExpense::where('truck_id', $truck->id)
                ->whereNull('trip_id')
                ->whereMonth('expense_date', $this->month)
                ->whereYear('expense_date', $this->year)
                ->sum('amount');

            $profit = $income - $expenses - $maintenanceExpenses;
            $tripCount = $truckTrips->count();
            $completedTrips = $truckTrips->whereIn('status', ['completed', 'pod_received', 'pod_submitted', 'settled'])->count();
            $totalKm = $truckTrips->sum(function ($trip) {
                if ($trip->start_km === null || $trip->end_km === null) {
                    return 0;
                }

                return max(0, (int) $trip->end_km - (int) $trip->start_km);
            });

            return [
                'id' => $truck->id,
                'truck_number' => $truck->truck_number,
                'truck_type' => $truck->truck_type,
                'ownership' => $truck->ownership,
                'status' => ucwords(str_replace('_', ' ', $truck->status ?? '-')),
                'driver_name' => $truck->driver?->name ?? 'Not Assigned',
                'trips_count' => $tripCount,
                'completed_trips' => $completedTrips,
                'ongoing_trips' => $truckTrips->whereIn('status', ['pending', 'start'])->count(),
                'cancelled_trips' => $truckTrips->where('status', 'cancelled')->count(),
                'total_km' => $totalKm,
                'income' => round($income, 2),
                'expenses' => round($expenses, 2),
                'maintenance_expenses' => round($maintenanceExpenses, 2),
                'profit' => round($profit, 2),
                'avg_profit_per_trip' => $tripCount > 0 ? round($profit / $tripCount, 2) : 0,
                'expense_per_trip' => $tripCount > 0 ? round(($expenses + $maintenanceExpenses) / $tripCount, 2) : 0,
                'utilization' => $tripCount > 0 ? round(($completedTrips / $tripCount) * 100, 2) : 0,
            ];
        });

        $topEarningTruck = $truckPerformance->isNotEmpty()
            ? $truckPerformance->sortByDesc('income')->first()
            : null;

        $utilizationOverview = [
            'total_trucks' => $totalTrucks,
            'active_trucks' => $activeTrucks,
            'used_trucks' => $trucksUsedInTrips,
            'idle_trucks' => $idleTrucks,
            'utilization_percentage' => $totalTrucks > 0 ? round(($trucksUsedInTrips / $totalTrucks) * 100, 2) : 0,
        ];

        $this->summary = [
            'total_trucks' => $totalTrucks,
            'active_trucks' => $activeTrucks,
            'trucks_used_in_trips' => $trucksUsedInTrips,
            'idle_trucks' => $idleTrucks,
            'truck_performance' => $truckPerformance,
            'top_earning_truck' => $topEarningTruck,
            'utilization_overview' => $utilizationOverview,
        ];
    }

    public function printReport()
    {
        $this->dispatch('printReport');
    }

    public function exportReport()
    {
        $data = [];
        foreach ($this->summary['truck_performance'] as $truck) {
            $data[] = [
                $truck['truck_number'],
                $truck['truck_type'],
                $truck['ownership'],
                $truck['status'],
                $truck['driver_name'],
                $truck['trips_count'],
                $truck['completed_trips'],
                $truck['ongoing_trips'],
                $truck['cancelled_trips'],
                $truck['total_km'],
                $truck['income'],
                $truck['expenses'],
                $truck['maintenance_expenses'],
                $truck['profit'],
                $truck['avg_profit_per_trip'],
                $truck['expense_per_trip'],
                $truck['utilization'] . '%',
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
                    'Truck Number', 'Type', 'Ownership', 'Status', 'Assigned Driver',
                    'Trips Count', 'Completed Trips', 'Ongoing Trips', 'Cancelled Trips',
                    'Total KM', 'Income', 'Expenses', 'Maintenance', 'Profit',
                    'Avg Profit/Trip', 'Expense/Trip', 'Utilization %',
                ];
            }

            public function styles($sheet)
            {
                $sheet->getStyle('1:1')->getFont()->setBold(true);
                foreach ($sheet->getColumnIterator() as $column) {
                    $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
                }
            }
        }, 'trucks-report.xlsx');
    }

    public function getMonths()
    {
        return [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
        ];
    }

    public function getYears()
    {
        $currentYear = Carbon::now()->year;
        return range($currentYear - 5, $currentYear + 5);
    }

    public function render()
    {
        $monthNames = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
        ];

        $currentYear = Carbon::now()->year;

        return view('livewire.admin.reports.trucks-report', [
            'monthNames' => $monthNames,
            'years' => range($currentYear - 5, $currentYear + 5),
            'selectedMonth' => $this->month,
            'selectedYear' => $this->year,
            'summary' => $this->summary,
        ]);
    }
}

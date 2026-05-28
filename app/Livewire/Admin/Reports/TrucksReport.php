<?php

namespace App\Livewire\Admin\Reports;

use App\Models\Trip;
use App\Models\Truck;
use App\Models\TripExpense;
use Carbon\Carbon;
use Livewire\Component;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TrucksReport extends Component
{
    // Filter properties
    public int $month;
    public int $year;

    // Report data
    public $trucks;
    public $summary = [];

    // Mount component - set default month/year
    public function mount()
    {
        $this->month = Carbon::now()->month;
        $this->year = Carbon::now()->year;
        $this->fetchData();
    }

    // Refresh data when filters change
    public function updatedMonth()
    {
        $this->fetchData();
    }

    public function updatedYear()
    {
        $this->fetchData();
    }

    // Fetch and process report data
    public function fetchData()
    {
        // Get trucks with their trips for the selected month/year
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

        // Calculate summary
        $this->calculateSummary();
    }

    // Calculate all required metrics
    public function calculateSummary()
    {
        $trucks = $this->trucks;

        // Get all trips for the month/year for calculations
        $tripsForMonth = Trip::whereMonth('start_date', $this->month)
            ->whereYear('start_date', $this->year)
            ->get();

        // Basic counts
        $totalTrucks = $trucks->count();
        $activeTrucks = $trucks->where('status', 'available')->count();
        $trucksUsedInTrips = $trucks->where('trips_count', '>', 0)->count();
        $idleTrucks = $totalTrucks - $trucksUsedInTrips;

        // Truck-wise calculations
        $truckPerformance = $trucks->map(function ($truck) use ($tripsForMonth) {
            $truckTrips = $tripsForMonth->where('truck_id', $truck->id);
            
            // Income from trips
            $income = $truckTrips->sum('freight_amount');
            
            // Expenses for this truck (from trip_expenses where truck_id matches)
            $expenses = TripExpense::where('truck_id', $truck->id)
                ->whereIn('trip_id', $truckTrips->pluck('id')->toArray())
                ->sum('amount');
            
            // Maintenance expenses (truck expenses not tied to specific trips)
            $maintenanceExpenses = TripExpense::where('truck_id', $truck->id)
                ->whereNull('trip_id')
                ->sum('amount');
            
            $profit = $income - $expenses - $maintenanceExpenses;

            return [
                'id' => $truck->id,
                'truck_number' => $truck->truck_number,
                'truck_type' => $truck->truck_type,
                'driver_name' => $truck->driver?->name ?? 'Not Assigned',
                'trips_count' => $truckTrips->count(),
                'income' => round($income, 2),
                'expenses' => round($expenses, 2),
                'maintenance_expenses' => round($maintenanceExpenses, 2),
                'profit' => round($profit, 2),
                'utilization' => $truckTrips->count() > 0 ? 
                    (($truckTrips->whereIn('status', ['completed', 'pod_received', 'pod_submitted', 'settled'])->count() / $truckTrips->count()) * 100) : 0,
            ];
        });

        // Top earning truck
        $topEarningTruck = null;
        if ($truckPerformance->isNotEmpty()) {
            $topEarningTruck = $truckPerformance->sortByDesc('income')->first();
        }

        // Utilization overview
        $utilizationOverview = [
            'total_trucks' => $totalTrucks,
            'active_trucks' => $activeTrucks,
            'used_trucks' => $trucksUsedInTrips,
            'idle_trucks' => $idleTrucks,
            'utilization_percentage' => $totalTrucks > 0 ? 
                round(($trucksUsedInTrips / $totalTrucks) * 100, 2) : 0,
        ];

        // Store summary
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

    // Print functionality
    public function printReport()
    {
        $this->dispatch('printReport');
    }

    // Export report data
    public function exportReport()
    {
        $data = [];
        foreach ($this->summary['truck_performance'] as $truck) {
            $data[] = [
                $truck['truck_number'],
                $truck['truck_type'],
                $truck['driver_name'],
                $truck['trips_count'],
                $truck['income'],
                $truck['expenses'],
                $truck['maintenance_expenses'],
                $truck['profit'],
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
                    'Truck Number',
                    'Type',
                    'Assigned Driver',
                    'Trips Count',
                    'Income',
                    'Expenses',
                    'Maintenance',
                    'Profit',
                    'Utilization %'
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
        }, 'trucks-report.xlsx');
    }

    // Helper methods for dropdowns
    public function getMonths()
    {
        return [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
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
            'monthNames'     => $monthNames,
            'years'          => range($currentYear - 5, $currentYear + 5),
            'selectedMonth'  => $this->month,
            'selectedYear'   => $this->year,
            'summary'        => $this->summary,
        ]);
    }
}
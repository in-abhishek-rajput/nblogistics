<?php

namespace App\Livewire\Admin\Reports;

use App\Models\Trip;
use App\Models\Driver;
use Carbon\Carbon;
use Livewire\Component;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DriversReport extends Component
{
    // Filter properties
    public int $month;
    public int $year;

    // Report data
    public $drivers;
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
        // Get drivers with their trips for the selected month/year
        $this->drivers = Driver::with(['truck'])
            ->withCount(['trips' => function ($query) {
                $query->whereMonth('start_date', $this->month)
                    ->whereYear('start_date', $this->year);
            }])
            ->withSum(['trips' => function ($query) {
                $query->whereMonth('start_date', $this->month)
                    ->whereYear('start_date', $this->year);
            }], 'freight_amount')
            ->orderBy('name')
            ->get();

        // Calculate summary
        $this->calculateSummary();
    }

    // Calculate all required metrics
    public function calculateSummary()
    {
        $drivers = $this->drivers;

        // Get all trips for the month/year for calculations
        $tripsForMonth = Trip::whereMonth('start_date', $this->month)
            ->whereYear('start_date', $this->year)
            ->get();

        // Basic counts
        $totalDrivers = $drivers->count();
        $activeDrivers = $drivers->where('status', 'available')->count();
        $driversAssignedToTrips = $drivers->where('trips_count', '>', 0)->count();

        // Driver-wise calculations
        $driverPerformance = $drivers->map(function ($driver) use ($tripsForMonth) {
            $driverTrips = $tripsForMonth->where('driver_id', $driver->id);
            
            // Earnings from trips
            $earnings = $driverTrips->sum('freight_amount');
            
            // Trip count
            $tripCount = $driverTrips->count();
            
            // Average earnings per trip
            $averageEarningsPerTrip = $tripCount > 0 ? $earnings / $tripCount : 0;

            return [
                'id' => $driver->id,
                'name' => $driver->name,
                'mobile' => $driver->mobile,
                'truck_number' => $driver->truck?->truck_number ?? 'Not Assigned',
                'trips_count' => $tripCount,
                'earnings' => round($earnings, 2),
                'average_earnings_per_trip' => round($averageEarningsPerTrip, 2),
                'completed_trips' => $driverTrips->whereIn('status', ['completed', 'pod_received', 'pod_submitted', 'settled'])->count(),
                'ongoing_trips' => $driverTrips->whereIn('status', ['pending', 'start'])->count(),
            ];
        });

        // Top performing driver (by earnings)
        $topPerformingDriver = null;
        if ($driverPerformance->isNotEmpty()) {
            $topPerformingDriver = $driverPerformance->sortByDesc('earnings')->first();
        }

        // Utilization overview
        $utilizationOverview = [
            'total_drivers' => $totalDrivers,
            'active_drivers' => $activeDrivers,
            'assigned_drivers' => $driversAssignedToTrips,
            'unassigned_drivers' => $totalDrivers - $driversAssignedToTrips,
            'utilization_percentage' => $totalDrivers > 0 ? 
                round(($driversAssignedToTrips / $totalDrivers) * 100, 2) : 0,
        ];

        // Driver expense overview (if exists - from trip_expenses where paid_by_driver)
        $driverExpenses = [];
        $driverIds = $drivers->pluck('id')->toArray();
        if (!empty($driverIds)) {
            $expenses = \App\Models\TripExpense::whereIn('trip_id', $tripsForMonth->pluck('id')->toArray())
                ->where('payment_mode', 'paid_by_driver')
                ->get();
            
            $driverExpenses = $expenses->groupBy('trip.driver_id')
                ->mapWithKeys(function ($group) {
                    $driverId = $group->first()->trip->driver_id;
                    $total = $group->sum('amount');
                    return [$driverId => round($total, 2)];
                })
                ->toArray();
        }

        // Store summary
        $this->summary = [
            'total_drivers' => $totalDrivers,
            'active_drivers' => $activeDrivers,
            'drivers_assigned_to_trips' => $driversAssignedToTrips,
            'driver_performance' => $driverPerformance,
            'top_performing_driver' => $topPerformingDriver,
            'utilization_overview' => $utilizationOverview,
            'driver_expenses' => $driverExpenses,
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
        foreach ($this->summary['driver_performance'] as $driver) {
            $data[] = [
                $driver['name'],
                $driver['mobile'],
                $driver['truck_number'],
                $driver['trips_count'],
                $driver['earnings'],
                $driver['average_earnings_per_trip'],
                $driver['completed_trips'],
                $driver['ongoing_trips'],
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
                    'Driver Name',
                    'Mobile',
                    'Truck Assigned',
                    'Trips Count',
                    'Earnings',
                    'Avg. Earnings/Trip',
                    'Completed Trips',
                    'Ongoing Trips'
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
        }, 'drivers-report.xlsx');
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

        return view('livewire.admin.reports.drivers-report', [
            'monthNames'     => $monthNames,
            'years'          => range($currentYear - 5, $currentYear + 5),
            'selectedMonth'  => $this->month,
            'selectedYear'   => $this->year,
            'summary'        => $this->summary,
        ]);
    }
}
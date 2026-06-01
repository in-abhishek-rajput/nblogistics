<?php

namespace App\Livewire\Admin\Reports;

use App\Models\Trip;
use App\Models\Driver;
use App\Models\DriverAttendance;
use App\Models\DriverSalaryRecord;
use App\Models\TripExpense;
use Carbon\Carbon;
use Livewire\Component;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;

class DriversReport extends Component
{
    public int $month;
    public int $year;

    public $drivers;
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

        $this->calculateSummary();
    }

    public function calculateSummary()
    {
        $drivers = $this->drivers;

        $monthStart = Carbon::createFromDate($this->year, $this->month, 1)->startOfMonth();
        $monthEnd = Carbon::createFromDate($this->year, $this->month, 1)->endOfMonth();
        $daysInMonth = $monthStart->daysInMonth;
        $monthString = $monthStart->format('Y-m');

        $tripsForMonth = Trip::with(['expenses'])
            ->whereMonth('start_date', $this->month)
            ->whereYear('start_date', $this->year)
            ->get();

        $attendancesByDriver = DriverAttendance::whereBetween('attendance_date', [
                $monthStart->format('Y-m-d'),
                $monthEnd->format('Y-m-d'),
            ])
            ->get()
            ->groupBy('driver_id');

        $salaryRecords = DriverSalaryRecord::where('month', $monthString)
            ->get()
            ->keyBy('driver_id');

        $totalDrivers = $drivers->count();
        $activeDrivers = $drivers->where('status', 'available')->count();
        $driversAssignedToTrips = $drivers->where('trips_count', '>', 0)->count();

        $driverPerformance = $drivers->map(function ($driver) use ($tripsForMonth, $attendancesByDriver, $salaryRecords, $daysInMonth) {
            $driverTrips = $tripsForMonth->where('driver_id', $driver->id);
            $tripCount = $driverTrips->count();
            $earnings = $driverTrips->sum('freight_amount');
            $paidByDriverExpenses = $driverTrips->sum(function ($trip) {
                return $trip->expenses->where('payment_mode', 'paid_by_driver')->sum('amount');
            });
            $totalKm = $driverTrips->sum(function ($trip) {
                if ($trip->start_km === null || $trip->end_km === null) {
                    return 0;
                }

                return max(0, (int) $trip->end_km - (int) $trip->start_km);
            });
            $attendances = $attendancesByDriver->get($driver->id, collect());
            $presentDays = $attendances->where('status', 'present')->count();
            $halfDays = $attendances->where('status', 'half_day')->count();
            $holidays = $attendances->where('status', 'holiday')->count();
            $paidFullDays = $presentDays + $holidays;
            $absentDays = max(0, $daysInMonth - ($presentDays + $halfDays + $holidays));
            $paidDays = $paidFullDays + ($halfDays * 0.5);
            $baseSalary = round((float) ($driver->base_salary ?? 0), 2);
            $calculatedGrossSalary = $daysInMonth > 0 ? ($baseSalary / $daysInMonth) * $paidDays : 0;
            $salaryRecord = $salaryRecords->get($driver->id);
            $advanceDeduction = $salaryRecord ? (float) $salaryRecord->advance_deduction : 0;
            $grossSalary = $salaryRecord ? (float) $salaryRecord->gross_salary : $calculatedGrossSalary;
            $netSalary = $salaryRecord ? (float) $salaryRecord->net_salary : ($grossSalary - $advanceDeduction);
            $reportPaidDays = $salaryRecord
                ? ((float) $salaryRecord->present_days + ((float) $salaryRecord->half_days * 0.5))
                : $paidDays;

            return [
                'id' => $driver->id,
                'name' => $driver->name,
                'email' => $driver->email,
                'mobile' => $driver->mobile,
                'status' => ucwords(str_replace('_', ' ', $driver->status ?? '-')),
                'truck_number' => $driver->truck?->truck_number ?? 'Not Assigned',
                'base_salary' => $baseSalary,
                'opening_balance' => round((float) ($driver->opening_balance ?? 0), 2),
                'salary_total_days' => $salaryRecord ? (int) $salaryRecord->total_days : $daysInMonth,
                'present_days' => $salaryRecord ? (float) $salaryRecord->present_days : $paidFullDays,
                'half_days' => $salaryRecord ? (float) $salaryRecord->half_days : $halfDays,
                'absent_days' => $salaryRecord ? (float) $salaryRecord->absent_days : $absentDays,
                'paid_days' => round($reportPaidDays, 2),
                'gross_salary' => round($grossSalary, 2),
                'advance_deduction' => round($advanceDeduction, 2),
                'net_salary' => round($netSalary, 2),
                'salary_status' => $salaryRecord?->status ?? 'UNPAID',
                'trips_count' => $tripCount,
                'earnings' => round($earnings, 2),
                'paid_by_driver_expenses' => round($paidByDriverExpenses, 2),
                'net_earnings' => round($earnings - $paidByDriverExpenses, 2),
                'average_earnings_per_trip' => $tripCount > 0 ? round($earnings / $tripCount, 2) : 0,
                'completed_trips' => $driverTrips->whereIn('status', ['completed', 'pod_received', 'pod_submitted', 'settled'])->count(),
                'ongoing_trips' => $driverTrips->whereIn('status', ['pending', 'start'])->count(),
                'cancelled_trips' => $driverTrips->where('status', 'cancelled')->count(),
                'total_km' => $totalKm,
            ];
        });

        $topPerformingDriver = $driverPerformance->isNotEmpty()
            ? $driverPerformance->sortByDesc('earnings')->first()
            : null;

        $utilizationOverview = [
            'total_drivers' => $totalDrivers,
            'active_drivers' => $activeDrivers,
            'assigned_drivers' => $driversAssignedToTrips,
            'unassigned_drivers' => $totalDrivers - $driversAssignedToTrips,
            'utilization_percentage' => $totalDrivers > 0 ? round(($driversAssignedToTrips / $totalDrivers) * 100, 2) : 0,
        ];

        $driverExpenses = [];
        $tripIds = $tripsForMonth->pluck('id')->toArray();
        if (!empty($tripIds)) {
            $expenses = TripExpense::with('trip')
                ->whereIn('trip_id', $tripIds)
                ->where('payment_mode', 'paid_by_driver')
                ->get();

            $driverExpenses = $expenses
                ->filter(fn($expense) => $expense->trip && $expense->trip->driver_id)
                ->groupBy(fn($expense) => $expense->trip->driver_id)
                ->map(fn($group) => round($group->sum('amount'), 2))
                ->toArray();
        }

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

    public function printReport()
    {
        $this->dispatch('printReport');
    }

    public function exportReport()
    {
        $data = [];
        foreach ($this->summary['driver_performance'] as $driver) {
            $data[] = [
                $driver['name'],
                $driver['email'],
                $driver['mobile'],
                $driver['status'],
                $driver['truck_number'],
                $driver['base_salary'],
                $driver['opening_balance'],
                $driver['salary_total_days'],
                $driver['present_days'],
                $driver['half_days'],
                $driver['absent_days'],
                $driver['paid_days'],
                $driver['gross_salary'],
                $driver['advance_deduction'],
                $driver['net_salary'],
                $driver['salary_status'],
                $driver['trips_count'],
                $driver['completed_trips'],
                $driver['ongoing_trips'],
                $driver['cancelled_trips'],
                $driver['total_km'],
                $driver['earnings'],
                $driver['paid_by_driver_expenses'],
                $driver['net_earnings'],
                $driver['average_earnings_per_trip'],
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
                    'Driver Name', 'Email', 'Mobile', 'Status', 'Truck Assigned',
                    'Base Salary', 'Opening Balance', 'Salary Total Days', 'Present Days',
                    'Half Days', 'Absent Days', 'Paid Days', 'Gross Salary',
                    'Advance Deduction', 'Net Salary', 'Salary Status', 'Trips Count',
                    'Completed Trips', 'Ongoing Trips', 'Cancelled Trips', 'Total KM',
                    'Earnings', 'Paid By Driver Expenses', 'Net Earnings', 'Avg. Earnings/Trip',
                ];
            }

            public function styles($sheet)
            {
                $sheet->getStyle('1:1')->getFont()->setBold(true);
                foreach ($sheet->getColumnIterator() as $column) {
                    $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
                }
            }
        }, 'drivers-report.xlsx');
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

        return view('livewire.admin.reports.drivers-report', [
            'monthNames' => $monthNames,
            'years' => range($currentYear - 5, $currentYear + 5),
            'selectedMonth' => $this->month,
            'selectedYear' => $this->year,
            'summary' => $this->summary,
        ]);
    }
}

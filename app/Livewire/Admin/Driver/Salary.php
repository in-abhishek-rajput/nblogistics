<?php

namespace App\Livewire\Admin\Driver;

use App\Models\Driver;
use App\Models\DriverAdvance;
use App\Models\DriverAttendance;
use App\Models\DriverSalaryRecord;
use Carbon\Carbon;
use Livewire\Component;

class Salary extends Component
{
    public $selectedMonth;
    public $selectedYear;
    public $daysInMonth;

    public $advances = [];

    protected $rules = [
        'advances.*' => 'numeric|min:0'
    ];

    public function mount()
    {
        $this->selectedMonth = Carbon::now()->month;
        $this->selectedYear = Carbon::now()->year;
        $this->calculateDays();
    }

    public function updatedSelectedMonth()
    {
        $this->calculateDays();
        $this->loadAdvances();
    }

    public function updatedSelectedYear()
    {
        $this->calculateDays();
        $this->loadAdvances();
    }

    private function calculateDays()
    {
        $this->daysInMonth = Carbon::createFromDate($this->selectedYear, $this->selectedMonth, 1)->daysInMonth;
    }

    private function getMonthString()
    {
        return Carbon::createFromDate($this->selectedYear, $this->selectedMonth, 1)->format('Y-m');
    }

    public function loadAdvances()
    {
        $startDate = Carbon::createFromDate($this->selectedYear, $this->selectedMonth, 1)->startOfMonth()->format('Y-m-d');
        $endDate = Carbon::createFromDate($this->selectedYear, $this->selectedMonth, 1)->endOfMonth()->format('Y-m-d');

        $advancesSum = DriverAdvance::selectRaw('driver_id, SUM(amount) as total')
            ->whereBetween('advance_date', [$startDate, $endDate])
            ->groupBy('driver_id')
            ->pluck('total', 'driver_id');

        $drivers = Driver::all();
        foreach ($drivers as $driver) {
            $this->advances[$driver->id] = $advancesSum[$driver->id] ?? 0;
        }
    }

    public function saveSalary($driverId)
    {
        $this->validate();

        $monthStr = $this->getMonthString();
        $driver = Driver::find($driverId);
        
        $startDate = Carbon::createFromDate($this->selectedYear, $this->selectedMonth, 1)->startOfMonth()->format('Y-m-d');
        $endDate = Carbon::createFromDate($this->selectedYear, $this->selectedMonth, 1)->endOfMonth()->format('Y-m-d');

        $attendances = DriverAttendance::where('driver_id', $driverId)
            ->whereBetween('attendance_date', [$startDate, $endDate])
            ->get();

        $presentDays = $attendances->where('status', 'present')->count();
        $holidays = $attendances->where('status', 'holiday')->count();

        // Default absent for any day without a record or marked absent
        $absentDays = $this->daysInMonth - ($presentDays + $holidays);

        $paidDays = $presentDays + $holidays;

        $grossSalary = 0;
        if ($this->daysInMonth > 0) {
            $grossSalary = ($driver->base_salary / $this->daysInMonth) * $paidDays;
        }

        // $advance = $this->advances[$driverId] ?? 0;
        // $netSalary = $grossSalary - $advance;
        $advance = isset($this->advances[$driverId]) ? (float) $this->advances[$driverId] : 0.0;
        $netSalary = $grossSalary - $advance;

        DriverSalaryRecord::updateOrCreate(
            [
                'driver_id' => $driverId,
                'month' => $monthStr,
            ],
            [
                'total_days' => $this->daysInMonth,
                'present_days' => $presentDays + $holidays,
                'absent_days' => $absentDays,
                'half_days' => 0,
                'gross_salary' => $grossSalary,
                'advance_deduction' => $advance,
                'net_salary' => $netSalary,
            ]
        );

        session()->flash('message', 'Salary saved successfully for ' . $driver->name);
    }

    public function markAsPaid($driverId)
    {
        $monthStr = $this->getMonthString();
        $record = DriverSalaryRecord::where('driver_id', $driverId)->where('month', $monthStr)->first();
        if ($record) {
            $record->update(['status' => 'PAID']);
            session()->flash('message', 'Marked as PAID for ' . $record->driver->name);
        }
    }

    public function render()
    {
        $monthStr = $this->getMonthString();
        
        $startDate = Carbon::createFromDate($this->selectedYear, $this->selectedMonth, 1)->startOfMonth()->format('Y-m-d');
        $endDate = Carbon::createFromDate($this->selectedYear, $this->selectedMonth, 1)->endOfMonth()->format('Y-m-d');

        $drivers = Driver::with(['attendances' => function ($query) use ($startDate, $endDate) {
            $query->whereBetween('attendance_date', [$startDate, $endDate]);
        }])->orderBy('name')->get();

        $savedRecords = DriverSalaryRecord::where('month', $monthStr)->get()->keyBy('driver_id');

        if (empty($this->advances) && $drivers->count() > 0) {
            $advancesSum = DriverAdvance::selectRaw('driver_id, SUM(amount) as total')
                ->whereBetween('advance_date', [$startDate, $endDate])
                ->groupBy('driver_id')
                ->pluck('total', 'driver_id');

            foreach ($drivers as $driver) {
                if (!isset($this->advances[$driver->id])) {
                    $this->advances[$driver->id] = $advancesSum[$driver->id] ?? 0;
                }
            }
        }

        return view('livewire.admin.driver.salary', [
            'drivers' => $drivers,
            'savedRecords' => $savedRecords,
        ]);
    }
}

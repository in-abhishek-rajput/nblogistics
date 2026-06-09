<?php

namespace App\Livewire\Admin\Driver;

use App\Models\Driver;
use App\Models\DriverAdvance;
use App\Models\DriverAttendance;
use App\Models\DriverSalaryRecord;
use Carbon\Carbon;
use Livewire\Component;

class SalaryDetails extends Component
{
    public $driverId;
    public $selectedMonth;
    public $selectedYear;

    public function mount($driver)
    {
        $this->driverId = $driver;
        $this->selectedMonth = Carbon::now()->month;
        $this->selectedYear = Carbon::now()->year;
    }

    public function render()
    {
        $driver = Driver::findOrFail($this->driverId);
        
        $startDate = Carbon::createFromDate($this->selectedYear, $this->selectedMonth, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($this->selectedYear, $this->selectedMonth, 1)->endOfMonth();
        
        $monthStr = Carbon::createFromDate($this->selectedYear, $this->selectedMonth, 1)->format('Y-m');

        // Section 1: Attendance
        $attendances = DriverAttendance::where('driver_id', $this->driverId)
            ->whereBetween('attendance_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get();
            
        $presentDays = $attendances->where('status', 'present')->count();
        $holidays = $attendances->where('status', 'holiday')->count();
        
        $daysInMonth = $startDate->daysInMonth;
        $absentDays = $daysInMonth - ($presentDays + $holidays);

        $attendanceSummary = [
            'present' => $presentDays,
            'holiday' => $holidays,
            'absent' => $absentDays,
            'total' => $daysInMonth
        ];

        // Section 2: Advances
        $advances = DriverAdvance::where('driver_id', $this->driverId)
            ->whereBetween('advance_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->orderBy('advance_date', 'desc')
            ->get();
            
        $totalAdvances = $advances->sum('amount');

        // Section 3: Salary Summary
        $salaryRecord = DriverSalaryRecord::where('driver_id', $this->driverId)
            ->where('month', $monthStr)
            ->first();

        // Calculate preview if not saved
        $paidDays = $presentDays + $holidays;
        $previewGross = $daysInMonth > 0 ? ($driver->base_salary / $daysInMonth) * $paidDays : 0;
        $previewNet = $previewGross - $totalAdvances;

        return view('livewire.admin.driver.salary-details', [
            'driver' => $driver,
            'attendanceSummary' => $attendanceSummary,
            'advances' => $advances,
            'totalAdvances' => $totalAdvances,
            'salaryRecord' => $salaryRecord,
            'previewGross' => $previewGross,
            'previewNet' => $previewNet,
            'monthName' => $startDate->format('F Y'),
        ]);
    }
}

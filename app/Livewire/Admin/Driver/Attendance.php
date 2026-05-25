<?php

namespace App\Livewire\Admin\Driver;

use App\Models\Driver;
use App\Models\DriverAttendance;
use Carbon\Carbon;
use Livewire\Component;

class Attendance extends Component
{
    public $selectedMonth;
    public $selectedYear;
    public $daysInMonth;

    public function mount()
    {
        $this->selectedMonth = Carbon::now()->month;
        $this->selectedYear = Carbon::now()->year;
        $this->calculateDays();
    }

    public function updatedSelectedMonth()
    {
        $this->calculateDays();
    }

    public function updatedSelectedYear()
    {
        $this->calculateDays();
    }

    private function calculateDays()
    {
        $this->daysInMonth = Carbon::createFromDate($this->selectedYear, $this->selectedMonth, 1)->daysInMonth;
    }

    public function toggleAttendance($driverId, $day, $isChecked)
    {
        $date = Carbon::createFromDate($this->selectedYear, $this->selectedMonth, $day)->format('Y-m-d');
        
        // Prevent editing past months or future dates securely on backend
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        $currentDay = Carbon::now()->day;

        $isPastMonth = $this->selectedYear < $currentYear || ($this->selectedYear == $currentYear && $this->selectedMonth < $currentMonth);
        $isFutureMonth = $this->selectedYear > $currentYear || ($this->selectedYear == $currentYear && $this->selectedMonth > $currentMonth);
        $isFutureDate = !$isPastMonth && !$isFutureMonth && $day > $currentDay;

        if ($isPastMonth || $isFutureMonth || $isFutureDate) {
            return; // Reject modification
        }

        if ($isChecked) {
            DriverAttendance::updateOrCreate(
                [
                    'driver_id' => $driverId,
                    'attendance_date' => $date,
                ],
                [
                    'status' => 'present' // Designed to be extensible for future statuses
                ]
            );
        } else {
            DriverAttendance::where('driver_id', $driverId)
                ->where('attendance_date', $date)
                ->delete();
        }
    }

    public function render()
    {
        $startDate = Carbon::createFromDate($this->selectedYear, $this->selectedMonth, 1)->startOfMonth()->format('Y-m-d');
        $endDate = Carbon::createFromDate($this->selectedYear, $this->selectedMonth, 1)->endOfMonth()->format('Y-m-d');

        // Eager load attendances for the selected month to minimize queries
        $drivers = Driver::with(['attendances' => function ($query) use ($startDate, $endDate) {
            $query->whereBetween('attendance_date', [$startDate, $endDate]);
        }])
        ->orderBy('name')
        ->get();

        $attendanceData = [];
        $totalPresent = [];

        foreach ($drivers as $driver) {
            $attendanceData[$driver->id] = [];
            $totalPresent[$driver->id] = 0;
            
            foreach ($driver->attendances as $attendance) {
                $day = Carbon::parse($attendance->attendance_date)->day;
                $attendanceData[$driver->id][$day] = true;
                if ($attendance->status === 'present') {
                    $totalPresent[$driver->id]++;
                }
            }
        }

        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        $currentDay = Carbon::now()->day;

        $isPastMonth = $this->selectedYear < $currentYear || ($this->selectedYear == $currentYear && $this->selectedMonth < $currentMonth);
        $isFutureMonth = $this->selectedYear > $currentYear || ($this->selectedYear == $currentYear && $this->selectedMonth > $currentMonth);

        return view('livewire.admin.driver.attendance', [
            'drivers' => $drivers,
            'attendanceData' => $attendanceData,
            'totalPresent' => $totalPresent,
            'isPastMonth' => $isPastMonth,
            'isFutureMonth' => $isFutureMonth,
            'currentDay' => $currentDay,
            'currentMonth' => $currentMonth,
            'currentYear' => $currentYear,
        ]);
    }
}

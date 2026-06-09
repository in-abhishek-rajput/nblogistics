<?php

namespace App\Livewire\Admin\Driver;

use App\Models\Driver;
use App\Models\DriverAdvance;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class AdvanceHistory extends Component
{
    use WithPagination;

    public $driverId;
    public $selectedMonth;
    public $selectedYear;
    public $search = '';

    public function mount($driver)
    {
        $this->driverId = $driver;
        $this->selectedMonth = Carbon::now()->month;
        $this->selectedYear = Carbon::now()->year;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSelectedMonth()
    {
        $this->resetPage();
    }

    public function updatingSelectedYear()
    {
        $this->resetPage();
    }

    public function getAdvancesProperty()
    {
        $query = DriverAdvance::with('user')
            ->where('driver_id', $this->driverId);

        if ($this->selectedMonth && $this->selectedYear) {
            $startDate = Carbon::createFromDate($this->selectedYear, $this->selectedMonth, 1)->startOfMonth();
            $endDate = Carbon::createFromDate($this->selectedYear, $this->selectedMonth, 1)->endOfMonth();
            $query->whereBetween('advance_date', [$startDate, $endDate]);
        }

        if ($this->search) {
            $query->where('remarks', 'like', '%' . $this->search . '%');
        }

        return $query->orderBy('advance_date', 'desc')->paginate(10);
    }

    public function getTotalAdvancesProperty()
    {
        $query = DriverAdvance::where('driver_id', $this->driverId);

        if ($this->selectedMonth && $this->selectedYear) {
            $startDate = Carbon::createFromDate($this->selectedYear, $this->selectedMonth, 1)->startOfMonth();
            $endDate = Carbon::createFromDate($this->selectedYear, $this->selectedMonth, 1)->endOfMonth();
            $query->whereBetween('advance_date', [$startDate, $endDate]);
        }

        return $query->sum('amount');
    }

    public function render()
    {
        $driver = Driver::findOrFail($this->driverId);
        
        return view('livewire.admin.driver.advance-history', [
            'driver' => $driver,
            'advances' => $this->advances,
            'totalAdvances' => $this->totalAdvances,
        ]);
    }
}

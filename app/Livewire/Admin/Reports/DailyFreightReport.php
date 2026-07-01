<?php

namespace App\Livewire\Admin\Reports;

use Livewire\Component;
use App\Models\Trip;
use Carbon\Carbon;

class DailyFreightReport extends Component
{
    public $date;

    public function mount()
    {
        $this->date = Carbon::today()->format('Y-m-d');
    }

    public function render()
    {
        $trips = Trip::with(['party', 'truck', 'driver'])
            ->whereDate('start_date', $this->date)
            ->orderBy('id', 'asc')
            ->get();
            
        $totalFreight = $trips->sum('freight_amount');
        $totalNetBalance = $trips->sum('pending_freight_amount');

        return view('livewire.admin.reports.daily-freight-report', compact('trips', 'totalFreight', 'totalNetBalance'));
    }
}

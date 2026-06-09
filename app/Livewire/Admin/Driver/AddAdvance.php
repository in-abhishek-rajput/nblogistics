<?php

namespace App\Livewire\Admin\Driver;

use App\Models\Driver;
use App\Models\DriverAdvance;
use Livewire\Component;

class AddAdvance extends Component
{
    public $driverId;
    public $advance_date;
    public $amount;
    public $remarks;

    protected $rules = [
        'advance_date' => 'required|date',
        'amount' => 'required|numeric|min:0.01',
        'remarks' => 'nullable|string|max:255',
    ];

    public function mount($driverId)
    {
        $this->driverId = $driverId;
        $this->advance_date = date('Y-m-d');
    }

    public function save()
    {
        $this->validate();

        DriverAdvance::create([
            'driver_id' => $this->driverId,
            'advance_date' => $this->advance_date,
            'amount' => $this->amount,
            'remarks' => $this->remarks,
            'created_by' => auth()->id() ?? 1,
        ]);

        $this->reset(['amount', 'remarks']);
        $this->advance_date = date('Y-m-d');
        
        $this->dispatch('flashMessage', 'success', 'Advance added successfully!');
        $this->dispatch('closeModal', 'addAdvanceModal');
    }

    public function render()
    {
        $driver = Driver::find($this->driverId);
        return view('livewire.admin.driver.add-advance', [
            'driver' => $driver
        ]);
    }
}

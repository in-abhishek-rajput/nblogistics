<?php

namespace App\Livewire\Admin\Trip;

use App\Models\Trip;
use Livewire\Component;

class ViewTrip extends Component
{
    public $tripId;
    public Trip $trip;

    public function mount($tripId)
    {
        $this->tripId = $tripId;
        $this->trip = Trip::with(['party', 'truck', 'driver'])->findOrFail($tripId);
    }

    public function render()
    {
        return view('livewire.admin.trip.view-trip', [
            'trip' => $this->trip,
        ]);
    }
}
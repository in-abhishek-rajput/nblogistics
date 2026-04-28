<?php

namespace App\Livewire\Admin\Trip;

use App\Models\Driver;
use App\Models\Party;
use App\Models\Trip;
use App\Models\Truck;
use Livewire\Component;
use Livewire\Attributes\Computed;

class EditTrip extends Component
{
    public $tripId;
    public Trip $trip;

    // Form properties - public for Livewire binding
    public $party_id = null;
    public $party_name = null;
    public $truck_id = null;
    public $truck_name = null;
    public $driver_id = null;
    public $driver_name = null;
    public  $origin = null;
    public  $destination = null;
    public  $billing_type = null;
    public  $per_unit_amount = null;
    public  $unit = null;
    public  $freight_amount = null;
    public  $start_date = null;
    public  $start_km = null;
    public $lr_number = null;
    public $material_name = null;
    public $note = null;

    // Manual entry flags
    public $party_manual_entry = false;
    public $driver_manual_entry = false;
    public $truck_manual_entry = false;

    // Autocomplete search
    public $partySearch = '';
    public $driverSearch = '';
    public $truckSearch = '';

    // Preloaded lists
    public $partyList = [];
    public $driverList = [];
    public $truckList = [];

    // UI state
    public $showPartyDropdown = false;
    public $showDriverDropdown = false;
    public $showTruckDropdown = false;

    // Loading state for submit button
    public bool $saving = false;

    public function mount($tripId)
    {
        $this->tripId = $tripId;
        $this->trip = Trip::findOrFail($tripId);

        // Load preloaded lists
        $this->partyList = Party::active()
            ->orderBy('name')
            ->get(['id', 'name'])
            ->toArray();

        $this->driverList = Driver::orderBy('name')
            ->get(['id', 'name'])
            ->toArray();

        $this->truckList = Truck::orderBy('truck_number')
            ->get(['id', 'truck_number as name'])
            ->toArray();

        // Populate form with existing data
        $this->party_id = $this->trip->party_id;
        $this->party_name = $this->trip->party_name ?: ($this->trip->party ? $this->trip->party->name : null);
        $this->partySearch = $this->party_name;

        $this->truck_id = $this->trip->truck_id;
        $this->truck_name = $this->trip->truck_name ?: ($this->trip->truck ? $this->trip->truck->truck_number : null);
        $this->truckSearch = $this->truck_name;

        $this->driver_id = $this->trip->driver_id;
        $this->driver_name = $this->trip->driver_name ?: ($this->trip->driver ? $this->trip->driver->name : null);
        $this->driverSearch = $this->driver_name;

        $this->origin = $this->trip->origin;
        $this->destination = $this->trip->destination;
        $this->billing_type = $this->trip->billing_type;
        $this->per_unit_amount = $this->trip->per_unit_amount;
        $this->unit = $this->trip->unit;
        $this->freight_amount = $this->trip->freight_amount;
        $this->start_date = $this->trip->start_date?->format('Y-m-d\TH:i');
        $this->start_km = $this->trip->start_km;
        $this->lr_number = $this->trip->lr_number;
        $this->material_name = $this->trip->material_name;
        $this->note = $this->trip->note;
    }

    /**
     * Computed property for filtered party list
     */
    #[Computed]
    public function filteredParties()
    {
        if (!$this->partySearch) {
            return $this->partyList;
        }

        $search = strtolower($this->partySearch);
        return array_filter($this->partyList, function ($party) use ($search) {
            return strpos(strtolower($party['name']), $search) !== false;
        });
    }

    /**
     * Computed property for filtered driver list
     */
    #[Computed]
    public function filteredDrivers()
    {
        if (!$this->driverSearch) {
            return $this->driverList;
        }

        $search = strtolower($this->driverSearch);
        return array_filter($this->driverList, function ($driver) use ($search) {
            return strpos(strtolower($driver['name']), $search) !== false;
        });
    }

    /**
     * Computed property for filtered truck list
     */
    #[Computed]
    public function filteredTrucks()
    {
        if (!$this->truckSearch) {
            return $this->truckList;
        }

        $search = strtolower($this->truckSearch);
        return array_filter($this->truckList, function ($truck) use ($search) {
            return strpos(strtolower($truck['name']), $search) !== false;
        });
    }

    /**
     * Centralized validation rules for updating a trip.
     * Rules are dynamic and include custom messages.
     */
    protected function rules()
    {
        $rules = [
            // Either party_id OR party_name required
            'party_id' => 'nullable|exists:parties,id',
            'party_name' => 'nullable|string|max:255',
            // Either truck_id OR truck_name required
            'truck_id' => 'nullable|exists:trucks,id',
            'truck_name' => 'nullable|string|max:255',
            // Either driver_id OR driver_name required
            'driver_id' => 'nullable|exists:drivers,id',
            'driver_name' => 'nullable|string|max:255',
            'origin' => 'required|string|max:255',
            'destination' => 'required|string|max:255',
            'billing_type' => 'required|in:' . implode(',', array_keys(config('trip.billing_types'))),
            'start_date' => 'required|date',
            'start_km' => 'required|integer|min:0',
            'lr_number' => 'nullable|string|max:255',
            'material_name' => 'nullable|string|max:255',
            'note' => 'nullable|string',
        ];

        if ($this->billing_type === 'fixed') {
            $rules['freight_amount'] = 'required|numeric|min:0';
        } else {
            $rules['per_unit_amount'] = 'required|numeric|min:0';
            $rules['unit'] = 'required|numeric|min:0';
        }

        return $rules;
    }

    // Get available statuses
    public function getStatusesProperty()
    {
        return collect(config('trip.statuses'))->map(function ($status) {
            return $status['label'];
        })->toArray();
    }

    // Get available billing types
    public function getBillingTypesProperty()
    {
        return config('trip.billing_types');
    }
    public function save()
    {
        $this->saving = true;

        // Validate required: either id or name for each field
        if (!$this->party_id && !$this->party_name) {
            $this->addError('party_id', 'Party is required.');
            $this->saving = false;
            return;
        }
        if (!$this->driver_id && !$this->driver_name) {
            $this->addError('driver_id', 'Driver is required.');
            $this->saving = false;
            return;
        }
        if (!$this->truck_id && !$this->truck_name) {
            $this->addError('truck_id', 'Truck is required.');
            $this->saving = false;
            return;
        }

        // Validate input
        $validated = $this->validate();

        // Handle Party: id prioritized over name
        if ($this->party_id) {
            $validated['party_name'] = null;
            $validated['party_manual_entry'] = false;
        } else {
            $validated['party_id'] = null;
            $validated['party_manual_entry'] = true;
        }

        // Handle Driver
        if ($this->driver_id) {
            $validated['driver_name'] = null;
            $validated['driver_manual_entry'] = false;
        } else {
            $validated['driver_id'] = null;
            $validated['driver_manual_entry'] = true;
        }

        // Handle Truck
        if ($this->truck_id) {
            $validated['truck_name'] = null;
            $validated['truck_manual_entry'] = false;
        } else {
            $validated['truck_id'] = null;
            $validated['truck_manual_entry'] = true;
        }

        // Calculate freight_amount if not fixed
        if ($this->billing_type !== 'fixed') {
            $validated['freight_amount'] = $this->per_unit_amount * $this->unit;
        }

        // Set pending_freight_amount equal to freight_amount
        $validated['pending_freight_amount'] = $validated['freight_amount'];

        try {
            // Update trip using validated data
            $validated['updated_by'] = auth()->id();
            $this->trip->update($validated);

            // Emit event to refresh list
            $this->dispatch('tripUpdated');

            // Flash success message
            $this->dispatch('flashMessage', 'success', 'Trip updated successfully!')->to(\App\Livewire\Admin\Trip\ListTrips::class);

            // Close offcanvas
            $this->dispatch('closeOffcanvas', 'editTripOffcanvas');

        } catch (\Exception $e) {
            // Handle errors
            $this->dispatch('flashMessage', 'error', 'Failed to update trip. Please try again.')->to(\App\Livewire\Admin\Trip\ListTrips::class);
        } finally {
            $this->saving = false;
        }
    }

    // Get available statuses
    // public function getStatusesProperty()
    // {
    //     return collect(config('trip.statuses'))->map(function ($status) {
    //         return $status['label'];
    //     })->toArray();
    // }

    // Get available billing types
    // public function getBillingTypesProperty()
    // {
    //     return config('trip.billing_types');
    // }

    // Update freight amount when unit changes
    public function updatedUnit()
    {
        // $this->calculateFreight();
    }

    // Update freight amount when per_unit_amount changes
    public function updatedPerUnitAmount()
    {
        // $this->calculateFreight();
    }

    public function updatedBillingType()
    {
        // Reset related fields when billing type changes
        $this->per_unit_amount = null;
        $this->unit = null;
        $this->freight_amount = null;
        $this->resetErrorBag(['per_unit_amount', 'unit', 'freight_amount']);
    }

    private function calculateFreight()
    {
        if ($this->billing_type !== 'fixed' && $this->per_unit_amount && $this->unit && is_numeric($this->per_unit_amount) && is_numeric($this->unit) && $this->per_unit_amount > 0 && $this->unit > 0) {
            $this->freight_amount = $this->per_unit_amount * $this->unit;
        } else {
            $this->freight_amount = null;
        }
    }

    public function render()
    {
        return view('livewire.admin.trip.edit-trip', [
            'statuses' => $this->statuses,
            'billingTypes' => $this->billingTypes,
            'filteredParties' => $this->filteredParties,
            'filteredTrucks' => $this->filteredTrucks,
            'filteredDrivers' => $this->filteredDrivers,
        ]);
    }
}
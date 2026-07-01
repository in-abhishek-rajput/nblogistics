<?php

namespace App\Livewire\Admin\Trip;

use App\Models\Party;
use App\Models\Trip;
use App\Models\Truck;
use App\Models\Driver;
use Livewire\Component;
use Livewire\Attributes\Computed;

class AddTrip extends Component
{
    // Form properties - public for Livewire binding
    public $party_id = null;
    public $truck_id = null;
    public $driver_id = null;
    public  $origin = null;
    public  $destination = null;
    public  $billing_type = 'fixed';
     public $per_unit_amount = '';
     public $unit = '';
     public $freight_amount = 0;
     public  $start_date = null;
     public  $start_km = null;
     public  $end_date = null;
     public  $end_km = null;
     public $lr_number = null;
     public $material_name = null;
     public $note = null;

    // Manual entry names
    public $party_name = null;
    public $driver_name = null;
    public $truck_name = null;

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

    /**
     * Load preloaded lists on component mount
     */
    public function mount()
    {
        $this->partyList = Party::
        //active()
            orderBy('name')
            ->get(['id', 'name'])
            ->toArray();

        $this->driverList = Driver::orderBy('name')
            ->get(['id', 'name'])
            ->toArray();

        $this->truckList = Truck::orderBy('truck_number')
            ->get(['id', 'truck_number as name'])
            ->toArray();
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
     * Centralized validation rules for creating a trip.
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
            'start_km' => 'nullable|integer|min:0',
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

    /**
     * Custom validation messages for better UX.
     */
    protected function messages()
    {
        return [
            'party_id.required' => 'Party is required.',
            'party_id.exists' => 'Selected party does not exist.',
            'truck_id.required' => 'Truck is required.',
            'truck_id.exists' => 'Selected truck does not exist.',
            'origin.required' => 'Origin is required.',
            'destination.required' => 'Destination is required.',
            'billing_type.required' => 'Billing type is required.',
            'billing_type.in' => 'Invalid billing type selected.',
            'driver_id.required' => 'Driver is required.',
            'driver_id.exists' => 'Selected driver does not exist.',
            'freight_amount.required' => 'Freight amount is required.',
            'freight_amount.numeric' => 'Freight amount must be a number.',
            'freight_amount.min' => 'Freight amount must be positive.',
            'per_unit_amount.required' => 'Per unit amount is required.',
            'per_unit_amount.numeric' => 'Per unit amount must be a number.',
            'per_unit_amount.min' => 'Per unit amount must be positive.',
            'unit.required' => 'Unit is required.',
            'unit.numeric' => 'Unit must be a number.',
            'unit.min' => 'Unit must be positive.',
            'start_date.required' => 'Start date is required.',

            'start_km.integer' => 'Start KM must be a whole number.',
            'start_km.min' => 'Start KM must be positive.',
            'lr_number.string' => 'LR Number must be a string.',
            'lr_number.max' => 'LR Number must not exceed 255 characters.',
            'material_name.string' => 'Material Name must be a string.',
            'material_name.max' => 'Material Name must not exceed 255 characters.',
            'note.string' => 'Note must be a string.',
        ];
    }

    /**
     * Save the trip - validates and creates the record.
     */
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
            $validated['party_id'] = $this->party_id;
            $validated['party_name'] = null;
            $validated['party_manual_entry'] = false;
        } else {
            $validated['party_id'] = null;
            $validated['party_name'] = $this->party_name;
            $validated['party_manual_entry'] = true;
        }

        // Handle Driver
        if ($this->driver_id) {
            $validated['driver_id'] = $this->driver_id;
            $validated['driver_name'] = null;
            $validated['driver_manual_entry'] = false;
        } else {
            $validated['driver_id'] = null;
            $validated['driver_name'] = $this->driver_name;
            $validated['driver_manual_entry'] = true;
        }

        // Handle Truck
        if ($this->truck_id) {
            $validated['truck_id'] = $this->truck_id;
            $validated['truck_name'] = null;
            $validated['truck_manual_entry'] = false;
        } else {
            $validated['truck_id'] = null;
            $validated['truck_name'] = $this->truck_name;
            $validated['truck_manual_entry'] = true;
        }

        // Calculate freight_amount if not fixed
        if ($this->billing_type !== 'fixed') {
            $validated['freight_amount'] = $this->per_unit_amount * $this->unit;
        }

        // Set pending_freight_amount equal to freight_amount initially
        $validated['pending_freight_amount'] = $validated['freight_amount'];

        // Ensure start_km is at least 0 if empty
        if (empty($validated['start_km'])) {
            $validated['start_km'] = 0;
        }

        try {
            // Create trip using validated data
            $validated['status'] = 'start';
            $validated['created_by'] = auth()->id();
            Trip::create($validated);

            // Reset form
            $this->reset();

            // Emit event to refresh list if needed
            $this->dispatch('tripAdded');

            // Flash success message
            $this->dispatch('flashMessage', 'success', 'Trip added successfully!')->to(\App\Livewire\Admin\Trip\ListTrips::class);

            // Close offcanvas
            $this->dispatch('closeOffcanvas', 'addTripOffcanvas');

        } catch (\Exception $e) {
            // Handle errors
            $this->dispatch('flashMessage', 'error', 'Failed to add trip. Please try again.')->to(\App\Livewire\Admin\Trip\ListTrips::class);
        } finally {
            $this->saving = false; // Re-enable button
        }
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



    public function updatedBillingType()
    {
        // Reset related fields when billing type changes
        $this->per_unit_amount = '';
        $this->unit = '';
        $this->freight_amount = 0;
        $this->resetErrorBag(['per_unit_amount', 'unit', 'freight_amount']);
    }

    /**
     * Handle party search input changes - supports manual entry
     */
    public function updatedPartySearch($value)
    {
        if (empty($value)) {
            // Search cleared - reset both id and name
            $this->party_id = null;
            $this->party_name = null;
            return;
        }

        // Check if the search matches the currently selected party
        if ($this->party_id) {
            $selectedParty = collect($this->partyList)->firstWhere('id', $this->party_id);
            if ($selectedParty && strtolower($selectedParty['name']) === strtolower($value)) {
                // Search matches selected party - keep the id, clear manual name
                $this->party_name = null;
                return;
            }
        }

        // Search doesn't match selected party - treat as manual entry
        $this->party_id = null;
        $this->party_name = $value;
    }

    /**
     * Handle truck search input changes - supports manual entry
     */
    public function updatedTruckSearch($value)
    {
        if (empty($value)) {
            // Search cleared - reset both id and name
            $this->truck_id = null;
            $this->truck_name = null;
            return;
        }

        // Check if the search matches the currently selected truck
        if ($this->truck_id) {
            $selectedTruck = collect($this->truckList)->firstWhere('id', $this->truck_id);
            if ($selectedTruck && strtolower($selectedTruck['name']) === strtolower($value)) {
                // Search matches selected truck - keep the id, clear manual name
                $this->truck_name = null;
                return;
            }
        }

        // Search doesn't match selected truck - treat as manual entry
        $this->truck_id = null;
        $this->truck_name = $value;
    }

    /**
     * Handle driver search input changes - supports manual entry
     */
    public function updatedDriverSearch($value)
    {
        if (empty($value)) {
            // Search cleared - reset both id and name
            $this->driver_id = null;
            $this->driver_name = null;
            return;
        }

        // Check if the search matches the currently selected driver
        if ($this->driver_id) {
            $selectedDriver = collect($this->driverList)->firstWhere('id', $this->driver_id);
            if ($selectedDriver && strtolower($selectedDriver['name']) === strtolower($value)) {
                // Search matches selected driver - keep the id, clear manual name
                $this->driver_name = null;
                return;
            }
        }

        // Search doesn't match selected driver - treat as manual entry
        $this->driver_id = null;
        $this->driver_name = $value;
    }



    public function render()
    {
        return view('livewire.admin.trip.add-trip', [
            'statuses' => $this->statuses,
            'billingTypes' => $this->billingTypes,
            'filteredParties' => $this->filteredParties,
            'filteredTrucks' => $this->filteredTrucks,
            'filteredDrivers' => $this->filteredDrivers,
        ]);
    }
}
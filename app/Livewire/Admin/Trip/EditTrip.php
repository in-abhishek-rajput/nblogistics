<?php

namespace App\Livewire\Admin\Trip;

use App\Models\Driver;
use App\Models\Party;
use App\Models\Trip;
use App\Models\Truck;
use Livewire\Component;

class EditTrip extends Component
{
    public $tripId;
    public Trip $trip;

    // Form properties - public for Livewire binding
    public $party_id = null;
    public $truck_id = null;
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

    // Loading state for submit button
    public bool $saving = false;

    public function mount($tripId)
    {
        $this->tripId = $tripId;
        $this->trip = Trip::findOrFail($tripId);

        // Populate form with existing data
        $this->party_id = $this->trip->party_id;
        $this->truck_id = $this->trip->truck_id;
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
     * Centralized validation rules for updating a trip.
     * Rules are dynamic and include custom messages.
     */
    protected function rules()
    {
        $rules = [
            'party_id' => 'required|exists:parties,id', // Party required and must exist
            'truck_id' => 'required|exists:trucks,id', // Truck required and must exist
            'origin' => 'required|string|max:255', // Origin required
            'destination' => 'required|string|max:255', // Destination required
            'billing_type' => 'required|in:' . implode(',', array_keys(config('trip.billing_types'))), // Billing type must be valid
            'start_date' => 'required|date', // Start date required and not future
            'start_km' => 'required|integer|min:0', // Start KM required and positive
            'lr_number' => 'nullable|string|max:255', // LR Number optional
            'material_name' => 'nullable|string|max:255', // Material Name optional
            'note' => 'nullable|string', // Note optional
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
            'start_date.before_or_equal' => 'Start date cannot be in the future.',
            'start_km.required' => 'Start KM reading is required.',
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
     * Update the trip - validates and updates the record.
     */
    public function save()
    {
        $this->saving = true; // Disable button

        // Validate input
        $validated = $this->validate();

        // Calculate freight_amount if not fixed
        if ($this->billing_type !== 'fixed') {
            $validated['freight_amount'] = $this->per_unit_amount * $this->unit;
        }

        // Set pending_freight_amount equal to freight_amount initially
        $validated['pending_freight_amount'] = $validated['freight_amount'];

        try {
            // Update trip using validated data
            $validated['updated_by'] = auth()->id();
            $driver_id = Truck::find($this->truck_id)->driver_id;
            $validated['driver_id'] = $driver_id;
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

    // Get available parties
    public function getPartiesProperty()
    {
        return Party::active()->orderBy('name')->get();
    }

    // Get available trucks
    public function getTrucksProperty()
    {
        return Truck::active()->orderBy('truck_number')->get();
    }

    // Get available drivers
    public function getDriversProperty()
    {
        return Driver::active()->orderBy('name')->get();
    }

    // Update freight amount when unit changes
    public function updatedUnit()
    {
        $this->calculateFreight();
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
            'parties' => $this->parties,
            'trucks' => $this->trucks,
            'drivers' => $this->drivers,
        ]);
    }
}
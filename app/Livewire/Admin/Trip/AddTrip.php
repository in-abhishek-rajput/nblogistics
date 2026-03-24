<?php

namespace App\Livewire\Admin\Trip;

use App\Models\Party;
use App\Models\Trip;
use App\Models\Truck;
use App\Models\Driver;
use Livewire\Component;

class AddTrip extends Component
{
    // Form properties - public for Livewire binding
    public $party_id = null;
     public $truck_id = null;
     public  $origin = null;
     public  $destination = null;
     public  $billing_type = null;
     public  $freight_amount = null;
     public  $start_date = null;
     public  $start_km = null;
     public  $end_date = null;
     public  $end_km = null;
     public $lr_number = null;
     public $material_name = null;
     public $note = null;

    // Loading state for submit button
    public bool $saving = false;

    /**
     * Centralized validation rules for creating a trip.
     * Rules are dynamic and include custom messages.
     */
    protected function rules()
    {
        return [
            'party_id' => 'required|exists:parties,id', // Party required and must exist
            'truck_id' => 'required|exists:trucks,id', // Truck required and must exist
            'origin' => 'required|string|max:255', // Origin required
            'destination' => 'required|string|max:255', // Destination required
            'billing_type' => 'required|in:' . implode(',', array_keys(config('trip.billing_types'))), // Billing type must be valid
            'freight_amount' => 'required|numeric|min:0', // Freight amount required and positive
            'start_date' => 'required|date|before_or_equal:today', // Start date required and not future
            'start_km' => 'required|integer|min:0', // Start KM required and positive
            'lr_number' => 'nullable|string|max:255', // LR Number optional
            'material_name' => 'nullable|string|max:255', // Material Name optional
            'note' => 'nullable|string', // Note optional
        ];
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
            'status.required' => 'Status is required.',
            'status.in' => 'Invalid status selected.',
        ];
    }

    /**
     * Save the trip - validates and creates the record.
     */
    public function save()
    {
        $this->saving = true; // Disable button

        // Validate input
        $validated = $this->validate();

        try {
            // Create trip using validated data
            $validated['created_by'] = auth()->id();
            $driver_id = Truck::find($this->truck_id)->driver_id;
            $validated['driver_id'] = $driver_id;
            // dd($validated);
            Trip::create($validated);

            // Reset form
            $this->reset(['party_id', 'truck_id', 'origin', 'destination', 'billing_type', 'freight_amount', 'start_date', 'start_km', 'end_date', 'end_km', 'lr_number', 'material_name', 'note']);
            $this->status = config('trip.default_status');

            // Emit event to refresh list if needed
            $this->dispatch('tripAdded');

            // Flash success message
            $this->dispatch('flashMessage', 'success', 'Trip added successfully!');

            // Close offcanvas
            $this->dispatch('closeOffcanvas', 'addTripOffcanvas');

        } catch (\Exception $e) {
            // Handle errors
            session()->flash('error', 'Failed to add trip. Please try again.');
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

    public function render()
    {
        return view('livewire.admin.trip.add-trip', [
            'statuses' => $this->statuses,
            'billingTypes' => $this->billingTypes,
            'parties' => $this->parties,
            'trucks' => $this->trucks,
            'drivers' => $this->drivers,
        ]);
    }
}
<?php

namespace App\Livewire\Admin\Truck;

use App\Models\Driver;
use App\Models\Truck;
use Livewire\Component;

class EditTruck extends Component
{
    public int $truckId; // Truck ID to edit

    // Form properties - same as AddTruck
    public string $truck_number = '';
    public string $truck_type = '';
    public string $ownership = '';
    public string $status = '';
    public $driver_id = null;

    // Loading state for submit button
    public bool $saving = false;

    // Mount to load truck data
    public function mount($truckId)
    {
        $this->truckId = $truckId;
        $truck = Truck::findOrFail($truckId);

        // Populate form with existing data
        $this->truck_number = $truck->truck_number;
        $this->truck_type = $truck->truck_type;
        $this->ownership = $truck->ownership;
        $this->status = $truck->status;
        $this->driver_id = $truck->driver_id;
    }

    /**
     * Centralized validation rules for updating a truck.
     */
    protected function rules()
    {
        return [
            'truck_number' => 'required|string|max:255|unique:trucks,truck_number,' . $this->truckId, // Truck number required and unique except current
            'truck_type' => 'required|string|max:255', // Type required
            'ownership' => 'required|in:market,self', // Ownership must be market or self
            'status' => 'required|string|in:available,not_available,hold', // Status must be valid
            'driver_id' => 'nullable|exists:drivers,id', // Driver must exist if provided
        ];
    }

    /**
     * Custom validation messages for better UX.
     */
    protected function messages()
    {
        return [
            'truck_number.required' => 'Truck number is required.',
            'truck_number.unique' => 'This truck number is already registered.',
            'truck_type.required' => 'Truck type is required.',
            'ownership.required' => 'Ownership is required.',
            'status.required' => 'Status is required.',
            'driver_id.exists' => 'Selected driver does not exist.',
        ];
    }

    /**
     * Update the truck - validates and updates the record.
     */
    public function update()
    {
        $this->saving = true; // Disable button

        // Validate input
        $validated = $this->validate();

        try {
            // Update truck using validated data
            Truck::findOrFail($this->truckId)->update($validated);

            // Emit event to refresh list
            $this->dispatch('truckUpdated');

            // Flash success message
            $this->dispatch('flashMessage', 'success', 'Truck updated successfully!');

            // Close modal
            $this->dispatch('closeModal', 'editTruckModal');

        } catch (\Exception $e) {
            // Handle errors
            session()->flash('error', 'Failed to update truck. Please try again.');
        } finally {
            $this->saving = false; // Re-enable button
        }
    }

    // Get available statuses
    public function getStatusesProperty()
    {
        return collect(config('truck.statuses'))->map(function ($status) {
            return $status['label'];
        })->toArray();
    }

    // Get available types
    public function getTypesProperty()
    {
        return config('truck.types');
    }

    // Get available ownerships
    public function getOwnershipsProperty()
    {
        return config('truck.ownerships');
    }

    // Get available drivers
    public function getDriversProperty()
    {
        return Driver::active()->orderBy('name')->get();
    }

    public function render()
    {
        return view('livewire.admin.truck.edit-truck', [
            'statuses' => $this->statuses,
            'types' => $this->types,
            'ownerships' => $this->ownerships,
            'drivers' => $this->drivers,
        ]);
    }
}
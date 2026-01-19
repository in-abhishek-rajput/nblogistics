<?php

namespace App\Livewire\Admin\Truck;

use App\Models\Driver;
use App\Models\Truck;
use Livewire\Component;

class AddTruck extends Component
{
    // Form properties - public for Livewire binding
    public string $truck_number = '';
    public string $truck_type = '';
    public string $ownership = 'self';
    public string $status = 'available';
    public $driver_id = null;

    // Loading state for submit button
    public bool $saving = false;

    /**
     * Centralized validation rules for creating a truck.
     * Rules are dynamic and include custom messages.
     */
    protected function rules()
    {
        return [
            'truck_number' => 'required|string|max:255|unique:trucks,truck_number', // Truck number required and unique
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
     * Save the truck - validates and creates the record.
     */
    public function save()
    {
        $this->saving = true; // Disable button

        // Validate input
        $validated = $this->validate();

        try {
            // Create truck using validated data
            Truck::create($validated);

            // Reset form
            $this->reset(['truck_number', 'truck_type', 'driver_id']);
            $this->ownership = 'self';
            $this->status = config('truck.default_status');

            // Emit event to refresh list if needed
            $this->dispatch('truckAdded');

            // Flash success message
            $this->dispatch('flashMessage', 'success', 'Truck added successfully!');

            // Close modal
            $this->dispatch('closeModal', 'addTruckModal');

        } catch (\Exception $e) {
            // Handle errors
            session()->flash('error', 'Failed to add truck. Please try again.');
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
        return view('livewire.admin.truck.add-truck', [
            'statuses' => $this->statuses,
            'types' => $this->types,
            'ownerships' => $this->ownerships,
            'drivers' => $this->drivers,
        ]);
    }
}
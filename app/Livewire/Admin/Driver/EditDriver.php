<?php

namespace App\Livewire\Admin\Driver;

use App\Models\Driver;
use Livewire\Component;

class EditDriver extends Component
{
    public int $driverId; // Driver ID to edit

    // Form properties - same as AddDriver
    public string $name = '';
    public string $mobile = '';
    public $opening_balance = 0;

    // Loading state for submit button
    public bool $saving = false;

    // Mount to load driver data
    public function mount($driverId)
    {
        $this->driverId = $driverId;
        $driver = Driver::findOrFail($driverId);

        // Populate form with existing data
        $this->name = $driver->name;
        $this->mobile = $driver->mobile ?? '';
        $this->opening_balance = $driver->opening_balance ?? 0;
    }

    /**
     * Centralized validation rules for updating a driver.
     */
    protected function rules()
    {
        return [
            'name' => 'required|string|max:255', // Name is required for identification
            'mobile' => 'required|numeric|digits:10|unique:drivers,mobile,' . $this->driverId, // Mobile required, max length for international numbers
            'opening_balance' => 'required|numeric|min:0|max:99999999.99', // Balance must be non-negative decimal
        ];
    }

    /**
     * Custom validation messages for better UX.
     */
    protected function messages()
    {
        return [
            'name.required' => 'Driver name is required.',
            'mobile.unique' => 'This mobile is already registered.',
            'opening_balance.numeric' => 'Opening balance must be a number.',
            'opening_balance.min' => 'Opening balance cannot be negative.',
        ];
    }

    /**
     * Update the driver - validates and updates the record.
     */
    public function save()
    {
        $this->saving = true; // Disable button

        // Validate input
        $validated = $this->validate();

        try {
            // Find and update driver
            $driver = Driver::findOrFail($this->driverId);
            $driver->update($validated);

            // Emit event to refresh list if needed
            $this->dispatch('driverUpdated');

            // Flash success message
            $this->dispatch('flashMessage', 'success', 'Driver updated successfully!');

            // Close modal
            $this->dispatch('closeModal', 'editDriverModal');

        } catch (\Exception $e) {
            // Handle errors
            session()->flash('error', 'Failed to update driver. Please try again.');
        } finally {
            $this->saving = false; // Re-enable button
        }
    }

    public function render()
    {
        return view('livewire.admin.driver.edit-driver');
    }
}

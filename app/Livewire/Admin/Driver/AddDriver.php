<?php

namespace App\Livewire\Admin\Driver;

use App\Models\Driver;
use Livewire\Component;

class AddDriver extends Component
{
    // Form properties - public for Livewire binding
    public string $name = '';
    public string $mobile = '';
    public float $opening_balance = 0;

    // Loading state for submit button
    public bool $saving = false;

    // Mount to set default status
    public function mount()
    {
        $this->status = config('driver.default_status');
    }

    /**
     * Centralized validation rules for creating a driver.
     * Rules are dynamic and include custom messages.
     */
    protected function rules()
    {
        return [
            'name' => 'required|string|max:255', // Name is required for identification
            'mobile' => 'required|numeric|digits:10|unique:drivers,mobile', // Mobile, max length for international numbers
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
     * Save the driver - validates and creates the record.
     */
    public function save()
    {
        $this->saving = true; // Disable button

        // Validate input
        $validated = $this->validate();

        try {
            // Create driver using validated data
            Driver::create($validated);

            // Reset form
            $this->reset(['name', 'mobile', 'opening_balance']);
            $this->status = config('driver.default_status');

            // Emit event to refresh list if needed
            $this->dispatch('driverAdded');

            // Flash success message
            $this->dispatch('flashMessage', 'success', 'Driver added successfully!');

            // Close modal
            $this->dispatch('closeModal', 'addDriverModal');

        } catch (\Exception $e) {
            // Handle errors
            session()->flash('error', 'Failed to add driver. Please try again.');
        } finally {
            $this->saving = false; // Re-enable button
        }
    }

    // Get statuses for dropdown
    public function getStatusesProperty()
    {
        return config('driver.statuses');
    }

    public function render()
    {
        return view('livewire.admin.driver.add-driver');
    }
}

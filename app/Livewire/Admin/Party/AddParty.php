<?php

namespace App\Livewire\Admin\Party;

use App\Models\Party;
use Livewire\Component;

class AddParty extends Component
{
    // Form properties - public for Livewire binding
    public string $name = '';
    public string $mobile = '';
    public float $opening_balance = 0;
    public string $opening_balance_date = '';
    public string $status = '';

    // Loading state for submit button
    public bool $saving = false;

    // Mount to set default status
    public function mount()
    {
        $this->status = config('party.default_status');
    }

    /**
     * Centralized validation rules for creating a party.
     * Rules are dynamic and include custom messages.
     */
    protected function rules()
    {
        return [
            'name' => 'required|string|max:255', // Name is required for identification
            'mobile' => 'required|numeric|digits:10|unique:parties,mobile', // Mobile, max length for international numbers
            'opening_balance' => 'required|numeric|min:0|max:99999999.99', // Balance must be non-negative decimal
            'opening_balance_date' => 'nullable|date', // Date for opening balance
            'status' => 'required|string|in:active,inactive', // Status validation
        ];
    }

    /**
     * Custom validation messages for better UX.
     */
    protected function messages()
    {
        return [
            'name.required' => 'Party name is required.',
            'mobile.unique' => 'This mobile is already registered.',
            'opening_balance.numeric' => 'Opening balance must be a number.',
            'opening_balance.min' => 'Opening balance cannot be negative.',
            'opening_balance_date.date' => 'Opening balance date must be a valid date.',
        ];
    }

    /**
     * Save the party - validates and creates the record.
     */
    public function save()
    {
        $this->saving = true; // Disable button

        // Validate input
        $validated = $this->validate();

        try {
            // Create party using validated data
            Party::create($validated);

            // Reset form
            $this->reset(['name', 'mobile', 'opening_balance', 'opening_balance_date']);
            $this->status = config('party.default_status');

            // Emit event to refresh list if needed
            $this->dispatch('partyAdded');

            // Flash success message
            $this->dispatch('flashMessage', 'success', 'Party added successfully!');

            // Close modal
            $this->dispatch('closeModal', 'addPartyModal');

        } catch (\Exception $e) {
            // Handle errors
            session()->flash('error', 'Failed to add party. Please try again.');
        } finally {
            $this->saving = false; // Re-enable button
        }
    }


    public function render()
    {
        return view('livewire.admin.party.add-party');
    }
}

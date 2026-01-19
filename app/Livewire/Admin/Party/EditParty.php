<?php

namespace App\Livewire\Admin\Party;

use App\Models\Party;
use Livewire\Component;

class EditParty extends Component
{
    public int $partyId; // Party ID to edit

    // Form properties - same as AddParty
    public string $name = '';
    public string $mobile = '';
    public $opening_balance = 0;
    public $opening_balance_date = '';
    public string $status = '';

    // Loading state for submit button
    public bool $saving = false;

    // Mount to load party data
    public function mount($partyId)
    {
        $this->partyId = $partyId;
        $party = Party::findOrFail($partyId);

        // Populate form with existing data
        $this->name = $party->name;
        $this->mobile = $party->mobile ?? '';
        $this->opening_balance = $party->opening_balance ?? 0;
        $this->opening_balance_date = $party->opening_balance_date ? $party->opening_balance_date->format('Y-m-d') : '';
        $this->status = $party->status;
    }

    /**
     * Centralized validation rules for updating a party.
     */
    protected function rules()
    {
        return [
            'name' => 'required|string|max:255', // Name is required for identification
            'mobile' => 'required|numeric|digits:10|unique:parties,mobile,' . $this->partyId, // Mobile required, max length for international numbers
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
     * Update the party - validates and updates the record.
     */
    public function save()
    {
        $this->saving = true; // Disable button

        // Validate input
        $validated = $this->validate();

        try {
            // Find and update party
            $party = Party::findOrFail($this->partyId);
            $party->update($validated);

            // Emit event to refresh list if needed
            $this->dispatch('partyUpdated');

            // Flash success message
            $this->dispatch('flashMessage', 'success', 'Party updated successfully!');

            // Close modal
            $this->dispatch('closeModal', 'editPartyModal');

        } catch (\Exception $e) {
            // Handle errors
            session()->flash('error', 'Failed to update party. Please try again.');
        } finally {
            $this->saving = false; // Re-enable button
        }
    }

    public function render()
    {
        return view('livewire.admin.party.edit-party');
    }
}

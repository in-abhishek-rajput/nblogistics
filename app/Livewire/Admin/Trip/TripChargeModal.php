<?php

namespace App\Livewire\Admin\Trip;

use App\Models\TripCharge;
use Livewire\Component;

class TripChargeModal extends Component
{
    public int $tripId;
    public ?TripCharge $editingCharge = null;

    // Form fields
    public string $charge_direction = 'add_to_bill';
    public string $charge_type = '';
    public float $amount = 0;
    public string $date = '';
    public string $notes = '';

    // Dynamic label
    public string $chargeTypeLabel = 'Charge Type';

    // Loading state
    public bool $saving = false;

    // Charge direction options
    public array $chargeDirections = [
        'add_to_bill' => 'Add to Bill',
        'reduce_from_bill' => 'Reduce from Bill',
    ];

    // Charge type options (static for now)
    public array $chargeTypeOptions = [
        'toll' => 'Toll',
        'parking' => 'Parking',
        'loading_unloading' => 'Loading/Unloading',
        'others' => 'Others',
    ];

    // Update charge type label when direction changes
    public function updatedChargeDirection(): void
    {
        $this->chargeTypeLabel = $this->charge_direction === 'add_to_bill' ? 'Charge Type' : 'Deduction Type';
    }

    /**
     * Centralized validation rules.
     */
    protected function rules(): array
    {
        return [
            'charge_direction' => 'required|string|in:' . implode(',', array_keys($this->chargeDirections)),
            'charge_type' => 'required|string',
            'amount' => 'required|numeric|min:0.01|max:99999999.99',
            'date' => 'required|date|before_or_equal:today',
            'notes' => 'nullable|string|max:500',
        ];
    }

    /**
     * Custom validation messages.
     */
    protected function messages(): array
    {
        return [
            'charge_direction.required' => 'Charge direction is required.',
            'charge_type.required' => 'Charge type is required.',
            'amount.required' => 'Amount is required.',
            'amount.min' => 'Amount must be greater than zero.',
            'date.required' => 'Date is required.',
            'date.before_or_equal' => 'Date cannot be in the future.',
        ];
    }

    /**
     * Open modal for adding new charge.
     */
    public function openChargeModal(int $tripId): void
    {
        dd($tripId);
        $this->tripId = $tripId;
        $this->resetForm();
        $this->dispatch('show-charge-modal');
    }

    /**
     * Open modal for editing existing charge.
     */
    public function editCharge(int $chargeId, int $tripId): void
    {
        $this->tripId = $tripId;
        $this->editingCharge = TripCharge::where('trip_id', $this->tripId)->findOrFail($chargeId);

        // Populate form
        $this->charge_direction = $this->editingCharge->charge_direction;
        $this->charge_type = $this->editingCharge->charge_type;
        $this->amount = $this->editingCharge->amount;
        $this->date = $this->editingCharge->date->format('Y-m-d');
        $this->notes = $this->editingCharge->notes ?? '';

        // Update label
        $this->updatedChargeDirection();

        $this->dispatch('show-charge-modal');
    }

    /**
     * Save the charge record.
     */
    public function save(): void
    {
        $this->saving = true;

        $validated = $this->validate();

        try {
            if ($this->editingCharge) {
                // Update existing
                $this->editingCharge->update($validated);
                $message = 'Charge updated successfully.';
            } else {
                // Create new
                $validated['trip_id'] = $this->tripId;
                TripCharge::create($validated);
                $message = 'Charge added successfully.';
            }

            $this->resetForm();
            $this->dispatch('close-modals');
            $this->dispatch('flashMessage', 'success', $message)->to(\App\Livewire\Admin\Trip\ViewTrip::class);
            $this->dispatch('chargeUpdated');

        } catch (\Exception $e) {
            $this->dispatch('flashMessage', 'error', 'Failed to save charge. Please try again.')->to(\App\Livewire\Admin\Trip\ViewTrip::class);
        } finally {
            $this->saving = false;
        }
    }

    /**
     * Delete a charge record.
     */
    public function deleteCharge(int $chargeId, int $tripId): void
    {
        $this->tripId = $tripId;
        try {
            $charge = TripCharge::where('trip_id', $this->tripId)->findOrFail($chargeId);
            $charge->delete();

            $this->dispatch('flashMessage', 'success', 'Charge deleted successfully.');
            $this->dispatch('chargeUpdated');

        } catch (\Exception $e) {
            $this->dispatch('flashMessage', 'error', 'Failed to delete charge. Please try again.');
        }
    }

    /**
     * Reset form fields.
     */
    private function resetForm(): void
    {
        $this->editingCharge = null;
        $this->charge_direction = 'add_to_bill';
        $this->charge_type = '';
        $this->amount = 0;
        $this->date = '';
        $this->notes = '';
        $this->chargeTypeLabel = 'Charge Type';
        $this->resetErrorBag();
    }

    /**
     * Close modal.
     */
    public function closeModal(): void
    {
        $this->resetForm();
        $this->dispatch('close-modals');
    }

    public function render()
    {
        return view('livewire.admin.trip.trip-charge-modal');
    }
}

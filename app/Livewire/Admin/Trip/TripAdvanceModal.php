<?php

namespace App\Livewire\Admin\Trip;

use App\Models\Trip;
use App\Models\TripAdvance;
use Livewire\Component;

class TripAdvanceModal extends Component
{
    public int $tripId;
    public ?TripAdvance $editingAdvance = null;

    // Form fields
    public float $amount = 0;
    public string $payment_method = 'cash';
    public string $payment_date = '';
    public bool $received_by_driver = false;
    public string $notes = '';

    // Loading state
    public bool $saving = false;

    // Payment method options
    public array $paymentMethods = [
        'cash' => 'Cash',
        'cheque' => 'Cheque',
        'upi' => 'UPI',
        'bank_transfer' => 'Bank Transfer',
        'fuel' => 'Fuel',
        'others' => 'Others',
    ];

    protected $listeners = ['openAdvanceModal', 'editAdvance'];

    /**
     * Centralized validation rules.
     */
    protected function rules(): array
    {
        return [
            'amount' => 'required|numeric|min:0.01|max:99999999.99',
            'payment_method' => 'required|string|in:' . implode(',', array_keys($this->paymentMethods)),
            'payment_date' => 'required|date|before_or_equal:today',
            'received_by_driver' => 'boolean',
            'notes' => 'nullable|string|max:500',
        ];
    }

    /**
     * Custom validation messages.
     */
    protected function messages(): array
    {
        return [
            'amount.required' => 'Advance amount is required.',
            'amount.min' => 'Amount must be greater than zero.',
            'payment_method.required' => 'Payment method is required.',
            'payment_date.required' => 'Payment date is required.',
            'payment_date.before_or_equal' => 'Payment date cannot be in the future.',
        ];
    }

    /**
     * Open modal for adding new advance.
     */
    public function openAdvanceModal(int $tripId): void
    {
        $this->tripId = $tripId;
        $this->resetForm();
        $this->dispatch('show-advance-modal');
    }

    /**
     * Open modal for editing existing advance.
     */
    public function editAdvance(int $advanceId, int $tripId): void
    {
        $this->tripId = $tripId;
        $this->editingAdvance = TripAdvance::where('trip_id', $this->tripId)->findOrFail($advanceId);

        // Populate form
        $this->amount = $this->editingAdvance->amount;
        $this->payment_method = $this->editingAdvance->payment_method;
        $this->payment_date = $this->editingAdvance->payment_date->format('Y-m-d');
        $this->received_by_driver = $this->editingAdvance->received_by_driver;
        $this->notes = $this->editingAdvance->notes ?? '';

        $this->dispatch('show-advance-modal');
    }

    /**
     * Save the advance record.
     */
    public function save(): void
    {
        $this->saving = true;

        $validated = $this->validate();

        try {
            if ($this->editingAdvance) {
                // Update existing
                $this->editingAdvance->update($validated);
                $message = 'Advance updated successfully.';
            } else {
                // Create new
                $validated['trip_id'] = $this->tripId;
                TripAdvance::create($validated);
                $message = 'Advance added successfully.';
            }

            $this->resetForm();
            $this->dispatch('close-modals');
            $this->dispatch('flashMessage', 'success', $message)->to(\App\Livewire\Admin\Trip\ViewTrip::class);
            $this->dispatch('advanceUpdated'); // To refresh parent component

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to save advance. Please try again.');
        } finally {
            $this->saving = false;
        }
    }

    /**
     * Delete an advance record.
     */
    public function deleteAdvance(int $advanceId, int $tripId): void
    {
        $this->tripId = $tripId;
        try {
            $advance = TripAdvance::where('trip_id', $this->tripId)->findOrFail($advanceId);
            $advance->delete();

            $this->dispatch('flashMessage', 'success', 'Advance deleted successfully.')->to(\App\Livewire\Admin\Trip\ViewTrip::class);
            $this->dispatch('advanceUpdated');

        } catch (\Exception $e) {
            $this->dispatch('flashMessage', 'error', 'Failed to delete advance. Please try again.');
        }
    }

    /**
     * Reset form fields.
     */
    private function resetForm(): void
    {
        $this->editingAdvance = null;
        $this->amount = 0;
        $this->payment_method = 'cash';
        $this->payment_date = '';
        $this->received_by_driver = false;
        $this->notes = '';
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
        return view('livewire.admin.trip.trip-advance-modal');
    }
}

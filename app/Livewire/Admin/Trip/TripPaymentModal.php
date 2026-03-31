<?php

namespace App\Livewire\Admin\Trip;

use App\Models\TripPayment;
use Livewire\Component;

class TripPaymentModal extends Component
{
    public int $tripId;
    public ?TripPayment $editingPayment = null;

    // Form fields
    public float $amount = 0;
    public string $payment_method = 'cash';
    public string $payment_date = '';
    public bool $received_by_driver = false;
    public string $notes = '';

    // Loading state
    public bool $saving = false;

    protected $listeners = ['openPaymentModal', 'editPayment'];

    // Payment method options
    public array $paymentMethods = [
        'cash' => 'Cash',
        'cheque' => 'Cheque',
        'upi' => 'UPI',
        'bank_transfer' => 'Bank Transfer',
        'fuel' => 'Fuel',
        'others' => 'Others',
    ];

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
            'amount.required' => 'Payment amount is required.',
            'amount.min' => 'Amount must be greater than zero.',
            'payment_method.required' => 'Payment method is required.',
            'payment_date.required' => 'Payment date is required.',
            'payment_date.before_or_equal' => 'Payment date cannot be in the future.',
        ];
    }

    /**
     * Open modal for adding new payment.
     */
    public function openPaymentModal(int $tripId): void
    {
        $this->tripId = $tripId;
        $this->resetForm();
        $this->dispatch('show-payment-modal');
    }

    /**
     * Open modal for editing existing payment.
     */
    public function editPayment(int $paymentId, int $tripId): void
    {
        $this->tripId = $tripId;
        $this->editingPayment = TripPayment::where('trip_id', $this->tripId)->findOrFail($paymentId);

        // Populate form
        $this->amount = $this->editingPayment->amount;
        $this->payment_method = $this->editingPayment->payment_method;
        $this->payment_date = $this->editingPayment->payment_date->format('Y-m-d');
        $this->received_by_driver = $this->editingPayment->received_by_driver;
        $this->notes = $this->editingPayment->notes ?? '';

        $this->dispatch('show-payment-modal');
    }

    /**
     * Save the payment record.
     */
    public function save(): void
    {
        $this->saving = true;

        $validated = $this->validate();

        try {
            if ($this->editingPayment) {
                // Update existing
                $this->editingPayment->update($validated);
                $message = 'Payment updated successfully.';
            } else {
                // Create new
                $validated['trip_id'] = $this->tripId;
                TripPayment::create($validated);
                $message = 'Payment added successfully.';
            }

            $this->resetForm();
            $this->dispatch('close-modals');
            $this->dispatch('flashMessage', 'success', $message)->to(\App\Livewire\Admin\Trip\ViewTrip::class);
            $this->dispatch('paymentUpdated');

        } catch (\Exception $e) {
            $this->dispatch('flashMessage', 'error', 'Failed to save payment. Please try again.')->to(\App\Livewire\Admin\Trip\ViewTrip::class);
        } finally {
            $this->saving = false;
        }
    }

    /**
     * Delete a payment record.
     */
    public function deletePayment(int $paymentId, int $tripId): void
    {
        $this->tripId = $tripId;
        try {
            $payment = TripPayment::where('trip_id', $this->tripId)->findOrFail($paymentId);
            $payment->delete();

            $this->dispatch('flashMessage', 'success', 'Payment deleted successfully.');
            $this->dispatch('paymentUpdated');

        } catch (\Exception $e) {
            $this->dispatch('flashMessage', 'error', 'Failed to delete payment. Please try again.');
        }
    }

    /**
     * Reset form fields.
     */
    private function resetForm(): void
    {
        $this->editingPayment = null;
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
        return view('livewire.admin.trip.trip-payment-modal');
    }
}

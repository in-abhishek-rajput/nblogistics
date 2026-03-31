<?php

namespace App\Livewire\Admin\Trip;

use App\Models\Trip;
use App\Models\TripExpense;
use Livewire\Component;

class AddExpense extends Component
{
    // Form properties - public for Livewire binding
    public int $trip_id = 0;
    public string $expense_type = '';
    public float $amount = 0;
    public string $expense_date = '';
    public string $payment_mode = 'cash';
    public bool $add_to_party_bill = false;
    public string $notes = '';

    // Loading state for submit button
    public bool $saving = false;

    // Expense type options (dynamic)
    public array $expenseTypeOptions = [
        'toll' => 'Toll',
        'parking' => 'Parking',
        'loading_unloading' => 'Loading/Unloading',
        'fuel' => 'Fuel',
        'maintenance' => 'Maintenance',
        'others' => 'Others',
    ];

    // Payment mode options
    public array $paymentModeOptions = [
        'cash' => 'Cash',
        'credit' => 'Credit',
        'paid_by_driver' => 'Paid By Driver',
        'online' => 'Online',
    ];

    /**
     * Centralized validation rules for creating an expense.
     */
    protected function rules()
    {
        return [
            'trip_id' => 'required|exists:trips,id',
            'expense_type' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01|max:99999999.99',
            'expense_date' => 'required|date|before_or_equal:today',
            'payment_mode' => 'required|string|in:' . implode(',', array_keys($this->paymentModeOptions)),
            'add_to_party_bill' => 'boolean',
            'notes' => 'nullable|string|max:500',
        ];
    }

    /**
     * Custom validation messages for better UX.
     */
    protected function messages()
    {
        return [
            'trip_id.required' => 'Trip is required.',
            'trip_id.exists' => 'Selected trip does not exist.',
            'expense_type.required' => 'Expense type is required.',
            'amount.required' => 'Expense amount is required.',
            'amount.min' => 'Amount must be greater than zero.',
            'expense_date.required' => 'Expense date is required.',
            'expense_date.before_or_equal' => 'Expense date cannot be in the future.',
            'payment_mode.required' => 'Payment mode is required.',
        ];
    }

    /**
     * Save the expense - validates and creates the record.
     */
    public function save()
    {
        $this->saving = true; // Disable button

        // Validate input
        $validated = $this->validate();

        try {
            // Create expense using validated data
            TripExpense::create($validated);

            // Reset form
            $this->reset(['trip_id', 'expense_type', 'amount', 'expense_date', 'payment_mode', 'add_to_party_bill', 'notes']);
            $this->payment_mode = 'cash';
            $this->add_to_party_bill = false;

            // Emit event to refresh list if needed
            $this->dispatch('expenseAdded');

            // Flash success message
            $this->dispatch('flashMessage', 'success', 'Expense added successfully!');

            // Close modal
            $this->dispatch('closeModal', 'addExpenseModal');

        } catch (\Exception $e) {
            // Handle errors
            session()->flash('error', 'Failed to add expense. Please try again.');
        } finally {
            $this->saving = false; // Re-enable button
        }
    }

    // Get available trips
    public function getTripsProperty()
    {
        return Trip::with(['party', 'truck'])->orderBy('created_at', 'desc')->get();
    }

    public function render()
    {
        return view('livewire.admin.trip.add-expense', [
            'trips' => $this->trips,
            'expenseTypeOptions' => $this->expenseTypeOptions,
            'paymentModeOptions' => $this->paymentModeOptions,
        ]);
    }
}
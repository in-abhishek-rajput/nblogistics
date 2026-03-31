<?php

namespace App\Livewire\Admin\Trip;

use App\Models\Trip;
use App\Models\TripExpense;
use Livewire\Component;

class EditExpense extends Component
{
    public int $expenseId; // Expense ID to edit

    // Form properties - same as AddExpense
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

    // Mount to load expense data
    public function mount($expenseId)
    {
        $this->expenseId = $expenseId;
        $expense = TripExpense::findOrFail($expenseId);

        // Populate form with existing data
        $this->trip_id = $expense->trip_id;
        $this->expense_type = $expense->expense_type;
        $this->amount = $expense->amount;
        $this->expense_date = $expense->expense_date->format('Y-m-d');
        $this->payment_mode = $expense->payment_mode;
        $this->add_to_party_bill = $expense->add_to_party_bill;
        $this->notes = $expense->notes ?? '';
    }

    /**
     * Centralized validation rules for updating an expense.
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
     * Update the expense - validates and updates the record.
     */
    public function update()
    {
        $this->saving = true; // Disable button

        // Validate input
        $validated = $this->validate();

        try {
            // Update expense using validated data
            TripExpense::findOrFail($this->expenseId)->update($validated);

            // Emit event to refresh list
            $this->dispatch('expenseUpdated');

            // Flash success message
            $this->dispatch('flashMessage', 'success', 'Expense updated successfully!');

            // Close modal
            $this->dispatch('closeModal', 'editExpenseModal');

        } catch (\Exception $e) {
            // Handle errors
            session()->flash('error', 'Failed to update expense. Please try again.');
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
        return view('livewire.admin.trip.edit-expense', [
            'trips' => $this->trips,
            'expenseTypeOptions' => $this->expenseTypeOptions,
            'paymentModeOptions' => $this->paymentModeOptions,
        ]);
    }
}
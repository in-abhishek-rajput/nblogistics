<?php

namespace App\Livewire\Admin\Trip;

use App\Models\Trip;
use App\Models\TripExpense;
use Livewire\Component;

class TripExpenseModal extends Component
{
    public int $tripId;
    public ?TripExpense $editingExpense = null;

    // Form fields
    public string $expense_type = '';
    public float $amount = 0;
    public string $expense_date = '';
    public string $payment_mode = 'cash';
    public bool $add_to_party_bill = false;
    public string $notes = '';

    // Loading state
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

    protected $listeners = ['openExpenseModal', 'editExpense'];

    /**
     * Centralized validation rules.
     */
    protected function rules(): array
    {
        return [
            'expense_type' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01|max:99999999.99',
            'expense_date' => 'required|date|before_or_equal:today',
            'payment_mode' => 'required|string|in:' . implode(',', array_keys($this->paymentModeOptions)),
            'add_to_party_bill' => 'boolean',
            'notes' => 'nullable|string|max:500',
        ];
    }

    /**
     * Custom validation messages.
     */
    protected function messages(): array
    {
        return [
            'expense_type.required' => 'Expense type is required.',
            'amount.required' => 'Expense amount is required.',
            'amount.min' => 'Amount must be greater than zero.',
            'expense_date.required' => 'Expense date is required.',
            'expense_date.before_or_equal' => 'Expense date cannot be in the future.',
            'payment_mode.required' => 'Payment mode is required.',
        ];
    }

    /**
     * Open modal for adding new expense.
     */
    public function openExpenseModal(int $tripId): void
    {
        $this->tripId = $tripId;
        $this->resetForm();
        $this->dispatch('show-expense-modal');
    }

    /**
     * Open modal for editing existing expense.
     */
    public function editExpense(int $expenseId, int $tripId): void
    {
        $this->tripId = $tripId;
        $this->editingExpense = TripExpense::where('trip_id', $this->tripId)->findOrFail($expenseId);

        // Populate form
        $this->expense_type = $this->editingExpense->expense_type;
        $this->amount = $this->editingExpense->amount;
        $this->expense_date = $this->editingExpense->expense_date->format('Y-m-d');
        $this->payment_mode = $this->editingExpense->payment_mode;
        $this->add_to_party_bill = $this->editingExpense->add_to_party_bill;
        $this->notes = $this->editingExpense->notes ?? '';

        $this->dispatch('show-expense-modal');
    }

    /**
     * Save the expense record.
     */
    public function save(): void
    {
        $this->saving = true;

        $validated = $this->validate();

        try {
            if ($this->editingExpense) {
                // Update existing
                $this->editingExpense->update($validated);
                $message = 'Expense updated successfully.';
            } else {
                // Create new
                $validated['trip_id'] = $this->tripId;
                TripExpense::create($validated);
                $message = 'Expense added successfully.';
            }

            $this->resetForm();
            $this->dispatch('close-modals');
            $this->dispatch('flashMessage', 'success', $message)->to(\App\Livewire\Admin\Trip\ViewTrip::class);
            $this->dispatch('expenseUpdated'); // To refresh parent component

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to save expense. Please try again.');
        } finally {
            $this->saving = false;
        }
    }

    /**
     * Delete an expense record.
     */
    public function deleteExpense(int $expenseId, int $tripId): void
    {
        $this->tripId = $tripId;
        try {
            $expense = TripExpense::where('trip_id', $this->tripId)->findOrFail($expenseId);
            $expense->delete();

            $this->dispatch('flashMessage', 'success', 'Expense deleted successfully.')->to(\App\Livewire\Admin\Trip\ViewTrip::class);
            $this->dispatch('expenseUpdated');

        } catch (\Exception $e) {
            $this->dispatch('flashMessage', 'error', 'Failed to delete expense. Please try again.');
        }
    }

    /**
     * Reset form fields.
     */
    private function resetForm(): void
    {
        $this->editingExpense = null;
        $this->expense_type = '';
        $this->amount = 0;
        $this->expense_date = '';
        $this->payment_mode = 'cash';
        $this->add_to_party_bill = false;
        $this->notes = '';
    }

    /**
     * Close modal.
     */
    public function closeModal(): void
    {
        $this->resetForm();
        $this->dispatch('close-modals');
    }
}
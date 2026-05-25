<?php

namespace App\Livewire\Admin\Trip;

use App\Models\Trip;
use App\Models\TripExpense;
use App\Models\Truck;
use Livewire\Component;

class AddExpense extends Component
{
    // Form properties - public for Livewire binding
    public string $expense_category = 'trip';
    public ?int $trip_id = null;
    public ?int $truck_id = null;
    public string $expense_type = '';
    public float $amount = 0;
    public string $expense_date = '';
    public string $payment_mode = 'cash';
    public bool $add_to_party_bill = false;
    public string $notes = '';

    // Loading state for submit button
    public bool $saving = false;

    // Expense types mapping from JSON
    public array $expenseTypesMap = [];
    public array $expenseTypeOptions = [];

    // Payment mode options
    public array $paymentModeOptions = [
        'cash' => 'Cash',
        'credit' => 'Credit',
        'paid_by_driver' => 'Paid By Driver',
        'online' => 'Online',
    ];

    public function mount()
    {
        // Load expense types from JSON
        $jsonPath = public_path('js/expense_types.json');
        if (file_exists($jsonPath)) {
            $this->expenseTypesMap = json_decode(file_get_contents($jsonPath), true) ?? [];
        }

        // Set initial options based on default category
        $this->expenseTypeOptions = $this->expenseTypesMap[$this->expense_category] ?? [];
    }

    public function updatedExpenseCategory($value)
    {
        // Auto update expense_type options from JSON mapping when category changes
        $this->expenseTypeOptions = $this->expenseTypesMap[$value] ?? [];
        
        // Fallback: If category not found or changed, reset type
        $this->expense_type = '';
        
        // Reset related IDs
        if ($value === 'trip') {
            $this->truck_id = null;
        } elseif ($value === 'truck') {
            $this->trip_id = null;
        } else {
            $this->trip_id = null;
            $this->truck_id = null;
        }
    }

    /**
     * Centralized validation rules for creating an expense.
     */
    protected function rules()
    {
        return [
            'expense_category' => 'required|in:trip,truck,office',
            'trip_id' => 'required_if:expense_category,trip|nullable|exists:trips,id',
            'truck_id' => 'required_if:expense_category,truck|nullable|exists:trucks,id',
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
            'trip_id.required_if' => 'Trip is required when expense is for a trip.',
            'truck_id.required_if' => 'Truck is required when expense is for a truck.',
            'trip_id.exists' => 'Selected trip does not exist.',
            'truck_id.exists' => 'Selected truck does not exist.',
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
            // Nullify unused IDs
            if ($validated['expense_category'] === 'trip') {
                $validated['truck_id'] = null;
            } elseif ($validated['expense_category'] === 'truck') {
                $validated['trip_id'] = null;
            } else {
                $validated['trip_id'] = null;
                $validated['truck_id'] = null;
            }

            // Create expense using validated data
            TripExpense::create($validated);

            // Reset form
            $this->reset(['expense_category', 'trip_id', 'truck_id', 'expense_type', 'amount', 'expense_date', 'payment_mode', 'add_to_party_bill', 'notes']);
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

    // Get available trucks
    public function getTrucksProperty()
    {
        return Truck::orderBy('truck_number', 'asc')->get();
    }

    public function render()
    {
        return view('livewire.admin.trip.add-expense', [
            'trips' => $this->trips,
            'trucks' => $this->trucks,
            'expenseTypeOptions' => $this->expenseTypeOptions,
            'paymentModeOptions' => $this->paymentModeOptions,
        ]);
    }
}
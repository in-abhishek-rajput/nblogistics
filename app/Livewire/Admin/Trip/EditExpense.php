<?php

namespace App\Livewire\Admin\Trip;

use App\Models\Trip;
use App\Models\TripExpense;
use App\Models\Truck;
use Livewire\Component;

class EditExpense extends Component
{
    public int $expenseId; // Expense ID to edit

    // Form properties - same as AddExpense
    public string $expense_category = 'trip';
    public ?int $trip_id = null;
    public ?int $truck_id = null;
    public string $expense_type = '';
    public float $amount = 0;
    public string $expense_date = '';
    public string $payment_mode = 'cash';
    public bool $add_to_party_bill = false;
    public string $notes = '';

    // Autocomplete search
    public $tripSearch = '';
    public $truckSearch = '';
    public $expenseTypeSearch = '';

    // Preloaded lists
    public $tripList = [];
    public $truckList = [];

    // UI state
    public $showTripDropdown = false;
    public $showTruckDropdown = false;
    public $showExpenseTypeDropdown = false;

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

    // Mount to load expense data
    public function mount($expenseId)
    {
        $this->expenseId = $expenseId;
        $expense = TripExpense::findOrFail($expenseId);

        // Populate form with existing data
        $this->expense_category = $expense->expense_category;
        $this->trip_id = $expense->trip_id;
        $this->truck_id = $expense->truck_id;
        $this->expense_type = $expense->expense_type;
        $this->amount = $expense->amount;
        $this->expense_date = $expense->expense_date->format('Y-m-d');
        $this->payment_mode = $expense->payment_mode;
        $this->add_to_party_bill = $expense->add_to_party_bill;
        $this->notes = $expense->notes ?? '';

        $this->tripList = Trip::with(['party', 'truck'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($trip) {
                return [
                    'id' => $trip->id,
                    'name' => ($trip->party->name ?? 'N/A') . ' - ' . ($trip->origin ?? 'N/A') . ' → ' . ($trip->destination ?? 'N/A'),
                ];
            })
            ->toArray();

        $this->truckList = Truck::orderBy('truck_number', 'asc')
            ->get()
            ->map(function ($truck) {
                return [
                    'id' => $truck->id,
                    'name' => $truck->truck_number,
                ];
            })
            ->toArray();

        $this->tripSearch = collect($this->tripList)->firstWhere('id', $this->trip_id)['name'] ?? '';
        $this->truckSearch = collect($this->truckList)->firstWhere('id', $this->truck_id)['name'] ?? '';

        // Load expense types from JSON
        $jsonPath = public_path('js/expense_types.json');
        if (file_exists($jsonPath)) {
            $this->expenseTypesMap = json_decode(file_get_contents($jsonPath), true) ?? [];
        }

        // Set initial options based on loaded category
        $this->expenseTypeOptions = $this->normalizeExpenseOptions($this->expenseTypesMap[$this->expense_category] ?? []);
        $this->expenseTypeSearch = $this->expense_type;
    }

    public function updatedExpenseCategory($value)
    {
        // Auto update expense_type options from JSON mapping when category changes
        $this->expenseTypeOptions = $this->normalizeExpenseOptions($this->expenseTypesMap[$value] ?? []);
        
        // Fallback: If category not found or changed, reset type
        $this->expense_type = '';
        $this->expenseTypeSearch = '';
        $this->tripSearch = '';
        $this->truckSearch = '';
        
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

    public function updatedTripSearch($value)
    {
        if (empty($value)) {
            $this->trip_id = null;
            return;
        }

        $selectedTrip = collect($this->tripList)->firstWhere('name', $value);
        $this->trip_id = $selectedTrip['id'] ?? null;
    }

    public function updatedTruckSearch($value)
    {
        if (empty($value)) {
            $this->truck_id = null;
            return;
        }

        $selectedTruck = collect($this->truckList)->firstWhere('name', $value);
        $this->truck_id = $selectedTruck['id'] ?? null;
    }

    public function updatedExpenseTypeSearch($value)
    {
        $this->expense_type = $value;
    }

    private function normalizeExpenseOptions(array $options): array
    {
        $options = array_values(array_unique($options));

        return array_combine($options, $options) ?: [];
    }

    /**
     * Centralized validation rules for updating an expense.
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
     * Update the expense - validates and updates the record.
     */
    public function update()
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

    // Get available trucks
    public function getTrucksProperty()
    {
        return Truck::orderBy('truck_number', 'asc')->get();
    }

    public function render()
    {
        return view('livewire.admin.trip.edit-expense', [
            'filteredTrips' => $this->filteredTrips,
            'filteredTrucks' => $this->filteredTrucks,
            'filteredExpenseTypes' => $this->filteredExpenseTypes,
            'expenseTypeOptions' => $this->expenseTypeOptions,
            'paymentModeOptions' => $this->paymentModeOptions,
        ]);
    }

    public function getFilteredTripsProperty()
    {
        if (!$this->tripSearch) {
            return $this->tripList;
        }

        $search = strtolower($this->tripSearch);
        return array_filter($this->tripList, function ($trip) use ($search) {
            return strpos(strtolower($trip['name']), $search) !== false;
        });
    }

    public function getFilteredTrucksProperty()
    {
        if (!$this->truckSearch) {
            return $this->truckList;
        }

        $search = strtolower($this->truckSearch);
        return array_filter($this->truckList, function ($truck) use ($search) {
            return strpos(strtolower($truck['name']), $search) !== false;
        });
    }

    public function getFilteredExpenseTypesProperty()
    {
        if (!$this->expenseTypeSearch) {
            return array_values($this->expenseTypeOptions);
        }

        $search = strtolower($this->expenseTypeSearch);
        return array_filter(
            array_values($this->expenseTypeOptions),
            function ($type) use ($search) {
                return strpos(strtolower($type), $search) !== false;
            }
        );
    }
}

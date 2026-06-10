<?php

namespace App\Livewire\Admin\Truck;

use App\Models\Driver;
use App\Models\Truck;
use App\Models\TruckFuelExpense;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class FuelBook extends Component
{
    use WithPagination;
    use WithFileUploads;

    protected $paginationTheme = 'bootstrap';

    public int $truckId;
    public Truck $truck;

    public string $monthFilter = 'all';
    public string $custom_from = '';
    public string $custom_to = '';

    public string $expense_amount = '';
    public string $fuel_quantity = '';
    public string $rate_per_litre = '';
    public bool $is_full_tank = false;
    public string $current_km_reading = '';
    public string $payment_mode = 'cash';
    public string $shop_name = '';
    public string $driver_search = '';
    public string $custom_driver_name = '';
    public ?int $driver_id = null;
    public string $transaction_id = '';
    public string $diesel_pump_name = '';
    public string $expense_date = '';
    public $bill_file = null;
    public string $remarks = '';

    public ?int $editingExpenseId = null;

    public int $perPage = 8;

    public bool $showDriverDropdown = false;
    public array $driverList = [];

    public array $monthOptions = [
        'all' => 'All Months',
        'current' => 'Current Month',
        'previous' => 'Previous Month',
        'three' => 'Last 3 Months',
        'six' => 'Last 6 Months',
        'custom' => 'Custom Range',
    ];

    public array $paymentModeOptions = [
        'cash' => 'Cash',
        'credit' => 'Credit',
        'paid_by_driver' => 'Paid By Driver',
        'online' => 'Online',
    ];

    protected $listeners = [
        'openFuelBookPanel' => 'openPanel',
    ];

    #[Computed]
    public function filteredDrivers()
    {
        if (!$this->driver_search) {
            return $this->driverList;
        }

        $search = strtolower($this->driver_search);
        return array_filter($this->driverList, function ($driver) use ($search) {
            return strpos(strtolower($driver['name']), $search) !== false;
        });
    }

    public function mount(int $truckId)
    {
        $this->truckId = $truckId;
        $this->driverList = Driver::orderBy('name')
            ->get(['id', 'name'])
            ->toArray();
        $this->refreshData();
    }

    public function refreshData(): void
    {
        $this->truck = Truck::with('driver')->findOrFail($this->truckId);
        $this->resetPage();
    }

    public function openPanel(): void
    {
        $this->refreshData();
        $this->dispatch('openFuelBookOffcanvas');
    }

    public function updatingMonthFilter(): void
    {
        $this->resetPage();
    }

    public function showAddFuelExpenseModal(): void
    {
        $this->reset([
            'expense_amount',
            'fuel_quantity',
            'rate_per_litre',
            'is_full_tank',
            'current_km_reading',
            'payment_mode',
            'shop_name',
            'driver_search',
            'driver_id',
            'custom_driver_name',
            'transaction_id',
            'diesel_pump_name',
            'expense_date',
            'bill_file',
            'remarks',
            'editingExpenseId',
        ]);

        $this->payment_mode = 'cash';
        $this->showDriverDropdown = false;
        $this->dispatch('showAddFuelExpenseModal');
    }

    public function createFuelExpense(): void
    {
        $this->validate($this->expenseValidationRules());

        [$driverId, $driverName] = $this->resolveDriverData();

        $billPath = $this->storeBillFile();

        TruckFuelExpense::create([
            'truck_id' => $this->truckId,
            'expense_amount' => $this->expense_amount,
            'fuel_quantity' => $this->fuel_quantity ?: null,
            'rate_per_litre' => $this->rate_per_litre ?: null,
            'is_full_tank' => $this->is_full_tank,
            'current_km_reading' => $this->current_km_reading ?: null,
            'payment_mode' => $this->payment_mode,
            'shop_name' => $this->shop_name ?: null,
            'driver_id' => $driverId,
            'driver_name' => $driverName,
            'transaction_id' => $this->transaction_id ?: null,
            'diesel_pump_name' => $this->diesel_pump_name ?: null,
            'expense_date' => $this->expense_date,
            'bill_file' => $billPath,
            'remarks' => $this->remarks ?: null,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        $this->dispatch('fuelBookUpdated');
        $this->dispatch('closeModal', 'addFuelExpenseModal');
        $this->dispatch('openFuelBookOffcanvas');
        $this->refreshData();
    }

    public function editFuelExpense(int $expenseId): void
    {
        $expense = TruckFuelExpense::findOrFail($expenseId);

        $this->editingExpenseId = $expense->id;
        $this->expense_amount = (string) $expense->expense_amount;
        $this->fuel_quantity = $expense->fuel_quantity !== null ? (string) $expense->fuel_quantity : '';
        $this->rate_per_litre = $expense->rate_per_litre !== null ? (string) $expense->rate_per_litre : '';
        $this->is_full_tank = (bool) $expense->is_full_tank;
        $this->current_km_reading = $expense->current_km_reading !== null ? (string) $expense->current_km_reading : '';
        $this->payment_mode = $expense->payment_mode;
        $this->shop_name = $expense->shop_name ?? '';
        $this->transaction_id = $expense->transaction_id ?? '';
        $this->diesel_pump_name = $expense->diesel_pump_name ?? '';
        $this->expense_date = $expense->expense_date?->format('Y-m-d') ?? '';
        $this->remarks = $expense->remarks ?? '';

        if ($expense->payment_mode === 'paid_by_driver') {
            if ($expense->driver_id) {
                $this->driver_id = $expense->driver_id;
                $this->driver_search = Driver::find($expense->driver_id)?->name ?? '';
                $this->custom_driver_name = '';
            } else {
                $this->driver_id = null;
                $this->driver_search = '';
                $this->custom_driver_name = $expense->driver_name ?? '';
            }
        } else {
            $this->driver_id = null;
            $this->driver_search = '';
            $this->custom_driver_name = '';
        }

        $this->showDriverDropdown = false;
        $this->dispatch('showEditFuelExpenseModal');
    }

    public function updateFuelExpense(): void
    {
        $this->validate($this->expenseValidationRules());

        $expense = TruckFuelExpense::findOrFail($this->editingExpenseId);
        [$driverId, $driverName] = $this->resolveDriverData();
        $billPath = $this->storeBillFile($expense->bill_file);

        $expense->update([
            'expense_amount' => $this->expense_amount,
            'fuel_quantity' => $this->fuel_quantity ?: null,
            'rate_per_litre' => $this->rate_per_litre ?: null,
            'is_full_tank' => $this->is_full_tank,
            'current_km_reading' => $this->current_km_reading ?: null,
            'payment_mode' => $this->payment_mode,
            'shop_name' => $this->shop_name ?: null,
            'driver_id' => $driverId,
            'driver_name' => $driverName,
            'transaction_id' => $this->transaction_id ?: null,
            'diesel_pump_name' => $this->diesel_pump_name ?: null,
            'expense_date' => $this->expense_date,
            'bill_file' => $billPath,
            'remarks' => $this->remarks ?: null,
            'updated_by' => auth()->id(),
        ]);

        $this->dispatch('fuelBookUpdated');
        $this->dispatch('closeModal', 'editFuelExpenseModal');
        $this->dispatch('openFuelBookOffcanvas');
        $this->refreshData();
    }

    public function deleteFuelExpense(int $expenseId): void
    {
        $expense = TruckFuelExpense::findOrFail($expenseId);
        $expense->delete();

        $this->dispatch('fuelBookUpdated');
        $this->dispatch('openFuelBookOffcanvas');
        $this->refreshData();
    }

    public function getFuelExpensesProperty()
    {
        return $this->fuelExpensesQuery()
            ->orderByDesc('expense_date')
            ->paginate($this->perPage);
    }

    public function getFuelSummaryProperty()
    {
        $expenses = $this->fuelExpensesQuery()->get();

        $quantity = $expenses->sum('fuel_quantity');
        $cost = $expenses->sum('expense_amount');
        $kmReadings = $expenses->pluck('current_km_reading')->filter(fn ($value) => is_numeric($value) && $value > 0)->sort();
        $averageMileage = 0;

        if ($quantity > 0 && $kmReadings->count() >= 2) {
            $averageMileage = round(($kmReadings->last() - $kmReadings->first()) / $quantity, 2);
        }

        return [
            'quantity' => $quantity,
            'mileage' => $averageMileage,
            'cost' => $cost,
        ];
    }

    public function getGroupedFuelExpensesProperty()
    {
        return $this->fuelExpenses->getCollection()->groupBy(function ($expense) {
            return $expense->expense_date?->format('F Y') ?: 'Unknown';
        });
    }

    public function getTypesProperty()
    {
        return config('truck.types');
    }

    protected function fuelExpensesQuery()
    {
        $query = TruckFuelExpense::where('truck_id', $this->truckId);
        $today = Carbon::today();

        if ($this->monthFilter === 'current') {
            $query->whereYear('expense_date', $today->year)
                ->whereMonth('expense_date', $today->month);
        } elseif ($this->monthFilter === 'previous') {
            $previous = $today->copy()->subMonth();
            $query->whereYear('expense_date', $previous->year)
                ->whereMonth('expense_date', $previous->month);
        } elseif ($this->monthFilter === 'three') {
            $query->whereBetween('expense_date', [$today->copy()->subMonths(3)->startOfMonth(), $today->endOfMonth()]);
        } elseif ($this->monthFilter === 'six') {
            $query->whereBetween('expense_date', [$today->copy()->subMonths(6)->startOfMonth(), $today->endOfMonth()]);
        } elseif ($this->monthFilter === 'custom') {
            if ($this->custom_from) {
                $query->whereDate('expense_date', '>=', $this->custom_from);
            }
            if ($this->custom_to) {
                $query->whereDate('expense_date', '<=', $this->custom_to);
            }
        }

        return $query;
    }

    protected function expenseValidationRules(): array
    {
        $rules = [
            'expense_amount' => 'required|numeric|min:0',
            'payment_mode' => 'required|in:cash,credit,paid_by_driver,online',
            'expense_date' => 'required|date',
            'bill_file' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
        ];

        if ($this->payment_mode === 'credit') {
            $rules['shop_name'] = 'required|string|max:255';
        }

        if ($this->payment_mode === 'paid_by_driver') {
            if (!$this->driver_id && !$this->custom_driver_name) {
                $rules['driver_id'] = 'required';
            }
        }

        if ($this->payment_mode === 'online') {
            $rules['transaction_id'] = 'required|string|max:255';
        }

        return $rules;
    }

    protected function resolveDriverData(): array
    {
        if ($this->payment_mode !== 'paid_by_driver') {
            return [null, null];
        }

        if ($this->driver_id) {
            return [$this->driver_id, Driver::find($this->driver_id)?->name];
        }

        return [null, $this->custom_driver_name ?: null];
    }

    protected function storeBillFile(?string $existingPath = null): ?string
    {
        if ($this->bill_file) {
            return $this->bill_file->store('trucks/fuel-bills', 'public');
        }

        return $existingPath;
    }

    public function render()
    {
        return view('livewire.admin.truck.fuel-book', [
            'fuelExpenses' => $this->fuelExpenses,
            'groupedFuelExpenses' => $this->groupedFuelExpenses,
            'summary' => $this->fuelSummary,
            'types' => $this->types,
            'drivers' => Driver::active()->orderBy('name')->get(),
            'paymentModeOptions' => $this->paymentModeOptions,
            'monthOptions' => $this->monthOptions,
        ]);
    }
}

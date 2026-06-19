<?php

namespace App\Livewire\Admin\Truck;

use App\Models\Driver;
use App\Models\Truck;
use App\Models\TruckMaintenanceExpense;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class MaintenanceBook extends Component
{
    use WithPagination;
    use WithFileUploads;

    protected $paginationTheme = 'bootstrap';

    public int $truckId;
    public Truck $truck;

    public string $monthFilter = 'all';
    public string $expenseTypeFilter = 'all';
    public string $custom_from = '';
    public string $custom_to = '';

    public string $expense_type = '';
    public string $expense_type_search = '';
    public string $amount = '';
    public string $expense_date = '';
    public string $due_date = '';
    public string $payment_mode = 'cash';
    public string $shop_name = '';
    public string $driver_search = '';
    public string $custom_driver_name = '';
    public ?int $driver_id = null;
    public string $transaction_id = '';
    public string $current_km_reading = '';
    public string $notes = '';
    public $expense_image = null;

    public ?int $editingExpenseId = null;
    public int $perPage = 8;

    public bool $showDriverDropdown = false;
    public bool $showExpenseTypeDropdown = false;
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
        'openMaintenanceBookPanel' => 'openPanel',
        'editMaintenanceExpense' => 'editMaintenanceExpense',
        'deleteMaintenanceExpense' => 'deleteMaintenanceExpense',
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

    #[Computed]
    public function filteredExpenseTypes()
    {
        $types = $this->expenseTypeOptions;
        if (!$this->expense_type_search) {
            return $types;
        }

        $search = strtolower($this->expense_type_search);
        return array_values(array_filter($types, fn ($type) => str_contains(strtolower($type), $search)));
    }

    public function mount(int $truckId): void
    {
        $this->truckId = $truckId;
        $this->driverList = Driver::orderBy('name')->get(['id', 'name'])->toArray();
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
        $this->dispatch('openMaintenanceBookOffcanvas');
    }

    public function updatingMonthFilter(): void
    {
        $this->resetPage();
    }

    public function updatingExpenseTypeFilter(): void
    {
        $this->resetPage();
    }

    public function showAddMaintenanceModal(): void
    {
        $this->resetForm();
        $this->payment_mode = 'cash';
        $this->expense_date = now()->format('Y-m-d');
        $this->dispatch('showAddMaintenanceModal');
    }

    public function selectExpenseType(string $type): void
    {
        $this->expense_type = $type;
        $this->expense_type_search = $type;
        $this->showExpenseTypeDropdown = false;
    }

    public function createMaintenance(): void
    {
        $this->validate($this->validationRules());

        [$driverId, $driverName] = $this->resolveDriverData();
        $expenseType = trim($this->expense_type ?: $this->expense_type_search);

        $status = 'completed';
        if ($this->due_date && Carbon::parse($this->due_date)->startOfDay()->gte(Carbon::today())) {
            $status = 'pending';
        }

        TruckMaintenanceExpense::create([
            'truck_id' => $this->truckId,
            'expense_type' => $expenseType,
            'amount' => $this->amount,
            'expense_date' => $this->expense_date,
            'payment_mode' => $this->payment_mode,
            'shop_name' => $this->shop_name ?: null,
            'driver_id' => $driverId,
            'driver_name' => $driverName,
            'transaction_id' => $this->transaction_id ?: null,
            'current_km_reading' => $this->current_km_reading ?: null,
            'notes' => $this->notes ?: null,
            'expense_image' => $this->storeExpenseImage(),
            'due_date' => $this->due_date ?: null,
            'status' => $status,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        $this->afterSave();
    }

    public function editMaintenanceExpense(int $expenseId): void
    {
        $expense = TruckMaintenanceExpense::where('truck_id', $this->truckId)->findOrFail($expenseId);

        $this->resetForm();
        $this->editingExpenseId = $expense->id;
        $this->expense_type = $expense->expense_type;
        $this->expense_type_search = $expense->expense_type;
        $this->amount = (string) $expense->amount;
        $this->expense_date = $expense->expense_date?->format('Y-m-d') ?? '';
        $this->due_date = $expense->due_date?->format('Y-m-d') ?? '';
        $this->payment_mode = $expense->payment_mode;
        $this->shop_name = $expense->shop_name ?? '';
        $this->transaction_id = $expense->transaction_id ?? '';
        $this->current_km_reading = $expense->current_km_reading !== null ? (string) $expense->current_km_reading : '';
        $this->notes = $expense->notes ?? '';

        if ($expense->payment_mode === 'paid_by_driver') {
            if ($expense->driver_id) {
                $this->driver_id = $expense->driver_id;
                $this->driver_search = Driver::find($expense->driver_id)?->name ?? '';
            } else {
                $this->custom_driver_name = $expense->driver_name ?? '';
            }
        }

        $this->dispatch('showAddMaintenanceModal');
    }

    public function updateMaintenance(): void
    {
        $this->validate($this->validationRules());

        $expense = TruckMaintenanceExpense::where('truck_id', $this->truckId)
            ->findOrFail($this->editingExpenseId);

        [$driverId, $driverName] = $this->resolveDriverData();
        $expenseType = trim($this->expense_type ?: $this->expense_type_search);

        $status = $expense->status;
        if ($this->due_date && Carbon::parse($this->due_date)->startOfDay()->gte(Carbon::today())) {
            $status = 'pending';
        } else {
            $status = 'completed';
        }

        $expense->update([
            'expense_type' => $expenseType,
            'amount' => $this->amount,
            'expense_date' => $this->expense_date,
            'payment_mode' => $this->payment_mode,
            'shop_name' => $this->shop_name ?: null,
            'driver_id' => $driverId,
            'driver_name' => $driverName,
            'transaction_id' => $this->transaction_id ?: null,
            'current_km_reading' => $this->current_km_reading ?: null,
            'notes' => $this->notes ?: null,
            'expense_image' => $this->storeExpenseImage($expense->expense_image),
            'due_date' => $this->due_date ?: null,
            'status' => $status,
            'updated_by' => auth()->id(),
        ]);

        $this->afterSave();
    }

    public function deleteMaintenanceExpense(int $expenseId): void
    {
        $expense = TruckMaintenanceExpense::where('truck_id', $this->truckId)->findOrFail($expenseId);
        $expense->update(['deleted_by' => auth()->id()]);
        $expense->delete();

        $this->dispatch('maintenanceBookUpdated');
        $this->dispatch('openMaintenanceBookOffcanvas');
        $this->refreshData();
    }

    public function markDone(int $expenseId): void
    {
        $expense = TruckMaintenanceExpense::where('truck_id', $this->truckId)
            ->where('status', 'pending')
            ->findOrFail($expenseId);

        $expense->update([
            'status' => 'completed',
            'expense_date' => now()->format('Y-m-d'),
            'updated_by' => auth()->id(),
        ]);

        $this->dispatch('maintenanceBookUpdated');
        $this->dispatch('openMaintenanceBookOffcanvas');
        $this->refreshData();
    }

    public function getExpensesProperty()
    {
        return $this->completedExpensesQuery()
            ->orderByDesc('expense_date')
            ->paginate($this->perPage);
    }

    public function getUpcomingMaintenanceProperty()
    {
        return TruckMaintenanceExpense::where('truck_id', $this->truckId)
            ->where('status', 'pending')
            ->orderBy('due_date')
            ->get();
    }

    public function getExpenseSummaryProperty()
    {
        return [
            'cost' => (float) $this->completedExpensesQuery()->sum('amount'),
        ];
    }

    public function getGroupedExpensesProperty()
    {
        return $this->expenses->getCollection()->groupBy(function ($expense) {
            return $expense->expense_date?->format('F Y') ?: 'Unknown';
        });
    }

    public function getExpenseTypeOptionsProperty(): array
    {
        $defaults = ['Engine Service', 'Tyre Change', 'Oil Change', 'Brake Service', 'Battery', 'Other'];
        $existing = TruckMaintenanceExpense::where('truck_id', $this->truckId)
            ->whereNotNull('expense_type')
            ->distinct()
            ->pluck('expense_type')
            ->filter()
            ->values()
            ->all();

        return array_values(array_unique(array_merge($defaults, $existing)));
    }

    public function getExpenseTypeFilterOptionsProperty(): array
    {
        return $this->expenseTypeOptions;
    }

    public function getTypesProperty()
    {
        return config('truck.types');
    }

    protected function completedExpensesQuery()
    {
        $query = TruckMaintenanceExpense::where('truck_id', $this->truckId)
            ->where('status', 'completed');

        if ($this->expenseTypeFilter !== 'all') {
            $query->where('expense_type', $this->expenseTypeFilter);
        }

        return $this->applyMonthFilter($query, 'expense_date');
    }

    protected function applyMonthFilter($query, string $column)
    {
        $today = Carbon::today();

        if ($this->monthFilter === 'current') {
            $query->whereYear($column, $today->year)->whereMonth($column, $today->month);
        } elseif ($this->monthFilter === 'previous') {
            $previous = $today->copy()->subMonth();
            $query->whereYear($column, $previous->year)->whereMonth($column, $previous->month);
        } elseif ($this->monthFilter === 'three') {
            $query->whereBetween($column, [$today->copy()->subMonths(3)->startOfMonth(), $today->endOfMonth()]);
        } elseif ($this->monthFilter === 'six') {
            $query->whereBetween($column, [$today->copy()->subMonths(6)->startOfMonth(), $today->endOfMonth()]);
        } elseif ($this->monthFilter === 'custom') {
            if ($this->custom_from) {
                $query->whereDate($column, '>=', $this->custom_from);
            }
            if ($this->custom_to) {
                $query->whereDate($column, '<=', $this->custom_to);
            }
        }

        return $query;
    }

    protected function validationRules(): array
    {
        $rules = [
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'payment_mode' => 'required|in:cash,credit,paid_by_driver,online',
            'due_date' => 'nullable|date',
            'expense_image' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ];

        if (!trim($this->expense_type) && !trim($this->expense_type_search)) {
            $rules['expense_type'] = 'required|string|max:255';
        }

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

    protected function storeExpenseImage(?string $existingPath = null): ?string
    {
        if ($this->expense_image) {
            return $this->expense_image->store('trucks/maintenance', 'public');
        }

        return $existingPath;
    }

    protected function resetForm(): void
    {
        $this->reset([
            'expense_type',
            'expense_type_search',
            'amount',
            'expense_date',
            'due_date',
            'payment_mode',
            'shop_name',
            'driver_search',
            'driver_id',
            'custom_driver_name',
            'transaction_id',
            'current_km_reading',
            'notes',
            'expense_image',
            'editingExpenseId',
            'showDriverDropdown',
            'showExpenseTypeDropdown',
        ]);
    }

    protected function afterSave(): void
    {
        $this->dispatch('maintenanceBookUpdated');
        $this->dispatch('closeModal', 'addMaintenanceModal');
        $this->dispatch('openMaintenanceBookOffcanvas');
        $this->refreshData();
    }

    public function render()
    {
        return view('livewire.admin.truck.maintenance-book', [
            'expenses' => $this->expenses,
            'groupedExpenses' => $this->groupedExpenses,
            'upcomingMaintenance' => $this->upcomingMaintenance,
            'summary' => $this->expenseSummary,
            'types' => $this->types,
            'paymentModeOptions' => $this->paymentModeOptions,
            'monthOptions' => $this->monthOptions,
            'expenseTypeFilterOptions' => $this->expenseTypeFilterOptions,
        ]);
    }
}

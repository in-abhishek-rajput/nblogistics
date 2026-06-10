<?php

namespace App\Livewire\Admin\Truck;

use App\Models\Truck;
use Illuminate\Support\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class ViewTruck extends Component
{
    use WithPagination;
    public int $truckId;
    public string $monthFilter = 'all';
    public string $activityFilter = 'all';

    public array $monthOptions = [
        'all' => 'All Months',
        'current' => 'Current Month',
        'previous' => 'Previous Month',
        'three' => 'Last 3 Months',
        'six' => 'Last 6 Months',
        'custom' => 'Custom Range',
    ];

    public array $activityOptions = [
        'all' => 'All Trips & Expenses',
        'trips' => 'Trip Book',
        'fuel' => 'Fuel Book',
        'maintenance' => 'Maintenance Book',
        'emi' => 'EMI Book',
        'documents' => 'Documents',
        'driver_expenses' => 'Driver & Other Expenses',
        'diesel_card' => 'Diesel Card',
    ];

    public $truck;
    public ?int $viewingTripId = null;
    public ?int $editingTruckId = null;

    public function getTotalRevenueProperty()
    {
        $query = $this->truck->trips();
        if ($range = $this->getDateRange()) {
            $query->whereBetween('start_date', $range);
        }

        $trips = $query->with('charges')->get();
        $totalFreight = (float) $trips->sum('freight_amount');
        
        $totalCharges = 0;
        foreach ($trips as $trip) {
            foreach ($trip->charges as $charge) {
                if ($charge->charge_direction === 'add_to_bill') {
                    $totalCharges += $charge->amount;
                } elseif ($charge->charge_direction === 'reduce_from_bill') {
                    $totalCharges -= $charge->amount;
                }
            }
        }

        return $totalFreight + $totalCharges;
    }

    public function getTotalExpensesProperty()
    {
        $emiQuery = $this->truck->truckEmiPayments()
            ->where('truck_emi_payments.status', 'paid');
        $fuelQuery = $this->truck->truckFuelExpenses();

        if ($range = $this->getDateRange()) {
            $emiQuery->whereBetween('payment_date', $range);
            $fuelQuery->whereBetween('expense_date', $range);
        }

        $emiExpenses = (float) $emiQuery->sum('amount');
        $fuelExpenses = (float) $fuelQuery->sum('expense_amount');

        // Add trip expenses and advances
        $tripQuery = $this->truck->trips();
        if ($range) {
            $tripQuery->whereBetween('start_date', $range);
        }
        
        $tripExpenses = 0;
        $advancesTotal = 0;
        
        $trips = $tripQuery->with(['expenses', 'advances'])->get();
        foreach ($trips as $trip) {
            $tripExpenses += $trip->expenses->sum('amount');
            $advancesTotal += $trip->advances->sum('amount');
        }

        return $emiExpenses + $fuelExpenses + $tripExpenses + $advancesTotal;
    }

    public function getTotalProfitProperty()
    {
        return $this->totalRevenue - $this->totalExpenses;
    }

    public function updatingMonthFilter(): void
    {
        $this->resetPage();
    }

    public function updatingActivityFilter(): void
    {
        $this->resetPage();
    }

    public function getStatsProperty()
    {
        $tripRevenue = $this->totalRevenue;
        $tripExpenses = $this->totalExpenses;
        
        return [
            ['label' => 'Trip Revenue', 'value' => '₹ ' . number_format($tripRevenue, 2)],
            ['label' => 'Total Expenses', 'value' => '₹ ' . number_format($tripExpenses, 2)],
            [
                'label' => 'Total Profit',
                'value' => '₹ ' . number_format($tripRevenue - $tripExpenses, 2),
                'negative' => ($tripRevenue - $tripExpenses) < 0,
            ],
        ];
    }

    public function getHistoryRowsProperty()
    {
        $range = $this->getDateRange();
        $rows = collect();

        if (in_array($this->activityFilter, ['all', 'trips'], true)) {
            $tripQuery = $this->truck->trips();
            if ($range) {
                $tripQuery->whereBetween('start_date', $range);
            }

            $rows = $rows->concat($tripQuery->with(['expenses', 'charges', 'advances', 'payments'])->orderByDesc('start_date')->get()->map(function ($trip) {
                $expenseTotal = $trip->expenses->sum('amount');
                $totalPayments = $trip->payments->sum('amount');
                
                $chargesTotal = 0;
                foreach ($trip->charges as $charge) {
                    if ($charge->charge_direction === 'add_to_bill') {
                        $chargesTotal += $charge->amount;
                    } elseif ($charge->charge_direction === 'reduce_from_bill') {
                        $chargesTotal -= $charge->amount;
                    }
                }
                
                $advancesTotal = $trip->advances->sum('amount');
                $pendingFreight = $trip->freight_amount - $advancesTotal - $totalPayments + $chargesTotal;
                $profit = $pendingFreight - $expenseTotal;
                
                return [
                    'type' => 'trip',
                    'id' => $trip->id,
                    'date' => $trip->start_date?->format('d M Y') ?? '-',
                    'reason' => ($trip->origin ?? '') . ' → ' . ($trip->destination ?? ''),
                    'expense' => '₹ ' . number_format($expenseTotal + $advancesTotal, 2),
                    'revenue' => '₹ ' . number_format($pendingFreight + $expenseTotal + $totalPayments - $chargesTotal, 2),
                    'profit' => '₹ ' . number_format($profit, 2),
                    'sortDate' => $trip->start_date,
                ];
            }));
        }

        if (in_array($this->activityFilter, ['all', 'emi'], true)) {
            $emiQuery = $this->truck->truckEmiPayments()
                ->where('truck_emi_payments.status', 'paid');

            if ($range) {
                $emiQuery->whereBetween('payment_date', $range);
            }

            $rows = $rows->concat($emiQuery->orderByDesc('payment_date')->get()->map(function ($payment) {
                return [
                    'type' => 'emi',
                    'id' => $payment->id,
                    'date' => $payment->payment_date?->format('d M Y') ?? '-',
                    'reason' => 'EMI Payment',
                    'expense' => '₹ ' . number_format($payment->amount, 2),
                    'revenue' => '',
                    'sortDate' => $payment->payment_date,
                ];
            }));
        }

        if (in_array($this->activityFilter, ['all', 'fuel'], true)) {
            $fuelQuery = $this->truck->truckFuelExpenses();
            if ($range) {
                $fuelQuery->whereBetween('expense_date', $range);
            }

            $rows = $rows->concat($fuelQuery->orderByDesc('expense_date')->get()->map(function ($expense) {
                return [
                    'type' => 'fuel',
                    'id' => $expense->id,
                    'date' => $expense->expense_date?->format('d M Y') ?? '-',
                    'reason' => 'Fuel Expense',
                    'expense' => '₹ ' . number_format($expense->expense_amount, 2),
                    'revenue' => '',
                    'sortDate' => $expense->expense_date,
                ];
            }));
        }

        return $rows->sortByDesc(fn ($row) => $row['sortDate'] ?? Carbon::minValue())
            ->map(function ($row) {
                unset($row['sortDate']);
                return $row;
            })
            ->values()
            ->toArray();
    }

    protected function getDateRange()
    {
        $today = Carbon::today();

        return match ($this->monthFilter) {
            'current' => [$today->copy()->startOfMonth(), $today->copy()->endOfMonth()],
            'previous' => [$today->copy()->subMonth()->startOfMonth(), $today->copy()->subMonth()->endOfMonth()],
            'three' => [$today->copy()->subMonths(3)->startOfMonth(), $today->copy()->endOfMonth()],
            'six' => [$today->copy()->subMonths(6)->startOfMonth(), $today->copy()->endOfMonth()],
            default => null,
        };
    }

    public array $activityCards = [
        ['label' => 'Trip Book', 'icon' => 'bi-truck', 'iconColor' => '#3b6fd4', 'href' => '#', 'openTripBook' => true],
        ['label' => 'Fuel Book', 'icon' => 'bi-droplet-fill', 'iconColor' => '#e67e22', 'href' => '#', 'openFuelBook' => true],
        ['label' => 'EMI Book', 'icon' => 'bi-receipt', 'iconColor' => '#e74c3c', 'href' => '#', 'openEmiBook' => true],
        ['label' => 'Documents', 'icon' => 'bi-person-badge', 'iconColor' => '#27ae60', 'href' => '#'],
        ['label' => 'Maintenance Book', 'icon' => 'bi-tools', 'iconColor' => '#7f8c8d', 'href' => '#'],
        ['label' => 'Driver & Other expenses', 'icon' => 'bi-person-circle', 'iconColor' => '#8e44ad', 'href' => '#'],
        ['label' => 'Diesel Card', 'icon' => 'bi-credit-card-2-front', 'iconColor' => '#16a085', 'href' => '#'],
    ];

    protected $listeners = [
        'truckUpdated' => 'onTruckUpdated',
        'emiBookUpdated' => 'refreshTruck',
        'fuelBookUpdated' => 'refreshTruck',
        'tripBookUpdated' => 'refreshTruck',
        'viewTripFromBook' => 'viewTripFromBook',
    ];

    public function onTruckUpdated(): void
    {
        $this->editingTruckId = null;
        $this->refreshTruck();
    }

    public function viewTripFromBook(int $tripId): void
    {
        $this->viewingTripId = $tripId;
        $this->dispatch('showViewTripOffcanvas');
    }

    public function mount(int $truckId)
    {
        $this->truckId = $truckId;
        $this->refreshTruck();
    }

    public function refreshTruck()
    {
        $this->truck = Truck::with('driver')->findOrFail($this->truckId);
    }

    public function editTruck()
    {
        $this->editingTruckId = $this->truck->id;
        $this->dispatch('showEditTruckModal');
    }

    public function deleteTruck()
    {
        $truck = Truck::findOrFail($this->truckId);
        $truck->update(['deleted_by' => auth()->id()]);
        $truck->delete();

        return redirect()->route('trucks.index');
    }

    public function viewTrip(int $tripId): void
    {
        $this->viewingTripId = $tripId;
        $this->dispatch('showViewTripOffcanvas');
    }

    public function editFuelExpense(int $expenseId): void
    {
        $this->dispatch('openFuelBookOffcanvas');
        $this->dispatch('editFuelExpense', $expenseId);
    }

    public function deleteFuelExpense(int $expenseId): void
    {
        if (!$this->confirmDeletion()) {
            return;
        }

        $this->dispatch('openFuelBookOffcanvas');
        $this->dispatch('deleteFuelExpense', $expenseId);
    }

    public function editEmiPayment(int $paymentId): void
    {
        $this->dispatch('openEmiBookOffcanvas');
        $this->dispatch('editEmiPayment', $paymentId);
    }

    public function deleteEmiPayment(int $paymentId): void
    {
        if (!$this->confirmDeletion()) {
            return;
        }

        $this->dispatch('openEmiBookOffcanvas');
        $this->dispatch('deleteEmiPayment', $paymentId);
    }

    protected function confirmDeletion(): bool
    {
        return true;
    }

    public function render()
    {
        return view('livewire.admin.truck.view-truck', [
            'stats' => $this->stats,
            'historyRows' => $this->historyRows,
            'editingTruckId' => $this->editingTruckId,
        ]);
    }

}

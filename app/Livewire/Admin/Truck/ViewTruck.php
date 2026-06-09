<?php

namespace App\Livewire\Admin\Truck;

use App\Models\Truck;
use Livewire\Component;

class ViewTruck extends Component
{
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

    public function getTotalRevenueProperty()
    {
        return (float) $this->truck->trips()->sum('freight_amount');
    }

    public function getTotalExpensesProperty()
    {
        return (float) $this->truck->truckEmiPayments()->where('truck_emi_payments.status', 'paid')->sum('amount');
    }

    public function getTotalProfitProperty()
    {
        return $this->totalRevenue - $this->totalExpenses;
    }

    public function getStatsProperty()
    {
        return [
            ['label' => 'Trip Revenue', 'value' => '₹ ' . number_format($this->totalRevenue, 2)],
            ['label' => 'Total Expenses', 'value' => '₹ ' . number_format($this->totalExpenses, 2)],
            [
                'label' => 'Total Profit',
                'value' => '₹ ' . number_format($this->totalProfit, 2),
                'negative' => $this->totalProfit < 0,
            ],
        ];
    }

    public function getHistoryRowsProperty()
    {
        return $this->truck->truckEmiPayments()
            ->where('truck_emi_payments.status', 'paid')
            ->orderByDesc('payment_date')
            ->get()
            ->map(function ($payment) {
                return [
                    'date' => $payment->payment_date?->format('d M Y') ?? '-',
                    'reason' => 'EMI Payment',
                    'expense' => '₹ ' . number_format($payment->amount, 2),
                    'revenue' => '',
                ];
            })
            ->toArray();
    }

    public array $activityCards = [
        ['label' => 'Trip Book', 'icon' => 'bi-truck', 'iconColor' => '#3b6fd4', 'href' => '#'],
        ['label' => 'Fuel Book', 'icon' => 'bi-droplet-fill', 'iconColor' => '#e67e22', 'href' => '#'],
        ['label' => 'EMI Book', 'icon' => 'bi-receipt', 'iconColor' => '#e74c3c', 'href' => '#', 'openEmiBook' => true],
        ['label' => 'Documents', 'icon' => 'bi-person-badge', 'iconColor' => '#27ae60', 'href' => '#'],
        ['label' => 'Maintenance Book', 'icon' => 'bi-tools', 'iconColor' => '#7f8c8d', 'href' => '#'],
        ['label' => 'Driver & Other expenses', 'icon' => 'bi-person-circle', 'iconColor' => '#8e44ad', 'href' => '#'],
        ['label' => 'Diesel Card', 'icon' => 'bi-credit-card-2-front', 'iconColor' => '#16a085', 'href' => '#'],
    ];

    protected $listeners = [
        'truckUpdated' => 'refreshTruck',
        'emiBookUpdated' => 'refreshTruck',
    ];

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
        return redirect()->route('trucks.edit', $this->truckId);
    }

    public function deleteTruck()
    {
        $truck = Truck::findOrFail($this->truckId);
        $truck->update(['deleted_by' => auth()->id()]);
        $truck->delete();

        return redirect()->route('trucks.index');
    }

    public function render()
    {
        return view('livewire.admin.truck.view-truck', [
            'stats' => $this->stats,
            'historyRows' => $this->historyRows,
        ]);
    }

}

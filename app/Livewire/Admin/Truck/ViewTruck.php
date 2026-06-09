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
        'trips' => 'Trips',
        'fuel' => 'Fuel',
        'maintenance' => 'Maintenance',
        'emi' => 'EMI',
        'documents' => 'Documents',
        'driver_expenses' => 'Driver Expenses',
        'diesel_card' => 'Diesel Card',
    ];

    public $truck;

    public array $stats = [
        ['label' => 'Trip Revenue', 'value' => '₹ 0'],
        ['label' => 'Total Expenses', 'value' => '₹ 1,20,000'],
        ['label' => 'Total Profit', 'value' => '-₹ 1,20,000', 'negative' => true],
    ];

    public array $activityCards = [
        ['label' => 'Trip Book', 'icon' => 'bi-truck', 'iconColor' => '#3b6fd4', 'href' => '#'],
        ['label' => 'Fuel Book', 'icon' => 'bi-fuel-pump', 'iconColor' => '#e67e22', 'href' => '#'],
        ['label' => 'EMI Book', 'icon' => 'bi-receipt', 'iconColor' => '#e74c3c', 'href' => '#'],
        ['label' => 'Documents', 'icon' => 'bi-person-vcard', 'iconColor' => '#27ae60', 'href' => '#'],
        ['label' => 'Maintenance Book', 'icon' => 'bi-tools', 'iconColor' => '#7f8c8d', 'href' => '#'],
        ['label' => 'Driver & Other expenses', 'icon' => 'bi-steering-wheel', 'iconColor' => '#8e44ad', 'href' => '#'],
        ['label' => 'Diesel Card', 'icon' => 'bi-credit-card-2-front', 'iconColor' => '#16a085', 'href' => '#'],
    ];

    public array $historyRows = [
        ['date' => '09 Jun 2026', 'reason' => 'EMI Payment', 'expense' => '-₹ 60,000', 'revenue' => ''],
        ['date' => '02 Jun 2026', 'reason' => 'EMI Payment', 'expense' => '-₹ 60,000', 'revenue' => ''],
    ];

    protected $listeners = [
        'truckUpdated' => 'refreshTruck',
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
        return view('livewire.admin.truck.view-truck');
    }
}
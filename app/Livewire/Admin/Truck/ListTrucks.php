<?php

namespace App\Livewire\Admin\Truck;

use App\Models\Truck;
use Livewire\Component;
use Livewire\WithPagination;

class ListTrucks extends Component
{
    use WithPagination;

    // Filter properties
    public string $search = ''; // Search term for truck_number
    public string $statusFilter = ''; // Status filter
    public string $typeFilter = ''; // Type filter
    public string $ownershipFilter = ''; // Ownership filter

    // Sorting properties
    public string $sortColumn = 'created_at'; // Default sort column
    public string $sortDirection = 'desc'; // Default sort direction

    // Edit properties
    public $editingTruckId = null;

    // Flash message properties
    public $flashMessage = null;
    public $flashType = null;

    // Listeners for refreshing table after add/edit
    protected $listeners = [
        'truckAdded' => 'onTruckAdded',
        'truckUpdated' => 'onTruckUpdated',
        'flashMessage' => 'showFlashMessage',
    ];

    // Reset pagination when filters change
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingTypeFilter()
    {
        $this->resetPage();
    }

    public function updatingOwnershipFilter()
    {
        $this->resetPage();
    }

    // Sort method to toggle direction or change column
    public function sortBy($column)
    {
        if ($this->sortColumn === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortColumn = $column;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    // Set truck for editing
    public function editTruck($id)
    {
        $this->editingTruckId = $id;
        $this->dispatch('showEditModal');
    }

    // Refresh table after truck added
    public function onTruckAdded()
    {
        $this->resetPage(); // Go to first page to show new record
    }

    // Refresh table after truck updated
    public function onTruckUpdated()
    {
        // No need to reset page, record stays in place
    }

    // Show flash message
    public function showFlashMessage($type, $message)
    {
        $this->flashType = $type;
        $this->flashMessage = $message;
    }

    // Computed property for trucks query - dynamic and efficient
    public function getTrucksProperty()
    {
        return Truck::query()
            ->with('driver') // Eager load driver
            ->search($this->search) // Use scope for search
            ->status($this->statusFilter) // Use scope for status filter
            ->type($this->typeFilter) // Use scope for type filter
            ->ownership($this->ownershipFilter) // Use scope for ownership filter
            ->orderBy($this->sortColumn, $this->sortDirection) // Dynamic sorting
            ->paginate(10); // Server-side pagination
    }

    // Get available statuses
    public function getStatusesProperty()
    {
        return config('truck.statuses');
    }

    // Get available types
    public function getTypesProperty()
    {
        return config('truck.types');
    }

    // Get available ownerships
    public function getOwnershipsProperty()
    {
        return config('truck.ownerships');
    }

    public function render()
    {
        return view('livewire.admin.truck.list-trucks', [
            'trucks' => $this->trucks, // Pass computed property
            'statuses' => $this->statuses, // Pass statuses for filter
            'types' => $this->types, // Pass types for filter
            'ownerships' => $this->ownerships, // Pass ownerships for filter
        ]);
    }
}
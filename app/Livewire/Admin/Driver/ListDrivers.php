<?php

namespace App\Livewire\Admin\Driver;

use App\Models\Driver;
use Livewire\Component;
use Livewire\WithPagination;

class ListDrivers extends Component
{
    use WithPagination;

    // Filter properties
    public string $search = ''; // Search term for name, email, mobile
    public string $statusFilter = ''; // Status filter from config

    // Sorting properties
    public string $sortColumn = 'created_at'; // Default sort column
    public string $sortDirection = 'desc'; // Default sort direction

    // Edit properties
    public $editingDriverId = null;

    // Flash message properties
    public $flashMessage = null;
    public $flashType = null;

    // Listeners for refreshing table after add/edit
    protected $listeners = [
        'driverAdded' => 'onDriverAdded',
        'driverUpdated' => 'onDriverUpdated',
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

    // Set driver for editing
    public function editDriver($id)
    {
        $this->editingDriverId = $id;
        $this->dispatch('showEditModal');
    }

    // Refresh table after driver added
    public function onDriverAdded()
    {
        $this->resetPage(); // Go to first page to show new record
    }

    // Refresh table after driver updated
    public function onDriverUpdated()
    {
        // No need to reset page, record stays in place
    }

    // Show flash message
    public function showFlashMessage($type, $message)
    {
        $this->flashType = $type;
        $this->flashMessage = $message;
    }

    // Computed property for drivers query - dynamic and efficient
    public function getDriversProperty()
    {
        return Driver::query()
            ->search($this->search) // Use scope for search
            ->status($this->statusFilter) // Use scope for status filter
            ->orderBy($this->sortColumn, $this->sortDirection) // Dynamic sorting
            ->paginate(10); // Server-side pagination
    }

    // Get available statuses for filter dropdown
    public function getStatusesProperty()
    {
        return config('driver.statuses');
    }

    // Get available types
    public function getTypesProperty()
    {
        return config('truck.types');
    }

    public function render()
    {
        return view('livewire.admin.driver.list-drivers', [
            'drivers' => $this->drivers, // Pass computed property
            'statuses' => $this->statuses, // Pass statuses for filter
            'types' => $this->types, 
        ]);
    }
}

<?php

namespace App\Livewire\Admin\Trip;

use App\Models\Trip;
use Livewire\Component;
use Livewire\WithPagination;

class ListTrips extends Component
{
    use WithPagination;

    // Filter properties
    public string $search = ''; // Search term for party, truck, route
    public string $statusFilter = ''; // Status filter
    public string $billingTypeFilter = ''; // Billing type filter

    // Sorting properties
    public string $sortColumn = 'created_at'; // Default sort column
    public string $sortDirection = 'desc'; // Default sort direction

    // Edit properties
    public $editingTripId = null;

    // View properties
    public $viewingTripId = null;

    // Flash message properties
    public $flashMessage = null;
    public $flashType = null;

    // Delete confirmation
    public $showDeleteConfirm = false;
    public $deleteTripId = null;

    // Refresh trigger for forcing re-render after updates
    public $refreshTrigger = 0;

    // Listeners for refreshing table after add/edit
    protected $listeners = [
        'tripAdded' => 'onTripAdded',
        'tripUpdated' => 'onTripUpdated',
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

    public function updatingBillingTypeFilter()
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

    // Set trip for viewing
    public function viewTrip($id)
    {
        $this->viewingTripId = $id;
        $this->dispatch('showViewOffcanvas');
    }

    // Set trip for editing
    public function editTrip($id)
    {
        $this->editingTripId = $id;
        $this->dispatch('showEditOffcanvas');
    }

    // Show delete confirmation
    public function confirmDeleteTrip($id)
    {
        $this->deleteTripId = $id;
        $this->showDeleteConfirm = true;
    }

    // Cancel delete
    public function cancelDelete()
    {
        $this->showDeleteConfirm = false;
        $this->deleteTripId = null;
    }

    // Delete trip with confirmation (soft delete)
    public function deleteTrip()
    {
        if (!$this->deleteTripId) return;

        $trip = Trip::findOrFail($this->deleteTripId);
        $trip->deleted_by = auth()->id(); // Assuming authentication
        $trip->save();
        $trip->delete();

        $this->showDeleteConfirm = false;
        $this->deleteTripId = null;
        $this->flashMessage = 'Trip deleted successfully!';
        $this->flashType = 'success';
    }

    // Refresh table after trip added
    public function onTripAdded()
    {
        $this->resetPage(); // Go to first page to show new record
    }

    // Refresh table after trip updated
    public function onTripUpdated()
    {
        // Force re-render to show updated data
        $this->refreshTrigger++;
    }

    // Show flash message
    public function showFlashMessage($type, $message)
    {
        $this->flashType = $type;
        $this->flashMessage = $message;
    }

    // Computed property for trips query - dynamic and efficient
    public function getTripsProperty()
    {
        return Trip::query()
            ->with(['party', 'truck']) // Eager load party and truck
            ->search($this->search) // Use scope for search
            ->status($this->statusFilter) // Use scope for status filter
            ->billingType($this->billingTypeFilter) // Use scope for billing type filter
            ->orderBy($this->sortColumn, $this->sortDirection) // Dynamic sorting
            ->paginate(10); // Server-side pagination
    }

    // Get available statuses
    public function getStatusesProperty()
    {
        return config('trip.statuses');
    }

    // Get available billing types
    public function getBillingTypesProperty()
    {
        return config('trip.billing_types');
    }

    public function render()
    {
        return view('livewire.admin.trip.list-trips', [
            'trips' => $this->trips, // Pass computed property
            'statuses' => $this->statuses, // Pass statuses for filter
            'billingTypes' => $this->billingTypes, // Pass billing types for filter
        ]);
    }
}
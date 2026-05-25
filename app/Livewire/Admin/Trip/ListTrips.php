<?php

namespace App\Livewire\Admin\Trip;

use App\Models\Trip;
use Carbon\Carbon;
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

    // Date filter properties
    public $from_date = null;
    public $to_date = null;
    public string $selectedDateFilter = 'all_months';

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

    public function updatingFromDate()
    {
        $this->resetPage();
    }

    public function updatingToDate()
    {
        $this->resetPage();
    }

    public function updatedSelectedDateFilter($value)
    {
        if ($value === 'custom') {
            $this->dispatch('showCustomDateModal');
        } else {
            $this->from_date = null;
            $this->to_date = null;
        }
        $this->resetPage();
    }

    // Reset all filters
    public function resetFilters()
    {
        $this->reset(['search', 'statusFilter', 'billingTypeFilter', 'from_date', 'to_date', 'selectedDateFilter']);
        $this->selectedDateFilter = 'all_months';
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
        $query = Trip::query()
            ->with(['party', 'truck']) // Eager load party and truck
            ->search($this->search) // Use scope for search
            ->status($this->statusFilter) // Use scope for status filter
            ->billingType($this->billingTypeFilter); // Use scope for billing type filter

        // Date filtering
        if ($this->selectedDateFilter === 'custom') {
            if ($this->from_date && $this->to_date) {
                $query->whereBetween('start_date', [
                    Carbon::parse($this->from_date)->startOfDay(),
                    Carbon::parse($this->to_date)->endOfDay(),
                ]);
            } else if ($this->from_date) {
                $query->whereDate('start_date', $this->from_date);
            }
        } else {
            switch ($this->selectedDateFilter) {
                case 'today':
                    $query->whereDate('start_date', Carbon::today());
                    break;
                case 'this_week':
                    $query->whereBetween('start_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                    break;
                case 'last_week':
                    $query->whereBetween('start_date', [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()]);
                    break;
                case 'this_month':
                    $query->whereMonth('start_date', Carbon::now()->month)
                          ->whereYear('start_date', Carbon::now()->year);
                    break;
                case 'last_month':
                    $query->whereMonth('start_date', Carbon::now()->subMonth()->month)
                          ->whereYear('start_date', Carbon::now()->subMonth()->year);
                    break;
                case 'last_3_months':
                    $query->where('start_date', '>=', Carbon::now()->subMonths(3));
                    break;
                case 'this_year':
                    $query->whereYear('start_date', Carbon::now()->year);
                    break;
            }
        }

        return $query->orderBy($this->sortColumn, $this->sortDirection) // Dynamic sorting
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

    // Get available date filters
    public function getDateFiltersProperty()
    {
        return config('trip.date_filters');
    }

    public function render()
    {
        return view('livewire.admin.trip.list-trips', [
            'trips' => $this->trips, // Pass computed property
            'statuses' => $this->statuses, // Pass statuses for filter
            'billingTypes' => $this->billingTypes, // Pass billing types for filter
            'dateFilters' => $this->dateFilters, // Pass date filters
        ]);
    }
}
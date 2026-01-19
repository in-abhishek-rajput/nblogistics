<?php

namespace App\Livewire\Admin\Party;

use App\Models\Party;
use Livewire\Component;
use Livewire\WithPagination;

class ListParties extends Component
{
    use WithPagination;

    // Filter properties
    public string $search = ''; // Search term for name, email, mobile
    public string $statusFilter = ''; // Status filter from config

    // Sorting properties
    public string $sortColumn = 'created_at'; // Default sort column
    public string $sortDirection = 'desc'; // Default sort direction

    // Edit properties
    public $editingPartyId = null;

    // Flash message properties
    public $flashMessage = null;
    public $flashType = null;

    // Listeners for refreshing table after add/edit
    protected $listeners = [
        'partyAdded' => 'onPartyAdded',
        'partyUpdated' => 'onPartyUpdated',
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

    // Set party for editing
    public function editParty($id)
    {
        $this->editingPartyId = $id;
        $this->dispatch('showEditModal');
    }

    // Refresh table after party added
    public function onPartyAdded()
    {
        $this->resetPage(); // Go to first page to show new record
    }

    // Refresh table after party updated
    public function onPartyUpdated()
    {
        // No need to reset page, record stays in place
    }

    // Show flash message
    public function showFlashMessage($type, $message)
    {
        $this->flashType = $type;
        $this->flashMessage = $message;
    }

    // Toggle party status
    public function toggleStatus($id)
    {
        $party = Party::findOrFail($id);
        $newStatus = $party->status === 'active' ? 'inactive' : 'active';
        $party->update(['status' => $newStatus]);

        $this->dispatch('flashMessage', 'success', 'Party status updated to ' . $newStatus . ' successfully!');
    }

    // Computed property for parties query - dynamic and efficient
    public function getPartiesProperty()
    {
        return Party::query()
            ->search($this->search) // Use scope for search
            ->status($this->statusFilter) // Use scope for status filter
            ->orderBy($this->sortColumn, $this->sortDirection) // Dynamic sorting
            ->paginate(10); // Server-side pagination
    }

    // Get available statuses for filter dropdown
    public function getStatusesProperty()
    {
        return config('party.statuses');
    }

    public function render()
    {
        return view('livewire.admin.party.list-parties', [
            'parties' => $this->parties, // Pass computed property
            'statuses' => $this->statuses, // Pass statuses for filter
        ]);
    }
}

<?php

namespace App\Livewire\Admin\Trip;

use App\Models\TripExpense;
use Livewire\Component;
use Livewire\WithPagination;

class ListExpenses extends Component
{
    use WithPagination;

    // Filter properties
    public string $search = ''; // Search term for expense type, notes
    public string $paymentModeFilter = ''; // Payment mode filter
    public string $dateFilter = ''; // Date filter

    // Sorting properties
    public string $sortColumn = 'created_at'; // Default sort column
    public string $sortDirection = 'desc'; // Default sort direction

    // Edit properties
    public $editingExpenseId = null;

    // Delete confirmation
    public $showDeleteConfirm = false;
    public $deleteExpenseId = null;

    // Flash message properties
    public $flashMessage = null;
    public $flashType = null;

    // Refresh trigger for forcing re-render after updates
    public $refreshTrigger = 0;

    // Listeners for refreshing table after add/edit
    protected $listeners = [
        'expenseAdded' => 'onExpenseAdded',
        'expenseUpdated' => 'onExpenseUpdated',
        'flashMessage' => 'showFlashMessage',
    ];

    // Reset pagination when filters change
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingPaymentModeFilter()
    {
        $this->resetPage();
    }

    public function updatingDateFilter()
    {
        $this->resetPage();
    }

    // Set expense for editing
    public function editExpense($id)
    {
        $this->editingExpenseId = $id;
        $this->dispatch('showEditModal');
    }

    /**
     * Get the expenses query with filters and sorting.
     */
    public function getExpensesQuery()
    {
        $query = TripExpense::with(['trip.party', 'trip.truck']);

        // Search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('expense_type', 'like', '%' . $this->search . '%')
                  ->orWhere('notes', 'like', '%' . $this->search . '%')
                  ->orWhereHas('trip.party', function ($partyQuery) {
                      $partyQuery->where('name', 'like', '%' . $this->search . '%');
                  })
                  ->orWhereHas('trip', function ($tripQuery) {
                      $tripQuery->where('origin', 'like', '%' . $this->search . '%')
                                ->orWhere('destination', 'like', '%' . $this->search . '%');
                  });
            });
        }

        // Payment mode filter
        if ($this->paymentModeFilter) {
            $query->where('payment_mode', $this->paymentModeFilter);
        }

        // Date filter
        if ($this->dateFilter) {
            $query->whereDate('expense_date', $this->dateFilter);
        }

        // Sorting
        $query->orderBy($this->sortColumn, $this->sortDirection);

        return $query;
    }

    /**
     * Sort the table by a given column.
     */
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

    /**
     * Confirm deletion of an expense.
     */
    public function confirmDelete($expenseId)
    {
        $this->deleteExpenseId = $expenseId;
        $this->showDeleteConfirm = true;
    }

    /**
     * Delete the confirmed expense.
     */
    public function deleteExpense()
    {
        if ($this->deleteExpenseId) {
            $expense = TripExpense::findOrFail($this->deleteExpenseId);
            $expense->delete();

            $this->showFlashMessage('success', 'Expense deleted successfully.');
            $this->refreshTrigger++;
        }

        $this->showDeleteConfirm = false;
        $this->deleteExpenseId = null;
    }

    /**
     * Cancel the delete confirmation.
     */
    public function cancelDelete()
    {
        $this->showDeleteConfirm = false;
        $this->deleteExpenseId = null;
    }

    /**
     * Handle expense added event.
     */
    public function onExpenseAdded()
    {
        $this->refreshTrigger++;
    }

    /**
     * Handle expense updated event.
     */
    public function onExpenseUpdated()
    {
        $this->refreshTrigger++;
    }

    /**
     * Show flash message.
     */
    public function showFlashMessage($type, $message)
    {
        $this->flashType = $type;
        $this->flashMessage = $message;
    }

    /**
     * Clear flash message.
     */
    public function clearFlashMessage()
    {
        $this->flashMessage = null;
        $this->flashType = null;
    }

    /**
     * Render the component.
     */
    public function render()
    {
        $expenses = $this->getExpensesQuery()->paginate(10);

        $paymentModes = [
            'cash' => 'Cash',
            'credit' => 'Credit',
            'paid_by_driver' => 'Paid By Driver',
            'online' => 'Online',
        ];

        return view('livewire.admin.trip.list-expenses', [
            'expenses' => $expenses,
            'paymentModes' => $paymentModes,
        ]);
    }
}
<div>
    {{-- Flash messages --}}
    @if ($flashMessage)
        <div class="alert alert-{{ $flashType == 'success' ? 'success' : 'danger' }} alert-dismissible fade show"
            role="alert">
            {{ $flashMessage }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"
                wire:click="clearFlashMessage"></button>
        </div>
    @endif

    {{-- Header & Add Button --}}
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <div class="d-flex align-items-center mb-3">
                <h5 class="mb-0 text-primary fw-bold me-2">
                    {{ $expenseMonth ? \Carbon\Carbon::parse($expenseMonth)->format('F Y') : 'Select Month' }}
                </h3>
                <div class="position-relative" style="display: inline-block;">
                    <div class="border rounded px-2 py-1 bg-white d-flex align-items-center justify-content-center" style="border-color: #ccc !important;">
                        <i class="bi bi-chevron-down text-primary" style="font-size: 14px; color: #0033cc !important;"></i>
                    </div>
                    <input type="month" wire:model.live="expenseMonth" class="position-absolute top-0 start-0 w-100 h-100" style="opacity: 0; cursor: pointer;">
                </div>
            </div>

            <div class="border rounded p-3 bg-white" style="display: inline-block; min-width: 250px;">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center me-4">
                        <span class="text-secondary fw-bold me-1" style="font-size: 12px;">TOTAL MONTH EXPENSE</span>
                        <i class="bi bi-info-circle text-secondary" style="font-size: 12px;" title="Total expenses for the selected month"></i>
                    </div>
                    <div class="text-danger fw-bold fs-4 mb-0">
                        <i class="fa fa-rupee-sign"></i> {{ number_format($this->totalMonthExpense, 0) }}
                    </div>
                </div>
            </div>
        </div>
        <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#addExpenseModal">
            <i class="bi bi-plus"></i> Add Expense
        </button>
    </div>

    {{-- Search and Filter Section --}}
    <div class="row mb-3">
        <div class="col-md-3">
            <input type="text" wire:model.live.debounce.300ms="search" class="form-control"
                placeholder="Search by notes, party..." />
        </div>
        <div class="col-md-2">
            <select wire:model.live="expenseCategoryFilter" class="form-select">
                <option value="">All Categories</option>
                <option value="trip">Trip</option>
                <option value="truck">Truck</option>
                <option value="office">Office</option>
            </select>
        </div>
        <div class="col-md-3">
            <select wire:model.live="paymentModeFilter" class="form-select">
                <option value="">All Payment Modes</option>
                @foreach ($paymentModes as $key => $mode)
                    <option value="{{ $key }}">{{ $mode }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <input type="date" wire:model.live="dateFilter" class="form-control"
                placeholder="Filter by date" />
        </div>
        <div class="col-md-2">
            <button type="button" wire:click="resetFilters" class="btn btn-secondary w-100">
                <i class="bi bi-arrow-counterclockwise"></i> Reset
            </button>
        </div>
    </div>

    {{-- Expenses Table --}}
    <div class="table-responsive">
        <table class="table table-borderless border">
            <thead class="bg-light-blue">
                <tr>
                    <th scope="col">
                        <a href="#" wire:click.prevent="sortBy('expense_category')" class="text-decoration-none">
                            Expense For
                            @if ($sortColumn === 'expense_category')
                                <i class="bi bi-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                            @endif
                        </a>
                    </th>
                    <th scope="col">
                        <a href="#" wire:click.prevent="sortBy('expense_type')" class="text-decoration-none">
                            Expense Type
                            @if ($sortColumn === 'expense_type')
                                <i class="bi bi-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                            @endif
                        </a>
                    </th>
                    <th scope="col">
                        <a href="#" wire:click.prevent="sortBy('amount')" class="text-decoration-none">
                            Amount
                            @if ($sortColumn === 'amount')
                                <i class="bi bi-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                            @endif
                        </a>
                    </th>
                    <th scope="col">
                        <a href="#" wire:click.prevent="sortBy('payment_mode')" class="text-decoration-none">
                            Payment Mode
                            @if ($sortColumn === 'payment_mode')
                                <i class="bi bi-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                            @endif
                        </a>
                    </th>
                    <th scope="col">
                        <a href="#" wire:click.prevent="sortBy('expense_date')" class="text-decoration-none">
                            Date
                            @if ($sortColumn === 'expense_date')
                                <i class="bi bi-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                            @endif
                        </a>
                    </th>
                    <th scope="col">Add to Bill</th>
                    <th scope="col" style="width: 100px">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenses as $expense)
                    <tr>
                        <td class="border">
                            @if($expense->expense_category === 'trip' && $expense->trip)
                                <div>
                                    <span class="badge bg-primary">Trip</span><br>
                                    <strong>{{ $expense->trip->party->name ?? 'N/A' }}</strong><br>
                                    <small class="text-muted">{{ $expense->trip->origin ?? 'N/A' }} → {{ $expense->trip->destination ?? 'N/A' }}</small>
                                </div>
                            @elseif($expense->expense_category === 'truck' && $expense->truck)
                                <div>
                                    <span class="badge bg-info">Truck</span><br>
                                    <strong>{{ $expense->truck->truck_number }}</strong>
                                </div>
                            @else
                                <div>
                                    <span class="badge bg-secondary">Office</span>
                                </div>
                            @endif
                        </td>
                        <td class="border">{{ ucfirst($expense->expense_type) }}</td>
                        <td class="border">
                            <i class="fa fa-rupee-sign"></i> {{ number_format($expense->amount, 2) }}
                        </td>
                        <td class="border">{{ $paymentModes[$expense->payment_mode] ?? ucfirst($expense->payment_mode) }}</td>
                        <td class="border">{{ $expense->expense_date->format('d M Y') }}</td>
                        <td class="border">
                            @if($expense->add_to_party_bill)
                                <span class="badge bg-success">Yes</span>
                            @else
                                <span class="badge bg-secondary">No</span>
                            @endif
                        </td>
                        <td class="border">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-three-dots"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="#" wire:click="editExpense({{ $expense->id }})">
                                            <i class="bi bi-pencil-square me-1"></i>Edit
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item text-danger" href="#" wire:click="confirmDelete({{ $expense->id }})">
                                            <i class="bi bi-trash me-1"></i>Delete
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <div class="text-muted">
                                <i class="bi bi-inbox fs-1"></i>
                                <p class="mt-2">No expenses found.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($expenses->hasPages())
        <div class="d-flex justify-content-center mt-3">
            {{ $expenses->links() }}
        </div>
    @endif

    {{-- Delete Confirmation Modal --}}
    @if($showDeleteConfirm)
        <div class="modal fade show" style="display: block;" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirm Delete</h5>
                        <button type="button" class="btn-close" wire:click="cancelDelete"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete this expense? This action cannot be undone.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="cancelDelete">Cancel</button>
                        <button type="button" class="btn btn-danger" wire:click="deleteExpense">Delete</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    @endif

    {{-- Add Expense Modal --}}
    <div class="modal fade" id="addExpenseModal" tabindex="-1" aria-labelledby="addExpenseModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addExpenseModalLabel">
                        <i class="bi bi-cash me-2 text-warning"></i>
                        Add Expense
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @livewire('admin.trip.add-expense')
                </div>
            </div>
        </div>
    </div>

    {{-- Edit Expense Modal --}}
    <div class="modal fade" id="editExpenseModal" tabindex="-1" aria-labelledby="editExpenseModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editExpenseModalLabel">
                        <i class="bi bi-cash me-2 text-warning"></i>
                        Edit Expense
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if ($editingExpenseId)
                        <livewire:admin.trip.edit-expense :expense-id="$editingExpenseId" :key="'edit-' . $editingExpenseId" />
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Scripts for modals --}}
    @script
        <script>
            Livewire.on('showEditModal', () => {
                const modal = new bootstrap.Modal(document.getElementById('editExpenseModal'));
                modal.show();
            });

            Livewire.on('closeModal', (modalId) => {
                const modalElement = document.getElementById(modalId);
                const modal = bootstrap.Modal.getInstance(modalElement);
                if (modal) {
                    // Blur any focused elements inside the modal to avoid aria-hidden focus issues
                    const focusedElement = modalElement.querySelector(':focus');
                    if (focusedElement) {
                        focusedElement.blur();
                    }
                    modal.hide();
                }
            });
        </script>
    @endscript
</div>
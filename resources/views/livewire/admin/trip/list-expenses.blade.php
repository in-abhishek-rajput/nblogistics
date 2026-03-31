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

    {{-- Search and Filter Section --}}
    <div class="row mb-3">
        <div class="col-md-4">
            <input type="text" wire:model.live.debounce.300ms="search" class="form-control"
                placeholder="Search by expense type, notes, party, or route..." />
        </div>
        <div class="col-md-3">
            <select wire:model.live="paymentModeFilter" class="form-select">
                <option value="">All Payment Modes</option>
                @foreach ($paymentModes as $key => $mode)
                    <option value="{{ $key }}">{{ $mode }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <input type="date" wire:model.live="dateFilter" class="form-control"
                placeholder="Filter by date" />
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-warning w-100" data-bs-toggle="modal" data-bs-target="#addExpenseModal">
                <i class="bi bi-plus"></i> Add Expense
            </button>
        </div>
    </div>

    {{-- Expenses Table --}}
    <div class="table-responsive">
        <table class="table table-borderless border">
            <thead class="bg-light">
                <tr>
                    <th scope="col">
                        <a href="#" wire:click.prevent="sortBy('trip.party.name')" class="text-decoration-none">
                            Trip Reference
                            @if ($sortColumn === 'trip.party.name')
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
                        <td>
                            <div>
                                <strong>{{ $expense->trip->party->name ?? 'N/A' }}</strong><br>
                                <small class="text-muted">{{ $expense->trip->origin ?? 'N/A' }} → {{ $expense->trip->destination ?? 'N/A' }}</small>
                            </div>
                        </td>
                        <td>{{ ucfirst($expense->expense_type) }}</td>
                        <td>
                            <i class="fa fa-rupee-sign"></i> {{ number_format($expense->amount, 2) }}
                        </td>
                        <td>{{ $paymentModes[$expense->payment_mode] ?? ucfirst($expense->payment_mode) }}</td>
                        <td>{{ $expense->expense_date->format('d M Y') }}</td>
                        <td>
                            @if($expense->add_to_party_bill)
                                <span class="badge bg-success">Yes</span>
                            @else
                                <span class="badge bg-secondary">No</span>
                            @endif
                        </td>
                        <td>
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
</div>
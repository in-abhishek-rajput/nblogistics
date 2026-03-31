<div>
    {{-- Flash messages --}}
    @if ($flashMessage)
        <div class="alert alert-{{ $flashType == 'success' ? 'success' : 'danger' }} alert-dismissible fade show"
            role="alert">
            {{ $flashMessage }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"
                wire:click="$set('flashMessage', null)"></button>
        </div>
    @endif

    {{-- Search and Filter Section --}}
    <div class="row mb-3">
        <div class="col-md-4">
            <input type="text" wire:model.live.debounce.300ms="search" class="form-control"
                placeholder="Search by party, truck, or route..." />
        </div>
        <div class="col-md-3">
            <select wire:model.live="statusFilter" class="form-select">
                <option value="">All Statuses</option>
                @foreach ($statuses as $key => $status)
                    <option value="{{ $key }}">{{ $status['label'] }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <select wire:model.live="billingTypeFilter" class="form-select">
                <option value="">All Billing Types</option>
                @foreach ($billingTypes as $key => $type)
                    <option value="{{ $key }}">{{ $type }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-primary w-100" data-bs-toggle="offcanvas" data-bs-target="#addTripOffcanvas" aria-controls="addTripOffcanvas">
                <i class="bi bi-plus"></i> Add Trip
            </button>
        </div>
    </div>

    {{-- Trips Table --}}
    <div class="table-responsive">
        <table class="table table-borderless border">
            <thead class="bg-light-blue">
                <tr>
                    <th scope="col">
                        <a href="#" wire:click.prevent="sortBy('party.name')" class="text-decoration-none">
                            Party
                            @if ($sortColumn === 'party.name')
                                <i class="bi bi-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                            @endif
                        </a>
                    </th>
                    <th scope="col">
                        <a href="#" wire:click.prevent="sortBy('truck.truck_number')" class="text-decoration-none">
                            Truck
                            @if ($sortColumn === 'truck.truck_number')
                                <i class="bi bi-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                            @endif
                        </a>
                    </th>
                    <th scope="col">Route</th>
                    <th scope="col">
                        <a href="#" wire:click.prevent="sortBy('billing_type')" class="text-decoration-none">
                            Billing Type
                            @if ($sortColumn === 'billing_type')
                                <i class="bi bi-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                            @endif
                        </a>
                    </th>
                    <th scope="col">
                        <a href="#" wire:click.prevent="sortBy('freight_amount')" class="text-decoration-none">
                            Freight Amount
                            @if ($sortColumn === 'freight_amount')
                                <i class="bi bi-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                            @endif
                        </a>
                    </th>
                    <th scope="col">
                        <a href="#" wire:click.prevent="sortBy('pending_freight_amount')" class="text-decoration-none">
                            Pending Party Balance
                            @if ($sortColumn === 'pending_freight_amount')
                                <i class="bi bi-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                            @endif
                        </a>
                    </th>
                    <th scope="col" style="width: 100px">
                        <a href="#" wire:click.prevent="sortBy('status')" class="text-decoration-none">
                            Status
                            @if ($sortColumn === 'status')
                                <i class="bi bi-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                            @endif
                        </a>
                    </th>
                    <th scope="col" style="width: 100px">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($trips as $trip)
                    <tr>
                        <td class="border">{{ $trip->party->name ?? 'N/A' }}</td>
                        <td class="border">{{ $trip->truck->truck_number ?? 'N/A' }}</td>
                        <td class="border">{{ $trip->origin }} → {{ $trip->destination }}</td>
                        <td class="border">{{ $billingTypes[$trip->billing_type] ?? ucfirst(str_replace('_', ' ', $trip->billing_type)) }}</td>
                        <td class="border"><i class="fas fa-rupee-sign"></i> {{ number_format($trip->freight_amount, 2) }}</td>
                        <td class="border"><i class="fas fa-rupee-sign"></i> {{ number_format($trip->pending_freight_amount, 2) }}</td>
                        <td class="border">
                            <span class="badge bg-{{ $statuses[$trip->status]['color'] ?? 'secondary' }}">
                                <i class="bi {{ $statuses[$trip->status]['icon'] ?? 'bi-question' }}"></i>
                                {{ $statuses[$trip->status]['label'] ?? 'Unknown' }}
                            </span>
                        </td>
                        <td class="border">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-three-dots"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="#" wire:click="viewTrip({{ $trip->id }})">
                                            <i class="bi bi-eye me-2"></i>View
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#" wire:click="editTrip({{ $trip->id }})">
                                            <i class="bi bi-pencil me-2"></i>Edit
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item text-danger" href="#" wire:click="confirmDeleteTrip({{ $trip->id }})">
                                            <i class="bi bi-trash me-2"></i>Delete
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <div class="text-muted">
                                <i class="bi bi-truck fs-1"></i>
                                <p class="mt-2">No trips found.</p>
                                @if ($search || $statusFilter || $billingTypeFilter)
                                    <p>Try adjusting your search or filter criteria.</p>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if ($trips->hasPages())
        <div class="d-flex justify-content-center mt-3">
            {{ $trips->links('vendor.pagination.bootstrap-5') }}
        </div>
    @endif

    {{-- Add Trip Offcanvas --}}
    <div class="offcanvas offcanvas-end" tabindex="-1" id="addTripOffcanvas" aria-labelledby="addTripOffcanvasLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="addTripOffcanvasLabel">Add New Trip</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <livewire:admin.trip.add-trip />
        </div>
    </div>

    {{-- Edit Trip Offcanvas --}}
    <div class="offcanvas offcanvas-end" tabindex="-1" id="editTripOffcanvas" aria-labelledby="editTripOffcanvasLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="editTripOffcanvasLabel">Edit Trip</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            @if ($editingTripId)
                <livewire:admin.trip.edit-trip :trip-id="$editingTripId" :key="'edit-' . $editingTripId" />
            @endif
        </div>
    </div>

    {{-- View Trip Offcanvas --}}
    <div class="offcanvas offcanvas-end" style="width: 80% !important" tabindex="-1" id="viewTripOffcanvas" aria-labelledby="viewTripOffcanvasLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="viewTripOffcanvasLabel">View Trip</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            @if ($viewingTripId)
                <livewire:admin.trip.view-trip :trip-id="$viewingTripId" :key="'view-' . $viewingTripId" />
            @endif
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    @if ($showDeleteConfirm)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" wire:click="cancelDelete"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this trip? This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="cancelDelete">Cancel</button>
                    <button type="button" class="btn btn-danger" wire:click="deleteTrip">Delete Trip</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Scripts for offcanvas --}}
    @script
        <script>
            Livewire.on('showEditOffcanvas', () => {
                const offcanvas = new bootstrap.Offcanvas(document.getElementById('editTripOffcanvas'));
                offcanvas.show();
            });

            Livewire.on('showViewOffcanvas', () => {
                const offcanvas = new bootstrap.Offcanvas(document.getElementById('viewTripOffcanvas'));
                offcanvas.show();
            });

            Livewire.on('closeOffcanvas', (offcanvasId) => {
                const offcanvasElement = document.getElementById(offcanvasId);
                const offcanvas = bootstrap.Offcanvas.getInstance(offcanvasElement);
                if (offcanvas) {
                    offcanvas.hide();
                }
            });

            // For trip view modals
            Livewire.on('show-confirm-modal', () => {
                $('#confirmModal').modal('show');
            });
            Livewire.on('show-complete-modal', () => {
                $('#completeModal').modal('show');
            });
            Livewire.on('close-modals', () => {
                $('.modal').modal('hide');
            });
        </script>
    @endscript
</div>
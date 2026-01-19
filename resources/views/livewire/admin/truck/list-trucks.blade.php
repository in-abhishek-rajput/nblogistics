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
        <div class="col-md-3">
            <input type="text" wire:model.live.debounce.300ms="search" class="form-control"
                placeholder="Search by truck number..." />
        </div>
        <div class="col-md-2">
            <select wire:model.live="statusFilter" class="form-select">
                <option value="">All Statuses</option>
                @foreach ($statuses as $key => $status)
                    <option value="{{ $key }}">{{ $status['label'] }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select wire:model.live="typeFilter" class="form-select">
                <option value="">All Types</option>
                @foreach ($types as $key => $type)
                    <option value="{{ $key }}">{{ $type }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select wire:model.live="ownershipFilter" class="form-select">
                <option value="">All Ownership</option>
                @foreach ($ownerships as $key => $ownership)
                    <option value="{{ $key }}">{{ $ownership }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#addTruckModal">
                <i class="bi bi-plus"></i> Add Truck
            </button>
        </div>
    </div>

    {{-- Trucks Table --}}
    <div class="table-responsive">
        <table class="table table-borderless border">
            <thead class="bg-light-blue">
                <tr>
                    <th scope="col">
                        <a href="#" wire:click.prevent="sortBy('truck_number')" class="text-decoration-none">
                            Truck Number
                            @if ($sortColumn === 'truck_number')
                                <i class="bi bi-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                            @endif
                        </a>
                    </th>
                    <th scope="col">
                        <a href="#" wire:click.prevent="sortBy('truck_type')" class="text-decoration-none">
                            Type
                            @if ($sortColumn === 'truck_type')
                                <i class="bi bi-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                            @endif
                        </a>
                    </th>
                    <th scope="col">
                        <a href="#" wire:click.prevent="sortBy('ownership')" class="text-decoration-none">
                            Ownership
                            @if ($sortColumn === 'ownership')
                                <i class="bi bi-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                            @endif
                        </a>
                    </th>
                    <th scope="col">Driver</th>
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
                @forelse($trucks as $truck)
                    <tr>
                        <td class="border">{{ $truck->truck_number }}</td>
                        <td class="border">{{ $types[$truck->truck_type] ?? $truck->truck_type }}</td>
                        <td class="border">{{ $ownerships[$truck->ownership] ?? ucfirst($truck->ownership) }}</td>
                        <td class="border">{{ $truck->driver ? $truck->driver->name : 'N/A' }}</td>
                        <td class="border">
                            <span class="badge bg-{{ $statuses[$truck->status]['color'] ?? 'secondary' }}">
                                <i class="bi {{ $statuses[$truck->status]['icon'] ?? 'bi-question' }}"></i>
                                {{ $statuses[$truck->status]['label'] ?? 'Unknown' }}
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
                                        <a class="dropdown-item" href="#"
                                            wire:click="editTruck({{ $truck->id }})">
                                            <i class="bi bi-pencil me-2"></i>Edit
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <div class="text-muted">
                                <i class="bi bi-truck fs-1"></i>
                                <p class="mt-2">No trucks found.</p>
                                @if ($search || $statusFilter || $typeFilter || $ownershipFilter)
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
    @if ($trucks->hasPages())
        <div class="d-flex justify-content-center mt-3">
            {{ $trucks->links('vendor.pagination.bootstrap-5') }}
        </div>
    @endif

    {{-- Add Truck Modal --}}
    <div class="modal fade" id="addTruckModal" tabindex="-1" aria-labelledby="addTruckModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addTruckModalLabel">Add New Truck</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <livewire:admin.truck.add-truck />
                </div>
            </div>
        </div>

        {{-- Scripts for modals --}}
        @script
            <script>
                Livewire.on('showEditModal', () => {
                    const modal = new bootstrap.Modal(document.getElementById('editTruckModal'));
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
    {{-- Edit Truck Modal --}}
    <div class="modal fade" id="editTruckModal" tabindex="-1" aria-labelledby="editTruckModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-md   ">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editTruckModalLabel">Edit Truck</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if ($editingTruckId)
                        <livewire:admin.truck.edit-truck :truck-id="$editingTruckId" :key="'edit-' . $editingTruckId" />
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>

<div>
    {{-- Flash messages --}}
    @if ($flashMessage)
        <div class="alert alert-{{ $flashType == 'success' ? 'success' : 'danger' }} alert-dismissible fade show" role="alert">
            {{ $flashMessage }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" wire:click="$set('flashMessage', null)"></button>
        </div>
    @endif

    {{-- Search and Filter Section --}}
    <div class="row mb-3">
        <div class="col-md-6">
            <input type="text" wire:model.live.debounce.300ms="search" class="form-control"
                placeholder="Search by name or mobile..." />
        </div>
        <div class="col-md-4">
            <select wire:model.live="statusFilter" class="form-select">
                <option value="">All Statuses</option>
                @foreach ($statuses as $key => $status)
                    <option value="{{ $key }}">{{ $status['label'] }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal"
                data-bs-target="#addDriverModal">
                <i class="bi bi-plus"></i> Add Driver
            </button>
        </div>
    </div>

    {{-- Drivers Table --}}
    <div class="table-responsive">
        <table class="table table-borderless border">
            <thead class="bg-light-blue">
                <tr>
                    <th scope="col">
                        <a href="#" wire:click.prevent="sortBy('name')" class="text-decoration-none">
                            Driver Name
                            @if ($sortColumn === 'name')
                                <i class="bi bi-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                            @endif
                        </a>
                    </th>
                    <th scope="col">Truck Type</th>
                    <th scope="col">Mobile</th>
                    <th scope="col">Opening Balance</th>
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
                @forelse($drivers as $driver)
                    <tr>
                        <td class="border">{{ $driver->name }}</td>
                        <td class="border">{{ $driver->truck ? ($types[$driver->truck->truck_type] ?? $driver->truck->truck_type) : 'N/A' }}</td>
                        <td class="border">{{ $driver->mobile ?? 'N/A' }}</td>
                        <td class="border">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" fill="currentColor"
                                class="bi bi-currency-rupee" viewBox="0 0 14 20">
                                <path
                                    d="M4 3.06h2.726c1.22 0 2.12.575 2.325 1.724H4v1.051h5.051C8.855 7.001 8 7.558 6.788 7.558H4v1.317L8.437 14h2.11L6.095 8.884h.855c2.316-.018 3.465-1.476 3.688-3.049H12V4.784h-1.345c-.08-.778-.357-1.335-.793-1.732H12V2H4z" />
                            </svg>
                            {{ number_format($driver->opening_balance, 2) }}
                        </td>
                        <td class="border">
                            <span class="badge bg-{{ $statuses[$driver->status]['color'] ?? 'secondary' }}">
                                <i class="bi {{ $statuses[$driver->status]['icon'] ?? 'bi-question' }}"></i>
                                {{ $statuses[$driver->status]['label'] ?? 'Unknown' }}
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
                                        <a class="dropdown-item" href="#" wire:click="editDriver({{ $driver->id }})">
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
                                <i class="bi bi-person-x fs-1"></i>
                                <p class="mt-2">No drivers found.</p>
                                @if ($search || $statusFilter)
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
    @if ($drivers->hasPages())
        <div class="d-flex justify-content-center mt-3">
           {{ $drivers->links('vendor.pagination.bootstrap-5') }}
        </div>
    @endif

    {{-- Add Driver Modal --}}
    <div class="modal fade" id="addDriverModal" tabindex="-1" aria-labelledby="addDriverModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addDriverModalLabel">Add New Driver</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <livewire:admin.driver.add-driver />
                </div>
            </div>
        </div>
   
        {{-- Scripts for modals --}}
        @script
            <script>
                Livewire.on('showEditModal', () => {
                    const modal = new bootstrap.Modal(document.getElementById('editDriverModal'));
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
    {{-- Edit Driver Modal --}}
    <div class="modal fade" id="editDriverModal" tabindex="-1" aria-labelledby="editDriverModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-md   ">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editDriverModalLabel">Edit Driver</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if($editingDriverId)
                        <livewire:admin.driver.edit-driver :driver-id="$editingDriverId" :key="'edit-'.$editingDriverId" />
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>

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
        <div class="col-md-4">
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
                data-bs-target="#addPartyModal">
                <i class="bi bi-plus"></i> Add Party
            </button>
        </div>
        <div class="col-md-2">
                <button type="button" class="btn btn-success w-100" data-bs-toggle="modal"
                data-bs-target="#addTripModal">
                <i class="bi bi-plus me-1"></i> Add Trip
            </button>
        </div>
    </div>

    {{-- Parties Table --}}
    <div class="table-responsive">
        <table class="table table-borderless border">
            <thead class="bg-light-blue">
                <tr>
                    <th scope="col">
                        <a href="#" wire:click.prevent="sortBy('name')" class="text-decoration-none">
                            Party Name
                            @if ($sortColumn === 'name')
                                <i class="bi bi-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                            @endif
                        </a>
                    </th>
                    <th scope="col">Mobile Number</th>
                    <th scope="col">Party Balance</th>
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
                @forelse($parties as $party)
                    <tr>
                        <td class="border">{{ $party->name }}</td>
                        <td class="border">{{ $party->mobile ?? 'N/A' }}</td>
                        <td class="border text-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" fill="currentColor"
                                class="bi bi-currency-rupee" viewBox="0 0 15 15">
                                <path
                                    d="M4 3.06h2.726c1.22 0 2.12.575 2.325 1.724H4v1.051h5.051C8.855 7.001 8 7.558 6.788 7.558H4v1.317L8.437 14h2.11L6.095 8.884h.855c2.316-.018 3.465-1.476 3.688-3.049H12V4.784h-1.345c-.08-.778-.357-1.335-.793-1.732H12V2H4z" />
                            </svg>
                            {{ number_format($party->opening_balance, 2) }}
                        </td>
                        <td class="border">
                            <span class="badge bg-{{ $statuses[$party->status]['color'] ?? 'secondary' }}">
                                <i class="bi {{ $statuses[$party->status]['icon'] ?? 'bi-question' }}"></i>
                                {{ $statuses[$party->status]['label'] ?? 'Unknown' }}
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
                                        <a class="dropdown-item" href="#" wire:click="editParty({{ $party->id }})">
                                            <i class="bi bi-pencil me-2"></i>Edit
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#" wire:click="toggleStatus({{ $party->id }})">
                                            <i class="bi bi-toggle-{{ $party->status === 'active' ? 'on' : 'off' }} me-2"></i>
                                            {{ $party->status === 'active' ? 'Deactivate' : 'Activate' }}
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4">
                            <div class="text-muted">
                                <i class="bi bi-person-x fs-1"></i>
                                <p class="mt-2">No parties found.</p>
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
    @if ($parties->hasPages())
        <div class="d-flex justify-content-center mt-3">
           {{ $parties->links('vendor.pagination.bootstrap-5') }}
        </div>
    @endif

    {{-- Add Party Modal --}}
    <div class="modal fade" id="addPartyModal" tabindex="-1" aria-labelledby="addPartyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addPartyModalLabel">Add New Party</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <livewire:admin.party.add-party />
                </div>
            </div>
        </div>

        {{-- Scripts for modals --}}
        @script
            <script>
                Livewire.on('showEditModal', () => {
                    const modal = new bootstrap.Modal(document.getElementById('editPartyModal'));
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
    {{-- Edit Party Modal --}}
    <div class="modal fade" id="editPartyModal" tabindex="-1" aria-labelledby="editPartyModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-md   ">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editPartyModalLabel">Edit Party</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if($editingPartyId)
                        <livewire:admin.party.edit-party :party-id="$editingPartyId" :key="'edit-'.$editingPartyId" />
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>

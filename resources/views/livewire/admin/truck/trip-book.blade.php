<div>
    <div wire:ignore.self class="offcanvas offcanvas-end" tabindex="-1" id="tripBookOffcanvas" aria-labelledby="tripBookOffcanvasLabel"
        style="width:500px;">
        <div class="offcanvas-header border-bottom py-3 px-4">
            <div>
                <h5 class="offcanvas-title fw-bold" id="tripBookOffcanvasLabel">Trip Book</h5>
                <div class="text-muted small mt-1">
                    {{ $truck->truck_number }} · {{ $types[$truck->truck_type] ?? ucfirst($truck->truck_type) }} · {{ ucfirst($truck->truck_status ?? $truck->status ?? 'Unknown') }}
                </div>
            </div>
            <button type="button" class="btn-close text-dark" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-4 position-relative" style="min-height: calc(100vh - 110px);">
            <div class="row g-3 mb-4">
                <div class="col-12">
                    <label class="form-label small mb-1 text-muted">Date</label>
                    <select wire:model.live="monthFilter" class="form-select form-select-sm">
                        @foreach ($monthOptions as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                @if ($monthFilter === 'custom')
                    <div class="col-6">
                        <label class="form-label small mb-1 text-muted">From</label>
                        <input type="date" wire:model.live="custom_from" class="form-control form-control-sm">
                    </div>
                    <div class="col-6">
                        <label class="form-label small mb-1 text-muted">To</label>
                        <input type="date" wire:model.live="custom_to" class="form-control form-control-sm">
                    </div>
                @endif
            </div>

            <div class="row g-3 mb-4">
                <div class="col-6">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
                        <div class="card-body py-3 px-4">
                            <p class="text-uppercase text-muted mb-1" style="font-size:0.70rem; letter-spacing:.05em;">Total Trips</p>
                            <h4 class="mb-0 fw-bold">{{ $summary['count'] ?? 0 }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
                        <div class="card-body py-3 px-4">
                            <p class="text-uppercase text-muted mb-1" style="font-size:0.70rem; letter-spacing:.05em;">Total Revenue</p>
                            <h4 class="mb-0 fw-bold">₹ {{ number_format($summary['revenue'] ?? 0, 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                <h6 class="mb-0">Trip Records</h6>
                <button type="button" class="btn btn-sm btn-primary px-3" wire:click="showAddTripModal">
                    + Add Trip
                </button>
            </div>

            <div class="row g-3">
                @forelse ($trips as $trip)
                    <div class="col-12">
                        <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <div class="text-muted small">{{ $trip->start_date?->format('d M Y') ?? '-' }}</div>
                                        <div class="fw-semibold">{{ $trip->origin }} → {{ $trip->destination }}</div>
                                        <div class="small">{{ $trip->party->name ?? $trip->party_name }}</div>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-{{ ($statuses[$trip->status]['color'] ?? 'secondary') }}">
                                            {{ $statuses[$trip->status]['label'] ?? ucfirst($trip->status) }}
                                        </span>
                                        <div class="fw-semibold mt-1">₹ {{ number_format($trip->freight_amount, 2) }}</div>
                                    </div>
                                </div>
                                <div class="d-flex flex-wrap gap-2">
                                    <button type="button" class="btn btn-sm btn-outline-primary" wire:click="viewTrip({{ $trip->id }})">
                                        View
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="editTrip({{ $trip->id }})">
                                        Edit
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                        onclick="confirm('Delete this trip?') || event.stopImmediatePropagation()"
                                        wire:click="deleteTrip({{ $trip->id }})">
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="text-center text-muted py-4">
                            No trip records found for the selected period.
                        </div>
                    </div>
                @endforelse
            </div>

            <div class="mt-4">
                {{ $trips->links('vendor.pagination.bootstrap-5') }}
            </div>
        </div>
    </div>

    @include('livewire.admin.truck.modals.add-trip-modal')
    @include('livewire.admin.truck.modals.edit-trip-modal')

    @script
    <script>
        const tripCanvasEl = document.getElementById('tripBookOffcanvas');
        const tripOffcanvas = new bootstrap.Offcanvas(tripCanvasEl);

        window.addEventListener('openTripBookOffcanvas', () => {
            tripOffcanvas.show();
        });

        window.addEventListener('showAddTripModal', () => {
            window.showTruckBookModal('addTripModal');
        });

        window.addEventListener('showEditTripModal', () => {
            window.showTruckBookModal('editTripModal');
        });

        window.addEventListener('closeModal', event => {
            window.hideTruckBookModal(event.detail);
        });
    </script>
    @endscript
</div>
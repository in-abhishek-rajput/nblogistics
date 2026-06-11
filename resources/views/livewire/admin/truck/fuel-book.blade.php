<div>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightgallery/2.7.2/lightgallery.min.css" />
    
    <div wire:ignore.self class="offcanvas offcanvas-end" tabindex="-1" id="fuelBookOffcanvas" aria-labelledby="fuelBookOffcanvasLabel"
        style="width:500px;">
        <div class="offcanvas-header border-bottom py-3 px-4">
            <div>
                <h5 class="offcanvas-title fw-bold" id="fuelBookOffcanvasLabel">Fuel Book</h5>
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
                            <p class="text-uppercase text-muted mb-1" style="font-size:0.70rem; letter-spacing:.05em;">Refuel Quantity</p>
                            <h4 class="mb-0 fw-bold">{{ $summary['quantity'] ? number_format($summary['quantity'], 2) . ' L' : '0 L' }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
                        <div class="card-body py-3 px-4">
                            <p class="text-uppercase text-muted mb-1" style="font-size:0.70rem; letter-spacing:.05em;">Average Mileage</p>
                            <h4 class="mb-0 fw-bold">{{ $summary['mileage'] > 0 ? number_format($summary['mileage'], 2) . ' KM/L' : '–' }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
                        <div class="card-body py-3 px-4">
                            <p class="text-uppercase text-muted mb-1" style="font-size:0.70rem; letter-spacing:.05em;">Total Fuel Cost</p>
                            <h4 class="mb-0 fw-bold">₹ {{ number_format($summary['cost'] ?? 0, 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                <h6 class="mb-0">Fuel Expense Records</h6>
                <button type="button" class="btn btn-sm btn-primary px-3" wire:click="showAddFuelExpenseModal">
                    + Add Fuel Expense
                </button>
            </div>

            <div class="row g-3">
                @forelse ($groupedFuelExpenses as $groupLabel => $expenses)
                    <div class="col-12">
                        <div class="text-uppercase text-muted small mb-2" style="letter-spacing:.08em;">{{ $groupLabel }}</div>
                    </div>
                    @foreach ($expenses as $expense)
                        <div class="col-12">
                            <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            @if ($expense->fuel_quantity && $expense->rate_per_litre)
                                                <div class="text-muted small">{{ number_format($expense->fuel_quantity, 2) }} L × ₹ {{ number_format($expense->rate_per_litre, 2) }}</div>
                                            @else
                                                <div class="text-muted small">Fuel Expense</div>
                                            @endif
                                            <div class="fw-semibold">{{ $expense->expense_date?->format('d M Y') }}</div>
                                        </div>
                                        <div class="text-end">
                                            @if ($expense->current_km_reading)
                                                <div class="text-muted small">{{ number_format($expense->current_km_reading, 0) }} KM</div>
                                            @endif
                                            <div class="fw-semibold">₹ {{ number_format($expense->expense_amount, 2) }}</div>
                                        </div>
                                    </div>
                                    @if ($expense->bill_file && Storage::disk('public')->exists($expense->bill_file))
                                        @php
                                            $billUrl = asset('storage/' . $expense->bill_file);
                                        @endphp
                                        <div class="mb-3" data-lightbox="fuel-bills-{{ $expense->id }}">
                                            <a href="{{ $billUrl }}" data-lightbox="fuel-bills-{{ $expense->id }}" class="d-inline-block">
                                                <img src="{{ $billUrl }}" alt="Bill" class="img-thumbnail" style="max-width: 100px; max-height: 100px; object-fit: cover; cursor: pointer;">
                                            </a>
                                        </div>
                                    @endif
                                    <div class="d-flex flex-wrap gap-2">
                                        <button type="button" class="btn btn-sm btn-outline-primary" wire:click="editFuelExpense({{ $expense->id }})">
                                            Edit
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                            onclick="confirm('Delete this fuel expense?') || event.stopImmediatePropagation()"
                                            wire:click="deleteFuelExpense({{ $expense->id }})">
                                            Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @empty
                    <div class="col-12">
                        <div class="text-center text-muted py-4">
                            No fuel expense records found for the selected period.
                        </div>
                    </div>
                @endforelse
            </div>

            <div class="mt-4">
                {{ $fuelExpenses->links('vendor.pagination.bootstrap-5') }}
            </div>
        </div>
    </div>

    @include('livewire.admin.truck.modals.add-fuel-expense-modal')
    @include('livewire.admin.truck.modals.edit-fuel-expense-modal')

    @script
        <script>
            const fuelCanvasEl = document.getElementById('fuelBookOffcanvas');
            const fuelOffcanvas = new bootstrap.Offcanvas(fuelCanvasEl);

            window.addEventListener('openFuelBookOffcanvas', () => {
                fuelOffcanvas.show();
            });

            window.addEventListener('showAddFuelExpenseModal', () => {
                const modal = new bootstrap.Modal(document.getElementById('addFuelExpenseModal'));
                modal.show();
            });

            window.addEventListener('showEditFuelExpenseModal', () => {
                const modal = new bootstrap.Modal(document.getElementById('editFuelExpenseModal'));
                modal.show();
            });

            window.addEventListener('closeModal', event => {
                const modalElement = document.getElementById(event.detail);
                if (!modalElement) {
                    return;
                }
                const modal = bootstrap.Modal.getInstance(modalElement);
                if (modal) {
                    const focusedElement = modalElement.querySelector(':focus');
                    if (focusedElement) {
                        focusedElement.blur();
                    }
                    modal.hide();
                }
            });

            function initFuelLightGallery() {
                if (typeof lightGallery === 'undefined') {
                    return;
                }

                const lgElements = document.querySelectorAll('[data-lightbox]');
                lgElements.forEach(el => {
                    if (!el.classList.contains('lg-initialized')) {
                        lightGallery(el, {
                            selector: 'img',
                            plugins: [],
                        });
                        el.classList.add('lg-initialized');
                    }
                });
            }

            document.addEventListener('DOMContentLoaded', initFuelLightGallery);
            window.addEventListener('livewire:update', () => {
                initFuelLightGallery();
                if (fuelCanvasEl.classList.contains('show')) {
                    fuelOffcanvas.show();
                }
            });
        </script>
    @endscript

    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightgallery/2.7.2/lightgallery.min.js"></script>
</div>

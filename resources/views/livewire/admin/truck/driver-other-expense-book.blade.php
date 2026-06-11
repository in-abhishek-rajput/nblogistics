<div>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightgallery/2.7.2/css/lightgallery-bundle.min.css" />

    <div wire:ignore.self class="offcanvas offcanvas-end" tabindex="-1" id="driverExpenseBookOffcanvas" aria-labelledby="driverExpenseBookOffcanvasLabel"
        style="width:500px;">
        <div class="offcanvas-header border-bottom py-3 px-4">
            <div>
                <h5 class="offcanvas-title fw-bold" id="driverExpenseBookOffcanvasLabel">Driver & Other Expenses</h5>
                <div class="text-muted small mt-1">
                    {{ $truck->truck_number }} · {{ $types[$truck->truck_type] ?? ucfirst($truck->truck_type) }} · {{ ucfirst($truck->status ?? 'Unknown') }}
                </div>
            </div>
            <button type="button" class="btn-close text-dark" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-4 position-relative" style="min-height: calc(100vh - 110px);">
            <div class="row g-3 mb-4">
                <div class="col-6">
                    <label class="form-label small mb-1 text-muted">Date</label>
                    <select wire:model.live="monthFilter" class="form-select form-select-sm">
                        @foreach ($monthOptions as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6">
                    <label class="form-label small mb-1 text-muted">Expense Type</label>
                    <select wire:model.live="expenseTypeFilter" class="form-select form-select-sm">
                        <option value="all">All Expense</option>
                        @foreach ($expenseTypeFilterOptions as $type)
                            <option value="{{ $type }}">{{ $type }}</option>
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

            <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
                <div class="card-body py-3 px-4">
                    <p class="text-uppercase text-muted mb-1" style="font-size:0.70rem; letter-spacing:.05em;">Total Cost</p>
                    <h4 class="mb-0 fw-bold">₹ {{ number_format($summary['cost'] ?? 0, 2) }}</h4>
                </div>
            </div>

            <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                <h6 class="mb-0">Expense Records</h6>
                <button type="button" class="btn btn-sm btn-primary px-3" wire:click="showAddExpenseModal">
                    + Add Entry
                </button>
            </div>

            <div class="row g-3">
                @forelse ($groupedExpenses as $groupLabel => $items)
                    <div class="col-12">
                        <div class="text-uppercase text-muted small mb-2" style="letter-spacing:.08em;">{{ $groupLabel }}</div>
                    </div>
                    @foreach ($items as $expense)
                        <div class="col-12">
                            <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            <div class="fw-semibold">{{ $expense->expense_type }}</div>
                                            <div class="text-muted small">{{ $expense->expense_date?->format('d M Y') }}</div>
                                            <div class="text-muted small text-capitalize">{{ str_replace('_', ' ', $expense->payment_mode) }}</div>
                                        </div>
                                        <div class="text-end">
                                            @if ($expense->current_km_reading)
                                                <div class="text-muted small">{{ number_format($expense->current_km_reading, 0) }} KM</div>
                                            @endif
                                            <div class="fw-semibold">₹ {{ number_format($expense->amount, 2) }}</div>
                                        </div>
                                    </div>
                                    @if ($expense->expense_image && Storage::disk('public')->exists($expense->expense_image))
                                        @php $imageUrl = asset('storage/' . $expense->expense_image); @endphp
                                        <div class="mb-3" data-lightbox="driver-expense-{{ $expense->id }}">
                                            <a href="{{ $imageUrl }}" data-src="{{ $imageUrl }}">
                                                <img src="{{ $imageUrl }}" alt="Receipt" class="img-thumbnail" style="max-width:100px;max-height:100px;object-fit:cover;cursor:pointer;">
                                            </a>
                                        </div>
                                    @endif
                                    <div class="d-flex flex-wrap gap-2">
                                        <button type="button" class="btn btn-sm btn-outline-primary" wire:click="editDriverExpense({{ $expense->id }})">Edit</button>
                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                            onclick="confirm('Delete this expense?') || event.stopImmediatePropagation()"
                                            wire:click="deleteDriverExpense({{ $expense->id }})">Delete</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @empty
                    <div class="col-12">
                        <div class="text-center text-muted py-4">No expense records found for the selected period.</div>
                    </div>
                @endforelse
            </div>

            <div class="mt-4">{{ $expenses->links('vendor.pagination.bootstrap-5') }}</div>
        </div>
    </div>

    @include('livewire.admin.truck.modals.add-driver-expense-modal')

    @script
        <script>
            const driverExpenseCanvasEl = document.getElementById('driverExpenseBookOffcanvas');
            const driverExpenseOffcanvas = new bootstrap.Offcanvas(driverExpenseCanvasEl);

            window.addEventListener('openDriverExpenseBookOffcanvas', () => driverExpenseOffcanvas.show());
            window.addEventListener('showAddDriverExpenseModal', () => {
                new bootstrap.Modal(document.getElementById('addDriverExpenseModal')).show();
            });
            window.addEventListener('closeModal', event => {
                const modalElement = document.getElementById(event.detail);
                if (!modalElement) return;
                const modal = bootstrap.Modal.getInstance(modalElement);
                if (modal) {
                    modalElement.querySelector(':focus')?.blur();
                    modal.hide();
                }
            });

            function initDriverExpenseLightGallery() {
                if (typeof lightGallery === 'undefined') return;
                document.querySelectorAll('[data-lightbox]').forEach(el => {
                    if (el.classList.contains('lg-initialized')) return;
                    lightGallery(el, { selector: 'a[data-src]', download: true, zoom: true });
                    el.classList.add('lg-initialized');
                });
            }

            document.addEventListener('DOMContentLoaded', initDriverExpenseLightGallery);
            window.addEventListener('livewire:update', () => {
                initDriverExpenseLightGallery();
                if (driverExpenseCanvasEl.classList.contains('show')) driverExpenseOffcanvas.show();
            });
        </script>
    @endscript

    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightgallery/2.7.2/lightgallery.min.js"></script>
</div>

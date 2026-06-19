<div>
    <div class="offcanvas offcanvas-end" tabindex="-1" id="emiBookOffcanvas" aria-labelledby="emiBookOffcanvasLabel"
        style="width:500px;">
        <div class="offcanvas-header border-bottom py-3 px-4">
            <div>
                <h5 class="offcanvas-title fw-bold" id="emiBookOffcanvasLabel">EMI Book</h5>
                <div class="text-muted small mt-1">{{ $truck->truck_number }} · {{ $types[$truck->truck_type] ?? ucfirst($truck->truck_type) }}</div>
            </div>
            <button type="button" class="btn-close text-dark" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-4 position-relative" style="min-height: calc(100vh - 110px);">
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-start justify-content-between gap-3">
                        <div>
                            <div class="text-muted small text-uppercase mb-2">Finance Company</div>
                            <div class="fw-semibold fs-5">{{ $summary['company'] ?? 'No EMI assigned' }}</div>
                        </div>
                        <span class="badge bg-{{ ($summary['status'] ?? null) === 'Paid' ? 'success' : 'warning' }} text-uppercase"
                            style="font-size:0.72rem;">{{ $summary['status'] ?? 'Pending' }}</span>
                    </div>
                    <div class="row mt-4 gy-3">
                        <div class="col-6">
                            <div class="text-muted small">Monthly EMI</div>
                            <div class="fw-semibold">₹ {{ number_format($summary['monthly_emi'] ?? 0, 2) }}</div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted small">Next Due</div>
                            <div class="fw-semibold">{{ $summary['next_due'] ?? '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                <h6 class="mb-0">EMI Schedule</h6>
                <div class="d-flex gap-2">
                    @if ($summary)
                        <button type="button" class="btn btn-sm btn-outline-secondary px-3" wire:click="showEditEmiPlanModal">
                            Edit EMI Plan
                        </button>
                    @endif
                    <button type="button" class="btn btn-sm btn-primary px-3" wire:click="showAddEmiModal">
                        + Add EMI
                    </button>
                </div>
            </div>

            <div class="row g-3">
                @forelse ($payments as $payment)
                    <div class="col-12">
                        <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <div class="text-muted small">{{ $payment->due_date->format('d M Y') }}</div>
                                        <div class="fw-semibold">₹ {{ number_format($payment->amount, 2) }}</div>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-{{ $payment->status === 'paid' ? 'success' : 'warning' }} text-uppercase">
                                            {{ $payment->status === 'paid' ? 'Paid' : 'Pending' }}
                                        </span>
                                        @if ($payment->status === 'paid')
                                            <div class="text-muted small mt-1">Paid on {{ $payment->payment_date?->format('d M Y') }}</div>
                                        @endif
                                    </div>
                                </div>
                                <div class="d-flex flex-wrap gap-2">
                                    @if ($payment->status === 'paid')
                                        <button type="button" class="btn btn-sm btn-outline-secondary"
                                            wire:click="viewPayment({{ $payment->id }})">
                                            View
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-sm btn-outline-primary"
                                            wire:click="editPayment({{ $payment->id }})">
                                            Edit
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-success"
                                            onclick="confirm('Mark this EMI as complete?') || event.stopImmediatePropagation()"
                                            wire:click="markPaymentComplete({{ $payment->id }})">
                                            Mark as Complete
                                        </button>
                                    @endif
                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                        onclick="confirm('Delete this EMI schedule?') || event.stopImmediatePropagation()"
                                        wire:click="deletePayment({{ $payment->id }})">
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="text-center text-muted py-4">
                            No EMI schedule has been created for this truck yet.
                        </div>
                    </div>
                @endforelse
            </div>

            <div class="mt-4">
                {{ $payments->links('vendor.pagination.bootstrap-5') }}
            </div>

        </div>
    </div>

    @include('livewire.admin.truck.modals.add-emi-modal')
    @include('livewire.admin.truck.modals.edit-emi-modal')
    @include('livewire.admin.truck.modals.edit-emi-plan-modal')
    @include('livewire.admin.truck.modals.view-payment-modal')

    @script
        <script>
            const emiCanvasEl = document.getElementById('emiBookOffcanvas');
            const emiOffcanvas = new bootstrap.Offcanvas(emiCanvasEl);

            window.addEventListener('openEmiBookOffcanvas', () => {
                emiOffcanvas.show();
            });

            window.addEventListener('showAddEmiModal', () => {
                window.showTruckBookModal('addEmiModal');
            });

            window.addEventListener('showEditEmiModal', () => {
                window.showTruckBookModal('editPaymentModal');
            });

            window.addEventListener('showEditEmiPlanModal', () => {
                window.showTruckBookModal('editEmiPlanModal');
            });

            window.addEventListener('showViewPaymentModal', () => {
                window.showTruckBookModal('viewPaymentModal');
            });

            window.addEventListener('closeModal', event => {
                window.hideTruckBookModal(event.detail);
            });
        </script>
    @endscript
</div>

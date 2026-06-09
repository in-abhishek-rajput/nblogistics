<div wire:ignore.self class="modal fade" id="viewPaymentModal" tabindex="-1" aria-labelledby="viewPaymentModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title fw-bold" id="viewPaymentModalLabel">EMI Payment Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-0 px-4">
                @php
                    $payment = optional(App\Models\TruckEmiPayment::find($viewingPaymentId));
                @endphp
                <div class="mb-3">
                    <label class="form-label fw-semibold">Due Date</label>
                    <div class="form-control bg-light">{{ $payment->due_date?->format('d M Y') ?? '-' }}</div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Amount Paid</label>
                    <div class="form-control bg-light">₹ {{ number_format($payment->amount ?? 0, 2) }}</div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Payment Date</label>
                    <div class="form-control bg-light">{{ $payment->payment_date?->format('d M Y') ?? '-' }}</div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Status</label>
                    <div class="form-control bg-light text-uppercase">{{ $payment->status ?? '-' }}</div>
                </div>
            </div>
            <div class="modal-footer border-top-0 pt-0 pb-4 px-4">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

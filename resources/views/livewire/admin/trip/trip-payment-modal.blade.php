{{-- Trip Payment Modal --}}
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="paymentModalLabel">
                    <i class="bi bi-cash-coin me-2 text-primary"></i>
                    {{ $editingPayment ? 'Edit Payment' : 'Add Payment' }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" wire:click="closeModal"></button>
            </div>

            <form wire:submit.prevent="save">
                <div class="modal-body">

                    {{-- Payment Amount --}}
                    <div class="mb-3">
                        <label for="payment_amount" class="form-label">
                            Payment Amount <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-rupee-sign"></i>
                            </span>
                            <input
                                type="number"
                                step="0.01"
                                min="0.01"
                                class="form-control @error('amount') is-invalid @enderror"
                                id="payment_amount"
                                wire:model="amount"
                                placeholder="0.00"
                                required>
                        </div>
                        @error('amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Payment Method --}}
                    <div class="mb-3">
                        <label for="payment_method" class="form-label">
                            Payment Method <span class="text-danger">*</span>
                        </label>
                        <select
                            class="form-select @error('payment_method') is-invalid @enderror"
                            id="payment_method"
                            wire:model="payment_method"
                            required>
                            <option value="">Select Payment Method</option>
                            @foreach($paymentMethods as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('payment_method')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Payment Date --}}
                    <div class="mb-3">
                        <label for="payment_date" class="form-label">
                            Payment Date <span class="text-danger">*</span>
                        </label>
                        <input
                            type="date"
                            class="form-control @error('payment_date') is-invalid @enderror"
                            id="payment_date"
                            wire:model="payment_date"
                            max="{{ date('Y-m-d') }}"
                            required>
                        @error('payment_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Received by Driver --}}
                    <div class="mb-3">
                        <div class="form-check">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                id="payment_received_by_driver"
                                wire:model="received_by_driver">
                            <label class="form-check-label" for="payment_received_by_driver">
                                Received by Driver
                            </label>
                        </div>
                    </div>

                    {{-- Notes --}}
                    <div class="mb-3">
                        <label for="payment_notes" class="form-label">Notes</label>
                        <textarea
                            class="form-control @error('notes') is-invalid @enderror"
                            id="payment_notes"
                            wire:model="notes"
                            rows="3"
                            placeholder="Optional notes..."></textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>

                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal"
                        wire:click="closeModal">
                        Cancel
                    </button>
                    <button
                        type="submit"
                        class="btn btn-primary"
                        wire:loading.attr="disabled"
                        wire:target="save">
                        <span wire:loading.remove wire:target="save">
                            <i class="bi bi-check2 me-1"></i>
                            {{ $editingPayment ? 'Update Payment' : 'Add Payment' }}
                        </span>
                        <span wire:loading wire:target="save">
                            <span class="spinner-border spinner-border-sm me-1"></span>
                            Saving...
                        </span>
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

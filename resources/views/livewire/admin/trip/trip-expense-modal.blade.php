{{-- Trip Expense Modal --}}
<div class="modal fade" id="expenseModal" tabindex="-1" aria-labelledby="expenseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="expenseModalLabel">
                    <i class="bi bi-cash me-2 text-warning"></i>
                    {{ $editingExpense ? 'Edit Expense' : 'Add Expense' }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" wire:click="closeModal"></button>
            </div>

            <form wire:submit.prevent="save">
                <div class="modal-body">

                    {{-- Expense Type --}}
                    <div class="mb-3">
                        <label for="expense_type" class="form-label">
                            Expense Type <span class="text-danger">*</span>
                        </label>
                        <select
                            class="form-select @error('expense_type') is-invalid @enderror"
                            id="expense_type"
                            wire:model="expense_type"
                            required>
                            <option value="">Select Expense Type</option>
                            @foreach($expenseTypeOptions as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('expense_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Expense Amount --}}
                    <div class="mb-3">
                        <label for="expense_amount" class="form-label">
                            Expense Amount <span class="text-danger">*</span>
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
                                id="expense_amount"
                                wire:model="amount"
                                placeholder="0.00"
                                required>
                        </div>
                        @error('amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Expense Date --}}
                    <div class="mb-3">
                        <label for="expense_date" class="form-label">
                            Expense Date <span class="text-danger">*</span>
                        </label>
                        <input
                            type="date"
                            class="form-control @error('expense_date') is-invalid @enderror"
                            id="expense_date"
                            wire:model="expense_date"
                            max="{{ date('Y-m-d') }}"
                            required>
                        @error('expense_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Payment Mode --}}
                    <div class="mb-3">
                        <label class="form-label">
                            Payment Mode <span class="text-danger">*</span>
                        </label>
                        <div class="row">
                            @foreach($paymentModeOptions as $key => $label)
                                <div class="col-md-3 mb-2">
                                    <div class="form-check">
                                        <input
                                            class="form-check-input"
                                            type="radio"
                                            id="payment_mode_{{ $key }}"
                                            wire:model="payment_mode"
                                            value="{{ $key }}">
                                        <label class="form-check-label" for="payment_mode_{{ $key }}">
                                            {{ $label }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @error('payment_mode')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Add to Party Bill --}}
                    <div class="mb-3">
                        <div class="form-check">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                id="add_to_party_bill"
                                wire:model="add_to_party_bill">
                            <label class="form-check-label" for="add_to_party_bill">
                                Add to Party Bill
                            </label>
                        </div>
                    </div>

                    {{-- Notes --}}
                    <div class="mb-3">
                        <label for="expense_notes" class="form-label">Notes</label>
                        <textarea
                            class="form-control @error('notes') is-invalid @enderror"
                            id="expense_notes"
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
                        class="btn btn-warning"
                        wire:loading.attr="disabled"
                        wire:target="save">
                        <span wire:loading.remove wire:target="save">
                            <i class="bi bi-check2 me-1"></i>
                            {{ $editingExpense ? 'Update Expense' : 'Add Expense' }}
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
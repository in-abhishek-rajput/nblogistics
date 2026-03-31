<div>
    <form wire:submit.prevent="save">
        <div class="row">
            {{-- Trip --}}
            <div class="col-md-12 mb-3">
                <label for="trip_id" class="form-label">
                    Trip <span class="text-danger">*</span>
                </label>
                <select
                    class="form-select @error('trip_id') is-invalid @enderror"
                    id="trip_id"
                    wire:model="trip_id"
                    required>
                    <option value="">Select Trip</option>
                    @foreach ($trips as $trip)
                        <option value="{{ $trip->id }}">{{ $trip->party->name ?? 'N/A' }} - {{ $trip->origin ?? 'N/A' }} → {{ $trip->destination ?? 'N/A' }}</option>
                    @endforeach
                </select>
                @error('trip_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Expense Type --}}
            <div class="col-md-12 mb-3">
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
            <div class="col-md-6 mb-3">
                <label for="amount" class="form-label">
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
                        id="amount"
                        wire:model="amount"
                        placeholder="0.00"
                        required>
                </div>
                @error('amount')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Expense Date --}}
            <div class="col-md-6 mb-3">
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
            <div class="col-md-12 mb-3">
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
            <div class="col-md-12 mb-3">
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
            <div class="col-md-12 mb-3">
                <label for="notes" class="form-label">Notes</label>
                <textarea
                    class="form-control @error('notes') is-invalid @enderror"
                    id="notes"
                    wire:model="notes"
                    rows="3"
                    placeholder="Optional notes..."></textarea>
                @error('notes')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Submit Button --}}
        <div class="d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-warning" wire:loading.attr="disabled" wire:target="save">
                <span wire:loading.remove wire:target="save">
                    <i class="bi bi-check2 me-1"></i>
                    Add Expense
                </span>
                <span wire:loading wire:target="save">
                    <span class="spinner-border spinner-border-sm me-1"></span>
                    Adding...
                </span>
            </button>
        </div>
    </form>
</div>
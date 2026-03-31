{{-- Trip Charge Modal --}}
<div class="modal fade" id="chargeModal" tabindex="-1" aria-labelledby="chargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="chargeModalLabel">
                    <i class="bi bi-receipt me-2 text-warning"></i>
                    {{ $editingCharge ? 'Edit Charge' : 'Add Charge' }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" wire:click="closeModal"></button>
            </div>

            <form wire:submit.prevent="save">
                <div class="modal-body">

                    {{-- Charge Direction --}}
                    <div class="mb-3">
                        <label class="form-label">Type</label>
                        <div class="row">
                            @foreach($chargeDirections as $key => $label)
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input
                                            class="form-check-input"
                                            type="radio"
                                            id="charge_direction_{{ $key }}"
                                            wire:model.live="charge_direction"
                                            value="{{ $key }}">
                                        <label class="form-check-label" for="charge_direction_{{ $key }}">
                                            {{ $label }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @error('charge_direction')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Charge Type --}}
                    <div class="mb-3">
                        <label for="charge_type" class="form-label">
                            {{ $chargeTypeLabel }} <span class="text-danger">*</span>
                        </label>
                        <select
                            class="form-select @error('charge_type') is-invalid @enderror"
                            id="charge_type"
                            wire:model="charge_type"
                            required>
                            <option value="">Select {{ $chargeTypeLabel }}</option>
                            @foreach($chargeTypeOptions as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('charge_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Amount --}}
                    <div class="mb-3">
                        <label for="charge_amount" class="form-label">
                            Amount <span class="text-danger">*</span>
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
                                id="charge_amount"
                                wire:model="amount"
                                placeholder="0.00"
                                required>
                        </div>
                        @error('amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Date --}}
                    <div class="mb-3">
                        <label for="charge_date" class="form-label">
                            Date <span class="text-danger">*</span>
                        </label>
                        <input
                            type="date"
                            class="form-control @error('date') is-invalid @enderror"
                            id="charge_date"
                            wire:model="date"
                            max="{{ date('Y-m-d') }}"
                            required>
                        @error('date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Notes --}}
                    <div class="mb-3">
                        <label for="charge_notes" class="form-label">Notes</label>
                        <textarea
                            class="form-control @error('notes') is-invalid @enderror"
                            id="charge_notes"
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
                            {{ $editingCharge ? 'Update Charge' : 'Add Charge' }}
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

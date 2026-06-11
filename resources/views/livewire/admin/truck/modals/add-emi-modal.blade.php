<div wire:ignore.self class="modal fade" id="addEmiModal" tabindex="-1" aria-labelledby="addEmiModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title fw-bold" id="addEmiModalLabel">Add EMI</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form wire:submit.prevent="createEmi">
                <div class="modal-body py-0 px-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Finance Company</label>
                        <input type="text" wire:model.defer="finance_company" class="form-control" required>
                        @error('finance_company') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Monthly EMI</label>
                        <input type="number" wire:model.defer="monthly_emi" class="form-control" required min="1">
                        @error('monthly_emi') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Due On</label>
                        <select wire:model.defer="due_day" class="form-select" required>
                            <option value="">Select due day</option>
                            @for ($day = 1; $day <= 31; $day++)
                                <option value="{{ $day }}">{{ $day }}{{ $day === 1 ? 'st' : ($day === 2 ? 'nd' : ($day === 3 ? 'rd' : 'th')) }}</option>
                            @endfor
                        </select>
                        @error('due_day') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0 pb-4 px-4">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Confirm</button>
                </div>
            </form>
        </div>
    </div>
</div>

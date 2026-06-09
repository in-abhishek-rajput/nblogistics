<div>
    <form wire:submit.prevent="save">
        <div class="mb-3">
            <label class="form-label">Driver</label>
            <input type="text" class="form-control" value="{{ $driver ? $driver->name : '' }}" disabled>
        </div>
        
        <div class="mb-3">
            <label class="form-label">Advance Date <span class="text-danger">*</span></label>
            <input type="date" wire:model="advance_date" class="form-control @error('advance_date') is-invalid @enderror">
            @error('advance_date') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Amount (₹) <span class="text-danger">*</span></label>
            <input type="number" step="0.01" wire:model="amount" class="form-control @error('amount') is-invalid @enderror" placeholder="Enter amount">
            @error('amount') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Remarks (Optional)</label>
            <textarea wire:model="remarks" class="form-control @error('remarks') is-invalid @enderror" rows="2" placeholder="Any remarks"></textarea>
            @error('remarks') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>

        <div class="d-flex justify-content-end mt-4">
            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">
                <span wire:loading.remove wire:target="save">Save Advance</span>
                <span wire:loading wire:target="save"><i class="spinner-border spinner-border-sm"></i> Saving...</span>
            </button>
        </div>
    </form>
</div>

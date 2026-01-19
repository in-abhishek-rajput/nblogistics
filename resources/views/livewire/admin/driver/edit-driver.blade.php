<div>
    <form wire:submit.prevent="save">
        <div class="row">
            {{-- Driver Name --}}
            <div class="col-md-12 mb-3">
                <label for="name" class="form-label">Driver Name <span class="text-danger">*</span></label>
                <input type="text" value="{{ $name }}" wire:model="name" class="form-control @error('name') is-invalid @enderror"
                       id="name" placeholder="Enter driver name">
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Mobile --}}
            <div class="col-md-12 mb-3">
                <label for="mobile" class="form-label">Mobile Number</label> <span class="text-danger">*</span></label>
                <input type="tel" value="{{ $mobile }}" wire:model="mobile" class="form-control @error('mobile') is-invalid @enderror"
                       id="mobile" placeholder="+91 Enter mobile number">
                @error('mobile')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Opening Balance --}}
            <div class="col-md-12 mb-3">
                <label for="opening_balance" class="form-label">Opening Balance <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" fill="currentColor" class="bi bi-currency-rupee" viewBox="0 0 14 20">
                            <path d="M4 3.06h2.726c1.22 0 2.12.575 2.325 1.724H4v1.051h5.051C8.855 7.001 8 7.558 6.788 7.558H4v1.317L8.437 14h2.11L6.095 8.884h.855c2.316-.018 3.465-1.476 3.688-3.049H12V4.784h-1.345c-.08-.778-.357-1.335-.793-1.732H12V2H4z"/>
                        </svg>
                    </span>
                    <input type="number" value="{{ $opening_balance }}" wire:model="opening_balance" step="0.01" min="0"
                           class="form-control @error('opening_balance') is-invalid @enderror"
                           id="opening_balance" placeholder="0.00">
                </div>
                @error('opening_balance')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Submit Button --}}
        <div class="d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="submit">
                <span wire:loading.remove wire:target="submit">Update Driver</span>
                <span wire:loading wire:target="submit">
                    <span class="spinner-border spinner-border-sm" role="status"></span>
                    Updating...
                </span>
            </button>
        </div>
    </form>
</div>

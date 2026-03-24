<div>
    <form wire:submit.prevent="save">
        <div class="row">
            {{-- Party --}}
            <div class="col-md-12 mb-3">
                <label class="form-label">Party<span class="text-danger">*</span></label>
                <select wire:model="party_id" class="form-select @error('party_id') is-invalid @enderror">
                    <option value="">Select Party</option>
                    @foreach ($parties as $party)
                        <option value="{{ $party->id }}">{{ $party->name }}</option>
                    @endforeach
                </select>
                @error('party_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Truck --}}
            <div class="col-md-12 mb-3">
                <label class="form-label">Truck<span class="text-danger">*</span></label>
                <select wire:model="truck_id" class="form-select @error('truck_id') is-invalid @enderror">
                    <option value="">Select Truck</option>
                    @foreach ($trucks as $truck)
                        <option value="{{ $truck->id }}">{{ $truck->truck_number }}</option>
                    @endforeach
                </select>
                @error('truck_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Origin --}}
            <div class="col-md-6 mb-3">
                <label class="form-label">Origin<span class="text-danger">*</span></label>
                <input type="text" wire:model="origin" class="form-control @error('origin') is-invalid @enderror"
                    placeholder="From location">
                @error('origin')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Destination --}}
            <div class="col-md-6 mb-3">
                <label class="form-label">Destination<span class="text-danger">*</span></label>
                <input type="text" wire:model="destination" class="form-control @error('destination') is-invalid @enderror"
                    placeholder="To location">
                @error('destination')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- LR Number --}}
            <div class="col-md-6 mb-3">
                <label class="form-label">LR Number</label>
                <input type="text" wire:model="lr_number" class="form-control @error('lr_number') is-invalid @enderror"
                    placeholder="LR Number (optional)">
                @error('lr_number')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Material Name --}}
            <div class="col-md-6 mb-3">
                <label class="form-label">Material Name</label>
                <input type="text" wire:model="material_name" class="form-control @error('material_name') is-invalid @enderror"
                    placeholder="Material Name (optional)">
                @error('material_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Note --}}
            <div class="col-md-12 mb-3">
                <label class="form-label">Note</label>
                <textarea wire:model="note" class="form-control @error('note') is-invalid @enderror" rows="3"
                    placeholder="Additional notes (optional)"></textarea>
                @error('note')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Billing Information Section --}}
            <div class="col-md-12 mb-3">
                <h6 class="fw-bold text-primary">Billing Information</h6>
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label">Billing Type<span class="text-danger">*</span></label>
                        <select wire:model="billing_type" class="form-select @error('billing_type') is-invalid @enderror">
                            <option value="">Select Billing Type</option>
                            @foreach ($billingTypes as $key => $type)
                                <option value="{{ $key }}">{{ $type }}</option>
                            @endforeach
                        </select>
                        @error('billing_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Freight Amount<span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-rupee-sign"></i></span>
                            <input type="number" step="0.01" wire:model="freight_amount" class="form-control @error('freight_amount') is-invalid @enderror"
                                placeholder="0.00">
                        </div>
                        @error('freight_amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Trip Start Info --}}
            <div class="col-md-12 mb-3">
                <h6 class="fw-bold text-primary">Trip Start Information</h6>
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label">Start Date<span class="text-danger">*</span></label>
                        <input type="datetime-local" wire:model="start_date" class="form-control @error('start_date') is-invalid @enderror">
                        @error('start_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Start KM Reading<span class="text-danger">*</span></label>
                        <input type="number" wire:model="start_km" class="form-control @error('start_km') is-invalid @enderror"
                            placeholder="Starting kilometer">
                        @error('start_km')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

        </div>

        {{-- Submit Button --}}
        <div class="d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">Cancel</button>
            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="save">
                <span wire:loading.remove wire:target="save">Add Trip</span>
                <span wire:loading wire:target="save">
                    <span class="spinner-border spinner-border-sm" role="status"></span>
                    Adding...
                </span>
            </button>
        </div>
    </form>
</div>
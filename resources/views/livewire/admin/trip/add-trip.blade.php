<div>
    <form wire:submit.prevent="save">
        <div class="row">
            {{-- Party Autocomplete --}}
            <div class="col-md-12 mb-3">
                <label class="form-label">Party<span class="text-danger">*</span></label>
                <div x-data="{ open: @entangle('showPartyDropdown') }" @click.outside="open = false" class="position-relative">
                    <input
                        type="text"
                        wire:model.live="partySearch"
                        @focus="open = true"
                        @input="open = true"
                        placeholder="Search or add party..."
                        class="form-control @error('party_id') is-invalid @enderror"
                        autocomplete="off"
                    >
                    <input type="hidden" wire:model="party_id">

                    {{-- Dropdown list --}}
                    <div x-show="open" class="position-absolute w-100 bg-white border border-top-0 shadow-sm" style="top: 100%; left: 0; z-index: 1000; max-height: 200px; overflow-y: auto;">
                        @if ($filteredParties)
                            @foreach ($filteredParties as $party)
                                <div
                                    @click="@this.set('party_id', {{ $party['id'] }}); @this.set('party_name', '{{ $party['name'] }}'); open = false; @this.set('partySearch', '{{ $party['name'] }}')"
                                    class="px-3 py-2 cursor-pointer hover-bg-light"
                                    style="cursor: pointer; background-color: #f8f9fa;"
                                >
                                    {{ $party['name'] }}
                                </div>
                            @endforeach
                        @else
                            <div class="px-3 py-2 text-muted text-sm">
                                No parties found. Press Enter to add as custom entry.
                            </div>
                        @endif
                    </div>

                    {{-- Display selected value --}}
                    @if ($party_id)
                        <small class="text-success d-block mt-1">✓ Selected: {{ $partySearch }}</small>
                    @elseif ($party_name)
                        <small class="text-info d-block mt-1">✎ Custom: {{ $party_name }}</small>
                    @endif
                </div>
                @error('party_id')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            {{-- Driver Autocomplete --}}
            <div class="col-md-12 mb-3">
                <label class="form-label">Driver<span class="text-danger">*</span></label>
                <div x-data="{ open: @entangle('showDriverDropdown') }" @click.outside="open = false" class="position-relative">
                    <input
                        type="text"
                        wire:model.live="driverSearch"
                        @focus="open = true"
                        @input="open = true"
                        placeholder="Search or add driver..."
                        class="form-control @error('driver_id') is-invalid @enderror"
                        autocomplete="off"
                    >
                    <input type="hidden" wire:model="driver_id">

                    {{-- Dropdown list --}}
                    <div x-show="open" class="position-absolute w-100 bg-white border border-top-0 shadow-sm" style="top: 100%; left: 0; z-index: 1000; max-height: 200px; overflow-y: auto;">
                        @if ($filteredDrivers)
                            @foreach ($filteredDrivers as $driver)
                                <div
                                    @click="@this.set('driver_id', {{ $driver['id'] }}); @this.set('driver_name', '{{ $driver['name'] }}'); open = false; @this.set('driverSearch', '{{ $driver['name'] }}')"
                                    class="px-3 py-2 cursor-pointer hover-bg-light"
                                    style="cursor: pointer; background-color: #f8f9fa;"
                                >
                                    {{ $driver['name'] }}
                                </div>
                            @endforeach
                        @else
                            <div class="px-3 py-2 text-muted text-sm">
                                No drivers found. Press Enter to add as custom entry.
                            </div>
                        @endif
                    </div>

                    {{-- Display selected value --}}
                    @if ($driver_id)
                        <small class="text-success d-block mt-1">✓ Selected: {{ $driverSearch }}</small>
                    @elseif ($driver_name)
                        <small class="text-info d-block mt-1">✎ Custom: {{ $driver_name }}</small>
                    @endif
                </div>
                @error('driver_id')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            {{-- Truck Autocomplete --}}
            <div class="col-md-12 mb-3">
                <label class="form-label">Truck<span class="text-danger">*</span></label>
                <div x-data="{ open: @entangle('showTruckDropdown') }" @click.outside="open = false" class="position-relative">
                    <input
                        type="text"
                        wire:model.live="truckSearch"
                        @focus="open = true"
                        @input="open = true"
                        placeholder="Search or add truck..."
                        class="form-control @error('truck_id') is-invalid @enderror"
                        autocomplete="off"
                    >
                    <input type="hidden" wire:model="truck_id">

                    {{-- Dropdown list --}}
                    <div x-show="open" class="position-absolute w-100 bg-white border border-top-0 shadow-sm" style="top: 100%; left: 0; z-index: 1000; max-height: 200px; overflow-y: auto;">
                        @if ($filteredTrucks)
                            @foreach ($filteredTrucks as $truck)
                                <div
                                    @click="@this.set('truck_id', {{ $truck['id'] }}); @this.set('truck_name', '{{ $truck['name'] }}'); open = false; @this.set('truckSearch', '{{ $truck['name'] }}')"
                                    class="px-3 py-2 cursor-pointer hover-bg-light"
                                    style="cursor: pointer; background-color: #f8f9fa;"
                                >
                                    {{ $truck['name'] }}
                                </div>
                            @endforeach
                        @else
                            <div class="px-3 py-2 text-muted text-sm">
                                No trucks found. Press Enter to add as custom entry.
                            </div>
                        @endif
                    </div>

                    {{-- Display selected value --}}
                    @if ($truck_id)
                        <small class="text-success d-block mt-1">✓ Selected: {{ $truckSearch }}</small>
                    @elseif ($truck_name)
                        <small class="text-info d-block mt-1">✎ Custom: {{ $truck_name }}</small>
                    @endif
                </div>
                @error('truck_id')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
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
                        <select wire:model.live="billing_type" class="form-select @error('billing_type') is-invalid @enderror">
                            <option value="">Select Billing Type</option>
                            @foreach ($billingTypes as $key => $type)
                                <option value="{{ $key }}" {{ $key === 'fixed' ? 'selected' : '' }}>{{ $type }}</option>
                            @endforeach
                        </select>
                        @error('billing_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    @if($billing_type === 'fixed')
                        <div class="col-md-6">
                            <label class="form-label">Freight Amount<span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-rupee-sign"></i></span>
                                <input type="number" step="0.01" wire:model="freight_amount" value="{{ $freight_amount }}" class="form-control @error('freight_amount') is-invalid @enderror"
                                    placeholder="0.00">
                            </div>
                            @error('freight_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    @else
                        <div class="col-md-6">
                            <label class="form-label">Per {{ ucfirst(str_replace('per_','', $billing_type)) }} Amount<span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-rupee-sign"></i></span>
                                <input type="number" step="0.01" wire:model="per_unit_amount" id="per_unit_amount" oninput="calculateFreight()" class="form-control @error('per_unit_amount') is-invalid @enderror"
                                    placeholder="0.00">
                            </div>
                            @error('per_unit_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ ucfirst(str_replace('per_','', $billing_type)) }} Unit<span class="text-danger">*</span></label>
                            <input type="number" step="0.01" wire:model="unit" id="unit" oninput="calculateFreight()" class="form-control @error('unit') is-invalid @enderror"
                                placeholder="0.00">
                            @error('unit')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Freight Amount</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-rupee-sign"></i></span>
                                <input type="number" step="0.01" wire:model="freight_amount" id="freight_amount" class="form-control" readonly
                                    placeholder="0">
                            </div>
                        </div>
                    @endif
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

<script>
    function calculateFreight() {
        let unit = parseFloat(document.getElementById('unit').value) || 0;
        let rate = parseFloat(document.getElementById('per_unit_amount').value) || 0;
        let freight = unit * rate;
        
        if (unit > 0 && rate > 0) {
            document.getElementById('freight_amount').value = freight;
            @this.set('freight_amount', freight);
        } else {
            document.getElementById('freight_amount').value = 0;
            @this.set('freight_amount', 0);
        }
    }
</script>

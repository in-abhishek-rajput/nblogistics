<div wire:ignore.self class="modal fade" id="editTripModal" tabindex="-1" aria-labelledby="editTripModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title fw-bold" id="editTripModalLabel">Edit Trip</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form wire:submit.prevent="updateTrip">
                <div class="modal-body py-0 px-4">
                    {{-- Party --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Party <span class="text-danger">*</span></label>
                        <div x-data="{ open: @entangle('showPartyDropdown') }" @click.outside="open = false" class="position-relative">
                            <input
                                type="text"
                                wire:model.live="partySearch"
                                @focus="open = true"
                                @input="open = true"
                                placeholder="Search party..."
                                class="form-control @error('party_id') is-invalid @enderror"
                                autocomplete="off"
                            >
                            <input type="hidden" wire:model="party_id">

                            <div x-show="open" class="position-absolute w-100 bg-white border border-top-0 shadow-sm" style="top: 100%; left: 0; z-index: 1000; max-height: 200px; overflow-y: auto;">
                                @if ($this->filteredParties)
                                    @foreach ($this->filteredParties as $party)
                                        <div
                                            @click="@this.set('party_id', {{ $party['id'] }}); @this.set('partySearch', '{{ $party['name'] }}'); open = false;"
                                            class="px-3 py-2 cursor-pointer"
                                            style="cursor: pointer; background-color: #f8f9fa; border-bottom: 1px solid #e9ecef;"
                                        >
                                            {{ $party['name'] }}
                                        </div>
                                    @endforeach
                                @endif
                            </div>

                            @if ($party_id)
                                <small class="text-success d-block mt-1">✓ Selected: {{ $partySearch }}</small>
                            @elseif ($partySearch)
                                <small class="text-info d-block mt-1">✎ Custom: {{ $partySearch }}</small>
                            @endif
                        </div>
                        @error('party_id') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>

                    {{-- Driver --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Driver <span class="text-danger">*</span></label>
                        <div x-data="{ open: @entangle('showDriverDropdown') }" @click.outside="open = false" class="position-relative">
                            <input
                                type="text"
                                wire:model.live="driverSearch"
                                @focus="open = true"
                                @input="open = true"
                                placeholder="Search driver..."
                                class="form-control @error('driver_id') is-invalid @enderror"
                                autocomplete="off"
                            >
                            <input type="hidden" wire:model="driver_id">

                            <div x-show="open" class="position-absolute w-100 bg-white border border-top-0 shadow-sm" style="top: 100%; left: 0; z-index: 1000; max-height: 200px; overflow-y: auto;">
                                @if ($this->filteredDrivers)
                                    @foreach ($this->filteredDrivers as $driver)
                                        <div
                                            @click="@this.set('driver_id', {{ $driver['id'] }}); @this.set('driverSearch', '{{ $driver['name'] }}'); open = false;"
                                            class="px-3 py-2 cursor-pointer"
                                            style="cursor: pointer; background-color: #f8f9fa; border-bottom: 1px solid #e9ecef;"
                                        >
                                            {{ $driver['name'] }}
                                        </div>
                                    @endforeach
                                @endif
                            </div>

                            @if ($driver_id)
                                <small class="text-success d-block mt-1">✓ Selected: {{ $driverSearch }}</small>
                            @elseif ($driverSearch)
                                <small class="text-info d-block mt-1">✎ Custom: {{ $driverSearch }}</small>
                            @endif
                        </div>
                        @error('driver_id') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>

                    <div class="row g-3">
                        {{-- Origin --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Origin <span class="text-danger">*</span></label>
                            <input type="text" wire:model="origin" class="form-control @error('origin') is-invalid @enderror" required>
                            @error('origin') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>

                        {{-- Destination --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Destination <span class="text-danger">*</span></label>
                            <input type="text" wire:model="destination" class="form-control @error('destination') is-invalid @enderror" required>
                            @error('destination') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>

                        {{-- Billing Type --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Billing Type <span class="text-danger">*</span></label>
                            <select wire:model.live="billing_type" class="form-select @error('billing_type') is-invalid @enderror">
                                @foreach ($billingTypes as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('billing_type') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>

                        @if ($billing_type === 'fixed')
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Freight Amount <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" wire:model="freight_amount" class="form-control @error('freight_amount') is-invalid @enderror" required>
                                @error('freight_amount') <div class="text-danger small">{{ $message }}</div> @enderror
                            </div>
                        @else
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Per Unit Amount <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" wire:model="per_unit_amount" class="form-control @error('per_unit_amount') is-invalid @enderror">
                                @error('per_unit_amount') <div class="text-danger small">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Unit <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" wire:model="unit" class="form-control @error('unit') is-invalid @enderror">
                                @error('unit') <div class="text-danger small">{{ $message }}</div> @enderror
                            </div>
                        @endif

                        {{-- Start Date --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Start Date <span class="text-danger">*</span></label>
                            <input type="date" wire:model="start_date" class="form-control @error('start_date') is-invalid @enderror" required>
                            @error('start_date') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>

                        {{-- Start KM --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Start KM <span class="text-danger">*</span></label>
                            <input type="number" step="1" wire:model="start_km" class="form-control @error('start_km') is-invalid @enderror" required>
                            @error('start_km') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>

                        {{-- LR Number --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">LR Number</label>
                            <input type="text" wire:model="lr_number" class="form-control @error('lr_number') is-invalid @enderror">
                            @error('lr_number') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>

                        {{-- Material Name --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Material Name</label>
                            <input type="text" wire:model="material_name" class="form-control @error('material_name') is-invalid @enderror">
                            @error('material_name') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>

                        {{-- Note --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">Note</label>
                            <textarea wire:model="note" class="form-control @error('note') is-invalid @enderror" rows="3"></textarea>
                            @error('note') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0 pb-4 px-4">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Trip</button>
                </div>
            </form>
        </div>
    </div>
</div>
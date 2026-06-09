<div>
    <form wire:submit.prevent="save">
        <div class="row">
            {{-- Expense For --}}
            <div class="col-md-12 mb-3">
                <label class="form-label">
                    Expense For <span class="text-danger">*</span>
                </label>
                <div class="d-flex gap-3">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" wire:model.live="expense_category" value="trip" id="for_trip">
                        <label class="form-check-label" for="for_trip">Trip</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" wire:model.live="expense_category" value="truck" id="for_truck">
                        <label class="form-check-label" for="for_truck">Truck</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" wire:model.live="expense_category" value="office" id="for_office">
                        <label class="form-check-label" for="for_office">Office</label>
                    </div>
                </div>
            </div>

            {{-- Trip --}}
            @if($expense_category === 'trip')
            <div class="col-md-12 mb-3">
                <label for="trip_id" class="form-label">
                    Trip <span class="text-danger">*</span>
                </label>
                <div x-data="{ open: @entangle('showTripDropdown') }" @click.outside="open = false" class="position-relative">
                    <input
                        type="text"
                        wire:model.live="tripSearch"
                        @focus="open = true"
                        @input="open = true"
                        placeholder="Search trip..."
                        class="form-control @error('trip_id') is-invalid @enderror"
                        autocomplete="off"
                    >
                    <input type="hidden" wire:model="trip_id">

                    <div x-show="open" class="position-absolute w-100 bg-white border border-top-0 shadow-sm" style="top: 100%; left: 0; z-index: 1000; max-height: 200px; overflow-y: auto;">
                        @if ($filteredTrips)
                            @foreach ($filteredTrips as $trip)
                                <div
                                    @click="@this.set('trip_id', {{ $trip['id'] }}); @this.set('tripSearch', @js($trip['name'])); open = false"
                                    class="px-3 py-2"
                                    style="cursor: pointer; background-color: #f8f9fa;"
                                >
                                    {{ $trip['name'] }}
                                </div>
                            @endforeach
                        @else
                            <div class="px-3 py-2 text-muted text-sm">
                                No trips found.
                            </div>
                        @endif
                    </div>
                </div>
                @error('trip_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            @endif

            {{-- Truck --}}
            @if($expense_category === 'truck')
            <div class="col-md-12 mb-3">
                <label for="truck_id" class="form-label">
                    Truck <span class="text-danger">*</span>
                </label>
                <div x-data="{ open: @entangle('showTruckDropdown') }" @click.outside="open = false" class="position-relative">
                    <input
                        type="text"
                        wire:model.live="truckSearch"
                        @focus="open = true"
                        @input="open = true"
                        placeholder="Search truck..."
                        class="form-control @error('truck_id') is-invalid @enderror"
                        autocomplete="off"
                    >
                    <input type="hidden" wire:model="truck_id">

                    <div x-show="open" class="position-absolute w-100 bg-white border border-top-0 shadow-sm" style="top: 100%; left: 0; z-index: 1000; max-height: 200px; overflow-y: auto;">
                        @if ($filteredTrucks)
                            @foreach ($filteredTrucks as $truck)
                                <div
                                    @click="@this.set('truck_id', {{ $truck['id'] }}); @this.set('truckSearch', @js($truck['name'])); open = false"
                                    class="px-3 py-2"
                                    style="cursor: pointer; background-color: #f8f9fa;"
                                >
                                    {{ $truck['name'] }}
                                </div>
                            @endforeach
                        @else
                            <div class="px-3 py-2 text-muted text-sm">
                                No trucks found.
                            </div>
                        @endif
                    </div>
                </div>
                @error('truck_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            @endif

            {{-- Expense Type --}}
            <div class="col-md-12 mb-3">
                <label for="expense_type" class="form-label">
                    Expense Type <span class="text-danger">*</span>
                </label>
                <input
                    type="text"
                    class="form-control @error('expense_type') is-invalid @enderror"
                    id="expense_type"
                    wire:model="expense_type"
                    list="expenseTypeOptions"
                    placeholder="Type or select expense type"
                    required>
                <datalist id="expenseTypeOptions">
                    @foreach($expenseTypeOptions as $type)
                        <option value="{{ $type }}">{{ $type }}</option>
                    @endforeach
                </datalist>
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

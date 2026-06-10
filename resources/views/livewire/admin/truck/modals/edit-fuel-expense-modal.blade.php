<div wire:ignore.self class="modal fade" id="editFuelExpenseModal" tabindex="-1" aria-labelledby="editFuelExpenseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title fw-bold" id="editFuelExpenseModalLabel">Edit Fuel Expense</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form wire:submit.prevent="updateFuelExpense">
                <div class="modal-body py-0 px-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Expense Amount</label>
                        <input type="number" wire:model.live="expense_amount" class="form-control" required min="0" step="0.01">
                        @error('expense_amount') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="row gx-3">
                        <div class="col-6 mb-3">
                            <label class="form-label fw-semibold">Fuel Quantity</label>
                            <input type="number" wire:model.live="fuel_quantity" class="form-control" min="0" step="0.01">
                            @error('fuel_quantity') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label fw-semibold">Rate Per Litre</label>
                            <input type="number" wire:model.live="rate_per_litre" class="form-control" min="0" step="0.01">
                            @error('rate_per_litre') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" wire:model.live="is_full_tank" id="editIsFullTank">
                        <label class="form-check-label" for="editIsFullTank">I Have Filled Full Tank</label>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Current KM Reading</label>
                        <input type="number" wire:model.live="current_km_reading" class="form-control" min="0" step="1">
                        @error('current_km_reading') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Payment Mode</label>
                        <div class="d-flex flex-column gap-2">
                            @foreach ($paymentModeOptions as $key => $label)
                                <label class="btn btn-outline-secondary d-flex align-items-center gap-2">
                                    <input type="radio" wire:model.live="payment_mode" name="payment_mode" value="{{ $key }}" class="form-check-input mt-0" />
                                    <span>{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('payment_mode') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Expense Date</label>
                        <input type="date" wire:model.live="expense_date" class="form-control" required>
                        @error('expense_date') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    @if ($payment_mode === 'credit')
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Shop</label>
                            <input type="text" wire:model.live="shop_name" class="form-control" required>
                            @error('shop_name') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>
                    @endif
                    @if ($payment_mode === 'paid_by_driver')
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Driver</label>
                            <div x-data="{ open: @entangle('showDriverDropdown') }" @click.outside="open = false" class="position-relative">
                                <input
                                    type="text"
                                    wire:model.live="driver_search"
                                    @focus="open = true"
                                    @input="open = true"
                                    placeholder="Search driver..."
                                    class="form-control"
                                    autocomplete="off"
                                >
                                <input type="hidden" wire:model="driver_id">

                                <div x-show="open" class="position-absolute w-100 bg-white border border-top-0 shadow-sm" style="top: 100%; left: 0; z-index: 1000; max-height: 200px; overflow-y: auto;">
                                    @if ($this->filteredDrivers)
                                        @foreach ($this->filteredDrivers as $driver)
                                            <div
                                                @click="@this.set('driver_id', {{ $driver['id'] }}); @this.set('driver_search', '{{ $driver['name'] }}'); @this.set('custom_driver_name', ''); open = false;"
                                                class="px-3 py-2 cursor-pointer"
                                                style="cursor: pointer; background-color: #f8f9fa; border-bottom: 1px solid #e9ecef;"
                                            >
                                                {{ $driver['name'] }}
                                            </div>
                                        @endforeach
                                        <div class="border-top">
                                            <div
                                                @click="open = false; @this.set('driver_id', null); @this.set('custom_driver_name', @this.get('driver_search'));"
                                                class="px-3 py-2 text-primary cursor-pointer"
                                                style="cursor: pointer; background-color: #f0f8ff;"
                                            >
                                                ➕ Add as custom: {{ $driver_search }}
                                            </div>
                                        </div>
                                    @else
                                        <div class="px-3 py-2">
                                            @if ($driver_search)
                                                <div
                                                    @click="open = false; @this.set('driver_id', null); @this.set('custom_driver_name', @this.get('driver_search'));"
                                                    class="text-primary cursor-pointer"
                                                    style="cursor: pointer;"
                                                >
                                                    ➕ Add as custom: {{ $driver_search }}
                                                </div>
                                            @else
                                                <div class="text-muted text-sm">
                                                    Type to search or add custom driver
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>

                                @if ($driver_id)
                                    <small class="text-success d-block mt-1">✓ Selected: {{ $driver_search }}</small>
                                @elseif ($custom_driver_name)
                                    <small class="text-info d-block mt-1">✎ Custom: {{ $custom_driver_name }}</small>
                                @endif
                            </div>
                            @error('driver_id') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>
                    @endif
                    @if ($payment_mode === 'online')
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Transaction ID</label>
                            <input type="text" wire:model.live="transaction_id" class="form-control" required>
                            @error('transaction_id') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>
                    @endif
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Diesel Pump Name</label>
                        <input type="text" wire:model.live="diesel_pump_name" class="form-control">
                        @error('diesel_pump_name') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Upload Bill (Images Only)</label>
                        <input type="file" wire:model.live="bill_file" class="form-control" accept="image/jpeg,image/png,image/jpg">
                        @if ($bill_file)
                            <div class="mt-2">
                                <img src="{{ $bill_file->temporaryUrl() }}" alt="Bill preview" class="img-thumbnail" style="max-width: 150px; cursor: pointer;" data-lightbox="bill-preview">
                            </div>
                        @endif
                        @error('bill_file') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Remarks</label>
                        <textarea wire:model.live="remarks" class="form-control" rows="3"></textarea>
                        @error('remarks') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0 pb-4 px-4">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>


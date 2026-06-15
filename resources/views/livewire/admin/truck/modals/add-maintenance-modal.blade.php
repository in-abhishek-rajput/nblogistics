<div wire:ignore.self class="modal fade" id="addMaintenanceModal" tabindex="-1" aria-labelledby="addMaintenanceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title fw-bold" id="addMaintenanceModalLabel">
                    {{ $editingExpenseId ? 'Edit Maintenance' : 'Add Maintenance' }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form wire:submit.prevent="{{ $editingExpenseId ? 'updateMaintenance' : 'createMaintenance' }}">
                <div class="modal-body py-0 px-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Expense Type *</label>
                        <div x-data="{ open: @entangle('showExpenseTypeDropdown') }" @click.outside="open = false" class="position-relative">
                            <input type="text" wire:model.live="expense_type_search" @focus="open = true" @input="open = true"
                                placeholder="Search or enter expense type..." class="form-control" autocomplete="off">
                            <div x-show="open" class="position-absolute w-100 bg-white border shadow-sm" style="top:100%;z-index:1000;max-height:200px;overflow-y:auto;">
                                @foreach ($this->filteredExpenseTypes as $type)
                                    <div wire:click="selectExpenseType('{{ $type }}')" class="px-3 py-2" style="cursor:pointer;background:#f8f9fa;border-bottom:1px solid #e9ecef;">{{ $type }}</div>
                                @endforeach
                                @if ($expense_type_search && !in_array($expense_type_search, $this->expenseTypeOptions))
                                    <div wire:click="selectExpenseType(@js($expense_type_search))" class="px-3 py-2 text-primary" style="cursor:pointer;background:#f0f8ff;">
                                        ➕ Use custom: {{ $expense_type_search }}
                                    </div>
                                @endif
                            </div>
                        </div>
                        @error('expense_type') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Amount Paid *</label>
                        <input type="number" wire:model.defer="amount" class="form-control" required min="0" step="0.01">
                        @error('amount') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Payment Mode *</label>
                        <div class="d-flex flex-column gap-2">
                            @foreach ($paymentModeOptions as $key => $label)
                                <label class="btn btn-outline-secondary d-flex align-items-center gap-2">
                                    <input type="radio" wire:model.live="payment_mode" name="maintenance_payment_mode" value="{{ $key }}" class="form-check-input mt-0" />
                                    <span>{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('payment_mode') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Expense Date *</label>
                        <input type="date" wire:model.defer="expense_date" class="form-control" required>
                        @error('expense_date') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Due Date</label>
                        <input type="date" wire:model.defer="due_date" class="form-control">
                        @error('due_date') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    @if ($payment_mode === 'credit')
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Shop Name *</label>
                            <input type="text" wire:model.defer="shop_name" class="form-control" required>
                            @error('shop_name') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>
                    @endif
                    @if ($payment_mode === 'paid_by_driver')
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Driver</label>
                            <div x-data="{ open: @entangle('showDriverDropdown') }" @click.outside="open = false" class="position-relative">
                                <input type="text" wire:model.live="driver_search" @focus="open = true" @input="open = true"
                                    placeholder="Search driver..." class="form-control" autocomplete="off">
                                <div x-show="open" class="position-absolute w-100 bg-white border shadow-sm" style="top:100%;z-index:1000;max-height:200px;overflow-y:auto;">
                                    @foreach ($this->filteredDrivers as $driver)
                                        <div @click="@this.set('driver_id', {{ $driver['id'] }}); @this.set('driver_search', '{{ $driver['name'] }}'); @this.set('custom_driver_name', ''); open = false;"
                                            class="px-3 py-2" style="cursor:pointer;background:#f8f9fa;border-bottom:1px solid #e9ecef;">{{ $driver['name'] }}</div>
                                    @endforeach
                                    @if ($driver_search)
                                        <div @click="open = false; @this.set('driver_id', null); @this.set('custom_driver_name', @this.get('driver_search'));"
                                            class="px-3 py-2 text-primary" style="cursor:pointer;background:#f0f8ff;">➕ Add as custom: {{ $driver_search }}</div>
                                    @endif
                                </div>
                            </div>
                            @error('driver_id') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>
                    @endif
                    @if ($payment_mode === 'online')
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Transaction ID *</label>
                            <input type="text" wire:model.defer="transaction_id" class="form-control" required>
                            @error('transaction_id') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>
                    @endif
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Current KM Reading</label>
                        <input type="number" wire:model.defer="current_km_reading" class="form-control" min="0" step="1">
                        @error('current_km_reading') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Notes</label>
                        <textarea wire:model.defer="notes" class="form-control" rows="2"></textarea>
                        @error('notes') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Image Upload</label>
                        <input type="file" wire:model="expense_image" class="form-control" accept="image/jpeg,image/png,image/jpg,application/pdf">
                        @error('expense_image') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0 pb-4 px-4">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">{{ $editingExpenseId ? 'Update Maintenance' : 'Save Maintenance' }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

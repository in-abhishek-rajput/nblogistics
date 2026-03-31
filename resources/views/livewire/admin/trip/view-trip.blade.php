{{--
    livewire/admin/trip/view-trip.blade.php
    ────────────────────────────────────────
    Dynamic Trip View – rendered by App\Livewire\Admin\Trip\ViewTrip

    Sections:
    1. Flash Messages
    2. Left Panel  — Trip header (truck, driver), details, status tracker, action buttons, billing summary
    3. Right Panel — Trip profit, document actions (LR, POD)
    4. Modals      — Bootstrap Confirm Modal + Complete Trip Modal
    5. Scripts     — Bootstrap modal lifecycle events via Livewire dispatch (no SweetAlert)
--}}

<div class="row">

    {{-- ═══════════════════════════════════════════════════════════════
         1. FLASH MESSAGES
    ═══════════════════════════════════════════════════════════════ --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════════════
         2. LEFT PANEL — MAIN TRIP CONTENT
    ═══════════════════════════════════════════════════════════════ --}}
    <div class="col-md-8">

        {{-- ── Truck & Driver Cards ── --}}
        <div class="row mb-2 g-2">
            {{-- Truck --}}
            <div class="col-md-6 col-6">
                <div class="bg-light p-3 shadow-sm">
                    <div class="d-row-set">
                        <i class="fa fa-truck text-dark fs-3"></i>
                        <div class="mob-font">
                            <h6 class="mb-0">{{ $trip->truck->truck_number ?? 'N/A' }}</h6>
                            <a href="#" class="fs-14 mob-font">
                                View Trucks <i class="bi bi-chevron-double-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Driver --}}
            <div class="col-md-6 col-6">
                <div class="bg-light p-3 shadow-sm">
                    <div class="d-space-b">
                        <div class="d-row-set">
                            <img src="{{ asset('img/steering-wheel.png') }}" width="40" alt="Driver" />
                            <div class="mob-font">
                                <span>Driver Name</span>
                                <h6 class="mb-0">{{ $trip->driver->name ?? 'N/A' }}</h6>
                            </div>
                        </div>
                        <a href="#" class="fs-14"><i class="bi bi-chevron-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
        {{-- /Truck & Driver --}}

        {{-- ── Trip Details Card ── --}}
        <div class="bg-light p-0 shadow-sm trip-details">

            {{-- Party Tab Header --}}
            <div class="d-space-b bg-light-white">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button
                            class="nav-link active"
                            id="party-tab"
                            data-bs-toggle="tab"
                            data-bs-target="#party-tab-pane"
                            type="button"
                            role="tab"
                            aria-controls="party-tab-pane"
                            aria-selected="true">
                            {{ $trip->party->name ?? 'N/A' }}
                        </button>
                    </li>
                </ul>
            </div>

            <div class="tab-content" id="myTabContent">
                <div
                    class="tab-pane fade show active"
                    id="party-tab-pane"
                    role="tabpanel"
                    aria-labelledby="party-tab"
                    tabindex="0">

                    {{-- ── Trip Meta Row ── --}}
                    <div class="p-3">
                        <div class="row g-2">

                            {{-- Left: Party & Route --}}
                            <div class="col-md-6">

                                {{-- Party name + balance --}}
                                <div class="border rounded-2 p-2 d-space-b mb-2">
                                    <div>
                                        <span class="fs-14">Party Name</span><br>
                                        <b class="text-primary">{{ $trip->party->name ?? 'N/A' }}</b>
                                    </div>
                                    <div>
                                        <span class="fs-14">Party Balance</span><br>
                                        <b class="text-success">
                                            <i class="fas fa-rupee-sign fs-14"></i>
                                            {{ number_format($trip->pending_freight_amount ?? 0, 2) }}
                                        </b>
                                    </div>
                                </div>

                                {{-- Origin → Destination --}}
                                <div class="border rounded-2 p-2 d-space-b mb-2">
                                    <div>
                                        <b class="text-dark">{{ $trip->origin ?? 'N/A' }}</b><br>
                                        <span class="fs-14">
                                            {{ $trip->start_date ? $trip->start_date->format('d M Y') : 'N/A' }}
                                        </span>
                                    </div>
                                    <div class="divider-line"></div>
                                    <div>
                                        <i class="bi bi-arrow-right-circle"></i>
                                    </div>
                                    <div class="divider-line"></div>
                                    <div>
                                        <b class="text-dark">{{ $trip->destination ?? 'N/A' }}</b><br>
                                        <span class="fs-14">
                                            {{ $trip->completed_date ? $trip->completed_date->format('d M Y') : 'N/A' }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- Right: LR, Material, KM --}}
                            <div class="col-md-6">
                                <div class="row g-2 text-center">
                                    {{-- LR Number --}}
                                    <div class="col-md-6 col-6">
                                        <div class="border rounded-2 p-2 mb-2">
                                            <span class="fs-14">LR Number</span><br>
                                            <b class="text-dark">{{ $trip->lr_number ?? 'N/A' }}</b>
                                        </div>
                                    </div>
                                    {{-- Material --}}
                                    <div class="col-md-6 col-6">
                                        <div class="border rounded-2 p-2 mb-2">
                                            <span class="fs-14">Material</span><br>
                                            <b class="text-dark">{{ $trip->material_name ?? 'N/A' }}</b>
                                        </div>
                                    </div>
                                </div>

                                {{-- KM Readings --}}
                                <div class="border rounded-2 p-2 d-space-b mb-2 text-center">
                                    <div>
                                        <span class="fs-14">Start KMS</span><br>
                                        <b class="text-dark">{{ $trip->start_km ?? 'N/A' }}</b>
                                    </div>
                                    <div>
                                        <span class="fs-14">End KMS</span><br>
                                        <b class="text-dark">{{ $trip->end_km ?? 'N/A' }}</b>
                                    </div>
                                </div>
                            </div>

                        </div>{{-- /row --}}
                    </div>{{-- /p-3 meta --}}

                    {{-- ── Status Tracker ── --}}
                    <div class="p-3 pt-0">

                        @php
                            /*
                             * $trackerSteps skips 'pending' — display starts from 'start'.
                             * $statusIndex from component: pending=0, start=1, completed=2, ...
                             * A step at display position $i (0-based) corresponds to full-order
                             * index ($i + 1). Step is "done" when $statusIndex >= ($i + 1).
                             */
                            $trackerSteps = ['start', 'completed', 'pod_received', 'pod_submitted', 'settled'];
                        @endphp

                        <div class="trip-tracker-section text-center">
                            @foreach ($trackerSteps as $stepIndex => $stepKey)
                                <div>
                                    @if ($statusIndex >= ($stepIndex + 1))
                                        <i class="bi bi-check-circle-fill text-success"></i>
                                    @else
                                        <i class="bi bi-circle-fill t-gray"></i>
                                    @endif
                                </div>

                                @if ($stepIndex < 4)
                                    @if ($statusIndex > ($stepIndex + 1))
                                        <div class="tracker-divider-success"></div>
                                    @else
                                        <div class="tracker-divider"></div>
                                    @endif
                                @endif
                            @endforeach
                        </div>

                        {{-- Tracker Labels + Dates --}}
                        <div class="trip-tracker-section-data text-center">
                            <div>
                                <h6 class="mb-0 mob-font">Started</h6>
                                <span class="fs-14 mob-font">
                                    {{ $trip->start_date ? $trip->start_date->format('d M Y') : 'N/A' }}
                                </span>
                            </div>
                            <div>
                                <h6 class="mb-0 mob-font">Completed</h6>
                                <span class="fs-14 mob-font">
                                    {{ $trip->completed_date ? $trip->completed_date->format('d M Y') : 'N/A' }}
                                </span>
                            </div>
                            <div>
                                <h6 class="mb-0 mob-font">POD Received</h6>
                                <span class="fs-14 mob-font">
                                    {{ $trip->pod_received_date ? $trip->pod_received_date->format('d M Y') : 'N/A' }}
                                </span>
                            </div>
                            <div>
                                <h6 class="mb-0 mob-font">POD Submitted</h6>
                                <span class="fs-14 mob-font">
                                    {{ $trip->pod_submitted_date ? $trip->pod_submitted_date->format('d M Y') : 'N/A' }}
                                </span>
                            </div>
                            <div>
                                <h6 class="mb-0 mob-font">Settled</h6>
                                <span class="fs-14 mob-font">
                                    {{ $trip->settled_date ? $trip->settled_date->format('d M Y') : 'N/A' }}
                                </span>
                            </div>
                        </div>

                    </div>{{-- /status tracker --}}

                    {{-- ── Action Buttons ── --}}
                    <div class="row p-3 g-2">

                        @if ($canShowStart)
                            <div class="col-md-6 col-6">
                                <button
                                    wire:click="confirmStatusChange('start')"
                                    wire:loading.attr="disabled"
                                    wire:target="confirmStatusChange('start')"
                                    class="btn btn-outline-success w-100">
                                    <span wire:loading.remove wire:target="confirmStatusChange('start')">
                                        <i class="bi bi-play-circle me-1"></i> Start Trip
                                    </span>
                                    <span wire:loading wire:target="confirmStatusChange('start')">
                                        <span class="spinner-border spinner-border-sm me-1"></span> Loading...
                                    </span>
                                </button>
                            </div>

                        @elseif ($canShowComplete)
                            <div class="col-md-6 col-6">
                                <button
                                    wire:click="openCompleteModal"
                                    wire:loading.attr="disabled"
                                    wire:target="openCompleteModal"
                                    class="btn btn-outline-success w-100">
                                    <span wire:loading.remove wire:target="openCompleteModal">
                                        <i class="bi bi-check2-circle me-1"></i> Complete Trip
                                    </span>
                                    <span wire:loading wire:target="openCompleteModal">
                                        <span class="spinner-border spinner-border-sm me-1"></span> Loading...
                                    </span>
                                </button>
                            </div>

                        @elseif ($canShowPodReceived)
                            <div class="col-md-6 col-6">
                                <button
                                    wire:click="confirmStatusChange('pod_received')"
                                    wire:loading.attr="disabled"
                                    wire:target="confirmStatusChange('pod_received')"
                                    class="btn btn-outline-success w-100">
                                    <span wire:loading.remove wire:target="confirmStatusChange('pod_received')">
                                        <i class="bi bi-file-earmark-check me-1"></i> POD Received
                                    </span>
                                    <span wire:loading wire:target="confirmStatusChange('pod_received')">
                                        <span class="spinner-border spinner-border-sm me-1"></span> Loading...
                                    </span>
                                </button>
                            </div>

                        @elseif ($canShowPodSubmitted)
                            <div class="col-md-6 col-6">
                                <button
                                    wire:click="confirmStatusChange('pod_submitted')"
                                    wire:loading.attr="disabled"
                                    wire:target="confirmStatusChange('pod_submitted')"
                                    class="btn btn-outline-success w-100">
                                    <span wire:loading.remove wire:target="confirmStatusChange('pod_submitted')">
                                        <i class="bi bi-file-earmark-arrow-up me-1"></i> POD Submitted
                                    </span>
                                    <span wire:loading wire:target="confirmStatusChange('pod_submitted')">
                                        <span class="spinner-border spinner-border-sm me-1"></span> Loading...
                                    </span>
                                </button>
                            </div>

                        @elseif ($canShowSettled)
                            <div class="col-md-6 col-6">
                                <button
                                    wire:click="confirmStatusChange('settled')"
                                    wire:loading.attr="disabled"
                                    wire:target="confirmStatusChange('settled')"
                                    class="btn btn-outline-success w-100">
                                    <span wire:loading.remove wire:target="confirmStatusChange('settled')">
                                        <i class="bi bi-bank me-1"></i> Settled
                                    </span>
                                    <span wire:loading wire:target="confirmStatusChange('settled')">
                                        <span class="spinner-border spinner-border-sm me-1"></span> Loading...
                                    </span>
                                </button>
                            </div>
                        @endif

                        <div class="col-md-6 col-6">
                            <a href="#" class="btn btn-primary w-100">
                                <i class="bi bi-receipt me-1"></i> View Bill
                            </a>
                        </div>

                    </div>{{-- /action buttons --}}

                    {{-- ── Billing Summary ── --}}
                    <div class="p-3">

                        <div class="d-space-b mb-2">
                            <b class="text-dark">Freight Amount</b>
                            <div>
                                <b class="text-primary">
                                    <i class="fa fa-rupee-sign me-1 fs-14"></i>
                                    {{ number_format($trip->freight_amount ?? 0, 2) }}
                                </b>
                                <a href="#"><i class="fa fa-pen fs-14 ms-2"></i></a>
                            </div>
                        </div>

                        {{-- Advances List --}}
                        <div class="mb-3">
                            <div class="d-space-b mb-2">
                                <span class="fs-14">(-) Advances</span>
                                <a href="#" class="fs-14 text-success" wire:click="openAdvanceForm">
                                    <i class="bi bi-plus-circle me-1"></i>Add Advance
                                </a>
                            </div>
                            @if($trip->advances->count() > 0)
                                <div class="border rounded p-2 mb-2" style="max-height: 150px; overflow-y: auto;">
                                    @foreach($trip->advances as $advance)
                                        <div class="d-space-b py-1 border-bottom">
                                            <div>
                                                <small class="text-muted">{{ $advance->payment_method }} - {{ $advance->payment_date->format('d M Y') }}</small><br>
                                                <span class="fw-bold">{{ $advance->received_by_driver ? 'Driver' : 'Party' }}</span>
                                            </div>
                                            <div class="text-end">
                                                <span class="text-danger">
                                                    <i class="fa fa-rupee-sign fs-12"></i> {{ number_format($advance->amount, 2) }}
                                                </span>
                                                <a href="#" wire:click="editAdvance({{ $advance->id }})" class="ms-2 text-primary">
                                                    <i class="bi bi-pencil-square fs-12"></i>
                                                </a>
                                                <a href="#" wire:click="deleteAdvance({{ $advance->id }})" class="ms-1 text-danger">
                                                    <i class="bi bi-trash fs-12"></i>
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            <div class="text-end">
                                <b class="text-danger">
                                    <i class="fa fa-rupee-sign me-1 fs-14"></i>
                                    {{ number_format($trip->advances->sum('amount'), 2) }}
                                </b>
                            </div>
                        </div>

                        {{-- Advance Form --}}
                        @if($showAdvanceForm)
                        <div class="border rounded p-3 mb-3 bg-light">
                            <h6 class="mb-3">{{ $editingAdvance ? 'Edit Advance' : 'Add Advance' }}</h6>
                            <form wire:submit="saveAdvance">
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <label class="form-label">Amount <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" class="form-control @error('advance_amount') is-invalid @enderror" wire:model="advance_amount">
                                        @error('advance_amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                                        <select class="form-select @error('advance_payment_method') is-invalid @enderror" wire:model="advance_payment_method">
                                            @foreach($paymentMethods as $key => $label)
                                                <option value="{{ $key }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        @error('advance_payment_method') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Payment Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control @error('advance_payment_date') is-invalid @enderror" wire:model="advance_payment_date">
                                        @error('advance_payment_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Received By</label>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" wire:model="advance_received_by_driver" id="advance_received_by_driver">
                                            <label class="form-check-label" for="advance_received_by_driver">
                                                Driver
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Notes</label>
                                        <textarea class="form-control @error('advance_notes') is-invalid @enderror" rows="2" wire:model="advance_notes"></textarea>
                                        @error('advance_notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end mt-3">
                                    <button type="button" class="btn btn-secondary me-2" wire:click="cancelAdvanceForm">Cancel</button>
                                    <button type="submit" class="btn btn-success" wire:loading.attr="disabled" wire:target="saveAdvance">
                                        <span wire:loading.remove wire:target="saveAdvance">{{ $editingAdvance ? 'Update' : 'Add' }} Advance</span>
                                        <span wire:loading wire:target="saveAdvance">
                                            <span class="spinner-border spinner-border-sm me-1"></span> Saving...
                                        </span>
                                    </button>
                                </div>
                            </form>
                        </div>
                        @endif

                        {{-- Charges List --}}
                        <div class="mb-3">
                            <div class="d-space-b mb-2">
                                <span class="fs-14">(+) Charges</span>
                                <a href="#" class="fs-14 text-warning" wire:click="openChargeForm">
                                    <i class="bi bi-plus-circle me-1"></i>Add Charge
                                </a>
                            </div>
                            @if($trip->charges->count() > 0)
                                <div class="border rounded p-2 mb-2" style="max-height: 150px; overflow-y: auto;">
                                    @foreach($trip->charges as $charge)
                                        <div class="d-space-b py-1 border-bottom">
                                            <div>
                                                <small class="text-muted">{{ $charge->charge_type }} - {{ $charge->date->format('d M Y') }}</small><br>
                                                <span class="fw-bold">{{ $charge->charge_direction === 'add_to_bill' ? 'Add' : 'Reduce' }}</span>
                                            </div>
                                            <div class="text-end">
                                                <span class="text-{{ $charge->charge_direction === 'add_to_bill' ? 'success' : 'danger' }}">
                                                    <i class="fa fa-rupee-sign fs-12"></i> {{ number_format($charge->amount, 2) }}
                                                </span>
                                                <a href="#" wire:click="editCharge({{ $charge->id }})" class="ms-2 text-primary">
                                                    <i class="bi bi-pencil-square fs-12"></i>
                                                </a>
                                                <a href="#" wire:click="deleteCharge({{ $charge->id }})" class="ms-1 text-danger">
                                                    <i class="bi bi-trash fs-12"></i>
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            <div class="text-end">
                                <b class="text-success">
                                    <i class="fa fa-rupee-sign me-1 fs-14"></i>
                                    {{ number_format($trip->charges->where('charge_direction', 'add_to_bill')->sum('amount') - $trip->charges->where('charge_direction', 'reduce_from_bill')->sum('amount'), 2) }}
                                </b>
                            </div>
                        </div>

                        {{-- Charge Form --}}
                        @if($showChargeForm)
                        <div class="border rounded p-3 mb-3 bg-light">
                            <h6 class="mb-3">{{ $editingCharge ? 'Edit Charge' : 'Add Charge' }}</h6>
                            <form wire:submit="saveCharge">
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <label class="form-label">Direction <span class="text-danger">*</span></label>
                                        <select class="form-select @error('charge_direction') is-invalid @enderror" wire:model.live="charge_direction">
                                            @foreach($chargeDirections as $key => $label)
                                                <option value="{{ $key }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        @error('charge_direction') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">{{ $chargeTypeLabel }} <span class="text-danger">*</span></label>
                                        <select class="form-select @error('charge_type') is-invalid @enderror" wire:model="charge_type">
                                            @foreach($chargeTypeOptions as $key => $label)
                                                <option value="{{ $key }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        @error('charge_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Amount <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" class="form-control @error('charge_amount') is-invalid @enderror" wire:model="charge_amount">
                                        @error('charge_amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control @error('charge_date') is-invalid @enderror" wire:model="charge_date">
                                        @error('charge_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Notes</label>
                                        <textarea class="form-control @error('charge_notes') is-invalid @enderror" rows="2" wire:model="charge_notes"></textarea>
                                        @error('charge_notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end mt-3">
                                    <button type="button" class="btn btn-secondary me-2" wire:click="cancelChargeForm">Cancel</button>
                                    <button type="submit" class="btn btn-success" wire:loading.attr="disabled" wire:target="saveCharge">
                                        <span wire:loading.remove wire:target="saveCharge">{{ $editingCharge ? 'Update' : 'Add' }} Charge</span>
                                        <span wire:loading wire:target="saveCharge">
                                            <span class="spinner-border spinner-border-sm me-1"></span> Saving...
                                        </span>
                                    </button>
                                </div>
                            </form>
                        </div>
                        @endif

                        {{-- Payments List --}}
                        <div class="mb-3">
                            <div class="d-space-b mb-2">
                                <span class="fs-14">(-) Payments</span>
                                <a href="#" class="fs-14 text-primary" wire:click="openPaymentForm">
                                    <i class="bi bi-plus-circle me-1"></i>Add Payment
                                </a>
                            </div>
                            @if($trip->payments->count() > 0)
                                <div class="border rounded p-2 mb-2" style="max-height: 150px; overflow-y: auto;">
                                    @foreach($trip->payments as $payment)
                                        <div class="d-space-b py-1 border-bottom">
                                            <div>
                                                <small class="text-muted">{{ $payment->payment_method }} - {{ $payment->payment_date->format('d M Y') }}</small><br>
                                                <span class="fw-bold">{{ $payment->received_by_driver ? 'Driver' : 'Party' }}</span>
                                            </div>
                                            <div class="text-end">
                                                <span class="text-primary">
                                                    <i class="fa fa-rupee-sign fs-12"></i> {{ number_format($payment->amount, 2) }}
                                                </span>
                                                <a href="#" wire:click="editPayment({{ $payment->id }})" class="ms-2 text-primary">
                                                    <i class="bi bi-pencil-square fs-12"></i>
                                                </a>
                                                <a href="#" wire:click="deletePayment({{ $payment->id }})" class="ms-1 text-danger">
                                                    <i class="bi bi-trash fs-12"></i>
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            <div class="text-end">
                                <b class="text-primary">
                                    <i class="fa fa-rupee-sign me-1 fs-14"></i>
                                    {{ number_format($trip->payments->sum('amount'), 2) }}
                                </b>
                            </div>
                        </div>

                        {{-- Payment Form --}}
                        @if($showPaymentForm)
                        <div class="border rounded p-3 mb-3 bg-light">
                            <h6 class="mb-3">{{ $editingPayment ? 'Edit Payment' : 'Add Payment' }}</h6>
                            <form wire:submit="savePayment">
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <label class="form-label">Amount <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" class="form-control @error('payment_amount') is-invalid @enderror" wire:model="payment_amount">
                                        @error('payment_amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                                        <select class="form-select @error('payment_payment_method') is-invalid @enderror" wire:model="payment_payment_method">
                                            @foreach($paymentMethods as $key => $label)
                                                <option value="{{ $key }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        @error('payment_payment_method') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Payment Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control @error('payment_payment_date') is-invalid @enderror" wire:model="payment_payment_date">
                                        @error('payment_payment_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Received By</label>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" wire:model="payment_received_by_driver" id="payment_received_by_driver">
                                            <label class="form-check-label" for="payment_received_by_driver">
                                                Driver
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Notes</label>
                                        <textarea class="form-control @error('payment_notes') is-invalid @enderror" rows="2" wire:model="payment_notes"></textarea>
                                        @error('payment_notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end mt-3">
                                    <button type="button" class="btn btn-secondary me-2" wire:click="cancelPaymentForm">Cancel</button>
                                    <button type="submit" class="btn btn-success" wire:loading.attr="disabled" wire:target="savePayment">
                                        <span wire:loading.remove wire:target="savePayment">{{ $editingPayment ? 'Update' : 'Add' }} Payment</span>
                                        <span wire:loading wire:target="savePayment">
                                            <span class="spinner-border spinner-border-sm me-1"></span> Saving...
                                        </span>
                                    </button>
                                </div>
                            </form>
                        </div>
                        @endif

                        <hr>

                        @php
                            $totalAdvances = $trip->advances->sum('amount');
                            $totalCharges = $trip->charges->where('charge_direction', 'add_to_bill')->sum('amount') - $trip->charges->where('charge_direction', 'reduce_from_bill')->sum('amount');
                            $totalPayments = $trip->payments->sum('amount');
                            $pendingBalance = ($trip->freight_amount ?? 0) - $totalAdvances - $totalPayments + $totalCharges;
                        @endphp
                        <div class="d-space-b mb-2">
                            <b class="text-dark">Pending Party Balance</b>
                            <b class="text-primary">
                                <i class="fa fa-rupee-sign me-1 fs-14"></i>
                                {{ number_format($pendingBalance, 2) }}
                            </b>
                        </div>

                    </div>{{-- /billing --}}

                </div>{{-- /tab-pane --}}
            </div>{{-- /tab-content --}}
        </div>{{-- /trip-details card --}}

    </div>{{-- /col-md-8 --}}

    {{-- ═══════════════════════════════════════════════════════════════
         3. RIGHT PANEL — PROFIT & DOCUMENTS
    ═══════════════════════════════════════════════════════════════ --}}
    <div class="col-md-4">

        {{-- ── Trip Profit ── --}}
        <div class="bg-light p-0 shadow-sm mb-2">
            <div class="d-space-b p-3">
                <h6 class="mb-0">Trip Profit</h6>
                <a href="#"
                   class="btn btn-sm btn-outline-primary"
                   data-bs-toggle="modal"
                   data-bs-target="#addExpenses">
                    <i class="bi bi-plus me-1"></i>Add Expenses
                </a>
            </div>
            <hr class="my-0">
            <div class="trip-profit-details bg-light p-3">

                <div class="d-space-b mb-2">
                    <label class="text-dark"><b>(+) Revenue</b></label>
                    <label class="text-primary">
                        <b><i class="fas fa-rupee-sign fs-14"></i> {{ number_format($trip->freight_amount ?? 0, 2) }}</b>
                    </label>
                </div>
                <div class="d-space-b fs-14 mb-2">
                    <label class="text-dark">{{ $trip->party->name ?? 'N/A' }}</label>
                    <label class="text-dark">
                        <i class="fas fa-rupee-sign fs-14"></i> {{ number_format($trip->freight_amount ?? 0, 2) }}
                    </label>
                </div>

                <div class="d-space-b mb-2">
                    <label class="text-dark"><b>(-) Expense</b></label>
                    <label class="text-primary">
                        <b><i class="fas fa-rupee-sign fs-14"></i> {{ number_format($trip->total_expense ?? 0, 2) }}</b>
                    </label>
                </div>

                <hr>

                @php
                    $profit = ($trip->freight_amount ?? 0) - ($trip->total_expense ?? 0);
                @endphp
                <div class="d-space-b">
                    <label class="text-dark"><b>Profit</b></label>
                    <label class="{{ $profit >= 0 ? 'text-success' : 'text-danger' }}">
                        <b><i class="fas fa-rupee-sign fs-14"></i> {{ number_format($profit, 2) }}</b>
                    </label>
                </div>

            </div>
        </div>{{-- /trip profit --}}

        {{-- ── Documents (LR + POD) ── --}}
        <div class="bg-light p-3 shadow-sm">
            <h6>{{ $trip->party->name ?? 'N/A' }}</h6>

            <div class="border rounded-2 p-2 d-space-b mb-2">
                <div class="d-row-set text-dark f-14">
                    <i class="bi bi-list"></i>
                    <b>Online Bilty/LR</b>
                </div>
                <a href="#" class="btn btn-sm btn-success">Create LR</a>
            </div>

            <div class="border rounded-2 p-2 d-space-b">
                <div class="d-row-set text-dark f-14">
                    <i class="bi bi-list"></i>
                    <b>POD Challan</b>
                </div>
                <a href="#" class="btn btn-sm btn-primary">
                    <i class="bi bi-camera-fill me-1"></i>Add POD
                </a>
            </div>
        </div>{{-- /documents --}}

    </div>{{-- /col-md-4 --}}

    {{-- ═══════════════════════════════════════════════════════════════
         4a. BOOTSTRAP CONFIRM MODAL
         ─────────────────────────────────────────────────────────────
         Replaces SweetAlert entirely.
         Shows the action label from $confirmLabel (e.g. "Started").
         "Yes, Confirm" calls updateStatus() via wire:click.
         "Cancel" calls closeModals() to reset PHP state.
    ═══════════════════════════════════════════════════════════════ --}}
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title" id="confirmModalLabel">
                        <i class="bi bi-question-circle text-warning me-2"></i>Confirm Action
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" wire:click="closeModals"></button>
                </div>

                <div class="modal-body pt-2">
                    <p class="mb-0">
                        Are you sure you want to mark this trip as
                        <strong class="text-primary">"{{ $confirmLabel }}"</strong>?
                    </p>
                    <p class="text-muted fs-14 mt-1 mb-0">This action cannot be undone.</p>
                </div>

                <div class="modal-footer border-0 pt-0">
                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal"
                        wire:click="closeModals">
                        Cancel
                    </button>
                    <button
                        type="button"
                        class="btn btn-success"
                        wire:click="updateStatus"
                        wire:loading.attr="disabled"
                        wire:target="updateStatus"
                        data-bs-dismiss="modal">
                        <span wire:loading.remove wire:target="updateStatus">
                            <i class="bi bi-check2 me-1"></i> Yes, Confirm
                        </span>
                        <span wire:loading wire:target="updateStatus">
                            <span class="spinner-border spinner-border-sm me-1"></span> Processing...
                        </span>
                    </button>
                </div>

            </div>
        </div>
    </div>{{-- /confirmModal --}}

    {{-- ═══════════════════════════════════════════════════════════════
         4b. COMPLETE TRIP MODAL
         ─────────────────────────────────────────────────────────────
         Shown only when the user clicks "Complete Trip".
         Requires: end_date (must be >= start_date)
                   end_km   (must be >= start_km)
    ═══════════════════════════════════════════════════════════════ --}}
    <div class="modal fade" id="completeModal" tabindex="-1" aria-labelledby="completeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="completeModalLabel">
                        <i class="bi bi-check2-circle me-2 text-success"></i>Complete Trip
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    {{-- End Date --}}
                    <div class="mb-3">
                        <label for="end_date" class="form-label">
                            End Date <span class="text-danger">*</span>
                        </label>
                        <input
                            type="date"
                            class="form-control @error('end_date') is-invalid @enderror"
                            id="end_date"
                            wire:model="end_date"
                            min="{{ $trip->start_date ? $trip->start_date->format('Y-m-d') : '' }}"
                            required>
                        @error('end_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if($trip->start_date)
                            <small class="text-muted">
                                Must be on or after {{ $trip->start_date->format('d M Y') }}
                            </small>
                        @endif
                    </div>

                    {{-- End KM --}}
                    <div class="mb-3">
                        <label for="end_km" class="form-label">
                            End KM Reading <span class="text-danger">*</span>
                        </label>
                        <input
                            type="number"
                            class="form-control @error('end_km') is-invalid @enderror"
                            id="end_km"
                            wire:model="end_km"
                            min="{{ $trip->start_km ?? 0 }}"
                            placeholder="Enter odometer reading"
                            required>
                        @error('end_km')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if($trip->start_km)
                            <small class="text-muted">
                                Must be at least {{ number_format($trip->start_km) }} km (start reading)
                            </small>
                        @endif
                    </div>

                </div>{{-- /modal-body --}}

                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button
                        type="button"
                        class="btn btn-success"
                        wire:click="completeTrip"
                        wire:loading.attr="disabled"
                        wire:target="completeTrip">
                        <span wire:loading.remove wire:target="completeTrip">
                            <i class="bi bi-check2 me-1"></i> Complete Trip
                        </span>
                        <span wire:loading wire:target="completeTrip">
                            <span class="spinner-border spinner-border-sm me-1"></span> Completing...
                        </span>
                    </button>
                </div>

            </div>{{-- /modal-content --}}
        </div>{{-- /modal-dialog --}}
    </div>{{-- /completeModal --}}


</div>{{-- /row --}}



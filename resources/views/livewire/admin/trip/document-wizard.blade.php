{{--
    livewire/admin/trip/document-wizard.blade.php
    ──────────────────────────────────────────────
    3-Step Document Wizard: Bilty → Invoice → Receipt
    Rendered by App\Livewire\Admin\Trip\DocumentWizard
--}}

<div>

    {{-- Flash Message --}}
    @if (session()->has('doc_success'))
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
            <i class="bi bi-check-circle me-1"></i> {{ session('doc_success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════
         STEP PROGRESS BAR
    ══════════════════════════════════════════════════════════════════ --}}
    <div class="wizard-steps mb-4">
        <div class="d-flex align-items-center justify-content-center">
            {{-- Step 1 --}}
            <div class="wizard-step {{ $currentStep >= 1 ? 'active' : '' }} {{ $biltyDone ? 'completed' : '' }}"
                 wire:click="goToStep(1)" style="cursor: pointer;">
                <div class="step-circle">
                    @if($biltyDone)
                        <i class="bi bi-check-lg"></i>
                    @else
                        1
                    @endif
                </div>
                <div class="step-label">Bilty / LR</div>
            </div>

            <div class="step-connector {{ $currentStep >= 2 ? 'active' : '' }}"></div>

            {{-- Step 2 --}}
            <div class="wizard-step {{ $currentStep >= 2 ? 'active' : '' }} {{ $invoiceDone ? 'completed' : '' }}"
                 wire:click="goToStep(2)" style="cursor: pointer;">
                <div class="step-circle">
                    @if($invoiceDone)
                        <i class="bi bi-check-lg"></i>
                    @else
                        2
                    @endif
                </div>
                <div class="step-label">Tax Invoice</div>
            </div>

            <div class="step-connector {{ $currentStep >= 3 ? 'active' : '' }}"></div>

            {{-- Step 3 --}}
            <div class="wizard-step {{ $currentStep >= 3 ? 'active' : '' }} {{ $receiptDone ? 'completed' : '' }}"
                 wire:click="goToStep(3)" style="cursor: pointer;">
                <div class="step-circle">
                    @if($receiptDone)
                        <i class="bi bi-check-lg"></i>
                    @else
                        3
                    @endif
                </div>
                <div class="step-label">Money Receipt</div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════
         STEP 1: BILTY / LR FORM
    ══════════════════════════════════════════════════════════════════ --}}
    @if($currentStep === 1)
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white py-3">
            <h5 class="mb-0"><i class="bi bi-file-text me-2"></i>Bilty / Lorry Receipt (LR)</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">

                {{-- Section: Basic Details --}}
                <div class="col-12">
                    <h6 class="text-primary border-bottom pb-2 mb-3">
                        <i class="bi bi-info-circle me-1"></i> Basic Details
                    </h6>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">LR Number <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('lr_number') is-invalid @enderror" wire:model="lr_number" placeholder="e.g. 0073">
                    @error('lr_number') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">LR Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control @error('lr_date') is-invalid @enderror" wire:model="lr_date">
                    @error('lr_date') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Vehicle No. <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('vehicle_no') is-invalid @enderror" wire:model="vehicle_no" placeholder="e.g. GJ-03-AB-1234">
                    @error('vehicle_no') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">GST Paid By</label>
                    <select class="form-select" wire:model="gst_paid_by">
                        <option value="consignor">Consignor</option>
                        <option value="consignee">Consignee</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">From (Origin) <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('bilty_from') is-invalid @enderror" wire:model="bilty_from" placeholder="e.g. Jamnagar">
                    @error('bilty_from') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">To (Destination) <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('bilty_to') is-invalid @enderror" wire:model="bilty_to" placeholder="e.g. Ahmedabad">
                    @error('bilty_to') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                {{-- Section: Consignor --}}
                <div class="col-12 mt-4">
                    <h6 class="text-primary border-bottom pb-2 mb-3">
                        <i class="bi bi-person me-1"></i> Consignor Details
                    </h6>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Consignor Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('consignor_name') is-invalid @enderror" wire:model="consignor_name">
                    @error('consignor_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">GST No.</label>
                    <input type="text" class="form-control" wire:model="consignor_gst" placeholder="e.g. 24AAACB1234F1Z5">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Address</label>
                    <input type="text" class="form-control" wire:model="consignor_address">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Mobile No.</label>
                    <input type="text" class="form-control" wire:model="consignor_mobile">
                </div>

                {{-- Section: Consignee --}}
                <div class="col-12 mt-4">
                    <h6 class="text-primary border-bottom pb-2 mb-3">
                        <i class="bi bi-person-check me-1"></i> Consignee Details
                    </h6>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Consignee Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('consignee_name') is-invalid @enderror" wire:model="consignee_name">
                    @error('consignee_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">GST No.</label>
                    <input type="text" class="form-control" wire:model="consignee_gst" placeholder="e.g. 24AAACX9876P1Z9">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Address</label>
                    <input type="text" class="form-control" wire:model="consignee_address">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Mobile No.</label>
                    <input type="text" class="form-control" wire:model="consignee_mobile">
                </div>

                {{-- Section: Goods --}}
                <div class="col-12 mt-4">
                    <h6 class="text-primary border-bottom pb-2 mb-3">
                        <i class="bi bi-box-seam me-1"></i> Goods Details
                    </h6>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Description of Goods</label>
                    <input type="text" class="form-control" wire:model="description_of_goods" placeholder="e.g. Steel Coils">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">No. of Packages</label>
                    <input type="number" class="form-control" wire:model="no_of_packages">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Invoice No.</label>
                    <input type="text" class="form-control" wire:model="bilty_invoice_no">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Actual Weight</label>
                    <input type="text" class="form-control" wire:model="actual_weight" placeholder="e.g. 25 MT">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Charged Weight</label>
                    <input type="text" class="form-control" wire:model="charged_weight" placeholder="e.g. 25 MT">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">E-Way Bill No.</label>
                    <input type="text" class="form-control" wire:model="eway_bill_no">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Rate</label>
                    <input type="number" step="0.01" class="form-control" wire:model="bilty_rate">
                </div>

                {{-- Section: Charges --}}
                <div class="col-12 mt-4">
                    <h6 class="text-primary border-bottom pb-2 mb-3">
                        <i class="bi bi-currency-rupee me-1"></i> Charges
                    </h6>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Freight Amount <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" class="form-control @error('bilty_freight_amount') is-invalid @enderror" wire:model.live="bilty_freight_amount">
                    @error('bilty_freight_amount') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Hamali Charges</label>
                    <input type="number" step="0.01" class="form-control" wire:model.live="hamali_charges">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Bilty Charges</label>
                    <input type="number" step="0.01" class="form-control" wire:model.live="bilty_charges">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Advance Amount</label>
                    <input type="number" step="0.01" class="form-control" wire:model="advance_amount">
                </div>

                {{-- Total --}}
                <div class="col-12">
                    <div class="bg-light rounded p-3 d-flex justify-content-between align-items-center">
                        <span class="fw-bold fs-5">Total Amount</span>
                        <span class="fw-bold fs-5 text-primary">
                            <i class="fas fa-rupee-sign"></i>
                            {{ number_format($this->biltyTotal, 2) }}
                        </span>
                    </div>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Remark</label>
                    <textarea class="form-control" rows="2" wire:model="bilty_remark" placeholder="Any remarks..."></textarea>
                </div>

            </div>
        </div>
    </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════
         STEP 2: BILL / TAX INVOICE FORM
    ══════════════════════════════════════════════════════════════════ --}}
    @if($currentStep === 2)
    <div class="card shadow-sm border-0">
        <div class="card-header bg-success text-white py-3">
            <h5 class="mb-0"><i class="bi bi-receipt me-2"></i>Bill / Tax Invoice</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">

                {{-- Section: Invoice Details --}}
                <div class="col-12">
                    <h6 class="text-success border-bottom pb-2 mb-3">
                        <i class="bi bi-info-circle me-1"></i> Invoice Details
                    </h6>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Invoice Number <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('invoice_number') is-invalid @enderror" wire:model="invoice_number">
                    @error('invoice_number') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Invoice Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control @error('invoice_date') is-invalid @enderror" wire:model="invoice_date">
                    @error('invoice_date') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Vehicle No.</label>
                    <input type="text" class="form-control" wire:model="inv_vehicle_no">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">L.R. No.</label>
                    <input type="text" class="form-control" wire:model="inv_lr_no">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">From</label>
                    <input type="text" class="form-control" wire:model="inv_from">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">To</label>
                    <input type="text" class="form-control" wire:model="inv_to">
                </div>

                {{-- Section: Bill To --}}
                <div class="col-12 mt-4">
                    <h6 class="text-success border-bottom pb-2 mb-3">
                        <i class="bi bi-building me-1"></i> Bill To
                    </h6>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Name (M/S) <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('bill_to_name') is-invalid @enderror" wire:model="bill_to_name">
                    @error('bill_to_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">GST No.</label>
                    <input type="text" class="form-control" wire:model="bill_to_gst">
                </div>
                <div class="col-md-12">
                    <label class="form-label fw-semibold">Address</label>
                    <input type="text" class="form-control" wire:model="bill_to_address">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">City | State</label>
                    <input type="text" class="form-control" wire:model="bill_to_city_state">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Pin Code</label>
                    <input type="text" class="form-control" wire:model="bill_to_pin">
                </div>

                {{-- Section: Bill From --}}
                <div class="col-12 mt-4">
                    <h6 class="text-success border-bottom pb-2 mb-3">
                        <i class="bi bi-shop me-1"></i> Bill From (Company)
                    </h6>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Name (M/S)</label>
                    <input type="text" class="form-control" wire:model="bill_from_name">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">GST No.</label>
                    <input type="text" class="form-control" wire:model="bill_from_gst">
                </div>
                <div class="col-md-12">
                    <label class="form-label fw-semibold">Address</label>
                    <input type="text" class="form-control" wire:model="bill_from_address">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">City | State</label>
                    <input type="text" class="form-control" wire:model="bill_from_city_state">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Pin Code</label>
                    <input type="text" class="form-control" wire:model="bill_from_pin">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Mobile No.</label>
                    <input type="text" class="form-control" wire:model="bill_from_mobile">
                </div>

                {{-- Section: Ship To --}}
                <div class="col-12 mt-4">
                    <h6 class="text-success border-bottom pb-2 mb-3">
                        <i class="bi bi-truck me-1"></i> Ship To
                    </h6>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Name (M/S)</label>
                    <input type="text" class="form-control" wire:model="ship_to_name">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">GST No.</label>
                    <input type="text" class="form-control" wire:model="ship_to_gst">
                </div>
                <div class="col-md-12">
                    <label class="form-label fw-semibold">Address</label>
                    <input type="text" class="form-control" wire:model="ship_to_address">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">City | State</label>
                    <input type="text" class="form-control" wire:model="ship_to_city_state">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Pin Code</label>
                    <input type="text" class="form-control" wire:model="ship_to_pin">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Mobile No.</label>
                    <input type="text" class="form-control" wire:model="ship_to_mobile">
                </div>

                {{-- Section: Charges & Tax --}}
                <div class="col-12 mt-4">
                    <h6 class="text-success border-bottom pb-2 mb-3">
                        <i class="bi bi-currency-rupee me-1"></i> Charges & Tax
                    </h6>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Freight Amount <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" class="form-control @error('inv_freight_amount') is-invalid @enderror" wire:model.live="inv_freight_amount">
                    @error('inv_freight_amount') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Loading Charge</label>
                    <input type="number" step="0.01" class="form-control" wire:model.live="loading_charge">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Unloading Charge</label>
                    <input type="number" step="0.01" class="form-control" wire:model.live="unloading_charge">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Sub Total</label>
                    <input type="text" class="form-control bg-light" value="{{ number_format($this->subTotal, 2) }}" readonly>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">SGST %</label>
                    <div class="input-group">
                        <input type="number" step="0.01" class="form-control" wire:model.live="sgst_percent">
                        <span class="input-group-text">= ₹{{ number_format($this->sgstAmount, 2) }}</span>
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">CGST %</label>
                    <div class="input-group">
                        <input type="number" step="0.01" class="form-control" wire:model.live="cgst_percent">
                        <span class="input-group-text">= ₹{{ number_format($this->cgstAmount, 2) }}</span>
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Grand Total</label>
                    <input type="text" class="form-control bg-light fw-bold text-primary" value="₹ {{ number_format($this->grandTotal, 2) }}" readonly>
                </div>

                {{-- Section: Additional --}}
                <div class="col-12 mt-4">
                    <h6 class="text-success border-bottom pb-2 mb-3">
                        <i class="bi bi-card-list me-1"></i> Additional Details
                    </h6>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Description of Goods</label>
                    <input type="text" class="form-control" wire:model="inv_description_of_goods">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">No. of Articles</label>
                    <input type="number" class="form-control" wire:model="inv_no_of_articles">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Total Weight</label>
                    <input type="text" class="form-control" wire:model="inv_total_weight">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Payment Paid By</label>
                    <select class="form-select" wire:model="payment_paid_by">
                        <option value="">— Select —</option>
                        <option value="cash">Cash</option>
                        <option value="cheque">Cheque</option>
                        <option value="upi">UPI</option>
                        <option value="bank_transfer">Bank Transfer</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">E-Way Bill No.</label>
                    <input type="text" class="form-control" wire:model="inv_eway_bill_no">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Remark</label>
                    <input type="text" class="form-control" wire:model="inv_remark">
                </div>

                {{-- Grand Total Summary --}}
                <div class="col-12">
                    <div class="bg-light rounded p-3 d-flex justify-content-between align-items-center">
                        <span class="fw-bold fs-5">Grand Total</span>
                        <span class="fw-bold fs-5 text-success">
                            <i class="fas fa-rupee-sign"></i>
                            {{ number_format($this->grandTotal, 2) }}
                        </span>
                    </div>
                </div>

            </div>
        </div>
    </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════
         STEP 3: MONEY RECEIPT FORM
    ══════════════════════════════════════════════════════════════════ --}}
    @if($currentStep === 3)
    <div class="card shadow-sm border-0">
        <div class="card-header bg-warning text-dark py-3">
            <h5 class="mb-0"><i class="bi bi-cash-stack me-2"></i>Money Receipt</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">

                {{-- Section: Receipt Details --}}
                <div class="col-12">
                    <h6 class="text-warning border-bottom pb-2 mb-3">
                        <i class="bi bi-info-circle me-1"></i> Receipt Details
                    </h6>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Receipt Number <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('receipt_number') is-invalid @enderror" wire:model="receipt_number">
                    @error('receipt_number') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Receipt Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control @error('receipt_date') is-invalid @enderror" wire:model="receipt_date">
                    @error('receipt_date') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Received From (M/S) <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('received_from') is-invalid @enderror" wire:model="received_from">
                    @error('received_from') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">From</label>
                    <input type="text" class="form-control" wire:model="receipt_from">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">To</label>
                    <input type="text" class="form-control" wire:model="receipt_to">
                </div>

                {{-- Section: Amounts --}}
                <div class="col-12 mt-4">
                    <h6 class="text-warning border-bottom pb-2 mb-3">
                        <i class="bi bi-currency-rupee me-1"></i> Particulars
                    </h6>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Freight</label>
                    <input type="number" step="0.01" class="form-control" wire:model.live="receipt_freight_amount">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Loading</label>
                    <input type="number" step="0.01" class="form-control" wire:model.live="receipt_loading_charge">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Unloading</label>
                    <input type="number" step="0.01" class="form-control" wire:model.live="receipt_unloading_charge">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Advance</label>
                    <input type="number" step="0.01" class="form-control" wire:model.live="receipt_advance_amount">
                </div>

                {{-- Net Amount --}}
                <div class="col-12">
                    <div class="bg-light rounded p-3 d-flex justify-content-between align-items-center">
                        <span class="fw-bold fs-5">Net Amount</span>
                        <span class="fw-bold fs-5 text-warning">
                            <i class="fas fa-rupee-sign"></i>
                            {{ number_format($this->netAmount, 2) }}
                        </span>
                    </div>
                </div>

                {{-- Section: Payment Mode --}}
                <div class="col-12 mt-4">
                    <h6 class="text-warning border-bottom pb-2 mb-3">
                        <i class="bi bi-wallet2 me-1"></i> Payment Mode
                    </h6>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">AC Pay (Bank Transfer)</label>
                    <input type="number" step="0.01" class="form-control" wire:model="ac_pay_amount">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">By Cash</label>
                    <input type="number" step="0.01" class="form-control" wire:model="cash_amount">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Cheque No.</label>
                    <input type="text" class="form-control" wire:model="cheque_no">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Cheque Date</label>
                    <input type="date" class="form-control" wire:model="cheque_date">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Bank Name</label>
                    <input type="text" class="form-control" wire:model="bank_name">
                </div>

                {{-- Section: Reference --}}
                <div class="col-12 mt-4">
                    <h6 class="text-warning border-bottom pb-2 mb-3">
                        <i class="bi bi-link-45deg me-1"></i> Reference Details
                    </h6>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Invoice No.</label>
                    <input type="text" class="form-control" wire:model="receipt_invoice_no">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Invoice Date</label>
                    <input type="date" class="form-control" wire:model="receipt_invoice_date">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">L.R. No.</label>
                    <input type="text" class="form-control" wire:model="receipt_lr_no">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">L.R. Date</label>
                    <input type="date" class="form-control" wire:model="receipt_lr_date">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Total Packages</label>
                    <input type="number" class="form-control" wire:model="total_packages">
                </div>

            </div>
        </div>
    </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════
         ACTION BUTTONS (Bottom Bar)
    ══════════════════════════════════════════════════════════════════ --}}
    <div class="d-flex justify-content-between align-items-center mt-4">
        {{-- Left: Previous --}}
        <div>
            @if($currentStep > 1)
                <button type="button" class="btn btn-outline-secondary px-4" wire:click="previousStep">
                    <i class="bi bi-arrow-left me-1"></i> Previous
                </button>
            @else
                <a href="{{ route('trips.index') }}" class="btn btn-outline-secondary px-4">
                    <i class="bi bi-arrow-left me-1"></i> Back to Trips
                </a>
            @endif
        </div>

        {{-- Center: Save Draft + Preview --}}
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-primary px-4" wire:click="saveDraft"
                    wire:loading.attr="disabled" wire:target="saveDraft">
                <span wire:loading.remove wire:target="saveDraft">
                    <i class="bi bi-save me-1"></i> Save Draft
                </span>
                <span wire:loading wire:target="saveDraft">
                    <span class="spinner-border spinner-border-sm me-1"></span> Saving...
                </span>
            </button>

            <button type="button" class="btn btn-outline-info px-4" wire:click="previewDocument"
                    wire:loading.attr="disabled" wire:target="previewDocument">
                <span wire:loading.remove wire:target="previewDocument">
                    <i class="bi bi-eye me-1"></i> Preview & Print
                </span>
                <span wire:loading wire:target="previewDocument">
                    <span class="spinner-border spinner-border-sm me-1"></span> Loading...
                </span>
            </button>
        </div>

        {{-- Right: Next / Finish --}}
        <div>
            @if($currentStep < 3)
                <button type="button" class="btn btn-primary px-4" wire:click="nextStep"
                        wire:loading.attr="disabled" wire:target="nextStep">
                    <span wire:loading.remove wire:target="nextStep">
                        Save & Next <i class="bi bi-arrow-right ms-1"></i>
                    </span>
                    <span wire:loading wire:target="nextStep">
                        <span class="spinner-border spinner-border-sm me-1"></span> Saving...
                    </span>
                </button>
            @else
                <button type="button" class="btn btn-success px-4" wire:click="saveReceipt"
                        wire:loading.attr="disabled" wire:target="saveReceipt">
                    <span wire:loading.remove wire:target="saveReceipt">
                        <i class="bi bi-check-circle me-1"></i> Finish & Save
                    </span>
                    <span wire:loading wire:target="saveReceipt">
                        <span class="spinner-border spinner-border-sm me-1"></span> Saving...
                    </span>
                </button>
            @endif
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════
         WIZARD STYLES
    ══════════════════════════════════════════════════════════════════ --}}
    @push('styles')
    <style>
        .wizard-steps {
            padding: 20px 0;
        }
        .wizard-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            min-width: 120px;
        }
        .step-circle {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: #e9ecef;
            color: #6c757d;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 16px;
            border: 3px solid #dee2e6;
            transition: all 0.3s ease;
        }
        .wizard-step.active .step-circle {
            background: #0d6efd;
            color: #fff;
            border-color: #0d6efd;
            box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.15);
        }
        .wizard-step.completed .step-circle {
            background: #198754;
            color: #fff;
            border-color: #198754;
            box-shadow: 0 0 0 4px rgba(25, 135, 84, 0.15);
        }
        .step-label {
            margin-top: 8px;
            font-size: 13px;
            font-weight: 600;
            color: #6c757d;
            text-align: center;
        }
        .wizard-step.active .step-label {
            color: #0d6efd;
        }
        .wizard-step.completed .step-label {
            color: #198754;
        }
        .step-connector {
            flex: 1;
            height: 3px;
            background: #dee2e6;
            margin: 0 5px;
            margin-bottom: 25px;
            transition: background 0.3s ease;
        }
        .step-connector.active {
            background: #0d6efd;
        }

        /* Responsive adjustments */
        @media (max-width: 576px) {
            .wizard-step {
                min-width: 80px;
            }
            .step-circle {
                width: 36px;
                height: 36px;
                font-size: 14px;
            }
            .step-label {
                font-size: 11px;
            }
        }
    </style>
    @endpush

    {{-- ══════════════════════════════════════════════════════════════════
         SCRIPTS
    ══════════════════════════════════════════════════════════════════ --}}
    @push('scripts')
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('openPreview', (data) => {
                if (data && data.url) {
                    window.open(data.url, '_blank');
                }
            });
        });
    </script>
    @endpush

</div>

<div class="position-relative">
    <div class="d-print-none">
        <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Salary Details: {{ $driver->name }}</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('drivers.index') }}">Drivers</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Salary Details</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('drivers.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Drivers
            </a>
        </div>
    </div>

    {{-- Month Filter --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-center">
                <div class="col-md-3">
                    <label class="form-label small text-muted mb-1">Month</label>
                    <select wire:model.live="selectedMonth" class="form-select">
                        @foreach (range(1, 12) as $m)
                            <option value="{{ $m }}">{{ date('F', mktime(0, 0, 0, $m, 10)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted mb-1">Year</label>
                    <select wire:model.live="selectedYear" class="form-select">
                        @foreach (range(date('Y') - 5, date('Y') + 1) as $y)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Section 1: Attendance Summary --}}
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-bottom pb-0 pt-3">
                    <h5 class="card-title text-primary"><i class="bi bi-calendar-check me-2"></i>Attendance Summary</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span><i class="bi bi-check-circle text-success me-2"></i>Present Days</span>
                            <span class="badge bg-success rounded-pill fs-6">{{ $attendanceSummary['present'] }}</span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span><i class="bi bi-x-circle text-danger me-2"></i>Absent Days</span>
                            <span class="badge bg-danger rounded-pill fs-6">{{ $attendanceSummary['absent'] }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span><i class="bi bi-brightness-high text-info me-2"></i>Holidays</span>
                            <span class="badge bg-info rounded-pill fs-6 text-white">{{ $attendanceSummary['holiday'] }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 fw-bold border-bottom-0">
                            <span>Total Days in Month</span>
                            <span>{{ $attendanceSummary['total'] }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Section 3: Salary Summary --}}
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 h-100 bg-light">
                <div class="card-header bg-transparent border-bottom pb-0 pt-3">
                    <h5 class="card-title text-success"><i class="bi bi-cash-stack me-2"></i>Salary Summary</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <span class="d-block small text-muted mb-1">Base Salary</span>
                        <span class="fs-5 fw-bold">₹{{ number_format($driver->base_salary, 2) }}</span>
                    </div>
                    
                    <ul class="list-group list-group-flush bg-transparent">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 bg-transparent">
                            <span>Gross Salary</span>
                            <span class="fw-bold">₹{{ number_format($salaryRecord ? $salaryRecord->gross_salary : $previewGross, 2) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 bg-transparent">
                            <span class="text-danger">Advance Deduction</span>
                            <span class="fw-bold text-danger">-₹{{ number_format($totalAdvances, 2) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 bg-transparent border-bottom-0 mt-2">
                            <span class="fs-5 fw-bold text-success">Net Salary</span>
                            <span class="fs-4 fw-bold text-success">₹{{ number_format($salaryRecord ? $salaryRecord->net_salary : $previewNet, 2) }}</span>
                        </li>
                    </ul>

                    <div class="mt-4 pt-3 border-top text-center">
                        <span class="d-block small text-muted mb-2">Payment Status</span>
                        @if($salaryRecord && $salaryRecord->status === 'PAID')
                            <span class="badge bg-success fs-6 px-3 py-2"><i class="bi bi-check-circle me-1"></i> PAID</span>
                        @elseif($salaryRecord)
                            <span class="badge bg-warning text-dark fs-6 px-3 py-2"><i class="bi bi-clock me-1"></i> PENDING</span>
                        @else
                            <span class="badge bg-secondary fs-6 px-3 py-2">NOT CALCULATED</span>
                        @endif
                    </div>
                    
                    <div class="mt-3 text-center">
                        <button type="button" class="btn btn-outline-primary w-100" onclick="window.print()">
                            <i class="bi bi-printer me-2"></i> Print Salary Slip
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 2: Advance History --}}
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-bottom pb-0 pt-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title text-info"><i class="bi bi-clock-history me-2"></i>Advance History</h5>
                    <span class="badge bg-primary rounded-pill">Total: ₹{{ number_format($totalAdvances, 2) }}</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($advances as $advance)
                                    <tr>
                                        <td class="small">{{ \Carbon\Carbon::parse($advance->advance_date)->format('d M') }}</td>
                                        <td class="fw-bold text-danger">-₹{{ number_format($advance->amount, 2) }}</td>
                                        <td class="small text-truncate" style="max-width: 100px;" title="{{ $advance->remarks }}">{{ $advance->remarks ?: '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-muted small">
                                            No advances taken in {{ $monthName }}.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white border-top-0 text-center py-3">
                    <a href="{{ route('drivers.advances', $driver->id) }}" class="btn btn-sm btn-outline-info w-100">View Full History</a>
                </div>
            </div>
        </div>
    </div>

    {{-- PRINT LAYOUT (Hidden on screen) --}}
    <div class="print-only-layout text-dark">
        <div class="text-center mb-4">
            <h2 class="mb-1 fw-bold text-uppercase" style="letter-spacing: 1px;">NB Logistics</h2>
            <h5 class="text-muted mb-0">Salary Slip - {{ $monthName }}</h5>
        </div>

        <div class="row mb-4">
            <div class="col-6">
                <p class="mb-1"><strong>Driver Name:</strong> {{ $driver->name }}</p>
                <p class="mb-1"><strong>Mobile:</strong> {{ $driver->mobile ?: 'N/A' }}</p>
            </div>
            <div class="col-6 text-end">
                <p class="mb-1"><strong>Date Generated:</strong> {{ now()->format('d M, Y') }}</p>
                <p class="mb-1"><strong>Status:</strong> {{ $salaryRecord && $salaryRecord->status === 'PAID' ? 'PAID' : 'PENDING' }}</p>
            </div>
        </div>

        <table class="table table-bordered border-dark mb-4 print-table">
            <thead class="table-light text-center">
                <tr>
                    <th colspan="2" class="text-uppercase bg-light">Earnings & Deductions Summary</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Base Salary (Monthly)</td>
                    <td class="text-end fw-bold">₹{{ number_format($driver->base_salary, 2) }}</td>
                </tr>
                <tr>
                    <td>Paid Days / Total Days</td>
                    <td class="text-end fw-bold">{{ $attendanceSummary['present'] + $attendanceSummary['holiday'] }} / {{ $attendanceSummary['total'] }}</td>
                </tr>
                <tr>
                    <td class="bg-light"><strong>Gross Salary</strong></td>
                    <td class="text-end bg-light"><strong>₹{{ number_format($salaryRecord ? $salaryRecord->gross_salary : $previewGross, 2) }}</strong></td>
                </tr>
                <tr>
                    <td>Advance Deductions</td>
                    <td class="text-end text-danger fw-bold">-₹{{ number_format($totalAdvances, 2) }}</td>
                </tr>
                <tr>
                    <td class="bg-light fs-5"><strong>Net Payable Amount</strong></td>
                    <td class="text-end bg-light fs-5"><strong>₹{{ number_format($salaryRecord ? $salaryRecord->net_salary : $previewNet, 2) }}</strong></td>
                </tr>
            </tbody>
        </table>

        <div class="row mt-5 pt-5 text-center signature-section">
            <div class="col-6">
                <div class="border-top border-dark mx-4 pt-2 d-inline-block" style="width: 200px;">
                    <strong>Driver's Signature</strong>
                </div>
            </div>
            <div class="col-6">
                <div class="border-top border-dark mx-4 pt-2 d-inline-block" style="width: 200px;">
                    <strong>Manager's Signature</strong>
                </div>
            </div>
        </div>
    </div>

    <style>
        .print-only-layout {
            display: none !important;
        }

        @media print {
            body {
                background-color: white !important;
                color: black !important;
                margin: 0;
                padding: 0;
            }
            /* Force hide all standard UI elements */
            .sidebar, .navbar, .footer, .d-print-none, .main-header, .topbar {
                display: none !important;
            }
            .print-only-layout {
                display: block !important;
                width: 100%;
                max-width: 800px;
                margin: 0 auto;
                padding: 20px;
            }
            .print-table th, .print-table td {
                border-color: #333 !important;
                padding: 12px 15px;
            }
            .print-table .bg-light {
                background-color: #f8f9fa !important;
                -webkit-print-color-adjust: exact; 
                print-color-adjust: exact;
            }
            .signature-section {
                page-break-inside: avoid;
            }
        }
    </style>
</div>

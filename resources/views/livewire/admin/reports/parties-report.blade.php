<div>
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="mb-0 fw-bold">
                <i class="bi bi-building me-2 text-primary"></i>Parties Report
            </h2>
            <small class="text-muted">
                Showing: <strong>{{ $monthNames[$selectedMonth] }} {{ $selectedYear }}</strong>
            </small>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap no-print">
            <select wire:model.live="month" class="form-select form-select-sm" style="width:140px">
                @foreach ($monthNames as $num => $name)
                    <option value="{{ $num }}" @selected($num == $selectedMonth)>{{ $name }}</option>
                @endforeach
            </select>

            <select wire:model.live="year" class="form-select form-select-sm" style="width:100px">
                @foreach ($years as $yr)
                    <option value="{{ $yr }}" @selected($yr == $selectedYear)>{{ $yr }}</option>
                @endforeach
            </select>

            <button wire:click="printReport" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-printer me-1"></i>Print
            </button>
            <button wire:click="exportReport" class="btn btn-outline-success btn-sm">
                <i class="bi bi-file-earmark-excel me-1"></i>Export
            </button>
        </div>
    </div>

    @if (session('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div id="report-content">
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-6">
                <div class="card border-0 shadow-sm h-100 border-start border-4 border-primary">
                    <div class="card-body d-flex align-items-center gap-3">
                        <i class="bi bi-building fs-4 text-primary"></i>
                        <div>
                            <div class="text-muted small fw-semibold text-uppercase">Total Parties</div>
                            <div class="fs-2 fw-bold text-primary">{{ $summary['total_parties'] ?? 0 }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-6">
                <div class="card border-0 shadow-sm h-100 border-start border-4 border-success">
                    <div class="card-body d-flex align-items-center gap-3">
                        <i class="bi bi-check-circle fs-4 text-success"></i>
                        <div>
                            <div class="text-muted small fw-semibold text-uppercase">Active Parties</div>
                            <div class="fs-2 fw-bold text-success">{{ $summary['active_parties'] ?? 0 }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-semibold border-bottom d-flex justify-content-between align-items-center">
                <span><i class="bi bi-table me-2 text-primary"></i>Party Performance Details</span>
                <span class="badge bg-primary rounded-pill">{{ count($summary['parties_performance'] ?? []) }} parties</span>
            </div>
            <div class="card-body p-0">
                @if (!empty($summary['parties_performance']))
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0 align-middle small">
                            <thead class="table-light">
                                <tr>
                                    <th>Party Name</th>
                                    <th>Email</th>
                                    <th>Mobile</th>
                                    <th>Status</th>
                                    <th>Opening Balance</th>
                                    <th>Total Trips</th>
                                    <th>Completed Trips</th>
                                    <th>Ongoing Trips</th>
                                    <th>Cancelled Trips</th>
                                    <th>Total Freight</th>
                                    <th>Pending Freight</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($summary['parties_performance'] as $party)
                                    <tr>
                                        <td>{{ $party['name'] ?? '-' }}</td>
                                        <td>{{ $party['email'] ?? '-' }}</td>
                                        <td>{{ $party['mobile'] ?? '-' }}</td>
                                        <td>
                                            @if(($party['status'] ?? '') == 'Active')
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-secondary">{{ $party['status'] ?? '-' }}</span>
                                            @endif
                                        </td>
                                        <td>Rs {{ number_format($party['opening_balance'] ?? 0, 2) }}</td>
                                        <td>{{ $party['total_trips'] ?? 0 }}</td>
                                        <td><span class="text-success fw-bold">{{ $party['completed_trips'] ?? 0 }}</span></td>
                                        <td><span class="text-warning fw-bold">{{ $party['ongoing_trips'] ?? 0 }}</span></td>
                                        <td><span class="text-danger fw-bold">{{ $party['cancelled_trips'] ?? 0 }}</span></td>
                                        <td>Rs {{ number_format($party['total_freight'] ?? 0, 2) }}</td>
                                        <td>Rs {{ number_format($party['pending_freight'] ?? 0, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-4 text-muted">
                        <i class="bi bi-inbox me-2"></i>No party data available.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <style>
        @media print {
            .no-print {
                display: none !important;
            }

            .card {
                box-shadow: none !important;
                border: 1px solid #dee2e6 !important;
            }

            .table {
                font-size: 11px;
            }
        }
    </style>
</div>

<script>
    document.addEventListener('printReport', () => window.print());
</script>

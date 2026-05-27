<div>

    {{-- ================================================================
         HEADER — Title + Month/Year filters + Print button
         Variables available: $monthNames, $years, $selectedMonth,
                              $selectedYear, $summary
    ================================================================ --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">

        <div>
            <h2 class="mb-0 fw-bold">
                <i class="bi bi-truck me-2 text-primary"></i>Trips Report
            </h2>
            <small class="text-muted">
                Showing: <strong>{{ $monthNames[$selectedMonth] }} {{ $selectedYear }}</strong>
            </small>
        </div>

        {{-- Filter controls — hidden when printing --}}
        <div class="d-flex align-items-center gap-2 flex-wrap no-print">

            {{-- Month dropdown — wire:model.live calls updatedMonth() on change --}}
            <select wire:model.live="month" class="form-select form-select-sm" style="width:140px">
                @foreach($monthNames as $num => $name)
                    <option value="{{ $num }}" @selected($num == $selectedMonth)>
                        {{ $name }}
                    </option>
                @endforeach
            </select>

            {{-- Year dropdown — wire:model.live calls updatedYear() on change --}}
            <select wire:model.live="year" class="form-select form-select-sm" style="width:100px">
                @foreach($years as $yr)
                    <option value="{{ $yr }}" @selected($yr == $selectedYear)>
                        {{ $yr }}
                    </option>
                @endforeach
            </select>

            <button wire:click="printReport" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-printer me-1"></i>Print
            </button>
        </div>
    </div>

    {{-- Flash message --}}
    @if(session('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ================================================================
         PRINTABLE REPORT CONTENT
    ================================================================ --}}
    <div id="report-content">

        {{-- ── ROW 1: Trip Status Cards ── --}}
        <div class="row g-3 mb-4">

            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100 border-start border-4 border-primary">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-primary bg-opacity-10 p-3">
                            <i class="bi bi-clipboard-data fs-4 text-primary"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-semibold text-uppercase">Total Trips</div>
                            <div class="fs-2 fw-bold text-primary">{{ $summary['total_trips'] }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100 border-start border-4 border-success">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-success bg-opacity-10 p-3">
                            <i class="bi bi-check-circle fs-4 text-success"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-semibold text-uppercase">Completed</div>
                            <div class="fs-2 fw-bold text-success">{{ $summary['completed_trips'] }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100 border-start border-4 border-warning">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-warning bg-opacity-10 p-3">
                            <i class="bi bi-hourglass-split fs-4 text-warning"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-semibold text-uppercase">Ongoing</div>
                            <div class="fs-2 fw-bold text-warning">{{ $summary['ongoing_trips'] }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100 border-start border-4 border-danger">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-danger bg-opacity-10 p-3">
                            <i class="bi bi-x-circle fs-4 text-danger"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-semibold text-uppercase">Cancelled</div>
                            <div class="fs-2 fw-bold text-danger">{{ $summary['cancelled_trips'] }}</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- ── ROW 2: Financial Summary Cards ── --}}
        @php $isProfit = $summary['profit_loss'] >= 0; @endphp
        <div class="row g-3 mb-4">

            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100 border-start border-4 border-info">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-info bg-opacity-10 p-3">
                            <i class="bi bi-currency-rupee fs-4 text-info"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-semibold text-uppercase">Party Amount</div>
                            <div class="fs-5 fw-bold text-info">
                                ₹{{ number_format($summary['total_freight'], 2) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100 border-start border-4 border-secondary">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-secondary bg-opacity-10 p-3">
                            <i class="bi bi-receipt fs-4 text-secondary"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-semibold text-uppercase">Total Expenses</div>
                            <div class="fs-5 fw-bold text-secondary">
                                ₹{{ number_format($summary['total_expenses'], 2) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100 border-start border-4 {{ $isProfit ? 'border-success' : 'border-danger' }}">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded-circle {{ $isProfit ? 'bg-success' : 'bg-danger' }} bg-opacity-10 p-3">
                            <i class="bi {{ $isProfit ? 'bi-graph-up-arrow text-success' : 'bi-graph-down-arrow text-danger' }} fs-4"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-semibold text-uppercase">Profit / Loss</div>
                            <div class="fs-5 fw-bold {{ $isProfit ? 'text-success' : 'text-danger' }}">
                                ₹{{ number_format($summary['profit_loss'], 2) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100 border-start border-4 border-dark">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-dark bg-opacity-10 p-3">
                            <i class="bi bi-bar-chart-line fs-4 text-dark"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-semibold text-uppercase">Avg. Trip Profit</div>
                            <div class="fs-5 fw-bold text-dark">
                                ₹{{ number_format($summary['average_trip_profit'], 2) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- ── ROW 3: Expense Breakdown + Top Route ── --}}
        <div class="row g-3 mb-4">

            {{-- Expense breakdown table --}}
            <div class="col-md-7">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white fw-semibold border-bottom">
                        <i class="bi bi-pie-chart me-2 text-secondary"></i>Expense Breakdown by Type
                    </div>
                    <div class="card-body p-0">
                        @if(!empty($summary['expense_breakdown']))
                            <table class="table table-hover table-striped mb-0 align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">#</th>
                                        <th>Expense Type</th>
                                        <th class="text-end">Amount (₹)</th>
                                        <th class="text-end pe-3">% Share</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($summary['expense_breakdown'] as $type => $amount)
                                        @php
                                            $pct = $summary['total_expenses'] > 0
                                                ? round(($amount / $summary['total_expenses']) * 100, 1)
                                                : 0;
                                        @endphp
                                        <tr>
                                            <td class="ps-3 text-muted">{{ $loop->iteration }}</td>
                                            <td>{{ ucwords(str_replace('_', ' ', $type)) }}</td>
                                            <td class="text-end fw-semibold">{{ number_format($amount, 2) }}</td>
                                            <td class="text-end pe-3">
                                                <div class="d-flex align-items-center justify-content-end gap-2">
                                                    <div class="progress flex-grow-1" style="height:5px;max-width:70px">
                                                        <div class="progress-bar bg-secondary" style="width:{{ $pct }}%"></div>
                                                    </div>
                                                    <span class="text-muted small">{{ $pct }}%</span>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light fw-bold">
                                    <tr>
                                        <td colspan="2" class="ps-3">Total</td>
                                        <td class="text-end">{{ number_format($summary['total_expenses'], 2) }}</td>
                                        <td class="text-end pe-3">100%</td>
                                    </tr>
                                </tfoot>
                            </table>
                        @else
                            <div class="p-4 text-muted">
                                <i class="bi bi-inbox me-2"></i>No expense data for this period.
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Top performing route --}}
            <div class="col-md-5">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white fw-semibold border-bottom">
                        <i class="bi bi-signpost-split me-2 text-primary"></i>Top Performing Route
                    </div>
                    <div class="card-body">
                        @if(!empty($summary['top_route']))
                            <div class="d-flex flex-column gap-3">
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <span class="badge bg-primary fs-6 px-3 py-2">
                                        {{ $summary['top_route']['origin'] }}
                                    </span>
                                    <i class="bi bi-arrow-right fs-5 text-muted"></i>
                                    <span class="badge bg-success fs-6 px-3 py-2">
                                        {{ $summary['top_route']['destination'] }}
                                    </span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-truck text-muted"></i>
                                    <span class="text-muted">Trips on this route:</span>
                                    <strong class="fs-5">{{ $summary['top_route']['trip_count'] }}</strong>
                                </div>
                            </div>
                        @else
                            <div class="text-muted">
                                <i class="bi bi-inbox me-2"></i>No route data available.
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>

        {{-- ── Monthly Overview Table — one row per trip ── --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-semibold border-bottom d-flex justify-content-between align-items-center">
                <span>
                    <i class="bi bi-table me-2 text-primary"></i>
                    Monthly Overview — {{ $monthNames[$selectedMonth] }} {{ $selectedYear }}
                </span>
                <span class="badge bg-primary rounded-pill">
                    {{ count($summary['trip_rows']) }} trips
                </span>
            </div>
            <div class="card-body p-0">
                @if(!empty($summary['trip_rows']))
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered mb-0 align-middle small">
                            <thead class="table-dark">
                                <tr>
                                    <th class="ps-3">#</th>
                                    <th>Date</th>
                                    <th>Party</th>
                                    <th>Truck</th>
                                    <th>Driver</th>
                                    <th>Origin</th>
                                    <th>Destination</th>
                                    <th class="text-end">Freight (₹)</th>
                                    <th class="text-end">Expenses (₹)</th>
                                    <th class="text-end">Profit (₹)</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($summary['trip_rows'] as $i => $trip)
                                    <tr class="{{ $trip['profit'] < 0 ? 'table-danger' : '' }}">
                                        <td class="ps-3 text-muted">{{ $i + 1 }}</td>
                                        <td class="text-nowrap">{{ $trip['date'] }}</td>
                                        <td>{{ $trip['party'] }}</td>
                                        <td>{{ $trip['truck'] }}</td>
                                        <td>{{ $trip['driver'] }}</td>
                                        <td>{{ $trip['origin'] }}</td>
                                        <td>{{ $trip['destination'] }}</td>
                                        <td class="text-end text-info fw-semibold">
                                            {{ number_format($trip['freight_amount'], 2) }}
                                        </td>
                                        <td class="text-end text-secondary">
                                            {{ number_format($trip['expenses'], 2) }}
                                        </td>
                                        <td class="text-end fw-bold {{ $trip['profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                                            {{ number_format($trip['profit'], 2) }}
                                        </td>
                                        <td class="text-center">
                                            @php
                                                // Map status to Bootstrap badge color
                                                $color = match(true) {
                                                    in_array($trip['status'], ['completed','pod_received','pod_submitted','settled']) => 'success',
                                                    in_array($trip['status'], ['pending','start'])                                   => 'warning text-dark',
                                                    $trip['status'] === 'cancelled'                                                  => 'danger',
                                                    default                                                                          => 'secondary',
                                                };
                                            @endphp
                                            <span class="badge bg-{{ $color }}">
                                                {{ ucwords(str_replace('_', ' ', $trip['status'])) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>

                            {{-- Totals footer row --}}
                            <tfoot class="table-secondary fw-bold">
                                <tr>
                                    <td colspan="7" class="ps-3">Totals</td>
                                    <td class="text-end text-info">
                                        ₹{{ number_format($summary['total_freight'], 2) }}
                                    </td>
                                    <td class="text-end text-secondary">
                                        ₹{{ number_format($summary['total_expenses'], 2) }}
                                    </td>
                                    <td class="text-end {{ $summary['profit_loss'] >= 0 ? 'text-success' : 'text-danger' }}">
                                        ₹{{ number_format($summary['profit_loss'], 2) }}
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <div class="p-4 text-muted">
                        <i class="bi bi-inbox me-2"></i>
                        No trips found for <strong>{{ $monthNames[$selectedMonth] }} {{ $selectedYear }}</strong>.
                    </div>
                @endif
            </div>
        </div>

    </div>{{-- /#report-content --}}

    {{-- Print: hide controls, remove shadows --}}
    <style>
    @media print {
        .no-print  { display: none !important; }
        .card      { box-shadow: none !important; border: 1px solid #dee2e6 !important; }
        .table     { font-size: 11px; }
    }
    </style>

    {{-- Listen for Livewire-dispatched event and trigger browser print --}}
    <script>
        document.addEventListener('printReport', () => window.print());
    </script>

</div>{{-- /root --}}
<div>
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="mb-0 fw-bold">
                <i class="bi bi-person-badge me-2 text-primary"></i>Drivers Report
            </h2>
            <small class="text-muted">
                Showing: <strong>{{ $monthNames[$selectedMonth] }} {{ $selectedYear }}</strong>
            </small>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap no-print">
            <select wire:model.live="month" class="form-select form-select-sm" style="width:140px">
                @foreach ($monthNames as $num => $name)
                    <option value="{{ $num }}" @selected($num == $selectedMonth)>
                        {{ $name }}
                    </option>
                @endforeach
            </select>

            <select wire:model.live="year" class="form-select form-select-sm" style="width:100px">
                @foreach ($years as $yr)
                    <option value="{{ $yr }}" @selected($yr == $selectedYear)>
                        {{ $yr }}
                    </option>
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
        <!-- Summary Cards -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100 border-start border-4 border-primary">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded-circle">
                            <i class="bi bi-person fs-4 text-primary"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-semibold text-uppercase">Total Drivers</div>
                            <div class="fs-2 fw-bold text-primary">{{ $summary['total_drivers'] ?? 0 }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100 border-start border-4 border-success">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded-circle ">
                            <i class="bi bi-person-check fs-4 text-success"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-semibold text-uppercase">Active Drivers</div>
                            <div class="fs-2 fw-bold text-success">{{ $summary['active_drivers'] ?? 0 }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100 border-start border-4 border-warning">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded-circle ">
                            <i class="bi bi-truck fs-4 text-warning"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-semibold text-uppercase">Drivers Assigned</div>
                            <div class="fs-2 fw-bold text-warning">{{ $summary['drivers_assigned_to_trips'] ?? 0 }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100 border-start border-4 border-danger">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded-circle ">
                            <i class="bi bi-person-x fs-4 text-danger"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-semibold text-uppercase">Unassigned</div>
                            <div class="fs-2 fw-bold text-danger">
                                {{ $summary['utilization_overview']['unassigned_drivers'] ?? 0 }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Performing Driver -->
        @if (!empty($summary['top_performing_driver']))
            <div class="card mb-4">
                <div class="card-header bg-white fw-semibold border-bottom">
                    <i class="bi bi-trophy me-2 text-warning"></i>Top Performing Driver
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h6>{{ $summary['top_performing_driver']['name'] ?? '—' }}</h6>
                                    <p class="mb-1">{{ $summary['top_performing_driver']['mobile'] ?? '—' }}</p>
                                    <small>License:
                                        {{ $summary['top_performing_driver']['license_number'] ?? '—' }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="card bg-success text-white text-center">
                                        <div class="card-body">
                                            <h6>₹{{ number_format($summary['top_performing_driver']['earnings'] ?? 0, 2) }}
                                            </h6>
                                            <small>Earnings</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-info text-white text-center">
                                        <div class="card-body">
                                            <h6>{{ $summary['top_performing_driver']['trips_count'] ?? 0 }}</h6>
                                            <small>Trips</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-warning text-white text-center">
                                        <div class="card-body">
                                            <h6>₹{{ number_format($summary['top_performing_driver']['average_earnings_per_trip'] ?? 0, 2) }}
                                            </h6>
                                            <small>Avg. Earnings/Trip</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-secondary text-white text-center">
                                        <div class="card-body">
                                            <h6>{{ $summary['top_performing_driver']['completed_trips'] ?? 0 }}</h6>
                                            <small>Completed</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Utilization Overview -->
        <div class="card mb-4">
            <div class="card-header bg-white fw-semibold border-bottom">
                <i class="bi bi-speedometer2 me-2 text-info"></i>Driver Utilization Overview
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="card bg-light text-center">
                            <div class="card-body">
                                <h6>{{ $summary['utilization_overview']['total_drivers'] ?? 0 }}</h6>
                                <small>Total Drivers</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white text-center">
                            <div class="card-body">
                                <h6>{{ $summary['utilization_overview']['active_drivers'] ?? 0 }}</h6>
                                <small>Active Drivers</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white text-center">
                            <div class="card-body">
                                <h6>{{ $summary['utilization_overview']['assigned_drivers'] ?? 0 }}</h6>
                                <small>Assigned Drivers</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-secondary text-white text-center">
                            <div class="card-body">
                                <h6>{{ $summary['utilization_overview']['unassigned_drivers'] ?? 0 }}</h6>
                                <small>Unassigned Drivers</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="progress">
                        <div class="progress-bar bg-success" role="progressbar"
                            style="width: {{ $summary['utilization_overview']['utilization_percentage'] ?? 0 }}%"
                            aria-valuenow="{{ $summary['utilization_overview']['utilization_percentage'] ?? 0 }}"
                            aria-valuemin="0" aria-valuemax="100">
                            {{ $summary['utilization_overview']['utilization_percentage'] ?? 0 }}%
                        </div>
                    </div>
                    <small class="text-muted">Overall Utilization:
                        {{ $summary['utilization_overview']['utilization_percentage'] ?? 0 }}%</small>
                </div>
            </div>
        </div>

        <!-- Driver Performance Table -->
        <div class="card border-0 shadow-sm mb-4">
            <div
                class="card-header bg-white fw-semibold border-bottom d-flex justify-content-between align-items-center">
                <span>
                    <i class="bi bi-table me-2 text-primary"></i>
                    Driver Performance Details
                </span>
                <span class="badge bg-primary rounded-pill">
                    {{ count($summary['driver_performance'] ?? []) }} drivers
                </span>
            </div>
            <div class="card-body p-0">
                @if (!empty($summary['driver_performance']))
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0 align-middle small">
                            <thead class="table-light">
                                <tr>
                                    <th>Driver Name</th>
                                    <th>Mobile</th>
                                    <th>Truck Assigned</th>
                                    <th>Trips Count</th>
                                    <th>Earnings</th>
                                    <th>Avg. Earnings/Trip</th>
                                    <th>Completed Trips</th>
                                    <th>Ongoing Trips</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($summary['driver_performance'] as $driver)
                                    <tr>
                                        <td>{{ $driver['name'] ?? '—' }}</td>
                                        <td>{{ $driver['mobile'] ?? '—' }}</td>
                                        <td>{{ $driver['truck_number'] ?? '—' }}</td>
                                        <td>{{ $driver['trips_count'] ?? 0 }}</td>
                                        <td>₹{{ number_format($driver['earnings'] ?? 0, 2) }}</td>
                                        <td>₹{{ number_format($driver['average_earnings_per_trip'] ?? 0, 2) }}</td>
                                        <td>{{ $driver['completed_trips'] ?? 0 }}</td>
                                        <td>{{ $driver['ongoing_trips'] ?? 0 }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-4 text-muted">
                        <i class="bi bi-inbox me-2"></i>No driver data available for the selected period.
                    </div>
                @endif
            </div>
        </div>

        <!-- Driver Expense Overview -->
        @if (!empty($summary['driver_expenses']))
            <div class="card mb-4">
                <div class="card-header bg-white fw-semibold border-bottom">
                    <i class="bi bi-cash-stack me-2 text-secondary"></i>Driver Expense Overview (Paid by Driver)
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0 align-middle small">
                            <thead class="table-light">
                                <tr>
                                    <th>Driver ID</th>
                                    <th>Total Expenses (₹)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($summary['driver_expenses'] as $driverId => $expenses)
                                    @php
                                        $driver = \App\Models\Driver::find($driverId);
                                    @endphp
                                    <tr>
                                        <td>#{{ $driverId }} - {{ $driver->name ?? 'Unknown' }}</td>
                                        <td>₹{{ number_format($expenses, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
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

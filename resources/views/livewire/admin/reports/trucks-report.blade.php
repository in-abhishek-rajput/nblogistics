<div>
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="mb-0 fw-bold">
                <i class="bi bi-truck me-2 text-primary"></i>Trucks Report
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
                        <div class="rounded-circle ">
                            <i class="bi bi-truck fs-4 text-primary"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-semibold text-uppercase">Total Trucks</div>
                            <div class="fs-2 fw-bold text-primary">{{ $summary['total_trucks'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100 border-start border-4 border-success">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded-circle ">
                            <i class="bi bi-check-circle fs-4 text-success"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-semibold text-uppercase">Active Trucks</div>
                            <div class="fs-2 fw-bold text-success">{{ $summary['active_trucks'] }}</div>
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
                            <div class="text-muted small fw-semibold text-uppercase">Trucks Used in Trips</div>
                            <div class="fs-2 fw-bold text-warning">{{ $summary['trucks_used_in_trips'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100 border-start border-4 border-danger">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded-circle ">
                            <i class="bi bi-x-circle fs-4 text-danger"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-semibold text-uppercase">Idle Trucks</div>
                            <div class="fs-2 fw-bold text-danger">{{ $summary['idle_trucks'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Earning Truck -->
        @if (!empty($summary['top_earning_truck']))
            <div class="card mb-4">
                <div class="card-header bg-white fw-semibold border-bottom">
                    <i class="bi bi-trophy me-2 text-warning"></i>Top Earning Truck
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h6>{{ $summary['top_earning_truck']['truck_number'] ?? '—' }}</h6>
                                    <p class="mb-1">{{ $summary['top_earning_truck']['truck_type'] ?? '—' }}</p>
                                    <small>{{ $summary['top_earning_truck']['driver_name'] ?? 'Not Assigned' }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="card bg-success text-white text-center">
                                        <div class="card-body">
                                            <h6>₹{{ number_format($summary['top_earning_truck']['income'] ?? 0, 2) }}
                                            </h6>
                                            <small>Income</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-danger text-white text-center">
                                        <div class="card-body">
                                            <h6>₹{{ number_format($summary['top_earning_truck']['expenses'] ?? 0, 2) }}
                                            </h6>
                                            <small>Expenses</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-warning text-white text-center">
                                        <div class="card-body">
                                            <h6>₹{{ number_format($summary['top_earning_truck']['maintenance_expenses'] ?? 0, 2) }}
                                            </h6>
                                            <small>Maintenance</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-info text-white text-center">
                                        <div class="card-body">
                                            <h6>₹{{ number_format($summary['top_earning_truck']['profit'] ?? 0, 2) }}
                                            </h6>
                                            <small>Profit</small>
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
                <i class="bi bi-speedometer2 me-2 text-info"></i>Truck Utilization Overview
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="card bg-light text-center">
                            <div class="card-body">
                                <h6>{{ $summary['utilization_overview']['total_trucks'] ?? 0 }}</h6>
                                <small>Total Trucks</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white text-center">
                            <div class="card-body">
                                <h6>{{ $summary['utilization_overview']['active_trucks'] ?? 0 }}</h6>
                                <small>Active Trucks</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white text-center">
                            <div class="card-body">
                                <h6>{{ $summary['utilization_overview']['used_trucks'] ?? 0 }}</h6>
                                <small>Used Trucks</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-secondary text-white text-center">
                            <div class="card-body">
                                <h6>{{ $summary['utilization_overview']['idle_trucks'] ?? 0 }}</h6>
                                <small>Idle Trucks</small>
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

        <!-- Truck Performance Table -->
        <div class="card border-0 shadow-sm mb-4">
            <div
                class="card-header bg-white fw-semibold border-bottom d-flex justify-content-between align-items-center">
                <span>
                    <i class="bi bi-table me-2 text-primary"></i>
                    Truck Performance Details
                </span>
                <span class="badge bg-primary rounded-pill">
                    {{ count($summary['truck_performance'] ?? []) }} trucks
                </span>
            </div>
            <div class="card-body p-0">
                @if (!empty($summary['truck_performance']))
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Truck Number</th>
                                    <th>Type</th>
                                    <th>Ownership</th>
                                    <th>Status</th>
                                    <th>Assigned Driver</th>
                                    <th>Trips Count</th>
                                    <th>Completed</th>
                                    <th>Ongoing</th>
                                    <th>Cancelled</th>
                                    <th>Total KM</th>
                                    <th>Income</th>
                                    <th>Expenses</th>
                                    <th>Maintenance</th>
                                    <th>Profit</th>
                                    <th>Avg Profit/Trip</th>
                                    <th>Expense/Trip</th>
                                    <th>Utilization %</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($summary['truck_performance'] as $truck)
                                    <tr>
                                        <td>{{ $truck['truck_number'] ?? '—' }}</td>
                                        <td>{{ $truck['truck_type'] ?? '—' }}</td>
                                        <td>{{ $truck['ownership'] ?? '-' }}</td>
                                        <td>{{ $truck['status'] ?? '-' }}</td>
                                        <td>{{ $truck['driver_name'] ?? '—' }}</td>
                                        <td>{{ $truck['trips_count'] ?? 0 }}</td>
                                        <td>{{ $truck['completed_trips'] ?? 0 }}</td>
                                        <td>{{ $truck['ongoing_trips'] ?? 0 }}</td>
                                        <td>{{ $truck['cancelled_trips'] ?? 0 }}</td>
                                        <td>{{ number_format($truck['total_km'] ?? 0) }}</td>
                                        <td>₹{{ number_format($truck['income'] ?? 0, 2) }}</td>
                                        <td>₹{{ number_format($truck['expenses'] ?? 0, 2) }}</td>
                                        <td>₹{{ number_format($truck['maintenance_expenses'] ?? 0, 2) }}</td>
                                        <td>₹{{ number_format($truck['profit'] ?? 0, 2) }}</td>
                                        <td>₹{{ number_format($truck['avg_profit_per_trip'] ?? 0, 2) }}</td>
                                        <td>₹{{ number_format($truck['expense_per_trip'] ?? 0, 2) }}</td>
                                        <td>{{ number_format($truck['utilization'] ?? 0, 2) }}%</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-4 text-muted">
                        <i class="bi bi-inbox me-2"></i>No truck data available for the selected period.
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

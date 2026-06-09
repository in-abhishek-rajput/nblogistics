<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Advance History: {{ $driver->name }}</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('drivers.index') }}">Drivers</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Advance History</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('drivers.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Drivers
            </a>
        </div>
    </div>

    {{-- Filters & Summary Card --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-3 mb-3 mb-md-0">

                    <div class="border p-3 rounded d-flex align-items-center gap-3">
                        <label>TOTAL ADVANCES <i class="bi bi-info-circle"></i></label>
                        <h3 class="text-primary mb-0">
                            ₹{{ number_format($totalAdvances, 2) }}
                        </h3>
                    </div>
                    <!-- <div class="card bg-primary bg-opacity-10 border-primary border-opacity-25 shadow-none">
                        <div class="card-body py-3">
                            <h6 class="card-subtitle mb-1">Total Advances</h6>
                            <h3 class="card-title mb-0 fw-bold">₹{{ number_format($totalAdvances, 2) }}</h3>
                        </div>
                    </div> -->
                </div>
                <div class="col-md-9">
                    <div class="row g-2 justify-content-end">
                        <div class="col-md-4">
                            <label class="form-label small text-muted mb-1">Search</label>
                            <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Search remarks...">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small text-muted mb-1">Month</label>
                            <select wire:model.live="selectedMonth" class="form-select">
                                <option value="">All Months</option>
                                @foreach (range(1, 12) as $m)
                                    <option value="{{ $m }}">{{ date('F', mktime(0, 0, 0, $m, 10)) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small text-muted mb-1">Year</label>
                            <select wire:model.live="selectedYear" class="form-select">
                                <option value="">All Years</option>
                                @foreach (range(date('Y') - 5, date('Y') + 1) as $y)
                                    <option value="{{ $y }}">{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-secondary w-100" wire:click="$set('search', ''); $set('selectedMonth', ''); $set('selectedYear', '');">
                                Clear
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Data Table Card --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Remarks</th>
                            <th>Created By</th>
                            <th>Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($advances as $advance)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($advance->advance_date)->format('d M, Y') }}</td>
                                <td class="fw-bold text-danger">-₹{{ number_format($advance->amount, 2) }}</td>
                                <td>{{ $advance->remarks ?: '-' }}</td>
                                <td>{{ $advance->user ? $advance->user->name : 'System' }}</td>
                                <td class="text-muted small">{{ $advance->created_at->format('d M, Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox fs-2"></i>
                                    <p class="mt-2 mb-0">No advances found for the selected period.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($advances->hasPages())
            <div class="card-footer bg-white border-top-0 pt-3 pb-0">
                <div class="d-flex justify-content-center">
                    {{ $advances->links('vendor.pagination.bootstrap-5') }}
                </div>
            </div>
        @endif
    </div>
</div>

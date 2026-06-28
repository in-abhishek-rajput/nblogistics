<div>
    <div wire:ignore.self class="offcanvas offcanvas-end" tabindex="-1" id="monthlyReportOffcanvas" aria-labelledby="monthlyReportOffcanvasLabel" style="width: 600px;">
        <div class="offcanvas-header border-bottom py-3 px-4">
            <div>
                <h5 class="offcanvas-title fw-bold" id="monthlyReportOffcanvasLabel">Monthly Profit & Loss Report</h5>
                <div class="text-muted small mt-1">
                    {{ $truck->truck_number }} · {{ $types[$truck->truck_type] ?? ucfirst($truck->truck_type) }} · {{ ucfirst($truck->status ?? 'Unknown') }}
                </div>
            </div>
            <button type="button" class="btn-close text-dark" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-4 position-relative" style="min-height: calc(100vh - 110px);">
            <div class="row g-3 mb-4">
                <div class="col-6">
                    <label class="form-label small mb-1 text-muted">Month</label>
                    <select wire:model.live="month" class="form-select form-select-sm">
                        @foreach ($months as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6">
                    <label class="form-label small mb-1 text-muted">Year</label>
                    <select wire:model.live="year" class="form-select form-select-sm">
                        @foreach ($years as $yearOption)
                            <option value="{{ $yearOption }}">{{ $yearOption }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-4">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
                        <div class="card-body py-3 px-4">
                            <p class="text-uppercase text-muted mb-1" style="font-size:0.70rem; letter-spacing:.05em;">Trips Started</p>
                            <h4 class="mb-0 fw-bold">{{ $totalTripsStarted ?? 0 }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
                        <div class="card-body py-3 px-4">
                            <p class="text-uppercase text-muted mb-1" style="font-size:0.70rem; letter-spacing:.05em;">Last Trip KM</p>
                            <h4 class="mb-0 fw-bold">{{ $lastTripKmReading ? number_format($lastTripKmReading, 0) . ' KM' : '–' }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
                        <div class="card-body py-3 px-4">
                            <p class="text-uppercase text-muted mb-1" style="font-size:0.70rem; letter-spacing:.05em;">Refuel Qty</p>
                            <h4 class="mb-0 fw-bold">{{ $totalRefuelQuantity ? number_format($totalRefuelQuantity, 2) . ' L' : '0 L' }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="d-flex align-items-center gap-2">
                    <h6 class="mb-0">Revenue</h6>
                    <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="$toggle('revenueExpanded')">
                        <i class="bi {{ $revenueExpanded ? 'bi-chevron-up' : 'bi-chevron-down' }}"></i>
                    </button>
                </div>
                <div class="fw-semibold text-success">₹ {{ number_format($totalRevenue, 2) }}</div>
            </div>

            @if ($revenueExpanded)
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
                    <div class="card-body p-3">
                        @if (!empty($revenueData))
                            @foreach ($revenueData as $trip)
                                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                    <div>
                                        <div class="fw-semibold">{{ $trip['date'] }}</div>
                                        <div class="text-muted small">{{ $trip['route'] }}</div>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-semibold">₹ {{ number_format($trip['total'], 2) }}</div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center text-muted py-3">No trips found for selected period.</div>
                        @endif
                    </div>
                </div>
            @endif

            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="d-flex align-items-center gap-2">
                    <h6 class="mb-0">Expenses</h6>
                    <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="$toggle('expensesExpanded')">
                        <i class="bi {{ $expensesExpanded ? 'bi-chevron-up' : 'bi-chevron-down' }}"></i>
                    </button>
                </div>
                <div class="fw-semibold text-danger">₹ {{ number_format($totalExpenses, 2) }}</div>
            </div>

            @if ($expensesExpanded)
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
                    <div class="card-body p-3">
                        @php
                            $expenseTypes = [
                                'fuel' => 'Fuel Expense',
                                'emi' => 'EMI Payment',
                                'driver' => 'Driver Payment',
                                'maintenance' => 'Maintenance',
                                'document' => 'Document Expense',
                                'trip_expense' => 'Trip Expense',
                            ];
                        @endphp
                        @foreach ($expenseTypes as $typeKey => $typeLabel)
                            @if (!empty($expensesData[$typeKey]))
                                <div class="mb-3">
                                    <div class="text-uppercase text-muted small mb-2" style="letter-spacing:.08em;">{{ $typeLabel }}</div>
                                    @foreach ($expensesData[$typeKey] as $expense)
                                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                            <div>
                                                <div class="fw-semibold">{{ $expense['date'] }}</div>
                                                <div class="text-muted small">{{ $expense['type'] }}</div>
                                                @if (!empty($expense['quantity']))
                                                    <div class="text-muted small">{{ $expense['quantity'] }}</div>
                                                @endif
                                            </div>
                                            <div class="text-end">
                                                <div class="fw-semibold text-danger">₹ {{ number_format($expense['amount'], 2) }}</div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        @endforeach
                        @if (empty($expensesData))
                            <div class="text-center text-muted py-3">No expenses found for selected period.</div>
                        @endif
                    </div>
                </div>
            @endif

            <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; background: #e8ecf5;">
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-6">
                            <p class="text-uppercase text-muted mb-1" style="font-size:0.70rem; letter-spacing:.05em;">Total Revenue</p>
                            <h5 class="mb-0 fw-bold text-success">₹ {{ number_format($totalRevenue, 2) }}</h5>
                        </div>
                        <div class="col-6">
                            <p class="text-uppercase text-muted mb-1" style="font-size:0.70rem; letter-spacing:.05em;">Total Expenses</p>
                            <h5 class="mb-0 fw-bold text-danger">₹ {{ number_format($totalExpenses, 2) }}</h5>
                        </div>
                        <div class="col-12">
                            <hr class="my-2">
                            <p class="text-uppercase text-muted mb-1" style="font-size:0.70rem; letter-spacing:.05em;">Net {{ $profitLossLabel }}</p>
                            <h4 class="mb-0 fw-bold {{ $profitLossClass }}">₹ {{ number_format(abs($profitLoss), 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-success flex-fill" onclick="window.open('{{ route('trucks.monthly-report-pdf', ['truck' => $truckId, 'month' => $month, 'year' => $year]) }}', '_blank')">
                    <i class="bi bi-download me-1"></i> Download PDF
                </button>
            </div>
        </div>
    </div>

    <div wire:ignore.self class="modal fade" id="viewReportPdfModal" tabindex="-1" aria-labelledby="viewReportPdfModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="viewReportPdfModalLabel">Monthly Profit & Loss Report</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="height: 80vh;">
                    @if($showPdf)
                        <iframe src="{{ route('trucks.monthly-report-pdf', ['truck' => $truckId, 'month' => $month, 'year' => $year]) }}" style="width: 100%; height: 100%; border: none;"></iframe>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @script
        <script>
            const monthlyReportCanvasEl = document.getElementById('monthlyReportOffcanvas');
            const monthlyReportOffcanvas = new bootstrap.Offcanvas(monthlyReportCanvasEl);

            window.addEventListener('openMonthlyReportOffcanvas', () => {
                monthlyReportOffcanvas.show();
            });

            window.addEventListener('showViewReportPdfModal', () => {
                const modal = new bootstrap.Modal(document.getElementById('viewReportPdfModal'));
                modal.show();
            });
        </script>
    @endscript
</div>
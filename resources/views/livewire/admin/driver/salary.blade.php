<div>
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Top Filters --}}
    <div class="row g-2 mb-3">
        <div class="col-6 col-md-2">
            <select wire:model.live="selectedMonth" class="form-select">
                @foreach (range(1, 12) as $m)
                    <option value="{{ $m }}">{{ date('F', mktime(0, 0, 0, $m, 10)) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-md-2">
            <select wire:model.live="selectedYear" class="form-select">
                @foreach (range(date('Y') - 5, date('Y') + 1) as $y)
                    <option value="{{ $y }}">{{ $y }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-12 col-md d-flex align-items-center">
            <div wire:loading class="ms-3 spinner-border spinner-border-sm text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>

    {{-- Salary Table --}}
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="bg-light">
                <tr>
                    <th>Driver Name</th>
                    <th>Base Salary</th>
                    <th>Paid Days</th>
                    <th>Gross Salary</th>
                    <th>Advances (Deduction)</th>
                    <th>Net Salary</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($drivers as $driver)
                    @php
                        $presentDays = $driver->attendances->where('status', 'present')->count();
                        $holidays = $driver->attendances->where('status', 'holiday')->count();
                        $paidDays = $presentDays + $holidays;

                        $record = $savedRecords[$driver->id] ?? null;
                        
                        // We show calculated live preview if not saved, or the saved values if they are saved.
                        // However, user might change advances before saving. Let's use live calculation for preview.
                        $advance = $advances[$driver->id] ?? 0;
                        $previewGross = ($driver->base_salary / $daysInMonth) * $paidDays;
                        $previewNet = $previewGross - (float) $advance;
                        
                        $isSaved = $record !== null;
                        $isPaid = $record && $record->status === 'PAID';
                    @endphp
                    <tr>
                        <td class="fw-bold">{{ $driver->name }}</td>
                        <td>₹{{ number_format($driver->base_salary, 2) }}</td>
                        <td>{{ $paidDays }} / {{ $daysInMonth }}</td>
                        <td>
                            @if($isSaved)
                                ₹{{ number_format($record->gross_salary, 2) }}
                            @else
                                <span class="text-muted">₹{{ number_format($previewGross, 2) }} (Preview)</span>
                            @endif
                        </td>
                        <td>
                            <div class="input-group input-group-sm" style="max-width: 150px;">
                                <span class="input-group-text bg-light text-danger fw-bold">₹</span>
                                <span class="form-control bg-light text-danger fw-bold">{{ number_format($advance, 2) }}</span>
                            </div>
                        </td>
                        <td>
                            @if($isSaved)
                                <strong class="text-success">₹{{ number_format($record->net_salary, 2) }}</strong>
                            @else
                                <strong class="text-muted">₹{{ number_format($previewNet, 2) }}</strong>
                            @endif
                        </td>
                        <td>
                            @if($isPaid)
                                <span class="badge bg-success">PAID</span>
                            @elseif($isSaved)
                                <span class="badge bg-warning text-dark">PENDING</span>
                            @else
                                <span class="badge bg-secondary">NOT CALCULATED</span>
                            @endif
                        </td>
                        <td>
                            @if(!$isPaid)
                                <button class="btn btn-sm btn-primary" wire:click="saveSalary({{ $driver->id }})">
                                    <i class="bi bi-save"></i> Calculate
                                </button>
                            @endif
                            @if($isSaved && !$isPaid)
                                <button class="btn btn-sm btn-success ms-1" wire:click="markAsPaid({{ $driver->id }})">
                                    <i class="bi bi-check-circle"></i> Mark Paid
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            <i class="bi bi-people fs-2"></i>
                            <p class="mt-2 mb-0">No drivers available.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div>
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
            @if($isPastMonth)
                <span class="badge bg-secondary">Viewing Past Month (Read Only)</span>
            @elseif($isFutureMonth)
                <span class="badge bg-warning text-dark">Viewing Future Month</span>
            @else
                <span class="badge bg-success">Current Month (Editable)</span>
            @endif
            <div wire:loading class="ms-3 spinner-border spinner-border-sm text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>

    {{-- Attendance Table --}}
    <div class="table-responsive" style="max-height: 70vh; overflow-y: auto;">
        <table class="table table-bordered table-hover text-center align-middle" style="min-width: max-content;">
            <thead class="bg-light sticky-top" style="z-index: 3;">
                <tr>
                    <th scope="col" class="text-start sticky-left bg-light" style="position: sticky; left: 0; z-index: 4;">Driver Name</th>
                    @for ($day = 1; $day <= $daysInMonth; $day++)
                        <th scope="col" style="width: 40px; min-width: 40px;" class="bg-light">
                            {{ $day }}
                        </th>
                    @endfor
                    <th scope="col" class="bg-light sticky-right" style="position: sticky; right: 0; z-index: 4; width: 80px; min-width: 80px;">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($drivers as $driver)
                    <tr>
                        <td class="text-start sticky-left bg-white fw-bold" style="position: sticky; left: 0; z-index: 1;">
                            {{ $driver->name }}
                        </td>
                        
                        @for ($day = 1; $day <= $daysInMonth; $day++)
                            @php
                                $isDisabled = false;
                                if ($isPastMonth) {
                                    $isDisabled = true;
                                } elseif ($isFutureMonth) {
                                    $isDisabled = true;
                                } elseif (!$isPastMonth && !$isFutureMonth && $day > $currentDay) {
                                    $isDisabled = true;
                                }
                                $isChecked = isset($attendanceData[$driver->id][$day]);
                            @endphp
                            <td class="p-1">
                                <input class="form-check-input m-0" type="checkbox" 
                                    @if($isChecked) checked @endif
                                    @if($isDisabled) disabled @endif
                                    wire:change="toggleAttendance({{ $driver->id }}, {{ $day }}, $event.target.checked)"
                                    style="cursor: {{ $isDisabled ? 'not-allowed' : 'pointer' }}; width: 1.2rem; height: 1.2rem;">
                            </td>
                        @endfor
                        
                        <td class="bg-light fw-bold sticky-right" style="position: sticky; right: 0; z-index: 1;">
                            {{ $totalPresent[$driver->id] }}/{{ $daysInMonth }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $daysInMonth + 2 }}" class="text-center py-4 text-muted">
                            <i class="bi bi-people fs-2"></i>
                            <p class="mt-2 mb-0">No drivers available.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <style>
        .sticky-left {
            box-shadow: 2px 0 5px -2px rgba(0,0,0,0.1);
        }
        .sticky-right {
            box-shadow: -2px 0 5px -2px rgba(0,0,0,0.1);
        }
    </style>
</div>

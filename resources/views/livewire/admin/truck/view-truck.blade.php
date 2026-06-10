<div>
    <div class="rounded-3 mb-4 px-4 py-3 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3"
        style="background: #dde3f0;">
        <div>
            <div class="text-muted small mb-1" style="font-size: 0.78rem;">Truck Details</div>
            <div class="fw-bold fs-5">{{ $truck->truck_number }}</div>
            <div class="text-muted small">{{ $types[$truck->truck_type] ?? ucfirst($truck->truck_type) }}</div>
        </div>
        <div class="d-flex gap-2">
            <button type="button" wire:click="editTruck" class="btn btn-light border fw-semibold"
                style="font-size:0.85rem;">
                <i class="bi bi-pencil-fill text-primary me-1"></i> Edit
            </button>
            <button type="button" wire:click="deleteTruck"
                onclick="confirm('Are you sure you want to delete this truck?') || event.stopImmediatePropagation()"
                class="btn btn-light border fw-semibold text-danger" style="font-size:0.85rem;">
                <i class="bi bi-trash-fill me-1"></i> Delete Truck
            </button>
        </div>
    </div>

    {{-- FILTERS ROW --}}
    <div class="row g-3 align-items-end mb-4">
        <div class="col-auto">
            <label class="form-label small mb-1 text-muted">Date</label>
            <select wire:model="monthFilter" class="form-select form-select-sm" style="min-width:140px;">
                @foreach ($monthOptions as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-auto">
            <label class="form-label small mb-1 text-muted">Filter</label>
            <select wire:model="activityFilter" class="form-select form-select-sm" style="min-width:180px;">
                @foreach ($activityOptions as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col text-end">
            <button type="button" class="btn btn-primary btn-sm px-4 py-2 fw-semibold">
                <i class="bi bi-file-earmark-bar-graph me-2"></i>Monthly Reports
            </button>
        </div>
    </div>

    {{-- STATS CARDS --}}
    <div class="row g-3 mb-4">
        @foreach ($stats as $card)
            <div class="col-6 col-md-4">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 10px;">
                    <div class="card-body py-3 px-4">
                        <p class="text-uppercase text-muted mb-1" style="font-size:0.70rem; letter-spacing:.05em;">
                            {{ $card['label'] }}</p>
                        <h4
                            class="mb-0 fw-bold {{ isset($card['negative']) && $card['negative'] ? 'text-danger' : '' }}">
                            {{ $card['value'] }}
                        </h4>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- ACTIVITY ICONS ROW --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius:12px; background:#f7f8fc;">
        <div class="card-body py-3">
            <div class="row g-2 justify-content-center justify-content-md-start text-center">
                @foreach ($activityCards as $card)
                    @php
                        $openEvent = false;
                        $eventName = null;
                        if (isset($card['openEmiBook']) && $card['openEmiBook']) {
                            $openEvent = true;
                            $eventName = 'openEmiBookPanel';
                        }
                        if (isset($card['openFuelBook']) && $card['openFuelBook']) {
                            $openEvent = true;
                            $eventName = 'openFuelBookPanel';
                        }
                    @endphp
                    <div class="col-auto">
                        @if ($openEvent)
                            <a href="#" wire:click.prevent="$dispatch('{{ $eventName }}')"
                                class="d-flex flex-column align-items-center text-decoration-none text-dark px-3 py-1"
                                style="min-width:80px;">
                        @else
                            <a href="{{ $card['href'] }}"
                                class="d-flex flex-column align-items-center text-decoration-none text-dark px-3 py-1"
                                style="min-width:80px;">
                        @endif
                            <div class="mb-2 d-flex align-items-center justify-content-center rounded-circle"
                                style="width:54px;height:54px;background:#fff;box-shadow:0 1px 6px rgba(0,0,0,.08);">
                                <i class="bi {{ $card['icon'] }} fs-4"
                                    style="color:{{ $card['iconColor'] ?? '#555' }};"></i>
                            </div>
                            <span class="small fw-semibold"
                                style="font-size:0.72rem; line-height:1.3;">{{ $card['label'] }}</span>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <livewire:admin.truck.emi-book :truck-id="$truck->id" />
    <livewire:admin.truck.fuel-book :truck-id="$truck->id" />

    {{-- HISTORY TABLE --}}
    <div class="card border-0 shadow-sm" style="border-radius:12px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-borderless align-middle mb-0">
                    <thead style="background:#e8ecf5;">
                        <tr>
                            <th class="fw-semibold ps-4" style="font-size:.82rem;">Date</th>
                            <th class="fw-semibold" style="font-size:.82rem;">Reason</th>
                            <th class="fw-semibold" style="font-size:.82rem;">Expenses</th>
                            <th class="fw-semibold" style="font-size:.82rem;">Revenue</th>
                            <th class="fw-semibold" style="font-size:.82rem;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($historyRows as $row)
                            <tr class="{{ $loop->even ? '' : 'bg-light' }}" style="border-bottom:1px solid #f0f0f0;">
                                <td class="ps-4 fw-semibold" style="font-size:.85rem;">{{ $row['date'] }}</td>
                                <td style="font-size:.85rem;">{{ $row['reason'] }}</td>
                                <td class="text-danger fw-semibold" style="font-size:.85rem;">
                                    {{ $row['expense'] ?? '' }}
                                </td>
                                <td style="font-size:.85rem;">{{ $row['revenue'] ?? '' }}</td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-sm btn-outline-primary rounded"
                                            style="width:32px;height:32px;padding:0;">
                                            <i class="bi bi-pencil-fill" style="font-size:.75rem;"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger rounded"
                                            style="width:32px;height:32px;padding:0;">
                                            <i class="bi bi-trash-fill" style="font-size:.75rem;"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    No EMI history available yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

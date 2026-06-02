<div>
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="mb-0">{{ $pageTitle }}</h5>
            <small class="text-muted">{{ $documents->total() }} records</small>
        </div>
        <div class="d-flex gap-2">
            <button type="button" wire:click="exportList" class="btn btn-outline-success btn-sm">
                <i class="bi bi-download me-1"></i>Export
            </button>
            <button type="button" wire:click="printList" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-printer me-1"></i>Print
            </button>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <div class="input-group">
                <span class="input-group-text bg-white">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" class="form-control" placeholder="Search documents" wire:model.live.debounce.300ms="search">
            </div>
        </div>
        <div class="col-md-2">
            <select class="form-select" wire:model.live="selectedDateFilter">
                @foreach ($dateFilters as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select class="form-select" wire:model.live="statusFilter">
                <option value="">All Statuses</option>
                @foreach ($statuses as $key => $status)
                    <option value="{{ $key }}">{{ $status['label'] }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <input type="date" class="form-control" wire:model.live="from_date" placeholder="From">
        </div>
        <div class="col-md-2">
            <input type="date" class="form-control" wire:model.live="to_date" placeholder="To">
        </div>
    </div>

    @if ($selectedDateFilter === 'custom')
        <div class="alert alert-light border mb-3">
            Custom range is active. Set both dates for a full range filter.
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    @php
                        $headers = [
                            'document_number' => 'Document Number',
                            'trip_number' => 'Trip Number',
                            'vehicle_number' => 'Vehicle Number',
                            'driver_name' => 'Driver Name',
                            'customer_name' => 'Customer Name',
                            'source_location' => 'Source Location',
                            'destination_location' => 'Destination Location',
                            'document_date' => 'Document Date',
                            'status' => 'Status',
                            'created_by' => 'Created By',
                        ];
                    @endphp

                    @foreach ($headers as $column => $label)
                        <th>
                            <button type="button" class="btn btn-link p-0 text-decoration-none text-dark" wire:click="sortBy('{{ $column }}')">
                                {{ $label }}
                                @if ($sortColumn === $column)
                                    <i class="bi bi-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </button>
                        </th>
                    @endforeach
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($documents as $document)
                    <tr>
                        <td>{{ $document->document_number ?? '-' }}</td>
                        <td>{{ $document->trip_number ?? '-' }}</td>
                        <td>{{ $document->vehicle_number ?? '-' }}</td>
                        <td>{{ $document->driver_name ?? '-' }}</td>
                        <td>{{ $document->customer_name ?? '-' }}</td>
                        <td>{{ $document->source_location ?? '-' }}</td>
                        <td>{{ $document->destination_location ?? '-' }}</td>
                        <td>{{ optional($document->document_date)->format('d M Y') ?? '-' }}</td>
                        <td>
                            <span class="badge bg-{{ $document->status_color }}">
                                {{ $document->status_label }}
                            </span>
                        </td>
                        <td>{{ $document->creator_name ?? '-' }}</td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="{{ route($routeName . '.show', $document->trip_id) }}" target="_blank"><i class="bi bi-eye me-2"></i>View</a></li>
                                    <li><a class="dropdown-item" href="{{ route('trip.documents', ['tripId' => $document->trip_id, 'step' => $documentType === 'invoice' ? 2 : 1]) }}"><i class="bi bi-pencil me-2"></i>Edit</a></li>
                                    <li><a class="dropdown-item" href="{{ route($routeName . '.print', $document->trip_id) }}" target="_blank"><i class="bi bi-printer me-2"></i>Print</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    @foreach (array_keys($statuses) as $statusKey)
                                        @if ($statusKey !== $document->status)
                                            <li>
                                                <button class="dropdown-item" wire:click="changeStatus({{ $document->id }}, '{{ $statusKey }}')">
                                                    <i class="bi bi-arrow-repeat me-2"></i>Mark {{ $statuses[$statusKey]['label'] }}
                                                </button>
                                            </li>
                                        @endif
                                    @endforeach
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <button class="dropdown-item text-danger" wire:click="deleteDocument({{ $document->id }})">
                                            <i class="bi bi-trash me-2"></i>Delete
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="text-center py-4 text-muted">No records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-3">
        <button type="button" class="btn btn-outline-secondary btn-sm" wire:click="resetFilters">
            <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
        </button>
        <div>
            {{ $documents->links('vendor.pagination.bootstrap-5') }}
        </div>
    </div>

    @script
        <script>
            Livewire.on('open-document-list-print', ({ url }) => {
                window.open(url, '_blank');
            });
        </script>
    @endscript
</div>

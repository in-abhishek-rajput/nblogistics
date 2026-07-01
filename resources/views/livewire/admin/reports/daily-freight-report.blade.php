<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h6 class="mb-0">Daily Freight Report</h6>
        <div class="d-flex gap-2 align-items-center">
            <input type="date" wire:model.live="date" class="form-control" style="width: 200px;">
            <a href="{{ route('reports.daily-freight.print', $date) }}" target="_blank" class="btn btn-primary">
                <i class="fa fa-print me-2"></i>Print
            </a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table text-start align-middle table-bordered table-hover mb-0">
            <thead class="table-primary">
                <tr>
                    <th scope="col">TRIP NO</th>
                    <th scope="col">DATE</th>
                    <th scope="col">PARTY NAME</th>
                    <th scope="col">FROM - TO</th>
                    <th scope="col">TRUCK NO</th>
                    <th scope="col">DRIVER NAME</th>
                    <th scope="col">TOTAL FREIGHT</th>
                    <th scope="col">NET BALANCE</th>
                    <th scope="col">PAID/UNPAID</th>
                </tr>
            </thead>
            <tbody>
                @forelse($trips as $index => $trip)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $trip->start_date ? $trip->start_date->format('d-m-Y') : '-' }}</td>
                        <td>{{ $trip->party_name ?? ($trip->party->name ?? '-') }}</td>
                        <td>{{ $trip->origin }} TO {{ $trip->destination }}</td>
                        <td>{{ $trip->truck_name ?? ($trip->truck->truck_number ?? '-') }}</td>
                        <td>{{ $trip->driver_name ?? ($trip->driver->name ?? '-') }}</td>
                        <td>{{ number_format($trip->freight_amount, 2) }}</td>
                        <td>{{ number_format($trip->pending_freight_amount, 2) }}</td>
                        <td>
                            @if($trip->pending_freight_amount <= 0)
                                <span class="text-success fw-bold">PAID</span>
                            @else
                                <span class="text-danger fw-bold">UNPAID</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center">No trips found for this date.</td>
                    </tr>
                @endforelse
            </tbody>
            @if($trips->count() > 0)
                <tfoot class="table-secondary">
                    <tr>
                        <td colspan="6" class="text-end fw-bold">Totals:</td>
                        <td class="fw-bold">{{ number_format($totalFreight, 2) }}</td>
                        <td class="fw-bold">{{ number_format($totalNetBalance, 2) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>
</div>

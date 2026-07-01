<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Daily Freight Report - {{ \Carbon\Carbon::parse($date)->format('d-m-Y') }}</title>
    <style>
        :root {
            --primary-color: #0d8258;
            --text-color: #333;
            --border-color: #ddd;
            --bg-light: #f9f9f9;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text-color);
            margin: 0;
            padding: 0;
            background-color: #fff;
        }

        .container {
            width: 100%;
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }

        .header-bar {
            background-color: var(--primary-color);
            color: white;
            padding: 10px 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 16px;
            margin-bottom: 20px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            margin-bottom: 20px;
        }

        th, td {
            border: 1px solid var(--border-color);
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: var(--bg-light);
            font-weight: bold;
        }

        .text-end {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .fw-bold {
            font-weight: bold;
        }
        
        .total-row {
            background-color: var(--bg-light);
        }

        @media print {
            body {
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
            @page {
                size: A4 landscape;
                margin: 10mm;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div>
            <img src="{{ asset('img/logo.png') }}" alt="Company Logo" style="max-height: 60px; object-fit: contain;">
        </div>
        <div style="text-align: right;">
            <h2 style="margin: 0; font-size: 20px; color: var(--primary-color);">N B LOGISTICS</h2>
        </div>
    </div>

    <div class="header-bar">
        <div>DAILY FREIGHT REPORT - {{ \Carbon\Carbon::parse($date)->format('d-m-Y') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>TRIP NO</th>
                <th>DATE</th>
                <th>PARTY NAME</th>
                <th>FROM - TO</th>
                <th>TRUCK NO</th>
                <th>DRIVER NAME</th>
                <th>TOTAL FREIGHT</th>
                <th>NET BALANCE</th>
                <th>PAID/UNPAID</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalFreight = 0;
                $totalNetBalance = 0;
            @endphp
            @forelse($trips as $index => $trip)
                @php
                    $totalFreight += $trip->freight_amount;
                    $totalNetBalance += $trip->pending_freight_amount;
                @endphp
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
                            <span style="color: #0d8258;">PAID</span>
                        @else
                            <span style="color: #dc3545;">UNPAID</span>
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
            <tfoot class="total-row">
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

<script>
    window.addEventListener('load', function () {
        window.print();
    });
</script>

</body>
</html>

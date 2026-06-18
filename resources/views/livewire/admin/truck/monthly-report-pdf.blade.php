<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Monthly Profit & Loss Report</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 20px; }
        .company-name { font-size: 24px; font-weight: bold; margin-bottom: 5px; }
        .report-title { font-size: 18px; color: #666; margin-bottom: 10px; }
        .report-period { font-size: 14px; color: #888; }
        .truck-details { margin-bottom: 20px; }
        .truck-details h4 { margin: 0; font-size: 16px; }
        .truck-details p { margin: 5px 0; color: #666; font-size: 14px; }
        .section { margin-bottom: 25px; }
        .section-title { font-size: 16px; font-weight: bold; margin-bottom: 15px; padding-bottom: 8px; border-bottom: 1px solid #ddd; }
        .stats-row { display: flex; gap: 15px; margin-bottom: 20px; }
        .stat-card { flex: 1; border: 1px solid #ddd; padding: 15px; border-radius: 8px; text-align: center; }
        .stat-label { font-size: 11px; text-transform: uppercase; color: #888; margin-bottom: 5px; }
        .stat-value { font-size: 18px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th { background: #f5f5f5; text-align: left; padding: 10px; font-size: 12px; text-transform: uppercase; border: 1px solid #ddd; }
        td { padding: 10px; border: 1px solid #ddd; font-size: 13px; }
        .text-end { text-align: right; }
        .amount-positive { color: #28a745; font-weight: bold; }
        .amount-negative { color: #dc3545; font-weight: bold; }
        .summary-box { background: #f8f9fa; padding: 20px; border-radius: 8px; margin-top: 20px; }
        .summary-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #ddd; }
        .summary-row:last-child { border-bottom: none; }
        .summary-label { font-weight: 600; font-size: 14px; }
        .summary-value { font-weight: bold; font-size: 16px; }
        .profit { color: #28a745; }
        .loss { color: #dc3545; }
        @media print {
            body { padding: 0; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">{{ config('app.name', 'Logistics Company') }}</div>
        <div class="report-title">Monthly Profit & Loss Report</div>
        <div class="report-period">{{ $monthLabel }} {{ $year }}</div>
    </div>

    <div class="truck-details">
        <h4>Truck: {{ $truck->truck_number }}</h4>
        <p>Type: {{ $types[$truck->truck_type] ?? ucfirst($truck->truck_type) }} | 
           Status: {{ ucfirst($truck->status ?? 'Unknown') }} |
           Driver: {{ $truck->driver?->name ?? 'Not Assigned' }}</p>
    </div>

    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-label">Total Trips Started</div>
            <div class="stat-value">{{ $totalTripsStarted ?? 0 }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Last Trip KM Reading</div>
            <div class="stat-value">{{ $lastTripKmReading ? number_format($lastTripKmReading, 0) . ' KM' : '-' }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Total Refuel Quantity</div>
            <div class="stat-value">{{ $totalRefuelQuantity ? number_format($totalRefuelQuantity, 2) . ' L' : '0 L' }}</div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Revenue Details</div>
        @if (!empty($revenueData))
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Route</th>
                        <th class="text-end">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($revenueData as $trip)
                        <tr>
                            <td>{{ $trip['date'] }}</td>
                            <td>{{ $trip['route'] }}</td>
                            <td class="text-end amount-positive">₹ {{ number_format($trip['freight_amount'], 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p style="color: #888;">No completed trips found for selected period.</p>
        @endif
    </div>

    <div class="section">
        <div class="section-title">Expense Details</div>
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
                <h6 style="font-size: 13px; margin-top: 15px; margin-bottom: 10px; color: #555;">{{ $typeLabel }}</h6>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Description</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($expensesData[$typeKey] as $expense)
                            <tr>
                                <td>{{ $expense['date'] }}</td>
                                <td>{{ $expense['type'] }}</td>
                                <td class="text-end amount-negative">₹ {{ number_format($expense['amount'], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        @endforeach
        @if (empty($expensesData))
            <p style="color: #888;">No expenses found for selected period.</p>
        @endif
    </div>

    <div class="summary-box">
        <div class="summary-row">
            <span class="summary-label">Total Revenue</span>
            <span class="summary-value amount-positive">₹ {{ number_format($totalRevenue, 2) }}</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">Total Expenses</span>
            <span class="summary-value amount-negative">₹ {{ number_format($totalExpenses, 2) }}</span>
        </div>
        <div class="summary-row" style="border-top: 2px solid #333; margin-top: 10px; padding-top: 15px;">
            <span class="summary-label">Net {{ $profitLossLabel }}</span>
            <span class="summary-value {{ $profitLoss >= 0 ? 'profit' : 'loss' }}">₹ {{ number_format(abs($profitLoss), 2) }}</span>
        </div>
    </div>
</body>
</html>
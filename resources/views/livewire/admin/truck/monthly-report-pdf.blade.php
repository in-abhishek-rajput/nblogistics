<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Monthly P&L Report – {{ $truck->truck_number }}</title>
    <style>
        /* ── Reset & Base ──────────────────────────────── */
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            color: #1a1a2e;
            background: #fff;
            padding: 0;
        }

        /* ── Color Tokens ──────────────────────────────── */
        /* Navy: #1a1a2e | Steel: #2d4a6e | Amber: #e8a020 */
        /* Light bg: #f4f6fa | Border: #dce3ed | Muted: #6b7a8d */

        /* ── Page Layout ───────────────────────────────── */
        .page-wrap {
            padding: 28px 32px;
            min-height: 100vh;
        }

        /* ── Header ─────────────────────────────────────── */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding-bottom: 16px;
            border-bottom: 3px solid #1a1a2e;
            margin-bottom: 20px;
        }

        .header-left .company-name {
            font-size: 20px;
            font-weight: 700;
            color: #1a1a2e;
            letter-spacing: 0.5px;
        }

        .header-left .report-title {
            font-size: 11px;
            color: #6b7a8d;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-top: 3px;
        }

        .header-right {
            text-align: right;
        }

        .period-badge {
            display: inline-block;
            background: #1a1a2e;
            color: #fff;
            font-size: 13px;
            font-weight: 700;
            padding: 6px 14px;
            border-radius: 4px;
            letter-spacing: 0.5px;
        }

        .generated-on {
            font-size: 10px;
            color: #6b7a8d;
            margin-top: 5px;
        }

        /* ── Truck Info Bar ─────────────────────────────── */
        .truck-bar {
            display: flex;
            gap: 0;
            background: #f4f6fa;
            border: 1px solid #dce3ed;
            border-radius: 6px;
            overflow: hidden;
            margin-bottom: 20px;
        }

        .truck-bar-item {
            flex: 1;
            padding: 10px 14px;
            border-right: 1px solid #dce3ed;
        }

        .truck-bar-item:last-child { border-right: none; }

        .truck-bar-label {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: #6b7a8d;
            margin-bottom: 3px;
        }

        .truck-bar-value {
            font-size: 12px;
            font-weight: 700;
            color: #1a1a2e;
        }

        /* ── KPI Cards ──────────────────────────────────── */
        .kpi-row {
            display: flex;
            gap: 12px;
            margin-bottom: 22px;
        }

        .kpi-card {
            flex: 1;
            border: 1px solid #dce3ed;
            border-radius: 6px;
            padding: 12px 14px;
            position: relative;
            overflow: hidden;
        }

        .kpi-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0;
            width: 4px; height: 100%;
            background: #e8a020;
        }

        .kpi-card.green::before  { background: #1a7a4a; }
        .kpi-card.red::before    { background: #c0392b; }
        .kpi-card.amber::before  { background: #e8a020; }
        .kpi-card.navy::before   { background: #2d4a6e; }

        .kpi-label {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #6b7a8d;
            margin-bottom: 5px;
        }

        .kpi-value {
            font-size: 17px;
            font-weight: 700;
            color: #1a1a2e;
            line-height: 1;
        }

        .kpi-value.green { color: #1a7a4a; }
        .kpi-value.red   { color: #c0392b; }

        /* ── Section ────────────────────────────────────── */
        .section { margin-bottom: 22px; }

        .section-header {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 10px;
        }

        .section-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #2d4a6e;
        }

        .section-line {
            flex: 1;
            height: 1px;
            background: #dce3ed;
        }

        .section-total {
            font-size: 11px;
            font-weight: 700;
            color: #1a1a2e;
        }

        /* ── Tables ─────────────────────────────────────── */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        thead tr {
            background: #1a1a2e;
        }

        thead th {
            color: #fff;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            font-weight: 600;
            padding: 7px 10px;
            text-align: left;
            border: none;
        }

        thead th.text-end { text-align: right; }

        tbody tr:nth-child(even) { background: #f9fafb; }
        tbody tr:nth-child(odd)  { background: #fff; }

        tbody td {
            padding: 7px 10px;
            font-size: 11px;
            color: #1a1a2e;
            border-bottom: 1px solid #eef1f5;
        }

        .text-end { text-align: right; }
        .text-muted { color: #6b7a8d; }

        .amount-positive { color: #1a7a4a; font-weight: 600; }
        .amount-negative { color: #c0392b; font-weight: 600; }

        /* ── Expense Sub-heading ────────────────────────── */
        .expense-group-title {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #2d4a6e;
            background: #edf1f7;
            padding: 5px 10px;
            border-left: 3px solid #e8a020;
            margin-top: 10px;
            margin-bottom: 0;
        }

        /* ── Summary Box ────────────────────────────────── */
        .summary-wrap {
            margin-top: 24px;
            border: 2px solid #1a1a2e;
            border-radius: 6px;
            overflow: hidden;
        }

        .summary-title-bar {
            background: #1a1a2e;
            color: #fff;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            padding: 8px 16px;
        }

        .summary-body {
            padding: 0;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 11px 16px;
            border-bottom: 1px solid #dce3ed;
        }

        .summary-row:last-child {
            border-bottom: none;
            background: #f4f6fa;
            padding: 14px 16px;
        }

        .summary-label {
            font-size: 12px;
            font-weight: 600;
            color: #1a1a2e;
        }

        .summary-value {
            font-size: 14px;
            font-weight: 700;
        }

        .net-label {
            font-size: 13px;
            font-weight: 700;
            color: #1a1a2e;
        }

        .net-value {
            font-size: 18px;
            font-weight: 700;
        }

        .color-profit { color: #1a7a4a; }
        .color-loss   { color: #c0392b; }
        .color-rev    { color: #1a7a4a; }
        .color-exp    { color: #c0392b; }

        /* ── Footer ─────────────────────────────────────── */
        .footer {
            margin-top: 28px;
            padding-top: 10px;
            border-top: 1px solid #dce3ed;
            display: flex;
            justify-content: space-between;
            font-size: 9px;
            color: #6b7a8d;
        }

        /* ── Empty States ───────────────────────────────── */
        .empty-state {
            padding: 12px 10px;
            font-size: 11px;
            color: #6b7a8d;
            font-style: italic;
            background: #f9fafb;
            border: 1px dashed #dce3ed;
            border-radius: 4px;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="page-wrap">

    {{-- ── Header ──────────────────────────────────────── --}}
    <div class="header">
        <div class="header-left">
            <div class="company-name">{{ config('app.name', 'Logistics Company') }}</div>
            <div class="report-title">Monthly Profit &amp; Loss Report</div>
        </div>
        <div class="header-right">
            <div class="period-badge">{{ $monthLabel }} {{ $year }}</div>
            <div class="generated-on">Generated: {{ now()->format('d M Y, h:i A') }}</div>
        </div>
    </div>

    {{-- ── Truck Info Bar ───────────────────────────────── --}}
    <div class="truck-bar">
        <div class="truck-bar-item">
            <div class="truck-bar-label">Truck Number</div>
            <div class="truck-bar-value">{{ $truck->truck_number }}</div>
        </div>
        <div class="truck-bar-item">
            <div class="truck-bar-label">Type</div>
            <div class="truck-bar-value">{{ $types[$truck->truck_type] ?? ucfirst($truck->truck_type) }}</div>
        </div>
        <div class="truck-bar-item">
            <div class="truck-bar-label">Status</div>
            <div class="truck-bar-value">{{ ucfirst($truck->status ?? 'Unknown') }}</div>
        </div>
        <div class="truck-bar-item">
            <div class="truck-bar-label">Driver</div>
            <div class="truck-bar-value">{{ $truck->driver?->name ?? 'Not Assigned' }}</div>
        </div>
    </div>

    {{-- ── KPI Cards ────────────────────────────────────── --}}
    <div class="kpi-row">
        <div class="kpi-card navy">
            <div class="kpi-label">Total Trips</div>
            <div class="kpi-value">{{ $totalTripsStarted ?? 0 }}</div>
        </div>
        <div class="kpi-card amber">
            <div class="kpi-label">Last KM Reading</div>
            <div class="kpi-value">{{ $lastTripKmReading ? number_format($lastTripKmReading, 0) . ' km' : '—' }}</div>
        </div>
        <div class="kpi-card amber">
            <div class="kpi-label">Total Refuel</div>
            <div class="kpi-value">{{ $totalRefuelQuantity ? number_format($totalRefuelQuantity, 2) . ' L' : '0 L' }}</div>
        </div>
        <div class="kpi-card green">
            <div class="kpi-label">Total Revenue</div>
            <div class="kpi-value green">&#8377; {{ number_format($totalRevenue, 2) }}</div>
        </div>
        <div class="kpi-card red">
            <div class="kpi-label">Total Expenses</div>
            <div class="kpi-value red">&#8377; {{ number_format($totalExpenses, 2) }}</div>
        </div>
    </div>

    {{-- ── Revenue Section ──────────────────────────────── --}}
    <div class="section">
        <div class="section-header">
            <span class="section-label">Revenue Details</span>
            <span class="section-line"></span>
            <span class="section-total amount-positive">&#8377; {{ number_format($totalRevenue, 2) }}</span>
        </div>

        @if (!empty($revenueData))
            <table>
                <thead>
                    <tr>
                        <th style="width:90px;">Date</th>
                        <th>Route</th>
                        <th class="text-end" style="width:110px;">Freight</th>
                        <th class="text-end" style="width:90px;">Adjustments</th>
                        <th class="text-end" style="width:110px;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($revenueData as $trip)
                        <tr>
                            <td class="text-muted">{{ $trip['date'] }}</td>
                            <td>{{ $trip['route'] }}</td>
                            <td class="text-end">&#8377; {{ number_format($trip['freight_amount'], 2) }}</td>
                            <td class="text-end {{ $trip['charges'] >= 0 ? 'amount-positive' : 'amount-negative' }}">
                                {{ $trip['charges'] >= 0 ? '+' : '' }}&#8377; {{ number_format($trip['charges'], 2) }}
                            </td>
                            <td class="text-end amount-positive">&#8377; {{ number_format($trip['total'], 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="empty-state">No trips found for this period.</div>
        @endif
    </div>

    {{-- ── Expense Section ──────────────────────────────── --}}
    <div class="section">
        <div class="section-header">
            <span class="section-label">Expense Details</span>
            <span class="section-line"></span>
            <span class="section-total amount-negative">&#8377; {{ number_format($totalExpenses, 2) }}</span>
        </div>

        @php
            $expenseGroups = [
                'fuel'         => 'Fuel Expenses',
                'emi'          => 'EMI Payments',
                'driver'       => 'Driver & Other Expenses',
                'maintenance'  => 'Maintenance Expenses',
                'document'     => 'Document Expenses',
                'trip_expense' => 'Trip Expenses',
                'advance'      => 'Trip Advances',
            ];
            $anyExpense = false;
            foreach ($expenseGroups as $key => $_) {
                if (!empty($expensesData[$key])) { $anyExpense = true; break; }
            }
        @endphp

        @if ($anyExpense)
            @foreach ($expenseGroups as $typeKey => $typeLabel)
                @if (!empty($expensesData[$typeKey]))
                    @php
                        $groupTotal = array_sum(array_column($expensesData[$typeKey], 'amount'));
                    @endphp
                    <div class="expense-group-title">
                        {{ $typeLabel }}
                        &nbsp;&nbsp;—&nbsp;&nbsp;&#8377; {{ number_format($groupTotal, 2) }}
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th style="width:90px;">Date</th>
                                <th>Description</th>
                                @if (!empty(array_column($expensesData[$typeKey], 'shop_name')))
                                    <th>Vendor / Party</th>
                                @endif
                                @if (!empty(array_column($expensesData[$typeKey], 'quantity')))
                                    <th class="text-end" style="width:80px;">Qty</th>
                                @endif
                                <th class="text-end" style="width:110px;">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($expensesData[$typeKey] as $expense)
                                <tr>
                                    <td class="text-muted">{{ $expense['date'] }}</td>
                                    <td>{{ $expense['type'] }}</td>
                                    @if (!empty(array_column($expensesData[$typeKey], 'shop_name')))
                                        <td class="text-muted">{{ $expense['shop_name'] ?? '—' }}</td>
                                    @endif
                                    @if (!empty(array_column($expensesData[$typeKey], 'quantity')))
                                        <td class="text-end text-muted">{{ $expense['quantity'] ?? '—' }}</td>
                                    @endif
                                    <td class="text-end amount-negative">&#8377; {{ number_format($expense['amount'], 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            @endforeach
        @else
            <div class="empty-state">No expenses found for this period.</div>
        @endif
    </div>

    {{-- ── Summary ───────────────────────────────────────── --}}
    <div class="summary-wrap">
        <div class="summary-title-bar">Financial Summary — {{ $monthLabel }} {{ $year }}</div>
        <div class="summary-body">
            <div class="summary-row">
                <span class="summary-label">Total Revenue</span>
                <span class="summary-value color-rev">&#8377; {{ number_format($totalRevenue, 2) }}</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Total Expenses</span>
                <span class="summary-value color-exp">&#8377; {{ number_format($totalExpenses, 2) }}</span>
            </div>
            <div class="summary-row">
                <span class="net-label">Net {{ $profitLossLabel }}</span>
                <span class="net-value {{ $profitLoss >= 0 ? 'color-profit' : 'color-loss' }}">
                    &#8377; {{ number_format(abs($profitLoss), 2) }}
                </span>
            </div>
        </div>
    </div>

    {{-- ── Footer ───────────────────────────────────────── --}}
    <div class="footer">
        <span>{{ config('app.name') }} &bull; Confidential</span>
        <span>Truck: {{ $truck->truck_number }} &bull; {{ $monthLabel }} {{ $year }}</span>
    </div>

</div>
</body>
</html>
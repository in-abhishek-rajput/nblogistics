<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Digital Invoice - {{ $trip->lr_number ?? 'N/A' }}</title>
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
            background-color: #f5f5f5;
        }

        .a4-container {
            width: 210mm;
            min-height: 297mm;
            padding: 10mm;
            margin: 10mm auto;
            background: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            box-sizing: border-box;
        }

        .header-bar {
            background-color: var(--primary-color);
            color: white;
            padding: 10px 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 14px;
            margin-bottom: 20px;
        }

        .header-bar .title {
            font-weight: 500;
        }

        .header-bar .date {
            font-size: 12px;
        }

        .top-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            gap: 20px;
        }

        .bill-to {
            display: flex;
            align-items: baseline;
            gap: 10px;
            margin-bottom: 20px;
        }

        .bill-to h4 {
            margin: 0;
            font-size: 16px;
        }

        .bill-to .party-name {
            font-weight: 700;
            font-size: 18px;
            text-transform: uppercase;
        }

        .trip-details {
            width: calc(50% - 10px);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            overflow: hidden;
        }

        .trip-details-header {
            background-color: var(--primary-color);
            color: white;
            padding: 10px 15px;
            font-weight: 600;
            font-size: 14px;
        }

        .trip-details-body {
            padding: 0;
            font-size: 13px;
        }

        .route-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid var(--border-color);
        }

        .route-point {
            text-align: center;
            width: 40%;
        }

        .route-point .city {
            font-weight: 700;
            font-size: 15px;
            margin-bottom: 3px;
        }

        .route-point .date {
            color: #777;
            font-size: 12px;
        }

        .route-arrow {
            width: 20%;
            text-align: center;
            color: #999;
            font-size: 20px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 15px;
            border-bottom: 1px solid var(--border-color);
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-item {
            width: 50%;
        }

        .info-item .label {
            color: #666;
            margin-right: 5px;
        }

        .info-item .value {
            font-weight: 600;
        }

        .payment-section {
            width: calc(50% - 10px);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            overflow: hidden;
        }

        .payment-header {
            background-color: var(--primary-color);
            color: white;
            padding: 10px 15px;
            font-weight: 600;
            font-size: 14px;
        }

        .payment-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .payment-table td {
            padding: 12px 15px;
            border-bottom: 1px solid var(--bg-light);
        }

        .payment-table tr:last-child td {
            border-bottom: none;
        }

        .payment-table .amount {
            text-align: right;
            font-weight: 600;
        }

        .total-row {
            background-color: var(--bg-light);
            font-size: 15px;
        }

        .total-row td {
            padding: 15px;
        }

        .total-row .amount {
            color: var(--primary-color);
            font-size: 16px;
        }

        .payment-details-info {
            display: flex;
            align-items: center;
        }

        .payment-details-info span {
            margin-right: 20px;
        }

        .rupee {
            font-family: Arial, sans-serif;
        }

        @media print {
            body {
                background-color: white;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }

            .a4-container {
                box-shadow: none;
                margin: 0;
                padding: 0;
                width: 100%;
                min-height: auto;
            }

            @page {
                size: A4 portrait;
                margin: 5mm;
            }
        }
    </style>
</head>

<body>

    <div class="a4-container">

        <div class="header-bar">
            <div class="title">Trip Report</div>
            <div class="date">Generated on {{ date('jS M Y \a\t h:i A') }}</div>
        </div>

        <div class="bill-to">
            <h4>Bill To:</h4>
            <div class="party-name">{{ $trip->party->name ?? ($trip->party_name ?? 'N/A') }}</div>
        </div>

        <div class="top-section">
            <div class="trip-details">
                <div class="trip-details-header">
                    Trip Details
                </div>

                <div class="trip-details-body">
                    <div class="info-row">
                        <div class="info-item" style="width: 100%;">
                            <span class="label">Trip ID / LR NO:</span>
                            <span class="value">{{ $trip->lr_number ?? 'N/A' }}</span>
                        </div>
                    </div>
                    <div class="route-info">
                        <div class="route-point" style="text-align: left;">
                            <div class="city">{{ $trip->origin ?? 'N/A' }}</div>
                            <div class="date">{{ $trip->start_date ? $trip->start_date->format('d M Y') : 'N/A' }}</div>
                        </div>
                        <div class="route-arrow">
                            &#10230;
                        </div>
                        <div class="route-point" style="text-align: right;">
                            <div class="city">{{ $trip->destination ?? 'N/A' }}</div>
                            <div class="date">
                                {{ $trip->completed_date ? $trip->completed_date->format('d M Y') : 'N/A' }}</div>
                        </div>
                    </div>

                    <div class="info-row">
                        <div class="info-item">
                            <span class="label">Truck:</span>
                            <span class="value">{{ $trip->truck->truck_number ?? ($trip->truck_name ?? 'N/A') }}</span>
                        </div>
                        <div class="info-item" style="text-align: right;">
                            <span class="label">Driver:</span>
                            <span class="value">{{ $trip->driver->name ?? ($trip->driver_name ?? 'N/A') }}</span>
                        </div>
                    </div>

                    <div class="info-row">
                        <div class="info-item">
                            <span class="label">Trip Status:</span>
                            <span class="value" style="text-transform: capitalize;">{{ ucfirst(str_replace("_", " ", $trip->status)) ?? 'N/A' }}</span>
                        </div>
                        <div class="info-item" style="text-align: right;">
                            <span class="label">Material:</span>
                            <span class="value">{{ $trip->material_name ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="payment-section">
                <div class="payment-header">
                    Payment Details
                </div>
                <table class="payment-table">
                    <tr>
                        <td>Freight Amount</td>
                        <td class="amount"><span
                                class="rupee">₹</span>{{ number_format($trip->freight_amount ?? 0, 0) }}</td>
                    </tr>
                    <tr>
                        <td>Advances (-)</td>
                        <td class="amount"><span class="rupee">₹</span>{{ number_format($totalAdvances, 0) }}</td>
                    </tr>
                    <tr>
                        <td>Charges (+)</td>
                        <td class="amount"><span
                                class="rupee">₹</span>{{ number_format($trip->charges->where('charge_direction', 'add_to_bill')->sum('amount'), 0) }}
                        </td>
                    </tr>
                    <tr>
                        <td>Deductions (-)</td>
                        <td class="amount"><span
                                class="rupee">₹</span>{{ number_format($trip->charges->where('charge_direction', 'reduce_from_bill')->sum('amount'), 0) }}
                        </td>
                    </tr>

                    @if($trip->payments->count() > 0)
                        <tr>
                            <td>
                                <div class="payment-details-info">
                                    Payments (-)
                                    @php
                                        $lastPayment = $trip->payments->last();
                                    @endphp
                                    @if($lastPayment)
                                        <span style="margin-left: 20px;">Via
                                            <b>{{ strtoupper($lastPayment->payment_method) }}</b></span>
                                        <span>On
                                            <b>{{ $lastPayment->payment_date ? $lastPayment->payment_date->format('d M Y') : 'N/A' }}</b></span>
                                        <span><b>Received By
                                                {{ $lastPayment->received_by_driver ? 'Driver' : 'Party' }}</b></span>
                                    @endif
                                </div>
                            </td>
                            <td class="amount">-<span class="rupee">₹</span>{{ number_format($totalPayments, 0) }}</td>
                        </tr>
                    @else
                        <tr>
                            <td>Payments (-)</td>
                            <td class="amount"><span class="rupee">₹</span>0</td>
                        </tr>
                    @endif

                    <tr class="total-row">
                        <td>Total Pending Balance</td>
                        <td class="amount"><span class="rupee">₹</span>{{ number_format($pendingBalance, 0) }}</td>
                    </tr>
                </table>
            </div>
        </div>
        @if($trip->pod_receipt)
            <div class="header-bar" style="margin-bottom: 20px;">
                <div class="title">Proof of Delivery (POD)</div>
                <div class="date">Trip ID / LR NO: {{ $trip->lr_number ?? 'N/A' }}</div>
            </div>
            <div style="text-align: center;">
                <img src="{{ asset('storage/' . $trip->pod_receipt) }}" alt="POD Receipt"
                    style="max-height: 400px; object-fit: contain; border: 1px solid black;">
            </div>
        @endif
    </div>



    @if(!empty($autoPrint))
        <script>
            window.addEventListener('load', function () {
                window.print();
            });
        </script>
    @endif

</body>

</html>
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
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }

        .a4-container {
            width: 100%;
            max-width: 210mm;
            min-height: 297mm;
            padding: 10mm;
            margin: 10mm auto;
            background: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            box-sizing: border-box;
        }

        .header-bar {
            background-color: #0d8258;
            color: white;
            padding: 10px 15px;
            font-size: 14px;
            margin-bottom: 20px;
            width: 100%;
            box-sizing: border-box;
        }
        .header-bar table {
            width: 100%;
            color: white;
        }
        .header-bar td.title {
            font-weight: 500;
        }
        .header-bar td.date {
            font-size: 12px;
            text-align: right;
        }

        .bill-to h4 {
            margin: 0;
            font-size: 16px;
            display: inline-block;
            margin-right: 10px;
        }

        .bill-to .party-name {
            font-weight: 700;
            font-size: 18px;
            text-transform: uppercase;
            display: inline-block;
        }

        .trip-details {
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            width: 100%;
        }

        .trip-details-header {
            background-color: #0d8258;
            color: white;
            padding: 10px 15px;
            font-weight: 600;
            font-size: 14px;
        }

        .trip-details-body {
            padding: 0;
            font-size: 13px;
        }
        
        .trip-details-table {
            width: 100%;
            border-collapse: collapse;
        }
        .trip-details-table td {
            padding: 10px 15px;
            border-bottom: 1px solid #ddd;
        }
        .trip-details-table tr:last-child td {
            border-bottom: none;
        }

        .route-info {
            padding: 15px;
            border-bottom: 1px solid #ddd;
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
            text-align: center;
            color: #999;
            font-size: 20px;
        }

        .payment-section {
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            width: 100%;
        }

        .payment-header {
            background-color: #0d8258;
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
            border-bottom: 1px solid #f9f9f9;
        }

        .payment-table tr:last-child td {
            border-bottom: none;
        }

        .payment-table .amount {
            text-align: right;
            font-weight: 600;
        }

        .total-row {
            background-color: #f9f9f9;
            font-size: 15px;
        }

        .total-row td {
            padding: 15px;
        }

        .total-row .amount {
            color: #0d8258;
            font-size: 16px;
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
                max-width: none;
                min-height: auto;
            }

            @page {
                size: A4 portrait;
                margin: 5mm;
            }

            .no-print {
                display: none !important;
            }
        }
        
        .action-bar {
            text-align: center;
            padding: 15px;
            background: #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .action-btn {
            display: inline-block;
            padding: 8px 15px;
            margin: 0 5px;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
            border: none;
            cursor: pointer;
            font-family: inherit;
        }
        .btn-whatsapp {
            background-color: #25D366;
        }
        .btn-print {
            background-color: #0d8258;
        }
    </style>
</head>

<body>

    @if(!isset($isPdf) || !$isPdf)
    <div class="action-bar no-print">
        <a href="{{ route('trips.share-whatsapp-invoice', ['id' => $trip->id]) }}" class="action-btn btn-whatsapp">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-whatsapp" viewBox="0 0 16 16" style="vertical-align: text-bottom; margin-right: 4px;">
              <path d="M13.601 2.326A7.854 7.854 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.933 7.933 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.898 7.898 0 0 0 13.6 2.326zM7.994 14.521a6.573 6.573 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.557 6.557 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592zm3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.729.729 0 0 0-.529.247c-.182.198-.691.677-.691 1.654 0 .977.71 1.916.81 2.049.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232z"/>
            </svg> Share on WA
        </a>
        <button onclick="window.print()" class="action-btn btn-print no-print">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-printer" viewBox="0 0 16 16" style="vertical-align: text-bottom; margin-right: 4px;">
              <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/>
              <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4V3zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2H5zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1z"/>
            </svg> Print
        </button>
    </div>
    @endif

    <div class="a4-container">
        
        <table style="width: 100%; margin-bottom: 20px;">
            <tr>
                <td style="vertical-align: middle;">
                    @php
                        $companyLogo = \App\Models\User::first()->logo ?? null;
                    @endphp
                    <img src="{{ $companyLogo ? asset('storage/' . $companyLogo) : asset('img/logo.png') }}" alt="Company Logo" style="max-height: 80px; object-fit: contain;">
                </td>
                <td style="text-align: right; vertical-align: middle;">
                    <h2 style="margin: 0; font-size: 24px; color: #0d8258;">N B LOGISTICS</h2>
                    <p style="margin: 5px 0 0; font-size: 13px; color: #555;">Patel Chowk, G.I.D.C. Phase - 2, Dared, Jamnagar<br>Mo. 9924328424 | 8401663180</p>
                </td>
            </tr>
        </table>

        <div class="header-bar">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td class="title" style="vertical-align: middle;">Trip Report</td>
                    <td class="date" style="text-align: right; vertical-align: middle;">Generated on {{ date('jS M Y \a\t h:i A') }}</td>
                </tr>
            </table>
        </div>

        <div class="bill-to" style="margin-bottom: 20px;">
            <h4>Bill To:</h4>
            <div class="party-name">{{ $trip->party->name ?? ($trip->party_name ?? 'N/A') }}</div>
        </div>

        <table style="width: 100%; margin-bottom: 20px; table-layout: fixed;">
            <tr>
                <td style="width: 49%; vertical-align: top; padding-right: 1%;">
                    
                    <div class="trip-details">
                        <div class="trip-details-header">
                            Trip Details
                        </div>

                        <div class="trip-details-body">
                            <table class="trip-details-table">
                                <tr>
                                    <td colspan="2">
                                        <span style="color: #666; margin-right: 5px;">Trip ID / LR NO:</span>
                                        <span style="font-weight: 600;">{{ $trip->lr_number ?? 'N/A' }}</span>
                                    </td>
                                </tr>
                            </table>
                            
                            <table style="width: 100%; border-bottom: 1px solid #ddd; padding: 15px;">
                                <tr>
                                    <td style="width: 40%; text-align: left;">
                                        <div style="font-weight: 700; font-size: 15px; margin-bottom: 3px;">{{ $trip->origin ?? 'N/A' }}</div>
                                        <div style="color: #777; font-size: 12px;">{{ $trip->start_date ? $trip->start_date->format('d M Y') : 'N/A' }}</div>
                                    </td>
                                    <td style="width: 20%; text-align: center; color: #999; font-size: 20px;">
                                        &#10230;
                                    </td>
                                    <td style="width: 40%; text-align: right;">
                                        <div style="font-weight: 700; font-size: 15px; margin-bottom: 3px;">{{ $trip->destination ?? 'N/A' }}</div>
                                        <div style="color: #777; font-size: 12px;">{{ $trip->completed_date ? $trip->completed_date->format('d M Y') : 'N/A' }}</div>
                                    </td>
                                </tr>
                            </table>

                            <table class="trip-details-table">
                                <tr>
                                    <td style="width: 50%;">
                                        <span style="color: #666; margin-right: 5px;">Truck:</span>
                                        <span style="font-weight: 600;">{{ $trip->truck->truck_number ?? ($trip->truck_name ?? 'N/A') }}</span>
                                    </td>
                                    <td style="width: 50%; text-align: right;">
                                        <span style="color: #666; margin-right: 5px;">Driver:</span>
                                        <span style="font-weight: 600;">{{ $trip->driver->name ?? ($trip->driver_name ?? 'N/A') }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 50%;">
                                        <span style="color: #666; margin-right: 5px;">Trip Status:</span>
                                        <span style="font-weight: 600; text-transform: capitalize;">{{ ucfirst(str_replace("_", " ", $trip->status)) ?? 'N/A' }}</span>
                                    </td>
                                    <td style="width: 50%; text-align: right;">
                                        <span style="color: #666; margin-right: 5px;">Material:</span>
                                        <span style="font-weight: 600;">{{ $trip->material_name ?? 'N/A' }}</span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                </td>
                <td style="width: 49%; vertical-align: top; padding-left: 1%;">
                    
                    <div class="payment-section">
                        <div class="payment-header">
                            Payment Details
                        </div>
                        <table class="payment-table">
                            <tr>
                                <td>Freight Amount</td>
                                <td class="amount"><span class="rupee">₹</span>{{ number_format($trip->freight_amount ?? 0, 0) }}</td>
                            </tr>
                            <tr>
                                <td>Advances (-)</td>
                                <td class="amount"><span class="rupee">₹</span>{{ number_format($totalAdvances, 0) }}</td>
                            </tr>
                            <tr>
                                <td>Charges (+)</td>
                                <td class="amount"><span class="rupee">₹</span>{{ number_format($trip->charges->where('charge_direction', 'add_to_bill')->sum('amount'), 0) }}</td>
                            </tr>
                            <tr>
                                <td>Deductions (-)</td>
                                <td class="amount"><span class="rupee">₹</span>{{ number_format($trip->charges->where('charge_direction', 'reduce_from_bill')->sum('amount'), 0) }}</td>
                            </tr>

                            @if($trip->payments->count() > 0)
                                <tr>
                                    <td>
                                        Payments (-)
                                        @php
                                            $lastPayment = $trip->payments->last();
                                        @endphp
                                        @if($lastPayment)
                                            <div style="font-size: 11px; color: #666; margin-top: 3px;">
                                                Via <b>{{ strtoupper($lastPayment->payment_method) }}</b> 
                                                on <b>{{ $lastPayment->payment_date ? $lastPayment->payment_date->format('d M Y') : 'N/A' }}</b>
                                                <br>
                                                Received By <b>{{ $lastPayment->received_by_driver ? 'Driver' : 'Party' }}</b>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="amount" style="vertical-align: top;">-<span class="rupee">₹</span>{{ number_format($totalPayments, 0) }}</td>
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

                </td>
            </tr>
        </table>
        
        @if($trip->pod_receipt)
            <div class="header-bar" style="margin-bottom: 20px;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td class="title" style="vertical-align: middle;">Proof of Delivery (POD)</td>
                        <td class="date" style="text-align: right; vertical-align: middle;">Trip ID / LR NO: {{ $trip->lr_number ?? 'N/A' }}</td>
                    </tr>
                </table>
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
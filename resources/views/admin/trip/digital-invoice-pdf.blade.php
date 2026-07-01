<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Digital Invoice - {{ $trip->lr_number ?? 'N/A' }}</title>
    <style>
        /*
         * DOMPDF-SAFE CSS RULES:
         *  1. No Flexbox / CSS Grid / box-shadow / border-radius on overflow:hidden
         *  2. Font: Arial (always available in dompdf)
         *  3. Sizes in pt (reliable across DPI settings)
         *  4. EXPLICITLY set color on EVERY element — dompdf does NOT inherit
         *     color from <body> into table cells reliably
         *  5. Use @page for margins and page size
         *  6. Images use absolute server paths (public_path), NOT asset() URLs
         */

        @page {
            size: A4 portrait;
            margin: 14mm 14mm 14mm 14mm;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10pt;
            color: #333333;
            margin: 0;
            padding: 0;
            background: #ffffff;
        }

        /* ==============================
           COMPANY HEADER
        ============================== */
        table.company-header {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12pt;
        }
        table.company-header td {
            color: #333333;
            vertical-align: middle;
            font-family: Arial, Helvetica, sans-serif;
        }
        .company-name {
            font-size: 16pt;
            font-weight: bold;
            color: #0d8258;
            font-family: Arial, Helvetica, sans-serif;
        }
        .company-address {
            font-size: 9pt;
            color: #555555;
            line-height: 1.5;
            font-family: Arial, Helvetica, sans-serif;
        }

        /* ==============================
           GREEN HEADER BAR
        ============================== */
        table.header-bar {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12pt;
        }
        table.header-bar td {
            background-color: #0d8258;
            color: #ffffff;
            padding: 7pt 10pt;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10pt;
        }
        table.header-bar td.bar-title {
            font-size: 11pt;
            font-weight: bold;
            color: #ffffff;
        }
        table.header-bar td.bar-date {
            font-size: 9pt;
            text-align: right;
            color: #ffffff;
        }

        /* ==============================
           BILL TO
        ============================== */
        .bill-to {
            margin-bottom: 12pt;
        }
        .bill-label {
            font-size: 9pt;
            color: #666666;
            font-family: Arial, Helvetica, sans-serif;
            margin-bottom: 2pt;
        }
        .party-name {
            font-size: 14pt;
            font-weight: bold;
            color: #222222;
            text-transform: uppercase;
            font-family: Arial, Helvetica, sans-serif;
        }

        /* ==============================
           TWO-COLUMN WRAPPER
        ============================== */
        table.two-col {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12pt;
        }
        table.two-col td.col-left {
            width: 50%;
            vertical-align: top;
            padding-right: 4pt;
        }
        table.two-col td.col-right {
            width: 50%;
            vertical-align: top;
            padding-left: 4pt;
        }

        /* ==============================
           SECTION BOX (bordered card)
        ============================== */
        table.section-box {
            width: 100%;
            border-collapse: collapse;
            border: 1pt solid #cccccc;
        }
        table.section-box td.section-header {
            background-color: #0d8258;
            color: #ffffff;
            font-size: 10pt;
            font-weight: bold;
            padding: 7pt 10pt;
            font-family: Arial, Helvetica, sans-serif;
        }

        /* ---- Normal detail rows ---- */
        table.section-box td.detail-cell {
            padding: 7pt 10pt;
            border-bottom: 1pt solid #eeeeee;
            color: #333333;
            font-size: 10pt;
            font-family: Arial, Helvetica, sans-serif;
            vertical-align: middle;
        }
        table.section-box td.detail-cell-last {
            padding: 7pt 10pt;
            color: #333333;
            font-size: 10pt;
            font-family: Arial, Helvetica, sans-serif;
            vertical-align: middle;
        }
        .detail-label {
            color: #666666;
            font-size: 9pt;
            font-family: Arial, Helvetica, sans-serif;
        }
        .detail-value {
            font-weight: bold;
            color: #222222;
            font-size: 10pt;
            font-family: Arial, Helvetica, sans-serif;
        }

        /* ---- Route row ---- */
        .route-city {
            font-size: 12pt;
            font-weight: bold;
            color: #222222;
            font-family: Arial, Helvetica, sans-serif;
        }
        .route-date {
            font-size: 9pt;
            color: #777777;
            font-family: Arial, Helvetica, sans-serif;
        }
        .route-arrow {
            font-size: 14pt;
            color: #aaaaaa;
            text-align: center;
            font-family: Arial, Helvetica, sans-serif;
        }

        /* ==============================
           PAYMENT TABLE
        ============================== */
        table.payment-table {
            width: 100%;
            border-collapse: collapse;
        }
        table.payment-table td {
            padding: 7pt 10pt;
            border-bottom: 1pt solid #eeeeee;
            color: #333333;
            font-size: 10pt;
            font-family: Arial, Helvetica, sans-serif;
            vertical-align: top;
        }
        table.payment-table tr.last-pay td {
            border-bottom: none;
        }
        .amount-cell {
            text-align: right;
            font-weight: bold;
            color: #222222;
            white-space: nowrap;
            font-family: Arial, Helvetica, sans-serif;
        }
        table.payment-table tr.total-row td {
            background-color: #f0f8f4;
            font-weight: bold;
            font-size: 11pt;
            color: #222222;
            padding: 9pt 10pt;
            border-bottom: none;
        }
        table.payment-table tr.total-row td.total-amount {
            color: #0d8258;
            font-size: 12pt;
            font-weight: bold;
            text-align: right;
        }
        .sub-info {
            font-size: 8pt;
            color: #777777;
            margin-top: 3pt;
            font-family: Arial, Helvetica, sans-serif;
        }

        /* ==============================
           POD IMAGE
        ============================== */
        .pod-image {
            text-align: center;
            margin-top: 8pt;
        }
        .pod-image img {
            max-width: 100%;
            max-height: 320pt;
            border: 1pt solid #333333;
        }

        /* ==============================
           FOOTER
        ============================== */
        .footer {
            margin-top: 16pt;
            font-size: 8pt;
            color: #999999;
            text-align: center;
            border-top: 1pt solid #eeeeee;
            padding-top: 6pt;
            font-family: Arial, Helvetica, sans-serif;
        }
    </style>
</head>
<body>

    {{-- ==============================
         COMPANY HEADER
    ============================== --}}
    @php
        $companyUser    = \App\Models\User::first();
        $companyLogoRel = $companyUser->logo ?? null;
        // Use absolute server path — dompdf cannot use HTTP URLs reliably
        $logoPath   = $companyLogoRel
                        ? public_path('storage/' . $companyLogoRel)
                        : public_path('img/logo.png');
        $logoExists = file_exists($logoPath);
    @endphp

    <table class="company-header">
        <tr>
            <td style="width:30%; vertical-align:middle;">
                @if($logoExists)
                    <img src="{{ $logoPath }}" alt="Company Logo"
                         style="max-height:60pt; max-width:140pt;">
                @else
                    <span style="font-size:12pt; font-weight:bold; color:#0d8258;
                                 font-family:Arial,Helvetica,sans-serif;">
                        Company Logo
                    </span>
                @endif
            </td>
            <td style="width:70%; text-align:right; vertical-align:middle;">
                <div class="company-name">N B LOGISTICS</div>
                <div class="company-address">
                    Patel Chowk, G.I.D.C. Phase - 2, Dared, Jamnagar<br>
                    Mo. 9924328424 | 8401663180
                </div>
            </td>
        </tr>
    </table>

    {{-- ==============================
         TRIP REPORT BAR
    ============================== --}}
    <table class="header-bar">
        <tr>
            <td class="bar-title">Trip Report</td>
            <td class="bar-date">Generated on {{ date('jS M Y \a\t h:i A') }}</td>
        </tr>
    </table>

    {{-- ==============================
         BILL TO
    ============================== --}}
    <div class="bill-to">
        <div class="bill-label">Bill To:</div>
        <div class="party-name">{{ $trip->party->name ?? ($trip->party_name ?? 'N/A') }}</div>
    </div>

    {{-- ==============================
         TWO COLUMNS: Trip Details | Payment Details
    ============================== --}}
    <table class="two-col">
        <tr>

            {{-- ---- LEFT: Trip Details ---- --}}
            <td class="col-left">
                <table class="section-box">

                    {{-- Section Header --}}
                    <tr>
                        <td class="section-header" colspan="3">Trip Details</td>
                    </tr>

                    {{-- LR Number --}}
                    <tr>
                        <td class="detail-cell" colspan="3">
                            <span class="detail-label">Trip ID / LR NO: </span>
                            <span class="detail-value">{{ $trip->lr_number ?? 'N/A' }}</span>
                        </td>
                    </tr>

                    {{-- Route --}}
                    <tr>
                        <td style="width:38%; padding:7pt 10pt; border-bottom:1pt solid #eeeeee;
                                   color:#333333; font-family:Arial,Helvetica,sans-serif;">
                            <div class="route-city">{{ $trip->origin ?? 'N/A' }}</div>
                            <div class="route-date">{{ $trip->start_date ? $trip->start_date->format('d M Y') : 'N/A' }}</div>
                        </td>
                        <td class="route-arrow"
                            style="width:24%; padding:7pt 4pt; border-bottom:1pt solid #eeeeee;
                                   color:#000000; text-align:center; vertical-align:middle;">
                            <img src="{{ public_path('img/arrow-right.png') }}"
                                 alt="->"
                                 style="width:30pt; ">
                        </td>
                        <td style="width:38%; text-align:right; padding:7pt 10pt;
                                   border-bottom:1pt solid #eeeeee; color:#333333;
                                   font-family:Arial,Helvetica,sans-serif;">
                            <div class="route-city">{{ $trip->destination ?? 'N/A' }}</div>
                            <div class="route-date">{{ $trip->completed_date ? $trip->completed_date->format('d M Y') : 'N/A' }}</div>
                        </td>
                    </tr>

                    {{-- Truck & Driver --}}
                    <tr>
                        <td class="detail-cell" colspan="2">
                            <span class="detail-label">Truck: </span>
                            <span class="detail-value">{{ $trip->truck->truck_number ?? ($trip->truck_name ?? 'N/A') }}</span>
                        </td>
                        <td class="detail-cell" style="text-align:right;">
                            <span class="detail-label">Driver: </span>
                            <span class="detail-value">{{ $trip->driver->name ?? ($trip->driver_name ?? 'N/A') }}</span>
                        </td>
                    </tr>

                    {{-- Status & Material --}}
                    <tr>
                        <td class="detail-cell-last" colspan="2">
                            <span class="detail-label">Trip Status: </span>
                            <span class="detail-value" style="text-transform:capitalize;">
                                {{ ucfirst(str_replace('_', ' ', $trip->status)) }}
                            </span>
                        </td>
                        <td class="detail-cell-last" style="text-align:right;">
                            <span class="detail-label">Material: </span>
                            <span class="detail-value">{{ $trip->material_name ?? 'N/A' }}</span>
                        </td>
                    </tr>

                </table>
            </td>

            {{-- ---- RIGHT: Payment Details ---- --}}
            <td class="col-right">
                <table class="section-box">

                    {{-- Section Header --}}
                    <tr>
                        <td class="section-header" colspan="2">Payment Details</td>
                    </tr>

                    {{-- Payment rows inside a nested table --}}
                    <tr>
                        <td colspan="2" style="padding:0;">
                            <table class="payment-table">
                                <tr>
                                    <td style="color:#333333; font-family:Arial,Helvetica,sans-serif;">
                                        Freight Amount
                                    </td>
                                    <td class="amount-cell">
                                        Rs. {{ number_format($trip->freight_amount ?? 0, 0) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="color:#333333; font-family:Arial,Helvetica,sans-serif;">
                                        Advances (-)
                                    </td>
                                    <td class="amount-cell">
                                        Rs. {{ number_format($totalAdvances, 0) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="color:#333333; font-family:Arial,Helvetica,sans-serif;">
                                        Charges (+)
                                    </td>
                                    <td class="amount-cell">
                                        Rs. {{ number_format($trip->charges->where('charge_direction','add_to_bill')->sum('amount'), 0) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="color:#333333; font-family:Arial,Helvetica,sans-serif;">
                                        Deductions (-)
                                    </td>
                                    <td class="amount-cell">
                                        Rs. {{ number_format($trip->charges->where('charge_direction','reduce_from_bill')->sum('amount'), 0) }}
                                    </td>
                                </tr>

                                @if($trip->payments->count() > 0)
                                    @php $lastPayment = $trip->payments->last(); @endphp
                                    <tr>
                                        <td style="color:#333333; font-family:Arial,Helvetica,sans-serif;">
                                            Payments (-)
                                            @if($lastPayment)
                                                <div class="sub-info">
                                                    Via <b>{{ strtoupper($lastPayment->payment_method) }}</b>
                                                    on <b>{{ $lastPayment->payment_date ? $lastPayment->payment_date->format('d M Y') : 'N/A' }}</b><br>
                                                    Received By <b>{{ $lastPayment->received_by_driver ? 'Driver' : 'Party' }}</b>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="amount-cell" style="vertical-align:top;">
                                            -Rs. {{ number_format($totalPayments, 0) }}
                                        </td>
                                    </tr>
                                @else
                                    <tr>
                                        <td style="color:#333333; font-family:Arial,Helvetica,sans-serif;">
                                            Payments (-)
                                        </td>
                                        <td class="amount-cell">Rs. 0</td>
                                    </tr>
                                @endif

                                <tr class="total-row">
                                    <td style="color:#222222; font-family:Arial,Helvetica,sans-serif;">
                                        Total Pending Balance
                                    </td>
                                    <td class="total-amount">
                                        Rs. {{ number_format($pendingBalance, 0) }}
                                    </td>
                                </tr>

                            </table>
                        </td>
                    </tr>

                </table>
            </td>

        </tr>
    </table>

    {{-- ==============================
         POD IMAGE
    ============================== --}}
    @if($trip->pod_receipt)
        <table class="header-bar" style="margin-top:12pt; margin-bottom:8pt;">
            <tr>
                <td class="bar-title">Proof of Delivery (POD)</td>
                <td class="bar-date">Trip ID / LR NO: {{ $trip->lr_number ?? 'N/A' }}</td>
            </tr>
        </table>
        @php $podPath = public_path('storage/' . $trip->pod_receipt); @endphp
        @if(file_exists($podPath))
            <div class="pod-image">
                <img src="{{ $podPath }}" alt="POD Receipt">
            </div>
        @endif
    @endif

    {{-- ==============================
         FOOTER
    ============================== --}}
    <div class="footer">
        This is a computer-generated invoice. No signature required. &mdash; N B Logistics
    </div>

</body>
</html>

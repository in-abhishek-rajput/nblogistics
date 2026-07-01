<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bilty/LR Template - {{ $trip->lr_number ?? '0073' }}</title>
    <style>
        /* CSS styling */
        :root {
            --border-color: #2b3d75;
            --text-color: #2b3d75;
            --font-family: 'Arial', sans-serif;
        }

        body {
            font-family: var(--font-family);
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
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            box-sizing: border-box;
        }

        .bilty-wrapper {
            border: 2px solid var(--border-color);
            width: 100%;
            position: relative;
            box-sizing: border-box;
        }

        /* Utility classes */
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        
        .flex { display: flex; }
        .justify-between { justify-content: space-between; }
        .items-center { align-items: center; }

        /* Header Section */
        .header-section {
            border-bottom: 2px solid var(--border-color);
            padding: 10px;
        }

        .header-top {
            display: flex;
            justify-content: space-between;
            font-size: 10px;
            font-weight: bold;
        }

        .logo-area {
            width: 150px;
        }

        .company-info {
            flex-grow: 1;
            text-align: center;
        }

        .company-title {
            font-size: 32px;
            font-weight: bold;
            margin: 5px 0;
            letter-spacing: 1px;
            font-family: 'Times New Roman', serif;
        }

        .company-subtitle {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .company-address {
            font-size: 11px;
        }

        .copy-type {
            font-size: 10px;
            text-align: right;
            line-height: 1.3;
        }

        /* Details Section */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            font-weight: bold;
        }

        .info-table td {
            border: 1px solid var(--border-color);
            padding: 4px 8px;
            vertical-align: top;
            height: 25px;
        }

        .pan-box {
            background-color: var(--border-color);
            color: white;
            text-align: center;
            font-size: 16px;
            padding: 4px;
            border-bottom: 1px solid var(--border-color);
        }

        .risk-box {
            text-align: center;
            font-size: 12px;
            padding: 4px;
        }
        
        .checkbox-group {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-top: 5px;
            font-size: 11px;
        }
        
        .checkbox-box {
            width: 14px;
            height: 14px;
            border: 1px solid var(--border-color);
            display: inline-block;
        }

        /* Goods Section */
        .goods-table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
            font-size: 12px;
            font-weight: bold;
        }

        .goods-table th {
            border: 1px solid var(--border-color);
            padding: 6px;
            font-weight: bold;
        }

        .goods-table td {
            border: 1px solid var(--border-color);
            padding: 6px;
            vertical-align: top;
        }
        
        /* Freight column specifics */
        .freight-col {
            padding: 0 !important;
            height: auto !important;
        }
        
        .freight-inner-table {
            width: 100%;
            height: 100%;
            border-collapse: collapse;
        }
        
        .freight-inner-table td {
            border: none;
            border-bottom: 1px solid var(--border-color);
            height: 30px;
            padding: 4px 8px;
            text-align: left;
            display: flex;
            justify-content: space-between;
        }
        .freight-inner-table td:last-child {
            border-bottom: none;
        }

        .charges-weight-box {
            border-top: 1px solid var(--border-color);
            padding: 4px 8px;
            text-align: left;
            height: auto !important;
            display: flex;
            justify-content: space-between;
        }

        /* Bottom Table */
        .bottom-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            font-weight: bold;
        }

        .bottom-table td {
            border: 1px solid var(--border-color);
            padding: 4px 8px;
            height: 25px;
        }

        /* Footer Section */
        .footer-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            font-weight: bold;
        }

        .footer-table td {
            border: 1px solid var(--border-color);
            padding: 8px;
            vertical-align: top;
            height: 90px;
            position: relative;
        }

        .bank-details {
            line-height: 1.6;
        }

        .signature-text {
            position: absolute;
            bottom: 8px;
            width: 100%;
            text-align: center;
            left: 0;
        }

        .text-red {
            color: #d12c2c;
        }

        /* Print Styles */
        @media print {
            body {
                background-color: white;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
                margin: 0;
                padding: 0;
            }
            .a4-container {
                box-shadow: none;
                margin: 0;
                padding: 0;
                width: 100%;
                height: auto;
                min-height: 0; /* Prevent spilling to second page due to 297mm min-height */
            }
            .bilty-wrapper {
                page-break-inside: avoid;
            }
            @page {
                size: A4 portrait;
                margin: 5mm;
            }
            /* Force borders and background colors in print */
            .pan-box {
                background-color: var(--border-color) !important;
                color: white !important;
            }
            .text-red {
                color: #d12c2c !important;
            }
        }
    </style>
</head>
<body>

@php
    $copies = [
        'CONSIGNEE COPY : WHITE',
        'CONSIGNOR COPY : BLUE',
        'TRANSPORT COPY : YELLOW'
    ];
@endphp
<div class="a4-container">
    @foreach($copies as $copyName)
    <div class="bilty-wrapper" style="{{ !$loop->last ? 'page-break-after: always; margin-bottom: 20mm;' : '' }}">
        
        <!-- Header -->
        <div class="header-section">
            <div class="header-top">
                <div style="width: 150px;"></div>
                <div class="text-center" style="font-weight: normal; flex-grow: 1;">SUBJECT TO JAMNAGAR JURISDICTION</div>
                <div class="copy-type" style="width: 150px;">
                    {!! $copyName !!}
                </div>
            </div>
            
            <div class="flex items-center mt-2" style="margin-top: 10px;">
                <div class="logo-area">
                    <img src="{{ asset('img/logo.png') }}" alt="NB Logistics" style="max-width: 130px; display: block;">
                </div>
                <div class="company-info">
                    <div class="company-title">N. B. LOGISTICS</div>
                    <div class="company-subtitle">Fleet Owner & Transport Contractor</div>
                    <div class="company-address">
                        Patel Chowk, GIDC Phase -2, Dared, Jamnagar - Gujarat - 361004, Mo. : 99243 28424 | Mo. : 84016 63180
                    </div>
                </div>
            </div>
        </div>

        <!-- Details -->
        <table class="info-table">
            <tr>
                <td style="width: 33%; border-right: none;" class="text-red">LR NO.: <span style="font-size: 16px;">{{ $document->data['lr_number'] ?? ($trip->lr_number ?? '') }}</span></td>
                <td style="width: 34%; padding: 0; border-left: none; border-right: none;">
                    <div class="pan-box">PAN NO.: GNWPS1050M</div>
                </td>
                <td style="width: 33%; border-left: none;">FROM : {{ $document->data['bilty_from'] ?? ($trip->origin ?? '') }}</td>
            </tr>
            <tr>
                <td>DATE : <span style="font-weight: normal;">{{ isset($document->data['lr_date']) ? \Carbon\Carbon::parse($document->data['lr_date'])->format('d/m/Y') : (isset($trip->start_date) && $trip->start_date ? $trip->start_date->format('d/m/Y') : date('d/m/Y')) }}</span></td>
                <td rowspan="2" style="padding: 0;">
                    <div class="risk-box">
                        <div>AT OWNER RISK</div>
                        <div class="checkbox-group">
                            <span>GST PAID BY: CONSIGNOR</span> <div class="checkbox-box" style="{{ (isset($document->data['gst_paid_by']) && $document->data['gst_paid_by'] == 'consignor') ? 'background-color: #2b3d75;' : '' }}"></div>
                            <span>CONSIGNEE</span> <div class="checkbox-box" style="{{ (isset($document->data['gst_paid_by']) && $document->data['gst_paid_by'] == 'consignee') ? 'background-color: #2b3d75;' : '' }}"></div>
                        </div>
                    </div>
                </td>
                <td>TO : {{ $document->data['bilty_to'] ?? ($trip->destination ?? '') }}</td>
            </tr>
            <tr>
                <td>VEHICLE NO. <span style="font-weight: normal;">{{ $document->data['vehicle_no'] ?? (isset($trip->truck) ? $trip->truck->truck_number : ($trip->truck_name ?? '')) }}</span></td>
                <td>INVOICE NO.: <span style="font-weight: normal;">{{ $document->data['bilty_invoice_no'] ?? '' }}</span></td>
            </tr>
            <tr>
                <td colspan="2" style="height: 35px;">CONSIGNOR : <span style="font-weight: normal;">{{ $document->data['consignor_name'] ?? (isset($trip->party) ? $trip->party->name : ($trip->party_name ?? '')) }}</span></td>
                <td style="height: 35px;">CONSIGNEE : <span style="font-weight: normal;">{{ $document->data['consignee_name'] ?? '' }}</span></td>
            </tr>
            <tr>
                <td colspan="2" style="height: 35px;">ADDRESS : <span style="font-weight: normal;">{{ $document->data['consignor_address'] ?? '' }}</span></td>
                <td style="height: 35px;">ADDRESS : <span style="font-weight: normal;">{{ $document->data['consignee_address'] ?? '' }}</span></td>
            </tr>
            <tr>
                <td colspan="2">MOBILE NO.: <span style="font-weight: normal;">{{ $document->data['consignor_mobile'] ?? '' }}</span></td>
                <td>MOBILE NO.: <span style="font-weight: normal;">{{ $document->data['consignee_mobile'] ?? '' }}</span></td>
            </tr>
            <tr>
                <td colspan="2">GST NO.: <span style="font-weight: normal;">{{ $document->data['consignor_gst'] ?? '' }}</span></td>
                <td>GST NO.: <span style="font-weight: normal;">{{ $document->data['consignee_gst'] ?? '' }}</span></td>
            </tr>
        </table>

        <!-- Goods Table -->
        <table class="goods-table">
            <tr>
                <th style="width: 8%;">NO. OF<br>PKGS</th>
                <th style="width: 47%;">DESCRIPTION OF GOODS</th>
                <th style="width: 15%;">ACTUAL<br>WEIGHT</th>
                <th style="width: 30%;">FRIGHT<br>TO PAY / PAID</th>
            </tr>
            <tr>
                <td style="height: 150px;"><span style="font-weight: normal;">{{ $document->data['no_of_packages'] ?? ($trip->number_of_packages ?? '') }}</span></td>
                <td><span style="font-weight: normal;">{{ $document->data['description_of_goods'] ?? ($trip->material_name ?? '') }}</span></td>
                <td><span style="font-weight: normal;">{{ $document->data['actual_weight'] ?? ($trip->actual_weight ?? '') }}</span></td>
                <td class="freight-col">
                    <table class="freight-inner-table">
                        <tr>
                            <td>
                                <span>FRIGHT :</span>
                                <span style="font-weight: normal;">{{ isset($document->data['bilty_freight_amount']) ? number_format((float)$document->data['bilty_freight_amount'], 2) : '' }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="border-top: none;"></td>
                <td class="freight-col">
                    <div class="charges-weight-box">
                        <span>CHARGES WEIGHT</span> <span style="font-weight: normal;">{{ $document->data['charged_weight'] ?? '' }}</span>
                    </div>
                </td>
                <td class="freight-col">
                    <table class="freight-inner-table">
                        <tr>
                            <td>
                                <span>HAMALI CHARGES :</span>
                                <span style="font-weight: normal;">{{ isset($document->data['hamali_charges']) && $document->data['hamali_charges'] > 0 ? number_format((float)$document->data['hamali_charges'], 2) : '' }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span>BILTY CHARGES :</span>
                                <span style="font-weight: normal;">{{ isset($document->data['bilty_charges']) && $document->data['bilty_charges'] > 0 ? number_format((float)$document->data['bilty_charges'], 2) : '' }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span>ADVANCE AMOUNT :</span>
                                <span style="font-weight: normal;">{{ isset($document->data['advance_amount']) && $document->data['advance_amount'] > 0 ? number_format((float)$document->data['advance_amount'], 2) : '' }}</span>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- Bottom Table -->
        <table class="bottom-table">
            <tr>
                <td style="width: 35%;">E-WAY BILL NO.: <span style="font-weight: normal;">{{ $document->data['eway_bill_no'] ?? '' }}</span></td>
                <td style="width: 20%; text-align: center;">INVOICE VALUE<br><span style="font-weight: normal;">{{ $document->data['invoice_value'] ?? '' }}</span></td>
                <td style="width: 15%; text-align: center;">RATE<br><span style="font-weight: normal;">{{ $document->data['bilty_rate'] ?? '' }}</span></td>
                <td style="width: 30%;">
                    <div style="display: flex; justify-content: space-between;">
                        <span>TOTAL AMOUNT :</span>
                        <span style="font-weight: normal;">{{ isset($document->data['bilty_total']) ? number_format((float)$document->data['bilty_total'], 2) : '' }}</span>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="4" style="height: 35px;">REMARK : <span style="font-weight: normal;">{{ $document->data['bilty_remark'] ?? ($trip->notes ?? '') }}</span></td>
            </tr>
        </table>

        <!-- Footer Section -->
        <table class="footer-table">
            <tr>
                <td style="width: 45%;">
                    <div class="bank-details">
                        BANK NAME : HDFC BANK, DARED BRANCH<br>
                        A/C. NAME : N B LOGISTICS<br>
                        A/C. NO. : 50200074315971<br>
                        ISFC CODE : HDFC0004482
                    </div>
                </td>
                <td style="width: 25%;">
                    <div>RECEIVED :</div>
                    <div style="position: absolute; bottom: 8px;">
                        DATE :<br><br>
                        CONSIGNEE'S SIGNATURE & SEAL
                    </div>
                </td>
                <td style="width: 30%;">
                    <div class="signature-text">AUTHORISED SIGNATURE</div>
                </td>
            </tr>
        </table>

    </div>
    @endforeach
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

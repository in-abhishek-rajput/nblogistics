<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bill / Tax Invoice - {{ $trip->lr_number ?? 'Invoice' }}</title>
    <style>
        :root {
            --border-color: #000;
            --blue-bg: #4a8bc9; /* The blue from the screenshot */
            --font-family: 'Arial', sans-serif;
        }

        body {
            font-family: var(--font-family);
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
            font-size: 11px;
            color: #000;
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

        .invoice-wrapper {
            border: 2px solid var(--border-color);
            width: 100%;
            position: relative;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
        }

        /* Generic Utilities */
        .flex { display: flex; }
        .flex-col { display: flex; flex-direction: column; }
        .justify-between { justify-content: space-between; }
        .justify-center { justify-content: center; }
        .items-center { align-items: center; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        .w-full { width: 100%; }
        
        .blue-bg {
            background-color: var(--blue-bg);
            color: white;
            font-weight: bold;
        }

        /* Header Section */
        .header-section {
            display: flex;
            border-bottom: 2px solid var(--border-color);
        }

        .pan-box {
            background-color: var(--blue-bg);
            color: white;
            padding: 6px;
            text-align: center;
            font-weight: bold;
            font-size: 14px;
            width: 100%;
            margin-left: -5%;
            margin-bottom: 10px;
            border: 1px solid var(--border-color);
        }

        .bill-title-box {
            background-color: var(--blue-bg);
            color: white;
            padding: 6px;
            text-align: center;
            font-weight: bold;
            font-size: 14px;
            width: 90%;
            border: 1px solid var(--border-color);
            border-radius: 4px;
        }

        .company-header-title {
            background-color: var(--blue-bg);
            color: white;
            font-size: 26px;
            font-weight: bold;
            text-align: center;
            padding: 6px;
            border-bottom: 1px solid var(--border-color);
            letter-spacing: 1px;
        }

        .company-address {
            padding: 10px;
            font-size: 12px;
            font-weight: bold;
            line-height: 1.4;
        }

        /* Top Details Grid (Invoice No, From/To, Freight) */
        .top-details {
            display: flex;
            border-bottom: 2px solid var(--border-color);
        }

        .col-left-details {
            width: 32%;
            border-right: 1px solid var(--border-color);
            padding: 5px;
        }
        
        .col-mid-details {
            width: 33%;
            border-right: 1px solid var(--border-color);
            padding: 5px;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 15px;
        }

        .col-right-freight {
            width: 35%;
        }

        .kv-row {
            display: flex;
            margin-bottom: 6px;
        }
        .kv-row .key {
            width: 45%;
            border: 1px solid var(--border-color);
            padding: 4px;
            font-weight: bold;
            font-size: 10px;
            display: flex;
            align-items: center;
        }
        .kv-row .val {
            width: 55%;
            border: 1px solid var(--border-color);
            border-left: none;
            padding: 4px;
            font-weight: normal;
        }

        .from-to-box {
            width: 80%;
            margin-bottom: 15px;
            text-align: center;
        }
        .from-to-title {
            font-weight: bold;
            margin-bottom: 2px;
        }
        .from-to-input {
            border: 1px solid var(--border-color);
            height: 28px;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Freight Table */
        .freight-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            font-weight: bold;
        }
        .freight-table th, .freight-table td {
            border-bottom: 1px solid var(--border-color);
            padding: 4px;
            height: 24px;
        }
        .freight-table th:first-child, .freight-table td:first-child {
            border-right: 1px solid var(--border-color);
            text-align: center;
        }
        .freight-table th:last-child, .freight-table td:last-child {
            text-align: center;
            width: 35%;
            font-weight: normal;
        }
        .freight-table th { border-bottom: 1px solid var(--border-color); font-weight: bold; }
        .freight-table tr:last-child td { border-bottom: none; }
        
        /* Addresses Row (Bill To, Bill From, Ship To) */
        .addresses-row {
            display: flex;
            border-bottom: 2px solid var(--border-color);
        }

        .address-col {
            flex: 1;
            border-right: 1px solid var(--border-color);
            padding-bottom: 5px;
        }
        .address-col:last-child {
            border-right: none;
        }

        .address-title {
            background-color: var(--blue-bg);
            color: white;
            font-weight: bold;
            text-align: center;
            padding: 4px;
            margin: 5px auto;
            width: 60%;
            border: 1px solid var(--border-color);
        }

        .addr-fields {
            padding: 0 5px;
        }

        .addr-row {
            display: flex;
            margin-bottom: 4px;
            font-size: 10px;
            font-weight: bold;
            align-items: center;
        }
        .addr-row .key {
            width: 35%;
        }
        .addr-row .val {
            width: 65%;
            border: 1px solid var(--border-color);
            height: 20px;
            padding: 2px 4px;
            font-weight: normal;
        }
        .addr-row.textarea-row {
            align-items: flex-start;
        }
        .addr-row.textarea-row .val {
            height: 50px;
        }

        /* Bottom Section (Goods, Payment) */
        .bottom-section {
            display: flex;
            border-bottom: 2px solid var(--border-color);
        }
        .bottom-left {
            width: 65%;
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
        }
        .bottom-right {
            width: 35%;
        }

        .desc-row {
            border-bottom: 1px solid var(--border-color);
            padding: 5px 8px;
            font-weight: bold;
            min-height: 40px;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
        }
        .desc-row:last-child { border-bottom: none; }

        /* Payment Fields Table */
        .payment-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            font-weight: bold;
        }
        .payment-table td {
            border-bottom: 1px solid var(--border-color);
            padding: 6px;
            height: 31.5px; /* Alignment for 4 rows in this section */
        }
        .payment-table td:first-child {
            border-right: 1px solid var(--border-color);
            width: 45%;
        }
        .payment-table tr:last-child td { border-bottom: none; }
        .payment-table td:last-child { font-weight: normal; text-align: center; }

        /* Footer Section */
        .footer-section {
            display: flex;
        }
        .footer-col {
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
        }
        .footer-col:last-child { border-right: none; }

        .bank-details {
            width: 35%;
            padding: 5px 8px;
            font-weight: bold;
            font-size: 10px;
            line-height: 1.8;
        }
        .bank-title {
            text-align: center;
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 5px;
            padding-bottom: 4px;
        }
        
        .signatures-area {
            width: 65%;
            display: flex;
            flex-direction: column;
        }
        .sig-top-row {
            display: flex;
            border-bottom: 1px solid var(--border-color);
        }
        .sig-top-col {
            flex: 1;
            border-right: 1px solid var(--border-color);
            text-align: center;
            font-weight: bold;
            padding: 6px;
            font-size: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .sig-top-col:last-child { border-right: none; }

        .sig-bottom-row {
            display: flex;
            flex-grow: 1;
        }
        .sig-bottom-col {
            flex: 1;
            border-right: 1px solid var(--border-color);
            position: relative;
            min-height: 80px;
        }
        .sig-bottom-col:last-child { border-right: none; }
        .sig-text {
            position: absolute;
            bottom: 5px;
            width: 100%;
            text-align: center;
            font-weight: bold;
            font-size: 10px;
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
                min-height: 0;
            }
            .invoice-wrapper {
                page-break-inside: avoid;
            }
            @page {
                size: A4 portrait;
                margin: 5mm;
            }
            .blue-bg, .pan-box, .bill-title-box, .company-header-title, .address-title {
                background-color: var(--blue-bg) !important;
                color: white !important;
            }
        }
    </style>
</head>
<body>

<div class="a4-container">
    <div class="invoice-wrapper">

        <!-- HEADER -->
        <div class="header-section">
            <div style="width: 32%; padding: 10px;">
                <div style="font-size: 9px; font-weight: bold; margin-bottom: 10px;">SUBJECT TO JAMNAGAR JURISDICTION</div>
                <div style="text-align: center;">
                    @if(auth()->check() && auth()->user()->logo)
                        <img src="{{ asset('storage/' . auth()->user()->logo) }}" alt="Company Logo" style="max-width: 140px; margin-top: 10px;">
                    @else
                        <img src="{{ asset('img/logo.png') }}" alt="NB Logistics Logo" style="max-width: 140px; margin-top: 10px;">
                    @endif
                </div>
            </div>
            <div style="width: 33%; display: flex; flex-direction: column; align-items: center; justify-content: center; padding-top: 15px;">
                <div class="bill-title-box" style="margin-bottom:10px;">PAN NO.: GNWPS1050M</div>
                <div class="bill-title-box">BILL (TAX INVOICE)</div>
            </div>
            <div style="width: 35%; border-left: 1px solid var(--border-color); display: flex; flex-direction: column;">
                <div class="company-header-title">N B LOGISTICS</div>
                <div class="company-address">
                    Patel Chowk, G.I.D.C. Phase - 2, Dared,<br>
                    Jamnagar - 361004 (Gujarat)<br>
                    Mo. 9924328424 | 8401663180
                </div>
            </div>
        </div>

        <!-- TOP DETAILS (Invoice, From/To, Freight) -->
        <div class="top-details">
            <div class="col-left-details">
                <div class="kv-row">
                    <div class="key">INVOICE NO.</div>
                    <div class="val">{{ $document->data['invoice_number'] ?? ($trip->id ?? '') }}</div>
                </div>
                <div class="kv-row">
                    <div class="key">INVOICE DATE</div>
                    <div class="val">{{ isset($document->data['invoice_date']) ? \Carbon\Carbon::parse($document->data['invoice_date'])->format('d/m/Y') : date('d/m/Y') }}</div>
                </div>
                <div class="kv-row">
                    <div class="key">VEHICLE NO.</div>
                    <div class="val">{{ $document->data['inv_vehicle_no'] ?? (isset($trip->truck) ? $trip->truck->truck_number : ($trip->truck_name ?? '')) }}</div>
                </div>
                <div class="kv-row">
                    <div class="key">L. R. NO.</div>
                    <div class="val">{{ $document->data['inv_lr_no'] ?? ($trip->lr_number ?? '') }}</div>
                </div>
            </div>
            
            <div class="col-mid-details">
                <div class="from-to-box">
                    <div class="from-to-title">FROM</div>
                    <div class="from-to-input">{{ $document->data['inv_from'] ?? ($trip->origin ?? '') }}</div>
                </div>
                <div class="from-to-box">
                    <div class="from-to-title">TO</div>
                    <div class="from-to-input">{{ $document->data['inv_to'] ?? ($trip->destination ?? '') }}</div>
                </div>
            </div>

            <div class="col-right-freight">
                <table class="freight-table">
                    <tr>
                        <th style="border-right: 1px solid var(--border-color);">PARTICULARS</th>
                        <th>AMOUNT (RS)</th>
                    </tr>
                    <tr>
                        <td>FREIGHT</td>
                        <td>{{ isset($document->data['inv_freight_amount']) ? number_format((float)$document->data['inv_freight_amount'], 2) : '' }}</td>
                    </tr>
                    <tr>
                        <td>LOADING CHARGE</td>
                        <td>{{ isset($document->data['loading_charge']) && $document->data['loading_charge'] > 0 ? number_format((float)$document->data['loading_charge'], 2) : '' }}</td>
                    </tr>
                    <tr>
                        <td>UNLOADING CHARGE</td>
                        <td>{{ isset($document->data['unloading_charge']) && $document->data['unloading_charge'] > 0 ? number_format((float)$document->data['unloading_charge'], 2) : '' }}</td>
                    </tr>
                    <tr>
                        <td>SUB TOTAL</td>
                        <td>{{ isset($document->data['sub_total']) ? number_format((float)$document->data['sub_total'], 2) : '' }}</td>
                    </tr>
                    <tr>
                        <td>SGST {{ isset($document->data['sgst_percent']) && $document->data['sgst_percent'] > 0 ? $document->data['sgst_percent'] . '%' : '' }}</td>
                        <td>{{ isset($document->data['sgst_amount']) && $document->data['sgst_amount'] > 0 ? number_format((float)$document->data['sgst_amount'], 2) : '' }}</td>
                    </tr>
                    <tr>
                        <td>CGST {{ isset($document->data['cgst_percent']) && $document->data['cgst_percent'] > 0 ? $document->data['cgst_percent'] . '%' : '' }}</td>
                        <td>{{ isset($document->data['cgst_amount']) && $document->data['cgst_amount'] > 0 ? number_format((float)$document->data['cgst_amount'], 2) : '' }}</td>
                    </tr>
                    <tr>
                        <td>GRAND TOTAL</td>
                        <td>{{ isset($document->data['grand_total']) ? number_format((float)$document->data['grand_total'], 2) : '' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- ADDRESSES ROW (Bill To, Bill From, Ship To) -->
        <div class="addresses-row">
            <!-- BILL TO -->
            <div class="address-col">
                <div class="address-title">BILL TO</div>
                <div class="addr-fields">
                    <div class="addr-row">
                        <div class="key">NAME : M/S</div>
                        <div class="val">{{ $document->data['bill_to_name'] ?? (isset($trip->party) ? $trip->party->name : ($trip->party_name ?? '')) }}</div>
                    </div>
                    <div class="addr-row textarea-row">
                        <div class="key">ADDRESS</div>
                        <div class="val">{{ $document->data['bill_to_address'] ?? '' }}</div>
                    </div>
                    <div class="addr-row" style="margin-top: 10px;">
                        <div class="key">GST NO.</div>
                        <div class="val">{{ $document->data['bill_to_gst'] ?? '' }}</div>
                    </div>
                    <div class="addr-row">
                        <div class="key">CITY | STATE</div>
                        <div class="val">{{ $document->data['bill_to_city_state'] ?? '' }}</div>
                    </div>
                    <div class="addr-row">
                        <div class="key">PIN CODE</div>
                        <div class="val">{{ $document->data['bill_to_pin'] ?? '' }}</div>
                    </div>
                </div>
            </div>
            
            <!-- BILL FROM -->
            <div class="address-col">
                <div class="address-title">BILL FROM</div>
                <div class="addr-fields">
                    <div class="addr-row">
                        <div class="key">NAME : M/S</div>
                        <div class="val">{{ $document->data['bill_from_name'] ?? 'N B LOGISTICS' }}</div>
                    </div>
                    <div class="addr-row textarea-row">
                        <div class="key">ADDRESS</div>
                        <div class="val">{{ $document->data['bill_from_address'] ?? '' }}</div>
                    </div>
                    <div class="addr-row" style="margin-top: 10px;">
                        <div class="key">GST NO.</div>
                        <div class="val">{{ $document->data['bill_from_gst'] ?? '' }}</div>
                    </div>
                    <div class="addr-row">
                        <div class="key">CITY | STATE</div>
                        <div class="val">{{ $document->data['bill_from_city_state'] ?? '' }}</div>
                    </div>
                    <div class="addr-row">
                        <div class="key">PIN CODE</div>
                        <div class="val">{{ $document->data['bill_from_pin'] ?? '' }}</div>
                    </div>
                    <div class="addr-row">
                        <div class="key">MOBILE NO.</div>
                        <div class="val">{{ $document->data['bill_from_mobile'] ?? '' }}</div>
                    </div>
                </div>
            </div>

            <!-- SHIP TO -->
            <div class="address-col">
                <div class="address-title">SHIP TO</div>
                <div class="addr-fields">
                    <div class="addr-row">
                        <div class="key">NAME : M/S</div>
                        <div class="val">{{ $document->data['ship_to_name'] ?? '' }}</div>
                    </div>
                    <div class="addr-row textarea-row">
                        <div class="key">ADDRESS</div>
                        <div class="val">{{ $document->data['ship_to_address'] ?? '' }}</div>
                    </div>
                    <div class="addr-row" style="margin-top: 10px;">
                        <div class="key">GST NO.</div>
                        <div class="val">{{ $document->data['ship_to_gst'] ?? '' }}</div>
                    </div>
                    <div class="addr-row">
                        <div class="key">CITY | STATE</div>
                        <div class="val">{{ $document->data['ship_to_city_state'] ?? '' }}</div>
                    </div>
                    <div class="addr-row">
                        <div class="key">PIN CODE</div>
                        <div class="val">{{ $document->data['ship_to_pin'] ?? '' }}</div>
                    </div>
                    <div class="addr-row">
                        <div class="key">MOBILE NO.</div>
                        <div class="val">{{ $document->data['ship_to_mobile'] ?? '' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- BOTTOM SECTION (Desc, Weight, etc.) -->
        <div class="bottom-section">
            <div class="bottom-left">
                <div class="desc-row" style="flex: 1;">
                    DESCRIPTION OF GOODS
                    <div style="font-weight: normal; margin-top: 5px;">{{ $document->data['inv_description_of_goods'] ?? ($trip->material_name ?? '') }}</div>
                </div>
                <div class="desc-row" style="height: 45px;">
                    TOTAL FREIGHT IN WORDS
                    <div style="font-weight: normal; margin-top: 5px;"></div>
                </div>
                <div class="desc-row" style="height: 35px; flex-direction: row; align-items: center;">
                    <span style="margin-right: 20px;">REMARK</span>
                    <span style="font-weight: normal;">{{ $document->data['inv_remark'] ?? ($trip->notes ?? '') }}</span>
                </div>
            </div>
            <div class="bottom-right">
                <table class="payment-table">
                    <tr>
                        <td>PAYMENT PAID BY</td>
                        <td>{{ $document->data['payment_paid_by'] ?? '' }}</td>
                    </tr>
                    <tr>
                        <td>E-WAY BILL NO.</td>
                        <td>{{ $document->data['inv_eway_bill_no'] ?? '' }}</td>
                    </tr>
                    <tr>
                        <td>NO. OF ARTICLES</td>
                        <td>{{ $document->data['inv_no_of_articles'] ?? ($trip->number_of_packages ?? '') }}</td>
                    </tr>
                    <tr>
                        <td>TOTAL WEIGHT</td>
                        <td>{{ $document->data['inv_total_weight'] ?? ($trip->actual_weight ?? '') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- FOOTER (Bank, Signatures) -->
        <div class="footer-section">
            <div class="footer-col bank-details">
                <div class="bank-title">BANK DETAILS</div>
                <div style="margin-top: 5px;">
                    ACCOUNT NAME : N B LOGISTICS<br>
                    ACCOUNT NUMBER : 50200074315971<br>
                    IFSC CODE : HDFC0002374<br>
                    BRANCH : DARED BRANCH,<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; JAMNAGAR<br>
                    ACCOUNT TYPE : CURRENT
                </div>
            </div>
            
            <div class="signatures-area">
                <div class="sig-top-row">
                    <div class="sig-top-col" style="flex: 0.6;">GST PAID BY</div>
                    <div class="sig-top-col" style="flex: 1;">CONSIGNEE'S</div>
                    <div class="sig-top-col" style="flex: 1;">CONSIGNOR'S</div>
                </div>
                <div class="sig-bottom-row">
                    <div class="sig-bottom-col" style="flex: 0.6;"></div>
                    <div class="sig-bottom-col" style="flex: 1;">
                        <div class="sig-text">AUTHORISED STMP</div>
                    </div>
                    <div class="sig-bottom-col" style="flex: 1;">
                        <div style="text-align: center; font-weight: bold; font-size: 10px; border-bottom: 1px solid var(--border-color); padding: 4px;">
                            FOR, N B LOGISTICS
                        </div>
                        <div class="sig-text">AUTHORISED SIGNATORY</div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

</body>

@if(!empty($autoPrint))
<script>
    window.addEventListener('load', function () {
        window.print();
    });
</script>
@endif
</html>

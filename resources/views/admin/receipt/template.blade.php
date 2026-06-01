<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Money Receipt - {{ $trip->lr_number ?? 'Receipt' }}</title>
    <style>
        :root {
            --border-color: #2b3d75;
            --blue-bg: #4a8bc9;
            --orange-bg: #e67e22;
            --text-color: #2b3d75;
            --font-family: 'Arial', sans-serif;
        }

        body {
            font-family: var(--font-family);
            color: var(--text-color);
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
            font-size: 11px;
        }

        .a4-container {
            width: 210mm;
            min-height: 148mm;
            padding: 10mm;
            margin: 10mm auto;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            box-sizing: border-box;
        }

        .receipt-wrapper {
            border: 2px solid var(--border-color);
            width: 100%;
            box-sizing: border-box;
        }

        /* Header Section */
        .header-section {
            display: flex;
            border-bottom: 2px solid var(--border-color);
        }

        .header-logo {
            width: 18%;
            padding: 8px;
            border-right: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .header-company {
            width: 47%;
            border-right: 1px solid var(--border-color);
        }

        .company-title {
            background-color: var(--blue-bg);
            color: white;
            font-size: 22px;
            font-weight: bold;
            text-align: center;
            padding: 5px;
            border-bottom: 1px solid var(--border-color);
            letter-spacing: 1px;
        }

        .company-address {
            padding: 6px 10px;
            font-size: 11px;
            font-weight: bold;
            line-height: 1.5;
            text-align: center;
        }

        .header-pan {
            width: 35%;
            display: flex;
            flex-direction: column;
        }

        .pan-box {
            background-color: var(--blue-bg);
            color: white;
            font-size: 14px;
            font-weight: bold;
            text-align: center;
            padding: 5px;
            border-bottom: 1px solid var(--border-color);
        }

        /* Receipt Title Row */
        .receipt-title-row {
            display: flex;
            border-bottom: 2px solid var(--border-color);
        }

        .receipt-no-box {
            width: 30%;
            display: flex;
            align-items: center;
            padding: 6px 10px;
            font-weight: bold;
            border-right: 1px solid var(--border-color);
        }

        .receipt-no-box input {
            border: 1px solid var(--border-color);
            width: 60%;
            margin-left: 5px;
            height: 22px;
            padding: 2px 5px;
        }

        .money-receipt-title {
            width: 40%;
            display: flex;
            align-items: center;
            justify-content: center;
            border-right: 1px solid var(--border-color);
        }

        .money-receipt-badge {
            background-color: var(--blue-bg);
            color: white;
            font-size: 18px;
            font-weight: bold;
            padding: 5px 20px;
            border: 1px solid var(--border-color);
            border-radius: 3px;
        }

        .receipt-date-box {
            width: 30%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 6px 10px;
            font-weight: bold;
        }

        /* Content Area */
        .content-area {
            display: flex;
            border-bottom: 2px solid var(--border-color);
        }

        .content-left {
            width: 65%;
            border-right: 2px solid var(--border-color);
            padding: 10px 15px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 170px;
        }

        .content-right {
            width: 35%;
        }

        .content-line {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 12px;
            display: flex;
            align-items: flex-end;
        }

        .content-line .label {
            white-space: nowrap;
            margin-right: 5px;
        }

        .content-line .underline {
            flex: 1;
            border-bottom: 1px solid var(--border-color);
            min-height: 20px;
            padding: 0 5px;
            font-weight: normal;
        }

        /* Particulars Table */
        .particulars-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            font-weight: bold;
        }

        .particulars-table th,
        .particulars-table td {
            border: 1px solid var(--border-color);
            padding: 5px 8px;
            height: 28px;
        }

        .particulars-table th {
            text-align: center;
        }

        .particulars-table td:first-child {
            width: 55%;
        }

        .particulars-table td:last-child {
            width: 45%;
            text-align: center;
            font-weight: normal;
        }

        /* Payment Row */
        .payment-row {
            display: flex;
            border-bottom: 2px solid var(--border-color);
        }

        .pay-cell {
            flex: 1;
            display: flex;
            align-items: center;
            padding: 6px 10px;
            font-weight: bold;
            font-size: 11px;
            border-right: 1px solid var(--border-color);
        }

        .pay-cell:last-child {
            border-right: none;
        }

        .pay-cell .val {
            border: 1px solid var(--border-color);
            flex: 1;
            margin-left: 5px;
            height: 22px;
            padding: 2px 5px;
            font-weight: normal;
        }

        /* Cheque Row */
        .cheque-row {
            display: flex;
            border-bottom: 1px solid var(--border-color);
        }

        .cheque-cell {
            display: flex;
            align-items: center;
            padding: 6px 10px;
            font-weight: bold;
            font-size: 11px;
            border-right: 1px solid var(--border-color);
        }

        .cheque-cell:last-child {
            border-right: none;
        }

        .cheque-cell .val {
            border: 1px solid var(--border-color);
            margin-left: 5px;
            height: 22px;
            padding: 2px 5px;
            min-width: 80px;
            font-weight: normal;
        }

        /* Bottom Details */
        .bottom-details {
            display: flex;
            border-bottom: 2px solid var(--border-color);
        }

        .bottom-left-cells {
            width: 65%;
            border-right: 2px solid var(--border-color);
        }

        .detail-row {
            display: flex;
            border-bottom: 1px solid var(--border-color);
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-cell {
            display: flex;
            align-items: center;
            padding: 5px 10px;
            font-weight: bold;
            font-size: 11px;
            border-right: 1px solid var(--border-color);
        }

        .detail-cell:last-child {
            border-right: none;
        }

        .detail-cell .val {
            border: 1px solid var(--border-color);
            margin-left: 5px;
            height: 22px;
            padding: 2px 5px;
            min-width: 70px;
            font-weight: normal;
        }

        .bottom-right-sig {
            width: 35%;
            position: relative;
            min-height: 80px;
        }

        .company-for {
            text-align: center;
            font-weight: bold;
            font-size: 11px;
            border-bottom: 1px solid var(--border-color);
            padding: 5px;
        }

        .sig-text {
            position: absolute;
            bottom: 5px;
            width: 100%;
            text-align: center;
            font-weight: bold;
            font-size: 10px;
        }

        /* Footer */
        .footer-row {
            display: flex;
        }

        .received-badge {
            background-color: var(--orange-bg);
            color: white;
            font-size: 14px;
            font-weight: bold;
            padding: 5px 15px;
            display: flex;
            align-items: center;
            border-right: 1px solid var(--border-color);
            width: 15%;
            justify-content: center;
        }

        .received-amount {
            width: 20%;
            display: flex;
            align-items: center;
            padding: 5px 10px;
            font-weight: bold;
            border-right: 1px solid var(--border-color);
            font-size: 12px;
        }

        .footer-sig-area {
            flex: 1;
            display: flex;
        }

        .footer-sig-col {
            flex: 1;
            text-align: center;
            font-weight: bold;
            font-size: 10px;
            padding: 5px;
            border-right: 1px solid var(--border-color);
            display: flex;
            align-items: flex-end;
            justify-content: center;
        }

        .footer-sig-col:last-child {
            border-right: none;
        }

        /* Print Styles */
        @media print {
            body {
                background-color: white;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
            .a4-container {
                box-shadow: none;
                margin: 0;
                padding: 5mm;
                width: 100%;
            }
            @page {
                size: A4 landscape;
                margin: 10mm;
            }
            .company-title, .pan-box, .money-receipt-badge, .received-badge {
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
        }
    </style>
</head>
<body>

<div class="a4-container">
    <div class="receipt-wrapper">

        <!-- HEADER -->
        <div class="header-section">
            <div class="header-logo">
                <img src="{{ asset('img/logo.png') }}" alt="NB Logistics Logo" style="max-width: 110px;">
            </div>
            <div class="header-company">
                <div class="company-title">N B LOGISTICS</div>
                <div class="company-address">
                    Patel Chowk, G.I.D.C. Phase - 2, Dared,<br>
                    Jamnagar - 361004 (Gujarat)<br>
                    Mo. 9924328424 | 8401663180
                </div>
            </div>
            <div class="header-pan">
                <div class="pan-box">PAN NO.: GNWPS1050M</div>
                <div style="flex: 1; padding: 10px; text-align: center;"></div>
            </div>
        </div>

        <!-- RECEIPT TITLE ROW -->
        <div class="receipt-title-row">
            <div class="receipt-no-box">
                RECEIPT NO. <span style="margin-left: 5px; font-weight: normal;">{{ $document->data['receipt_number'] ?? ($trip->id ?? '001') }}</span>
            </div>
            <div class="money-receipt-title">
                <div class="money-receipt-badge">MONEY RECEIPT</div>
            </div>
            <div class="receipt-date-box">
                <div>RECEIPT DATE <span style="font-weight: normal; margin-left: 5px;">{{ isset($document->data['receipt_date']) ? \Carbon\Carbon::parse($document->data['receipt_date'])->format('d/m/Y') : date('d/m/Y') }}</span></div>
            </div>
        </div>

        <!-- CONTENT AREA -->
        <div class="content-area">
            <div class="content-left">
                <div class="content-line" style="margin-top: 10px;">
                    <span class="label">RECEIVED WITH THANKS FROM M/S.</span>
                    <span class="underline">{{ $document->data['received_from'] ?? '' }}</span>
                </div>
                <div class="content-line">
                    <span class="underline"></span>
                </div>
                <div class="content-line">
                    <span class="label">THE SUM OF RUPEES</span>
                    <span class="underline"></span>
                </div>
                <div class="content-line" style="margin-top: 5px;">
                    <span class="label">FROM</span>
                    <span class="underline" style="margin-right: 20px;">{{ $document->data['receipt_from'] ?? '' }}</span>
                    <span class="label">TO</span>
                    <span class="underline">{{ $document->data['receipt_to'] ?? '' }}</span>
                </div>
            </div>
            <div class="content-right">
                <table class="particulars-table">
                    <tr>
                        <th>PARTICULARS</th>
                        <th>AMOUNT (RS)</th>
                    </tr>
                    <tr>
                        <td>FREIGHT</td>
                        <td>{{ isset($document->data['receipt_freight_amount']) ? number_format((float)$document->data['receipt_freight_amount'], 2) : '' }}</td>
                    </tr>
                    <tr>
                        <td>LOADING</td>
                        <td>{{ isset($document->data['receipt_loading_charge']) && $document->data['receipt_loading_charge'] > 0 ? number_format((float)$document->data['receipt_loading_charge'], 2) : '' }}</td>
                    </tr>
                    <tr>
                        <td>UNLOADING</td>
                        <td>{{ isset($document->data['receipt_unloading_charge']) && $document->data['receipt_unloading_charge'] > 0 ? number_format((float)$document->data['receipt_unloading_charge'], 2) : '' }}</td>
                    </tr>
                    <tr>
                        <td>ADVANCE</td>
                        <td>{{ isset($document->data['receipt_advance_amount']) && $document->data['receipt_advance_amount'] > 0 ? number_format((float)$document->data['receipt_advance_amount'], 2) : '' }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">NET AMOUNT</td>
                        <td style="font-weight: bold;">{{ isset($document->data['net_amount']) ? number_format((float)$document->data['net_amount'], 2) : '' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- PAYMENT ROW -->
        <div class="payment-row">
            <div class="pay-cell">
                PAID BY
            </div>
            <div class="pay-cell">
                AC PAY <div class="val">{{ isset($document->data['ac_pay_amount']) && $document->data['ac_pay_amount'] > 0 ? number_format((float)$document->data['ac_pay_amount'], 2) : '' }}</div>
            </div>
            <div class="pay-cell">
                BY CASH <div class="val">{{ isset($document->data['cash_amount']) && $document->data['cash_amount'] > 0 ? number_format((float)$document->data['cash_amount'], 2) : '' }}</div>
            </div>
        </div>

        <!-- CHEQUE ROW -->
        <div class="cheque-row">
            <div class="cheque-cell" style="width: 33%;">
                CHEQUE NO. <div class="val">{{ $document->data['cheque_no'] ?? '' }}</div>
            </div>
            <div class="cheque-cell" style="width: 33%;">
                DATE <div class="val">{{ isset($document->data['cheque_date']) && $document->data['cheque_date'] ? \Carbon\Carbon::parse($document->data['cheque_date'])->format('d/m/Y') : '' }}</div>
            </div>
            <div class="cheque-cell" style="width: 34%;">
                BANK NAME <div class="val">{{ $document->data['bank_name'] ?? '' }}</div>
            </div>
        </div>

        <!-- BOTTOM DETAILS -->
        <div class="bottom-details">
            <div class="bottom-left-cells">
                <div class="detail-row">
                    <div class="detail-cell" style="width: 50%;">
                        <span style="color: #d12c2c;">INVOICE NO.</span> <div class="val">{{ $document->data['receipt_invoice_no'] ?? '' }}</div>
                    </div>
                    <div class="detail-cell" style="width: 50%;">
                        DATE <div class="val">{{ isset($document->data['receipt_invoice_date']) && $document->data['receipt_invoice_date'] ? \Carbon\Carbon::parse($document->data['receipt_invoice_date'])->format('d/m/Y') : '' }}</div>
                    </div>
                </div>
                <div class="detail-row">
                    <div class="detail-cell" style="width: 50%;">
                        <span style="color: #d12c2c;">L.R. NO.</span> <div class="val">{{ $document->data['receipt_lr_no'] ?? '' }}</div>
                    </div>
                    <div class="detail-cell" style="width: 50%;">
                        DATE <div class="val">{{ isset($document->data['receipt_lr_date']) && $document->data['receipt_lr_date'] ? \Carbon\Carbon::parse($document->data['receipt_lr_date'])->format('d/m/Y') : '' }}</div>
                    </div>
                </div>
                <div class="detail-row">
                    <div class="detail-cell" style="width: 100%;">
                        TOTAL PACKAGES <div class="val">{{ $document->data['total_packages'] ?? '' }}</div>
                    </div>
                </div>
            </div>
            <div class="bottom-right-sig">
                <div class="company-for">FOR, N B LOGISTICS</div>
                <div class="sig-text">AUTHORISED SIGNATORY</div>
            </div>
        </div>

        <!-- FOOTER -->
        <div class="footer-row">
            <div class="received-badge">RECEIVED RS.</div>
            <div class="received-amount"></div>
            <div class="footer-sig-area">
                <div class="footer-sig-col">AUTHORISED STMP</div>
                <div class="footer-sig-col">AUTHORISED SIGNATORY</div>
            </div>
        </div>

    </div>
</div>

</body>
</html>

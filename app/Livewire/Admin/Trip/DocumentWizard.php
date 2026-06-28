<?php

namespace App\Livewire\Admin\Trip;

use App\Models\Trip;
use App\Models\TripDocument;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DocumentWizard extends Component
{
    // ─── Trip Identity ────────────────────────────────────────────────
    public int $tripId;
    public $trip;

    // ─── Wizard State ─────────────────────────────────────────────────
    public int $currentStep = 1;
    public bool $biltyDone = false;
    public bool $invoiceDone = false;
    public bool $receiptDone = false;

    // ─── Step 1: Bilty / LR Fields ───────────────────────────────────
    public string $lr_number = '';
    public string $lr_date = '';
    public string $vehicle_no = '';
    public string $bilty_from = '';
    public string $bilty_to = '';
    public string $consignor_name = '';
    public string $consignor_address = '';
    public string $consignor_mobile = '';
    public string $consignor_gst = '';
    public string $consignee_name = '';
    public string $consignee_address = '';
    public string $consignee_mobile = '';
    public string $consignee_gst = '';
    public string $gst_paid_by = 'consignor';
    public string $bilty_invoice_no = '';
    public string $description_of_goods = '';
    public $no_of_packages = '';
    public string $actual_weight = '';
    public string $charged_weight = '';
    public $bilty_freight_amount = 0;
    public $hamali_charges = 0;
    public $bilty_charges = 0;
    public $advance_amount = 0;
    public string $eway_bill_no = '';
    public $invoice_value = '';
    public $bilty_rate = '';
    public string $bilty_remark = '';

    // ─── Step 2: Invoice Fields ──────────────────────────────────────
    public string $invoice_number = '';
    public string $invoice_date = '';
    public string $inv_vehicle_no = '';
    public string $inv_lr_no = '';
    public string $inv_from = '';
    public string $inv_to = '';
    // Bill To
    public string $bill_to_name = '';
    public string $bill_to_address = '';
    public string $bill_to_gst = '';
    public string $bill_to_city_state = '';
    public string $bill_to_pin = '';
    // Bill From
    public string $bill_from_name = 'N B LOGISTICS';
    public string $bill_from_address = 'Patel Chowk, G.I.D.C. Phase - 2, Dared';
    public string $bill_from_gst = 'GNWPS1050M';
    public string $bill_from_city_state = 'Jamnagar, Gujarat';
    public string $bill_from_pin = '361004';
    public string $bill_from_mobile = '9924328424';
    // Ship To
    public string $ship_to_name = '';
    public string $ship_to_address = '';
    public string $ship_to_gst = '';
    public string $ship_to_city_state = '';
    public string $ship_to_pin = '';
    public string $ship_to_mobile = '';
    // Financials
    public $inv_freight_amount = 0;
    public $loading_charge = 0;
    public $unloading_charge = 0;
    public $sgst_percent = 0;
    public $cgst_percent = 0;
    public string $inv_description_of_goods = '';
    public string $inv_remark = '';
    public string $payment_paid_by = '';
    public string $inv_eway_bill_no = '';
    public $inv_no_of_articles = '';
    public string $inv_total_weight = '';

    // ─── Step 3: Receipt Fields ──────────────────────────────────────
    public string $receipt_number = '';
    public string $receipt_date = '';
    public string $received_from = '';
    public string $receipt_from = '';
    public string $receipt_to = '';
    public $receipt_freight_amount = 0;
    public $receipt_loading_charge = 0;
    public $receipt_unloading_charge = 0;
    public $receipt_advance_amount = 0;
    public $ac_pay_amount = 0;
    public $cash_amount = 0;
    public string $cheque_no = '';
    public string $cheque_date = '';
    public string $bank_name = '';
    public string $receipt_invoice_no = '';
    public string $receipt_invoice_date = '';
    public string $receipt_lr_no = '';
    public string $receipt_lr_date = '';
    public $total_packages = '';

    // ─── Lifecycle ────────────────────────────────────────────────────

    public function mount(int $tripId, int $step = 1): void
    {
        $this->tripId = $tripId;
        $this->trip = Trip::with(['party', 'truck', 'driver', 'advances', 'documents'])->findOrFail($tripId);
        $this->currentStep = $step;

        $this->prefillFromTrip();
    }

    /**
     * Pre-fill form fields from trip data or existing documents.
     */
    private function prefillFromTrip(): void
    {
        // Fetch existing documents if any
        $biltyDoc = $this->trip->documents->where('document_type', 'bilty')->first();
        $invoiceDoc = $this->trip->documents->where('document_type', 'invoice')->first();
        $receiptDoc = $this->trip->documents->where('document_type', 'receipt')->first();

        // ─── BILTY ───
        if ($biltyDoc && is_array($biltyDoc->data)) {
            $this->biltyDone = true;
            $this->fillBiltyFromData($biltyDoc->data);
        } else {
            // Default Bilty values from Trip
            $this->lr_number = $this->trip->lr_number ?? '';
            $this->lr_date = $this->trip->start_date ? $this->trip->start_date->format('Y-m-d') : now()->format('Y-m-d');
            $this->vehicle_no = $this->trip->truck->truck_number ?? $this->trip->truck_name ?? '';
            $this->bilty_from = $this->trip->origin ?? '';
            $this->bilty_to = $this->trip->destination ?? '';
            $this->consignor_name = $this->trip->party->name ?? $this->trip->party_name ?? '';
            $this->consignor_address = $this->trip->party->address ?? '';
            $this->consignor_mobile = $this->trip->party->mobile ?? '';
            $this->consignor_gst = '';
            $this->consignee_name = '';
            $this->consignee_address = '';
            $this->consignee_mobile = '';
            $this->consignee_gst = '';
            $this->description_of_goods = $this->trip->material_name ?? '';
            $this->bilty_freight_amount = $this->trip->freight_amount ?? 0;
            $this->advance_amount = $this->trip->advances ? $this->trip->advances->sum('amount') : 0;
            $this->bilty_rate = $this->trip->per_unit_amount ?? 0;
            $this->bilty_remark = $this->trip->note ?? '';
        }

        // ─── INVOICE ───
        if ($invoiceDoc && is_array($invoiceDoc->data)) {
            $this->invoiceDone = true;
            $this->fillInvoiceFromData($invoiceDoc->data);
        } else {
            // Default Invoice values (Cascade from Bilty if it exists)
            $this->cascadeBiltyToInvoice();
            
            // Auto-generate invoice number
            $year = date('Y');
            $count = TripDocument::invoice()->whereYear('created_at', $year)->count() + 1;
            $this->invoice_number = 'NB-INV-' . $year . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
            $this->invoice_date = now()->format('Y-m-d');
        }

        // ─── RECEIPT ───
        if ($receiptDoc && is_array($receiptDoc->data)) {
            $this->receiptDone = true;
            $this->fillReceiptFromData($receiptDoc->data);
        } else {
            // Default Receipt values (Cascade from Invoice)
            $this->cascadeInvoiceToReceipt();
            
            // Auto-generate receipt number
            $year = date('Y');
            $count = TripDocument::receipt()->whereYear('created_at', $year)->count() + 1;
            $this->receipt_number = 'NB-RCP-' . $year . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
            $this->receipt_date = now()->format('Y-m-d');
        }
    }

    private function fillBiltyFromData(array $data)
    {
        $this->lr_number = $data['lr_number'] ?? '';
        $this->lr_date = $data['lr_date'] ?? '';
        $this->vehicle_no = $data['vehicle_no'] ?? '';
        $this->bilty_from = $data['bilty_from'] ?? '';
        $this->bilty_to = $data['bilty_to'] ?? '';
        $this->consignor_name = $data['consignor_name'] ?? '';
        $this->consignor_address = $data['consignor_address'] ?? '';
        $this->consignor_mobile = $data['consignor_mobile'] ?? '';
        $this->consignor_gst = $data['consignor_gst'] ?? '';
        $this->consignee_name = $data['consignee_name'] ?? '';
        $this->consignee_address = $data['consignee_address'] ?? '';
        $this->consignee_mobile = $data['consignee_mobile'] ?? '';
        $this->consignee_gst = $data['consignee_gst'] ?? '';
        $this->gst_paid_by = $data['gst_paid_by'] ?? 'consignor';
        $this->bilty_invoice_no = $data['bilty_invoice_no'] ?? '';
        $this->description_of_goods = $data['description_of_goods'] ?? '';
        $this->no_of_packages = $data['no_of_packages'] ?? '';
        $this->actual_weight = $data['actual_weight'] ?? '';
        $this->charged_weight = $data['charged_weight'] ?? '';
        $this->bilty_freight_amount = $data['bilty_freight_amount'] ?? 0;
        $this->hamali_charges = $data['hamali_charges'] ?? 0;
        $this->bilty_charges = $data['bilty_charges'] ?? 0;
        $this->advance_amount = $data['advance_amount'] ?? 0;
        $this->eway_bill_no = $data['eway_bill_no'] ?? '';
        $this->invoice_value = $data['invoice_value'] ?? '';
        $this->bilty_rate = $data['bilty_rate'] ?? '';
        $this->bilty_remark = $data['bilty_remark'] ?? '';
    }

    private function fillInvoiceFromData(array $data)
    {
        $this->invoice_number = $data['invoice_number'] ?? '';
        $this->invoice_date = $data['invoice_date'] ?? '';
        $this->inv_vehicle_no = $data['inv_vehicle_no'] ?? '';
        $this->inv_lr_no = $data['inv_lr_no'] ?? '';
        $this->inv_from = $data['inv_from'] ?? '';
        $this->inv_to = $data['inv_to'] ?? '';
        $this->bill_to_name = $data['bill_to_name'] ?? '';
        $this->bill_to_address = $data['bill_to_address'] ?? '';
        $this->bill_to_gst = $data['bill_to_gst'] ?? '';
        $this->bill_to_city_state = $data['bill_to_city_state'] ?? '';
        $this->bill_to_pin = $data['bill_to_pin'] ?? '';
        $this->bill_from_name = $data['bill_from_name'] ?? 'N B LOGISTICS';
        $this->bill_from_address = $data['bill_from_address'] ?? '';
        $this->bill_from_gst = $data['bill_from_gst'] ?? '';
        $this->bill_from_city_state = $data['bill_from_city_state'] ?? '';
        $this->bill_from_pin = $data['bill_from_pin'] ?? '';
        $this->bill_from_mobile = $data['bill_from_mobile'] ?? '';
        $this->ship_to_name = $data['ship_to_name'] ?? '';
        $this->ship_to_address = $data['ship_to_address'] ?? '';
        $this->ship_to_gst = $data['ship_to_gst'] ?? '';
        $this->ship_to_city_state = $data['ship_to_city_state'] ?? '';
        $this->ship_to_pin = $data['ship_to_pin'] ?? '';
        $this->ship_to_mobile = $data['ship_to_mobile'] ?? '';
        $this->inv_freight_amount = $data['inv_freight_amount'] ?? 0;
        $this->loading_charge = $data['loading_charge'] ?? 0;
        $this->unloading_charge = $data['unloading_charge'] ?? 0;
        $this->sgst_percent = $data['sgst_percent'] ?? 0;
        $this->cgst_percent = $data['cgst_percent'] ?? 0;
        $this->inv_description_of_goods = $data['inv_description_of_goods'] ?? '';
        $this->inv_remark = $data['inv_remark'] ?? '';
        $this->payment_paid_by = $data['payment_paid_by'] ?? '';
        $this->inv_eway_bill_no = $data['inv_eway_bill_no'] ?? '';
        $this->inv_no_of_articles = $data['inv_no_of_articles'] ?? '';
        $this->inv_total_weight = $data['inv_total_weight'] ?? '';
    }

    private function fillReceiptFromData(array $data)
    {
        $this->receipt_number = $data['receipt_number'] ?? '';
        $this->receipt_date = $data['receipt_date'] ?? '';
        $this->received_from = $data['received_from'] ?? '';
        $this->receipt_from = $data['receipt_from'] ?? '';
        $this->receipt_to = $data['receipt_to'] ?? '';
        $this->receipt_freight_amount = $data['receipt_freight_amount'] ?? 0;
        $this->receipt_loading_charge = $data['receipt_loading_charge'] ?? 0;
        $this->receipt_unloading_charge = $data['receipt_unloading_charge'] ?? 0;
        $this->receipt_advance_amount = $data['receipt_advance_amount'] ?? 0;
        $this->ac_pay_amount = $data['ac_pay_amount'] ?? 0;
        $this->cash_amount = $data['cash_amount'] ?? 0;
        $this->cheque_no = $data['cheque_no'] ?? '';
        $this->cheque_date = $data['cheque_date'] ?? '';
        $this->bank_name = $data['bank_name'] ?? '';
        $this->receipt_invoice_no = $data['receipt_invoice_no'] ?? '';
        $this->receipt_invoice_date = $data['receipt_invoice_date'] ?? '';
        $this->receipt_lr_no = $data['receipt_lr_no'] ?? '';
        $this->receipt_lr_date = $data['receipt_lr_date'] ?? '';
        $this->total_packages = $data['total_packages'] ?? '';
    }

    // ─── Computed Values ──────────────────────────────────────────────

    public function getBiltyTotalProperty(): float
    {
        return (float) $this->bilty_freight_amount + (float) $this->hamali_charges + (float) $this->bilty_charges;
    }

    public function getSubTotalProperty(): float
    {
        return (float) $this->inv_freight_amount + (float) $this->loading_charge + (float) $this->unloading_charge;
    }

    public function getSgstAmountProperty(): float
    {
        return round($this->subTotal * ((float) $this->sgst_percent / 100), 2);
    }

    public function getCgstAmountProperty(): float
    {
        return round($this->subTotal * ((float) $this->cgst_percent / 100), 2);
    }

    public function getGrandTotalProperty(): float
    {
        return round($this->subTotal + $this->sgstAmount + $this->cgstAmount, 2);
    }

    public function getNetAmountProperty(): float
    {
        return (float) $this->receipt_freight_amount + (float) $this->receipt_loading_charge + (float) $this->receipt_unloading_charge - (float) $this->receipt_advance_amount;
    }

    // ─── Navigation ───────────────────────────────────────────────────

    public function goToStep(int $step): void
    {
        if ($step >= 1 && $step <= 3) {
            // Auto cascade if target step is empty
            if ($step === 2 && !$this->invoiceDone) {
                $this->cascadeBiltyToInvoice();
            } elseif ($step === 3 && !$this->receiptDone) {
                $this->cascadeInvoiceToReceipt();
            }
            $this->currentStep = $step;
        }
    }

    public function nextStep(): void
    {
        if ($this->currentStep === 1) {
            $this->saveBilty();
        } elseif ($this->currentStep === 2) {
            $this->saveInvoice();
        }

        if ($this->currentStep < 3) {
            $this->goToStep($this->currentStep + 1);
        }
    }

    public function previousStep(): void
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    // ─── Data Cascading ───────────────────────────────────────────────

    private function cascadeBiltyToInvoice(): void
    {
        $this->inv_vehicle_no = $this->vehicle_no;
        $this->inv_lr_no = $this->lr_number;
        $this->inv_from = $this->bilty_from;
        $this->inv_to = $this->bilty_to;
        $this->bill_to_name = $this->consignor_name;
        $this->bill_to_address = $this->consignor_address;
        $this->bill_to_gst = $this->consignor_gst;
        $this->ship_to_name = $this->consignee_name;
        $this->ship_to_address = $this->consignee_address;
        $this->ship_to_gst = $this->consignee_gst;
        $this->ship_to_mobile = $this->consignee_mobile;
        $this->inv_freight_amount = $this->bilty_freight_amount;
        $this->inv_no_of_articles = $this->no_of_packages;
        $this->inv_total_weight = $this->actual_weight;
        $this->inv_eway_bill_no = $this->eway_bill_no;
        $this->inv_description_of_goods = $this->description_of_goods;
        $this->inv_remark = $this->bilty_remark;
    }

    private function cascadeInvoiceToReceipt(): void
    {
        $this->received_from = $this->bill_to_name ?: $this->consignor_name;
        $this->receipt_from = $this->inv_from ?: $this->bilty_from;
        $this->receipt_to = $this->inv_to ?: $this->bilty_to;
        $this->receipt_freight_amount = $this->inv_freight_amount ?: $this->bilty_freight_amount;
        $this->receipt_loading_charge = $this->loading_charge;
        $this->receipt_unloading_charge = $this->unloading_charge;
        $this->receipt_advance_amount = $this->advance_amount;
        $this->receipt_invoice_no = $this->invoice_number;
        $this->receipt_invoice_date = $this->invoice_date;
        $this->receipt_lr_no = $this->inv_lr_no ?: $this->lr_number;
        $this->receipt_lr_date = $this->lr_date;
        $this->total_packages = $this->inv_no_of_articles ?: $this->no_of_packages;
    }

    // ─── Save Methods ───────────────────────────────────────────────

    public function saveBilty(): void
    {
        $this->validate([
            'lr_number' => 'required',
            'lr_date' => 'required|date',
            'vehicle_no' => 'required',
            'bilty_from' => 'required',
            'bilty_to' => 'required',
            'consignor_name' => 'required',
            'consignee_name' => 'required',
            'bilty_freight_amount' => 'required|numeric',
        ]);

        $data = [
            'lr_number' => $this->lr_number,
            'lr_date' => $this->lr_date,
            'vehicle_no' => $this->vehicle_no,
            'bilty_from' => $this->bilty_from,
            'bilty_to' => $this->bilty_to,
            'consignor_name' => $this->consignor_name,
            'consignor_address' => $this->consignor_address,
            'consignor_mobile' => $this->consignor_mobile,
            'consignor_gst' => $this->consignor_gst,
            'consignee_name' => $this->consignee_name,
            'consignee_address' => $this->consignee_address,
            'consignee_mobile' => $this->consignee_mobile,
            'consignee_gst' => $this->consignee_gst,
            'gst_paid_by' => $this->gst_paid_by,
            'bilty_invoice_no' => $this->bilty_invoice_no,
            'description_of_goods' => $this->description_of_goods,
            'no_of_packages' => $this->no_of_packages,
            'actual_weight' => $this->actual_weight,
            'charged_weight' => $this->charged_weight,
            'bilty_freight_amount' => $this->bilty_freight_amount,
            'hamali_charges' => $this->hamali_charges,
            'bilty_charges' => $this->bilty_charges,
            'advance_amount' => $this->advance_amount,
            'eway_bill_no' => $this->eway_bill_no,
            'invoice_value' => $this->invoice_value,
            'bilty_rate' => $this->bilty_rate,
            'bilty_remark' => $this->bilty_remark,
            'bilty_total' => $this->biltyTotal,
        ];

        TripDocument::updateOrCreate(
            ['trip_id' => $this->tripId, 'document_type' => 'bilty'],
            [
                'document_number' => $this->lr_number,
                'document_date' => $this->lr_date,
                'data' => $data,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]
        );

        $this->biltyDone = true;
        session()->flash('doc_success', 'Bilty / LR saved successfully.');
    }

    public function saveInvoice(): void
    {
        $this->validate([
            'invoice_number' => 'required',
            'invoice_date' => 'required|date',
            'bill_to_name' => 'required',
            'inv_freight_amount' => 'required|numeric',
        ]);

        $data = [
            'invoice_number' => $this->invoice_number,
            'invoice_date' => $this->invoice_date,
            'inv_vehicle_no' => $this->inv_vehicle_no,
            'inv_lr_no' => $this->inv_lr_no,
            'inv_from' => $this->inv_from,
            'inv_to' => $this->inv_to,
            'bill_to_name' => $this->bill_to_name,
            'bill_to_address' => $this->bill_to_address,
            'bill_to_gst' => $this->bill_to_gst,
            'bill_to_city_state' => $this->bill_to_city_state,
            'bill_to_pin' => $this->bill_to_pin,
            'bill_from_name' => $this->bill_from_name,
            'bill_from_address' => $this->bill_from_address,
            'bill_from_gst' => $this->bill_from_gst,
            'bill_from_city_state' => $this->bill_from_city_state,
            'bill_from_pin' => $this->bill_from_pin,
            'bill_from_mobile' => $this->bill_from_mobile,
            'ship_to_name' => $this->ship_to_name,
            'ship_to_address' => $this->ship_to_address,
            'ship_to_gst' => $this->ship_to_gst,
            'ship_to_city_state' => $this->ship_to_city_state,
            'ship_to_pin' => $this->ship_to_pin,
            'ship_to_mobile' => $this->ship_to_mobile,
            'inv_freight_amount' => $this->inv_freight_amount,
            'loading_charge' => $this->loading_charge,
            'unloading_charge' => $this->unloading_charge,
            'sgst_percent' => $this->sgst_percent,
            'cgst_percent' => $this->cgst_percent,
            'sgst_amount' => $this->sgstAmount,
            'cgst_amount' => $this->cgstAmount,
            'sub_total' => $this->subTotal,
            'grand_total' => $this->grandTotal,
            'inv_description_of_goods' => $this->inv_description_of_goods,
            'inv_remark' => $this->inv_remark,
            'payment_paid_by' => $this->payment_paid_by,
            'inv_eway_bill_no' => $this->inv_eway_bill_no,
            'inv_no_of_articles' => $this->inv_no_of_articles,
            'inv_total_weight' => $this->inv_total_weight,
        ];

        TripDocument::updateOrCreate(
            ['trip_id' => $this->tripId, 'document_type' => 'invoice'],
            [
                'document_number' => $this->invoice_number,
                'document_date' => $this->invoice_date,
                'data' => $data,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]
        );

        $this->invoiceDone = true;
        session()->flash('doc_success', 'Tax Invoice saved successfully.');
    }

    public function saveReceipt(): void
    {
        $this->validate([
            'receipt_number' => 'required',
            'receipt_date' => 'required|date',
            'received_from' => 'required',
        ]);

        $data = [
            'receipt_number' => $this->receipt_number,
            'receipt_date' => $this->receipt_date,
            'received_from' => $this->received_from,
            'receipt_from' => $this->receipt_from,
            'receipt_to' => $this->receipt_to,
            'receipt_freight_amount' => $this->receipt_freight_amount,
            'receipt_loading_charge' => $this->receipt_loading_charge,
            'receipt_unloading_charge' => $this->receipt_unloading_charge,
            'receipt_advance_amount' => $this->receipt_advance_amount,
            'net_amount' => $this->netAmount,
            'ac_pay_amount' => $this->ac_pay_amount,
            'cash_amount' => $this->cash_amount,
            'cheque_no' => $this->cheque_no,
            'cheque_date' => $this->cheque_date,
            'bank_name' => $this->bank_name,
            'receipt_invoice_no' => $this->receipt_invoice_no,
            'receipt_invoice_date' => $this->receipt_invoice_date,
            'receipt_lr_no' => $this->receipt_lr_no,
            'receipt_lr_date' => $this->receipt_lr_date,
            'total_packages' => $this->total_packages,
        ];

        TripDocument::updateOrCreate(
            ['trip_id' => $this->tripId, 'document_type' => 'receipt'],
            [
                'document_number' => $this->receipt_number,
                'document_date' => $this->receipt_date,
                'data' => $data,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]
        );

        $this->receiptDone = true;
        session()->flash('doc_success', 'Money Receipt saved successfully.');
    }

    public function saveDraft(): void
    {
        if ($this->currentStep === 1) {
            $this->saveBilty();
        } elseif ($this->currentStep === 2) {
            $this->saveInvoice();
        } elseif ($this->currentStep === 3) {
            $this->saveReceipt();
        }
    }

    public function previewDocument(): void
    {
        $this->saveDraft();

        $routes = [
            1 => route('builty.print', $this->tripId),
            2 => route('invoices.print', $this->tripId),
            3 => route('receipts.print', $this->tripId),
        ];

        $url = $routes[$this->currentStep] ?? '#';
        $this->dispatch('openPreview', url: $url);
    }

    public function render()
    {
        return view('livewire.admin.trip.document-wizard');
    }
}

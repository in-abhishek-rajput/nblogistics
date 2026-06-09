<?php

namespace App\Livewire\Admin\Truck;

use App\Models\Truck;
use App\Models\TruckEmi;
use App\Models\TruckEmiPayment;
use Illuminate\Support\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class EmiBook extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public int $truckId;
    public $truck;

    public string $finance_company = '';
    public string $monthly_emi = '';
    public ?int $due_day = null;

    public ?int $editingPaymentId = null;
    public ?int $viewingPaymentId = null;
    public string $payment_due_date = '';
    public string $payment_date = '';
    public string $payment_amount = '';
    public string $payment_status = 'pending';
    public string $remarks = '';

    public ?int $editingEmiId = null;
    public string $edit_finance_company = '';
    public string $edit_monthly_emi = '';
    public ?int $edit_due_day = null;

    public int $perPage = 8;

    protected $listeners = [
        'openEmiBookPanel' => 'openPanel',
    ];

    public function mount(int $truckId)
    {
        $this->truckId = $truckId;
        $this->refreshData();
    }

    public function refreshData()
    {
        $this->truck = Truck::with('truckEmis.payments')->findOrFail($this->truckId);
        $this->resetPage();
    }

    public function openPanel()
    {
        $this->refreshData();
        $this->dispatch('openEmiBookOffcanvas');
    }

    public function showAddEmiModal()
    {
        $this->reset(['finance_company', 'monthly_emi', 'due_day']);
        $this->dispatch('showAddEmiModal');
    }

    public function createEmi()
    {
        $this->validate([
            'finance_company' => 'required|string|max:255',
            'monthly_emi' => 'required|numeric|min:1',
            'due_day' => 'required|integer|min:1|max:31',
        ]);

        $emi = TruckEmi::create([
            'truck_id' => $this->truckId,
            'finance_company' => $this->finance_company,
            'monthly_emi' => $this->monthly_emi,
            'due_day' => $this->due_day,
            'status' => 'pending',
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        $this->createEmiSchedule($emi);
        $this->dispatch('emiBookUpdated');
        $this->dispatch('closeModal', 'addEmiModal');
        $this->dispatch('openEmiBookOffcanvas');
    }

    public function showEditEmiPlanModal()
    {
        $currentEmi = $this->truck->truckEmis->last();

        if (! $currentEmi) {
            return;
        }

        $this->editingEmiId = $currentEmi->id;
        $this->edit_finance_company = $currentEmi->finance_company;
        $this->edit_monthly_emi = number_format($currentEmi->monthly_emi, 2, '.', '');
        $this->edit_due_day = $currentEmi->due_day;

        $this->dispatch('showEditEmiPlanModal');
    }

    public function updateEmi()
    {
        $this->validate([
            'edit_finance_company' => 'required|string|max:255',
            'edit_monthly_emi' => 'required|numeric|min:1',
            'edit_due_day' => 'required|integer|min:1|max:31',
        ]);

        $emi = TruckEmi::findOrFail($this->editingEmiId);

        $emi->update([
            'finance_company' => $this->edit_finance_company,
            'monthly_emi' => $this->edit_monthly_emi,
            'due_day' => $this->edit_due_day,
            'updated_by' => auth()->id(),
        ]);

        $this->refreshPendingSchedule($emi);
        $this->refreshData();
        $this->dispatch('emiBookUpdated');
        $this->dispatch('closeModal', 'editEmiPlanModal');
        $this->dispatch('openEmiBookOffcanvas');
    }

    protected function refreshPendingSchedule(TruckEmi $emi): void
    {
        $pendingPayments = $emi->payments()
            ->where('status', 'pending')
            ->orderBy('due_date')
            ->get();

        if ($pendingPayments->isEmpty()) {
            return;
        }

        $startDate = $this->resolveNextDueDate($emi->due_day);

        foreach ($pendingPayments as $index => $payment) {
            $dueDate = $startDate->copy()->addMonths($index);
            $dueDate->day(min($emi->due_day, $dueDate->daysInMonth));

            $payment->update([
                'due_date' => $dueDate->toDateString(),
                'amount' => $emi->monthly_emi,
                'updated_by' => auth()->id(),
            ]);
        }
    }

    protected function createEmiSchedule(TruckEmi $emi): void
    {
        $startDate = $this->resolveNextDueDate($this->due_day);

        for ($month = 0; $month < 12; $month++) {
            $dueDate = $startDate->copy()->addMonths($month);
            $dueDate->day(min($this->due_day, $dueDate->daysInMonth));

            TruckEmiPayment::create([
                'truck_emi_id' => $emi->id,
                'due_date' => $dueDate->toDateString(),
                'amount' => $emi->monthly_emi,
                'status' => 'pending',
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);
        }
    }

    protected function resolveNextDueDate(int $dueDay): Carbon
    {
        $today = Carbon::today();
        $year = $today->year;
        $month = $today->month;
        $day = min($dueDay, Carbon::create($year, $month, 1)->daysInMonth);
        $firstDue = Carbon::create($year, $month, $day);

        if ($firstDue->lessThanOrEqualTo($today)) {
            $firstDue->addMonth();
        }

        return $firstDue;
    }

    public function getPaymentsProperty()
    {
        $today = Carbon::today();

        return TruckEmiPayment::with('emi')
            ->whereHas('emi', fn ($query) => $query->where('truck_id', $this->truckId))
            ->whereYear('due_date', $today->year)
            ->whereMonth('due_date', $today->month)
            ->orderBy('due_date')
            ->paginate($this->perPage);
    }

    public function getSummaryProperty()
    {
        $currentEmi = $this->truck->truckEmis->last();
        $nextPayment = $this->truck->truckEmiPayments()
            ->where('truck_emi_payments.status', 'pending')
            ->orderBy('due_date')
            ->first();

        if (!$currentEmi) {
            return null;
        }

        return [
            'company' => $currentEmi->finance_company,
            'monthly_emi' => $currentEmi->monthly_emi,
            'status' => $nextPayment ? 'Pending' : 'Paid',
            'next_due' => $nextPayment ? $nextPayment->due_date->format('d M Y') : null,
        ];
    }

    public function getTypesProperty()
    {
        return config('truck.types');
    }

    public function editPayment(int $paymentId)
    {
        $payment = TruckEmiPayment::findOrFail($paymentId);

        $this->editingPaymentId = $payment->id;
        $this->payment_amount = number_format($payment->amount, 2, '.', '');
        $this->payment_due_date = $payment->due_date?->format('Y-m-d') ?? '';
        $this->payment_date = $payment->payment_date?->format('Y-m-d') ?? '';
        $this->payment_status = $payment->status;
        $this->remarks = $payment->remarks ?? '';

        $this->dispatch('showEditEmiModal');
    }

    public function updatePayment()
    {
        $this->validate([
            'payment_due_date' => 'required|date',
            'payment_amount' => 'required|numeric|min:0',
            'payment_status' => 'required|in:pending,paid',
            'payment_date' => 'nullable|date',
        ]);

        $payment = TruckEmiPayment::findOrFail($this->editingPaymentId);
        $payment->update([
            'due_date' => $this->payment_due_date,
            'amount' => $this->payment_amount,
            'status' => $this->payment_status,
            'payment_date' => $this->payment_date ?: null,
            'remarks' => $this->remarks,
            'updated_by' => auth()->id(),
        ]);

        $this->dispatch('emiBookUpdated');
        $this->dispatch('closeModal', 'editPaymentModal');
        $this->dispatch('openEmiBookOffcanvas');
        $this->refreshData();
    }

    public function viewPayment(int $paymentId)
    {
        $this->viewingPaymentId = $paymentId;
        $this->dispatch('showViewPaymentModal');
    }

    public function deletePayment(int $paymentId)
    {
        $payment = TruckEmiPayment::findOrFail($paymentId);
        $payment->delete();

        $this->refreshData();
        $this->dispatch('emiBookUpdated');
        $this->dispatch('openEmiBookOffcanvas');
    }

    public function markPaymentComplete(int $paymentId)
    {
        $payment = TruckEmiPayment::findOrFail($paymentId);
        $payment->update([
            'status' => 'paid',
            'payment_date' => Carbon::today()->toDateString(),
            'updated_by' => auth()->id(),
        ]);

        $this->refreshData();
        $this->dispatch('emiBookUpdated');
        $this->dispatch('openEmiBookOffcanvas');
    }

    public function render()
    {
        return view('livewire.admin.truck.emi-book', [
            'payments' => $this->payments,
            'summary' => $this->summary,
            'types' => $this->types,
        ]);
    }
}

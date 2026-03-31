<?php

namespace App\Livewire\Admin\Trip;

use App\Models\Trip;
use App\Models\TripAdvance;
use App\Models\TripCharge;
use App\Models\TripPayment;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

/**
 * ViewTrip Livewire Component
 *
 * Handles the complete lifecycle of a single trip view:
 *  - Displays all trip details (party, truck, driver, route, billing, KM, dates, status)
 *  - Manages the sequential status workflow with Bootstrap confirmation modal
 *  - Special "Complete" action requires end_date + end_km inputs before transition
 *  - All status changes record their respective timestamp columns
 *
 * Status Flow:
 *   pending → start → completed → pod_received → pod_submitted → settled
 *
 * Timestamp mapping:
 *   start         → start_date
 *   completed     → completed_date  (also saves end_date, end_km)
 *   pod_received  → pod_received_date
 *   pod_submitted → pod_submitted_date
 *   settled       → settled_date
 */
class ViewTrip extends Component
{
    // ─── Trip Identity ────────────────────────────────────────────────────────
    public int  $tripId;
    public Trip $trip;

    // ─── Confirm Modal State ──────────────────────────────────────────────────

    /** The status key pending confirmation e.g. 'start', 'pod_received' */
    public string $confirmAction = '';

    /**
     * Human-readable label shown inside the Bootstrap confirm modal body.
     * e.g. "Started", "POD Received"
     */
    public string $confirmLabel = '';

    // ─── "Complete Trip" Modal Fields ─────────────────────────────────────────

    /** End date — required only for the "Complete" action */
    public string $end_date = '';

    /** End KM reading — required only for the "Complete" action */
    public string $end_km = '';

    // ─── Processing Guard ─────────────────────────────────────────────────────

    /** Prevents double-clicks while a Livewire request is in flight */
    public bool $updatingStatus = false;

    // ─── Inline Form Visibility ───────────────────────────────────────────────

    public bool $showAdvanceForm = false;
    public bool $showChargeForm = false;
    public bool $showPaymentForm = false;

    // ─── Advance Form Fields ──────────────────────────────────────────────────

    public ?TripAdvance $editingAdvance = null;
    public float $advance_amount = 0;
    public string $advance_payment_method = 'cash';
    public string $advance_payment_date = '';
    public bool $advance_received_by_driver = false;
    public string $advance_notes = '';
    public bool $savingAdvance = false;

    // ─── Charge Form Fields ───────────────────────────────────────────────────

    public ?TripCharge $editingCharge = null;
    public string $charge_direction = 'add_to_bill';
    public string $charge_type = '';
    public float $charge_amount = 0;
    public string $charge_date = '';
    public string $charge_notes = '';
    public string $chargeTypeLabel = 'Charge Type';
    public bool $savingCharge = false;

    // ─── Payment Form Fields ──────────────────────────────────────────────────

    public ?TripPayment $editingPayment = null;
    public float $payment_amount = 0;
    public string $payment_payment_method = 'cash';
    public string $payment_payment_date = '';
    public bool $payment_received_by_driver = false;
    public string $payment_notes = '';
    public bool $savingPayment = false;

    // ─── Options ──────────────────────────────────────────────────────────────

    public array $paymentMethods = [
        'cash' => 'Cash',
        'cheque' => 'Cheque',
        'upi' => 'UPI',
        'bank_transfer' => 'Bank Transfer',
        'fuel' => 'Fuel',
        'others' => 'Others',
    ];

    public array $chargeDirections = [
        'add_to_bill' => 'Add to Bill',
        'reduce_from_bill' => 'Reduce from Bill',
    ];

    public array $chargeTypeOptions = [
        'toll' => 'Toll',
        'parking' => 'Parking',
        'loading_unloading' => 'Loading/Unloading',
        'others' => 'Others',
    ];

    protected $listeners = [
        'advanceUpdated' => 'reloadTrip',
        'chargeUpdated' => 'reloadTrip',
        'paymentUpdated' => 'reloadTrip',
        'deleteAdvance',
        'deleteCharge',
        'deletePayment',
        'flashMessage' => 'relayFlashMessage',
    ];

    // ─── Status Flow Definition ───────────────────────────────────────────────
    // Changed from `private` to `protected` so Livewire can correctly
    // serialize/hydrate these arrays across requests.

    /**
     * Maps each status value to the NEXT valid status.
     * Only forward, sequential transitions are allowed — no skipping.
     */
    protected array $statusFlow = [
        'pending'       => 'start',
        'start'         => 'completed',
        'completed'     => 'pod_received',
        'pod_received'  => 'pod_submitted',
        'pod_submitted' => 'settled',
    ];

    /**
     * Human-readable labels shown in the confirm modal and flash notices.
     */
    protected array $statusLabels = [
        'start'         => 'Started',
        'completed'     => 'Completed',
        'pod_received'  => 'POD Received',
        'pod_submitted' => 'POD Submitted',
        'settled'       => 'Settled',
    ];

    /**
     * Maps each action to the DB column that records its timestamp.
     * The 'completed' action additionally saves end_date + end_km (handled separately).
     */
    protected array $timestampColumns = [
        'start'         => 'start_date',
        'completed'     => 'completed_date',
        'pod_received'  => 'pod_received_date',
        'pod_submitted' => 'pod_submitted_date',
        'settled'       => 'settled_date',
    ];

    // ─── Lifecycle ────────────────────────────────────────────────────────────

    public function mount(int $tripId): void
    {
        $this->tripId = $tripId;
        $this->reloadTrip();
    }

    // ─── Private Helpers ──────────────────────────────────────────────────────

    /**
     * Reloads the trip model with all needed relations.
     * Must be called after every status update so the view reflects fresh data.
     */
    private function reloadTrip(): void
    {
        $this->trip = Trip::with(['party', 'truck', 'driver', 'advances', 'charges', 'payments'])->findOrFail($this->tripId);
    }

    /**
     * Returns TRUE if the given action button should be visible.
     * Logic: current trip status must map to exactly $action in $statusFlow.
     */
    public function canShowButton(string $action): bool
    {
        return ($this->statusFlow[$this->trip->status] ?? null) === $action;
    }

    /**
     * Returns the 0-based index of the current status in the ordered progression.
     * Used by the tracker UI to determine which steps are "done" (green) vs pending (grey).
     *
     * Order: pending=0, start=1, completed=2, pod_received=3, pod_submitted=4, settled=5
     */
    public function getStatusIndex(): int
    {
        $order = ['pending', 'start', 'completed', 'pod_received', 'pod_submitted', 'settled'];
        $index = array_search($this->trip->status, $order, true);
        return $index === false ? 0 : (int) $index;
    }

    // ─── Modal Triggers (called from Blade buttons) ───────────────────────────

    /**
     * Validates the transition, stores confirm state, then opens the
     * Bootstrap confirmation modal via JS dispatch.
     * Used for: start, pod_received, pod_submitted, settled.
     */
    public function confirmStatusChange(string $action): void
    {
        // Guard: only allow the valid next-step transition
        if (($this->statusFlow[$this->trip->status] ?? null) !== $action) {
            session()->flash('error', 'Invalid status transition.');
            return;
        }

        $this->confirmAction = $action;
        $this->confirmLabel  = $this->statusLabels[$action];

        $this->dispatch('show-confirm-modal');
    }

    /**
     * Opens the Bootstrap "Complete Trip" modal.
     * Resets fields and clears previous validation errors so the form is always fresh.
     */
    public function openCompleteModal(): void
    {
        $this->end_date = '';
        $this->end_km   = '';
        $this->resetErrorBag();

        $this->dispatch('show-complete-modal');
    }

    /**
     * Resets all modal-related state and dispatches the close event to JS.
     * Called: after successful status update, on Cancel, or on modal dismiss.
     */
    public function closeModals(): void
    {
        $this->confirmAction = '';
        $this->confirmLabel  = '';
        $this->end_date      = '';
        $this->end_km        = '';
        $this->resetErrorBag();

        $this->dispatch('close-modals');
    }

    // ─── Status Update Actions ────────────────────────────────────────────────

    /**
     * Processes status changes for: start, pod_received, pod_submitted, settled.
     * Called when user clicks "Yes, Confirm" inside the Bootstrap confirm modal.
     *
     * Flow:
     *   1. Re-validates confirmAction is still a legal transition (race-condition guard)
     *   2. Updates `status` and the corresponding timestamp column
     *   3. Reloads trip, closes modals, flashes success
     */
    public function updateStatus(): void
    {
        if (!$this->confirmAction) {
            return;
        }

        // Re-validate: transition must still be legal (guards against double-submit / stale page)
        if (($this->statusFlow[$this->trip->status] ?? null) !== $this->confirmAction) {
            session()->flash('error', 'Invalid status transition. Please refresh the page.');
            $this->closeModals();
            return;
        }

        $this->updatingStatus = true;

        try {
            DB::transaction(function () {
                $timestampCol = $this->timestampColumns[$this->confirmAction];

                $this->trip->update([
                    'status'      => $this->confirmAction,
                    $timestampCol => now(),
                    'updated_by'  => auth()->id(),
                ]);
            });

            $label = $this->statusLabels[$this->confirmAction];
            $this->reloadTrip();
            $this->closeModals();
            session()->flash('success', "Trip marked as \"{$label}\" successfully.");

        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        } finally {
            $this->updatingStatus = false;
        }
    }

    /**
     * Processes the "Complete" action which requires extra user inputs.
     *
     * Validation:
     *   end_date — required, valid date, must be on or after start_date
     *   end_km   — required, integer, must be >= start_km (odometer never goes backward)
     *
     * DB changes:
     *   status         → 'completed'
     *   end_date       → user input (the actual trip end date)
     *   end_km         → user input (odometer at end)
     *   completed_date → now() (system timestamp of when status was set)
     *   updated_by     → current auth user
     */
    public function completeTrip(): void
    {
        // Build dynamic validation rules from actual trip data
        $minKm     = (int) ($this->trip->start_km ?? 0);
        $startDate = $this->trip->start_date
                       ? $this->trip->start_date->format('Y-m-d')
                       : '1970-01-01';

        $this->validate(
            [
                'end_date' => ['required', 'date', "after_or_equal:{$startDate}"],
                'end_km'   => ['required', 'integer', "min:{$minKm}"],
            ],
            [
                'end_date.required'       => 'End date is required.',
                'end_date.after_or_equal' => 'End date must be on or after the trip start date (' . $this->trip->start_date?->format('d M Y') . ').',
                'end_km.required'         => 'End KM reading is required.',
                'end_km.min'              => "End KM must be at least {$minKm} km (start reading).",
            ]
        );

        // Status guard: trip must be in 'start' to allow completion
        if ($this->trip->status !== 'start') {
            session()->flash('error', 'Trip can only be completed when it is in "Started" status.');
            $this->closeModals();
            return;
        }

        $this->updatingStatus = true;

        try {
            DB::transaction(function () {
                $this->trip->update([
                    'status'         => 'completed',
                    'end_date'       => $this->end_date,
                    'end_km'         => (int) $this->end_km,
                    'completed_date' => now(),
                    'updated_by'     => auth()->id(),
                ]);
            });

            $this->reloadTrip();
            $this->closeModals();
            session()->flash('success', 'Trip completed successfully.');

        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        } finally {
            $this->updatingStatus = false;
        }
    }

    // ─── Delete Methods ───────────────────────────────────────────────────────

    public function deleteAdvance(int $advanceId): void
    {
        try {
            $advance = TripAdvance::where('trip_id', $this->tripId)->findOrFail($advanceId);
            $advance->delete();
            $this->reloadTrip();
            session()->flash('success', 'Advance deleted successfully.');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete advance. Please try again.');
        }
    }

    public function deleteCharge(int $chargeId): void
    {
        try {
            $charge = TripCharge::where('trip_id', $this->tripId)->findOrFail($chargeId);
            $charge->delete();
            $this->reloadTrip();
            session()->flash('success', 'Charge deleted successfully.');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete charge. Please try again.');
        }
    }

    public function deletePayment(int $paymentId): void
    {
        try {
            $payment = TripPayment::where('trip_id', $this->tripId)->findOrFail($paymentId);
            $payment->delete();
            $this->reloadTrip();
            session()->flash('success', 'Payment deleted successfully.');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete payment. Please try again.');
        }
    }

     // ─── Inline Form Methods ──────────────────────────────────────────────────
 
     public function updatedChargeDirection(): void
     {
         $this->chargeTypeLabel = $this->charge_direction === 'add_to_bill' ? 'Charge Type' : 'Deduction Type';
     }
 
     public function openAdvanceForm(): void
     {
         $this->resetAdvanceForm();
         $this->showAdvanceForm = true;
         $this->showChargeForm = false;
         $this->showPaymentForm = false;
     }
 
     public function editAdvance(int $advanceId): void
     {
         $this->editingAdvance = TripAdvance::where('trip_id', $this->tripId)->findOrFail($advanceId);
         $this->advance_amount = $this->editingAdvance->amount;
         $this->advance_payment_method = $this->editingAdvance->payment_method;
         $this->advance_payment_date = $this->editingAdvance->payment_date->format('Y-m-d');
         $this->advance_received_by_driver = $this->editingAdvance->received_by_driver;
         $this->advance_notes = $this->editingAdvance->notes ?? '';
         $this->showAdvanceForm = true;
         $this->showChargeForm = false;
         $this->showPaymentForm = false;
     }
 
     public function saveAdvance(): void
     {
         $this->validateAdvance();
         $this->savingAdvance = true;
 
         try {
             if ($this->editingAdvance) {
                 $this->editingAdvance->update($this->getAdvanceData());
                 $message = 'Advance updated successfully.';
             } else {
                 TripAdvance::create(array_merge($this->getAdvanceData(), ['trip_id' => $this->tripId]));
                 $message = 'Advance added successfully.';
             }
 
             $this->resetAdvanceForm();
             $this->reloadTrip();
             session()->flash('success', $message);
 
         } catch (\Exception $e) {
             session()->flash('error', 'Failed to save advance. Please try again.');
         } finally {
             $this->savingAdvance = false;
         }
     }
 
     public function cancelAdvanceForm(): void
     {
         $this->resetAdvanceForm();
     }
 
     private function resetAdvanceForm(): void
     {
         $this->editingAdvance = null;
         $this->advance_amount = 0;
         $this->advance_payment_method = 'cash';
         $this->advance_payment_date = '';
         $this->advance_received_by_driver = false;
         $this->advance_notes = '';
         $this->showAdvanceForm = false;
         $this->resetErrorBag();
     }
 
     private function validateAdvance(): void
     {
         $this->validate([
             'advance_amount' => 'required|numeric|min:0.01|max:99999999.99',
             'advance_payment_method' => 'required|string|in:' . implode(',', array_keys($this->paymentMethods)),
             'advance_payment_date' => 'required|date|before_or_equal:today',
             'advance_received_by_driver' => 'boolean',
             'advance_notes' => 'nullable|string|max:500',
         ], [
             'advance_amount.required' => 'Advance amount is required.',
             'advance_amount.min' => 'Amount must be greater than zero.',
             'advance_payment_method.required' => 'Payment method is required.',
             'advance_payment_date.required' => 'Payment date is required.',
             'advance_payment_date.before_or_equal' => 'Payment date cannot be in the future.',
         ]);
     }
 
     private function getAdvanceData(): array
     {
         return [
             'amount' => $this->advance_amount,
             'payment_method' => $this->advance_payment_method,
             'payment_date' => $this->advance_payment_date,
             'received_by_driver' => $this->advance_received_by_driver,
             'notes' => $this->advance_notes,
         ];
     }
 
     public function openChargeForm(): void
     {
         $this->resetChargeForm();
         $this->showChargeForm = true;
         $this->showAdvanceForm = false;
         $this->showPaymentForm = false;
     }
 
     public function editCharge(int $chargeId): void
     {
         $this->editingCharge = TripCharge::where('trip_id', $this->tripId)->findOrFail($chargeId);
         $this->charge_direction = $this->editingCharge->charge_direction;
         $this->charge_type = $this->editingCharge->charge_type;
         $this->charge_amount = $this->editingCharge->amount;
         $this->charge_date = $this->editingCharge->date->format('Y-m-d');
         $this->charge_notes = $this->editingCharge->notes ?? '';
         $this->updatedChargeDirection();
         $this->showChargeForm = true;
         $this->showAdvanceForm = false;
         $this->showPaymentForm = false;
     }
 
     public function saveCharge(): void
     {
         $this->validateCharge();
         $this->savingCharge = true;
 
         try {
             if ($this->editingCharge) {
                 $this->editingCharge->update($this->getChargeData());
                 $message = 'Charge updated successfully.';
             } else {
                 TripCharge::create(array_merge($this->getChargeData(), ['trip_id' => $this->tripId]));
                 $message = 'Charge added successfully.';
             }
 
             $this->resetChargeForm();
             $this->reloadTrip();
             session()->flash('success', $message);
 
         } catch (\Exception $e) {
             session()->flash('error', 'Failed to save charge. Please try again.');
         } finally {
             $this->savingCharge = false;
         }
     }
 
     public function cancelChargeForm(): void
     {
         $this->resetChargeForm();
     }
 
     private function resetChargeForm(): void
     {
         $this->editingCharge = null;
         $this->charge_direction = 'add_to_bill';
         $this->charge_type = '';
         $this->charge_amount = 0;
         $this->charge_date = '';
         $this->charge_notes = '';
         $this->chargeTypeLabel = 'Charge Type';
         $this->showChargeForm = false;
         $this->resetErrorBag();
     }
 
     private function validateCharge(): void
     {
         $this->validate([
             'charge_direction' => 'required|string|in:' . implode(',', array_keys($this->chargeDirections)),
             'charge_type' => 'required|string|in:' . implode(',', array_keys($this->chargeTypeOptions)),
             'charge_amount' => 'required|numeric|min:0.01|max:99999999.99',
             'charge_date' => 'required|date|before_or_equal:today',
             'charge_notes' => 'nullable|string|max:500',
         ], [
             'charge_direction.required' => 'Charge direction is required.',
             'charge_type.required' => 'Charge type is required.',
             'charge_amount.required' => 'Charge amount is required.',
             'charge_amount.min' => 'Amount must be greater than zero.',
             'charge_date.required' => 'Charge date is required.',
             'charge_date.before_or_equal' => 'Charge date cannot be in the future.',
         ]);
     }
 
     private function getChargeData(): array
     {
         return [
             'charge_direction' => $this->charge_direction,
             'charge_type' => $this->charge_type,
             'amount' => $this->charge_amount,
             'date' => $this->charge_date,
             'notes' => $this->charge_notes,
         ];
     }
 
     public function openPaymentForm(): void
     {
         $this->resetPaymentForm();
         $this->showPaymentForm = true;
         $this->showAdvanceForm = false;
         $this->showChargeForm = false;
     }
 
     public function editPayment(int $paymentId): void
     {
         $this->editingPayment = TripPayment::where('trip_id', $this->tripId)->findOrFail($paymentId);
         $this->payment_amount = $this->editingPayment->amount;
         $this->payment_payment_method = $this->editingPayment->payment_method;
         $this->payment_payment_date = $this->editingPayment->payment_date->format('Y-m-d');
         $this->payment_received_by_driver = $this->editingPayment->received_by_driver;
         $this->payment_notes = $this->editingPayment->notes ?? '';
         $this->showPaymentForm = true;
         $this->showAdvanceForm = false;
         $this->showChargeForm = false;
     }
 
     public function savePayment(): void
     {
         $this->validatePayment();
         $this->savingPayment = true;
 
         try {
             if ($this->editingPayment) {
                 $this->editingPayment->update($this->getPaymentData());
                 $message = 'Payment updated successfully.';
             } else {
                 TripPayment::create(array_merge($this->getPaymentData(), ['trip_id' => $this->tripId]));
                 $message = 'Payment added successfully.';
             }
 
             $this->resetPaymentForm();
             $this->reloadTrip();
             session()->flash('success', $message);
 
         } catch (\Exception $e) {
             session()->flash('error', 'Failed to save payment. Please try again.');
         } finally {
             $this->savingPayment = false;
         }
     }
 
     public function cancelPaymentForm(): void
     {
         $this->resetPaymentForm();
     }
 
     private function resetPaymentForm(): void
     {
         $this->editingPayment = null;
         $this->payment_amount = 0;
         $this->payment_payment_method = 'cash';
         $this->payment_payment_date = '';
         $this->payment_received_by_driver = false;
         $this->payment_notes = '';
         $this->showPaymentForm = false;
         $this->resetErrorBag();
     }
 
     private function validatePayment(): void
     {
         $this->validate([
             'payment_amount' => 'required|numeric|min:0.01|max:99999999.99',
             'payment_payment_method' => 'required|string|in:' . implode(',', array_keys($this->paymentMethods)),
             'payment_payment_date' => 'required|date|before_or_equal:today',
             'payment_received_by_driver' => 'boolean',
             'payment_notes' => 'nullable|string|max:500',
         ], [
             'payment_amount.required' => 'Payment amount is required.',
             'payment_amount.min' => 'Amount must be greater than zero.',
             'payment_payment_method.required' => 'Payment method is required.',
             'payment_payment_date.required' => 'Payment date is required.',
             'payment_payment_date.before_or_equal' => 'Payment date cannot be in the future.',
         ]);
     }
 
     private function getPaymentData(): array
     {
         return [
             'amount' => $this->payment_amount,
             'payment_method' => $this->payment_payment_method,
             'payment_date' => $this->payment_payment_date,
             'received_by_driver' => $this->payment_received_by_driver,
             'notes' => $this->payment_notes,
         ];
     }
 
     // ─── Render ───────────────────────────────────────────────────────────────

    public function render()
    {
        // All button-visibility logic stays here — blade stays display-only.
        return view('livewire.admin.trip.view-trip', [
            'trip'                => $this->trip,
            'statusIndex'         => $this->getStatusIndex(),
            'canShowStart'        => $this->canShowButton('start'),
            'canShowComplete'     => $this->canShowButton('completed'),
            'canShowPodReceived'  => $this->canShowButton('pod_received'),
            'canShowPodSubmitted' => $this->canShowButton('pod_submitted'),
            'canShowSettled'      => $this->canShowButton('settled'),
        ]);
    }
}
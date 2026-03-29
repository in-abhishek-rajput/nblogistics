<?php

namespace App\Livewire\Admin\Trip;

use App\Models\Trip;
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
        $this->trip = Trip::with(['party', 'truck', 'driver'])->findOrFail($this->tripId);
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
            session()->flash('error', 'Failed to update trip status. Please try again.');
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
            session()->flash('error', 'Failed to complete trip. Please try again.');
        } finally {
            $this->updatingStatus = false;
        }
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
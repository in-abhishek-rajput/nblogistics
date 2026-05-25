# Trip Module Understanding

## Module Overview
This module implements trip lifecycle, billing, and financial operations with Livewire components under `app/Livewire/Admin/Trip/` and Blade views under `resources/views/livewire/admin/trip/`.

Core domains:
- Trip master data (`Trip`) with party/truck/driver relation
- Trip status progression (`pending -> start -> completed -> pod_received -> pod_submitted -> settled`)
- Financial sub-records:
  - `TripAdvance`
  - `TripCharge`
  - `TripPayment`
  - `TripExpense`
- List + CRUD surfaces for trips and expenses

## Component-by-Component Reference

### `AddTrip.php`
Purpose:
- Creates a new trip record from add-trip offcanvas form.

Key public properties:
- Base fields: `party_id`, `truck_id`, `origin`, `destination`, `start_date`, `start_km`
- Billing fields: `billing_type` (default `fixed`), `freight_amount`, `per_unit_amount`, `unit`
- Optional metadata: `lr_number`, `material_name`, `note`

Key logic:
- `rules()` has dynamic billing validation:
  - `fixed` -> `freight_amount` required
  - otherwise -> `per_unit_amount` and `unit` required
- `save()`:
  - validates
  - computes `freight_amount` for non-fixed
  - sets `pending_freight_amount = freight_amount`
  - sets `created_by`, resolves `driver_id` from selected truck
  - creates trip and emits `tripAdded`
- `updatedUnit()` and `updatedBillingType()` control live freight behavior
- `calculateFreight()` computes `per_unit_amount * unit`

Data providers:
- `getPartiesProperty()` -> `Party::active()`
- `getTrucksProperty()` -> `Truck::active()`
- `getDriversProperty()` -> `Driver::active()`
- `getBillingTypesProperty()` -> `config('trip.billing_types')`

### `EditTrip.php`
Purpose:
- Updates an existing trip in edit offcanvas.

Differences from AddTrip:
- loads existing trip in `mount($tripId)`
- uses `updated_by` in `save()`
- validation `start_date` does not include `before_or_equal:today` (unlike AddTrip)
- `calculateFreight()` is `private`

Shared behavior:
- same dynamic billing rules
- recalculates freight for non-fixed
- sets `pending_freight_amount` from freight amount
- updates `driver_id` from truck selection

### `ListTrips.php`
Purpose:
- Paginated trip table with search/filter/sort + delete + open view/edit panels.

Key behaviors:
- search/filter inputs reset pagination
- dynamic sorting via `sortBy($column)`
- open actions:
  - `viewTrip($id)` dispatches `showViewOffcanvas`
  - `editTrip($id)` dispatches `showEditOffcanvas`
- soft delete path in `deleteTrip()` sets `deleted_by` then `delete()`
- listens to `tripAdded`, `tripUpdated`, `flashMessage`

Query layer:
- `getTripsProperty()` uses model scopes:
  - `search($search)`
  - `status($statusFilter)`
  - `billingType($billingTypeFilter)`

### `ViewTrip.php`
Purpose:
- Complete trip details page with status transitions and inline financial CRUD.

Status workflow:
- controlled by:
  - `$statusFlow`
  - `$statusLabels`
  - `$timestampColumns`
- guards transition sequence in `confirmStatusChange()` and `updateStatus()`
- `completeTrip()` has extra validation for `end_date` and `end_km`

Financial logic:
- inline forms for advances/charges/payments/expenses with open/edit/save/cancel methods
- `updatePendingFreightAmount()` recomputes:
  - total advances
  - total payments
  - charge net effect (`add_to_bill` positive, `reduce_from_bill` negative)
  - pending freight: `freight_amount - advances - payments + net_charges`
  - profit: `pendingFreight - totalExpenses`
  - both clamped with `max(0, value)`

Important note:
- Expenses reduce `profit`, but do not reduce `pending_freight_amount` in current formula.

Event listeners:
- refresh/recompute hooks: `advanceUpdated`, `chargeUpdated`, `paymentUpdated`, `expenseUpdated`, and delete variants
- `flashMessage` routed via `relayFlashMessage` listener entry (method should exist in class/view integration)

### `AddExpense.php` and `EditExpense.php`
Purpose:
- separate generic expense create/edit flow (outside ViewTrip inline forms).

Highlights:
- strict typed properties
- shared payment/expense option arrays
- date validation (`before_or_equal:today`)
- emits `expenseAdded`/`expenseUpdated` and close-modal events

### `ListExpenses.php`
Purpose:
- paginated expense listing with multi-field search and sorting.

Query behavior:
- eager loads `trip.party` and `trip.truck`
- search across `expense_type`, `notes`, party name, trip route
- optional `payment_mode` and date filter
- listens for `expenseAdded`, `expenseUpdated`, `flashMessage`

### Modal components
- `TripAdvanceModal.php`
- `TripChargeModal.php`
- `TripPaymentModal.php`
- `TripExpenseModal.php`

Purpose:
- modal-based CRUD for finance records scoped to a trip.

Shared structure:
- open/create/edit/delete methods
- centralized `rules()` and `messages()`
- emits refresh events (`advanceUpdated`, `chargeUpdated`, `paymentUpdated`, `expenseUpdated`)

Critical issue:
- `TripChargeModal::openChargeModal()` contains `dd($tripId);` which halts execution and breaks modal flow.

## Add Trip Form Mapping (`add-trip.blade.php`)
Path: `resources/views/livewire/admin/trip/add-trip.blade.php`

Form structure:
1. Party select -> `wire:model="party_id"`
2. Truck select -> `wire:model="truck_id"`
3. Route fields -> `origin`, `destination`
4. Optional metadata -> `lr_number`, `material_name`, `note`
5. Billing section:
   - billing type select -> `wire:model.live="billing_type"`
   - if fixed: editable `freight_amount`
   - else: `per_unit_amount`, `unit`, read-only calculated `freight_amount`
6. Trip start info -> `start_date`, `start_km`
7. Submit button -> loading state tied to `save`

Validation UI:
- each field has `@error(...)` and `is-invalid` class binding.

Binding caveat:
- multiple inputs use both `wire:model` and explicit `value="{{ ... }}"`.
- In Livewire, explicit `value` is usually redundant and can cause confusion during rerenders.

## Status and Financial Lifecycle

Trip creation:
- created in pending state (assumed at model/default level)
- freight baseline set from fixed or computed per-unit mode
- pending freight initialized to freight amount

Status transitions:
- `pending -> start`
- `start -> completed` (requires end date + end km)
- `completed -> pod_received`
- `pod_received -> pod_submitted`
- `pod_submitted -> settled`

Financial adjustments over lifecycle:
- Advances and payments reduce pending amount
- Charges can add or reduce bill
- Expenses impact profit calculation

## Events Matrix

Primary emitted events:
- Trip create/update: `tripAdded`, `tripUpdated`
- Expense create/update: `expenseAdded`, `expenseUpdated`
- Finance modals: `advanceUpdated`, `chargeUpdated`, `paymentUpdated`, `expenseUpdated`
- UI: `show...` and `close...` modal/offcanvas events
- Flash: `flashMessage` with `(type, message)` payload

Primary listeners:
- `ListTrips`: `tripAdded`, `tripUpdated`, `flashMessage`
- `ListExpenses`: `expenseAdded`, `expenseUpdated`, `flashMessage`
- `ViewTrip`: finance update/delete events for recomputation

## Validation Matrix (High-Level)

Common patterns:
- amounts: numeric, min > 0, max cap
- dates: mostly `before_or_equal:today`
- selects: `in:` from local option arrays/config

Notable inconsistencies:
- `AddTrip.start_date` enforces `before_or_equal:today`
- `EditTrip.start_date` currently allows any date
- charge type validation differs between `TripChargeModal` (generic required string) and `ViewTrip` inline form (`in:` constrained)

## Known Issues and Risks

1. Blocking debug code
- `app/Livewire/Admin/Trip/TripChargeModal.php`: `dd($tripId)` in `openChargeModal()`.

2. Duplicate finance CRUD logic
- Finance records are handled both in dedicated modal components and in large inline methods inside `ViewTrip`.
- Increases maintenance overhead and drift risk.

3. Mixed messaging patterns
- Some components use `session()->flash(...)`, others dispatch `flashMessage` events.
- Leads to inconsistent UX and integration complexity.

4. Livewire binding redundancy
- `wire:model` combined with hardcoded `value="{{ ... }}"` in `add-trip.blade.php` can create stale-value confusion.

5. Freight/profit logic coupling
- Profit and pending freight recomputation currently centralized in `ViewTrip::updatePendingFreightAmount()` only.
- Any writes outside this flow may produce temporary mismatches unless recompute is triggered.

## Suggested Improvement Backlog

1. Remove debug halt
- delete `dd($tripId)` from `TripChargeModal`.

2. Standardize feedback strategy
- pick one pattern for user messages (event-dispatched flash vs session flash).

3. Consolidate finance services
- extract repeated finance CRUD + validation logic into reusable actions/service classes.

4. Normalize validation contracts
- align `AddTrip` and `EditTrip` date constraints where business rule requires consistency.

5. Clean Livewire bindings
- remove explicit `value` attributes from `wire:model` inputs in add/edit trip forms unless there is a specific technical need.

6. Add targeted tests
- status transition guard tests
- freight/pending/profit recompute tests
- billing mode switch behavior tests
- modal open/save/delete event tests

## Quick Navigation
- `app/Livewire/Admin/Trip/AddTrip.php`
- `app/Livewire/Admin/Trip/EditTrip.php`
- `app/Livewire/Admin/Trip/ListTrips.php`
- `app/Livewire/Admin/Trip/ViewTrip.php`
- `app/Livewire/Admin/Trip/TripAdvanceModal.php`
- `app/Livewire/Admin/Trip/TripChargeModal.php`
- `app/Livewire/Admin/Trip/TripPaymentModal.php`
- `app/Livewire/Admin/Trip/TripExpenseModal.php`
- `app/Livewire/Admin/Trip/AddExpense.php`
- `app/Livewire/Admin/Trip/EditExpense.php`
- `app/Livewire/Admin/Trip/ListExpenses.php`
- `resources/views/livewire/admin/trip/add-trip.blade.php`

# Plan: Update Trip Module Date Validation and Add Driver Selection

## TL;DR
Remove date validation restrictions from start_date in AddTrip (and align EditTrip) to accept any date with default value of today. Add a driver dropdown UI after the truck dropdown that allows manual driver override while defaulting to the truck's assigned driver.

---

## Steps

### Phase 1: Date Validation Update
1. Modify `app/Livewire/Admin/Trip/AddTrip.php` `rules()` method
   - Remove `before_or_equal:today` from `start_date` validation
   - Keep: `'required|date'` only
   - This removes past/future date restriction entirely
   
2. Modify `app/Livewire/Admin/Trip/EditTrip.php` `rules()` method (for consistency)
   - Currently allows any date; no change needed unless we want explicit `'required|date'` only
   - Ensure it's consistent with AddTrip
   
3. Verify UI default value (in blade templates)
   - `add-trip.blade.php` and `edit-trip.blade.php` should display today as default via Livewire property initialization
   - No blade changes needed if currently showing today via `value="{{ date('Y-m-d') }}"` or similar

---

### Phase 2: Driver Dropdown Implementation
1. Add public property to components (`AddTrip.php` and `EditTrip.php`)
   - `public $driver_id = null;` (independent public property)
   - During mount (EditTrip) and init (AddTrip), set to null or load existing value
   
2. Add public property getter for drivers list
   - `getDriversProperty()` already exists but is unused
   - Ensure it returns `Driver::active()->orderBy('name')->get()`
   
3. Modify save() logic in both components
   - Use manually selected `driver_id` value from form
   - Validation: `'driver_id' => 'required|exists:drivers,id'` added to rules()
   
4. Update Blade templates (`add-trip.blade.php` and `edit-trip.blade.php`)
   - Add driver select dropdown AFTER truck dropdown
   - Wire: `wire:model="driver_id"`
   - Iterate over `$drivers` to populate options
   - Add same error validation styling as other fields

---

### Phase 3: Consistency Alignment
- Ensure EditTrip behavior matches AddTrip for both date and driver handling
- Truck and driver selections are completely independent (no cross-reactivity)

---

## Relevant Files
- `app/Livewire/Admin/Trip/AddTrip.php` — rules() method (date validation, driver validation), save() method
- `app/Livewire/Admin/Trip/EditTrip.php` — rules() method (consistency), mount() driver initialization, save() method
- `resources/views/livewire/admin/trip/add-trip.blade.php` — add driver dropdown after truck, wire bindings
- `resources/views/livewire/admin/trip/edit-trip.blade.php` — add driver dropdown after truck, wire bindings
- `app/Models/Driver.php` — verify active() scope exists
- `app/Models/Truck.php` — verify driver relationship exists

---

## Verification Checklist
1. **Date validation removal**
   - [ ] Submit AddTrip form with past date → should accept
   - [ ] Submit AddTrip form with future date → should accept
   - [ ] Submit AddTrip form with today → should accept
   - [ ] Default value displayed is today (no UI changes needed, just confirm)
   
2. **Driver dropdown functionality (independent selection)**
   - [ ] Truck dropdown populated with active trucks
   - [ ] Driver dropdown populated with active drivers
   - [ ] Truck and driver can be selected independently
   - [ ] Form saves with selected truck_id and driver_id
   - [ ] Edit existing trip → both truck and driver dropdowns show currently assigned values
   - [ ] Validation error if driver_id omitted → form rejects
   - [ ] Changing truck selection does NOT affect driver dropdown
   - [ ] Changing driver selection does NOT affect truck dropdown
   
3. **Integration tests**
   - [ ] Create trip with truck A and driver X → saves both correctly
   - [ ] Create trip with truck B and driver Y → saves both correctly (independent of truck's default driver)
   - [ ] Edit trip, change truck to B but keep driver Y → saves with truck B and driver Y
   - [ ] Validation rule `driver_id required|exists:drivers,id` enforced

---

## Decisions
- Date validation removed entirely; any valid date accepted (user responsibility for correctness)
- Driver and truck dropdowns are independent selections (no auto-update cross-reactivity)
- Driver validation added to rules() for data integrity
- Both AddTrip and EditTrip aligned for consistency

---

## Implementation Notes
- Requirement clarified: accept any valid date (no past/future restriction), default is today
- Driver dropdown is independent from truck dropdown
- User can select any truck with any driver (no automatic driver assignment based on truck)
- No reactive methods needed for truck-driver coupling

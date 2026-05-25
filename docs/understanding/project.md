# Project Understanding Document

## 1. Project Overview
This project is a transportation and logistics management system built with **Laravel 12**, **PHP 8.2+**, and **Livewire 3**. It is designed to manage the core operations of a transport business, including customers (Parties), vehicles (Trucks), staff (Drivers), and operations (Trips, Expenses, Finances).

## 2. Architecture
The application follows a modernized Laravel architecture heavily relying on Livewire for frontend reactivity:
- **Directory Structure:**
  - `app/Models/`: Contains Eloquent models and business logic via model scopes and event hooks (boot methods).
  - `app/Http/Controllers/Admin/`: Very thin controllers (e.g., `TripsController`) that primarily return initial Blade views without much business logic.
  - `app/Livewire/Admin/`: The core application controllers. Components like `AddTrip`, `ListTrips` handle form submissions, validation, real-time UI updates, and data fetching.
  - `resources/views/livewire/admin/`: Blade views for the Livewire components, often utilizing Modals and Offcanvas elements for CRUD operations.
- **Pattern Used:** 
  - **Component-Driven MVC:** The traditional View and Controller layers are merged into Livewire components. 
  - **Fat Models:** Business rules (like updating related entity statuses) and query building (Scopes) are kept in the Models.
  - **Event-Driven UI:** Extensive use of Livewire event dispatching (`dispatch('tripAdded')`, `dispatch('flashMessage')`) to communicate between components.

## 3. Module Breakdown

### Party (Customer)
- **Purpose:** Manages the clients/customers who request transport services.
- **Fields:** `name, email, mobile, address, status, opening_balance, opening_balance_date`.
- **Relationships:** A Party has many Trips.
- **Flow:** Basic CRUD via Livewire (`AddParty`, `EditParty`, `ListParties`).

### Truck
- **Purpose:** Manages the fleet of vehicles.
- **Fields:** `truck_number, truck_type, ownership, status, driver_id`.
- **Relationships:** Belongs to a Driver (default driver), has many Trips.
- **Flow:** CRUD operations. Status toggles between `available` and `not_available` depending on whether it is currently assigned to an active trip.

### Driver
- **Purpose:** Manages the drivers operating the trucks.
- **Fields:** `name, email, mobile, status, opening_balance`.
- **Relationships:** Has one Truck (assigned), has many Trips.
- **Flow:** CRUD operations. Similar to Trucks, their status dynamically changes based on active trips.

### Trip
- **Purpose:** The core operational module connecting a Party, a Truck, and a Driver to transport goods from an origin to a destination.
- **Fields:** `party_id, truck_id, driver_id, origin, destination, billing_type, per_unit_amount, unit, freight_amount, pending_freight_amount, start_date, status, lr_number, etc.`
- **Relationships:** Belongs to Party, Truck, Driver. Has many TripAdvances, TripCharges, TripExpenses, TripPayments.
- **Flow:** Created with complex validation. Allows for "manual entry" of missing entities. Automatically manages the status of assigned Drivers and Trucks.

### Expense (Trip Financials)
- **Purpose:** Manages financial transactions occurring during a trip.
- **Models:** Broken down into `TripExpense`, `TripAdvance`, `TripCharge`, and `TripPayment`.
- **Fields (TripExpense):** `trip_id, expense_type, amount, expense_date, payment_mode, add_to_party_bill`.
- **Relationships:** All belong to a Trip.
- **Flow:** Managed via Livewire modals (`TripExpenseModal`, `TripAdvanceModal`, etc.) embedded within the Trip management views.

## 4. Data Flow (Core Flow)
1. **Initiation:** A `Party` requests a shipment.
2. **Trip Creation:** An admin creates a `Trip` via the `AddTrip` Livewire component. They assign a `Truck` and a `Driver`. If the entities don't exist in the DB, the system accepts them as "manual entries".
3. **Resource Locking:** Upon saving the Trip, the `Trip` model's `boot()` method fires. It marks the assigned `Driver` and `Truck` statuses as `not_available`.
4. **Freight Calculation:** Based on the `billing_type` (fixed vs per_unit), the `freight_amount` is calculated (e.g., `per_unit_amount * unit`). The `pending_freight_amount` is initialized.
5. **Ongoing Operations:** During the trip, `TripAdvances` and `TripExpenses` are logged.
6. **Completion:** When the trip is marked `completed`, the `boot()` method fires again, freeing up the `Driver` and `Truck` by setting them back to `available`.

## 5. DB Relationships
- **Trip:**
  - `belongsTo(Party::class)`
  - `belongsTo(Truck::class)`
  - `belongsTo(Driver::class)`
  - `hasMany(TripAdvance::class)`
  - `hasMany(TripCharge::class)`
  - `hasMany(TripPayment::class)`
  - `hasMany(TripExpense::class)`
- **Truck:**
  - `belongsTo(Driver::class)`
- **Driver:**
  - `hasOne(Truck::class)`

## 6. Business Logic
- **Freight Calculation:** Done synchronously in Livewire components (`AddTrip.php`). `freight_amount = per_unit_amount * unit`.
- **Status Flow:** State machines for Trucks and Drivers are fully automated inside `app/Models/Trip.php` using Eloquent lifecycle hooks (`static::saving`). If a trip driver changes during an update, the old driver is freed, and the new driver is marked busy.
- **Validation:** Deeply integrated into Livewire components with dynamic rules. E.g., either a `party_id` (from DB) OR a `party_name` (manual entry string) must be provided.
- **Soft Deletes:** Used on Trips (`$trip->deleted_by = auth()->id(); $trip->delete();`) to preserve historical data.

## 7. UI Flow
- **Layout:** Standard admin dashboard shell (Bootstrap-based).
- **CRUD Flow:** 
  - List views (`ListTrips`) provide robust DataTables-like features (Pagination, Search, Column Sorting, Date Range Filtering) powered by Livewire.
  - Adding/Editing relies heavily on UI Offcanvas/Modals rather than full page reloads.
  - Dropdown dependencies use Livewire computed properties for autocomplete filtering (e.g., `filteredParties`, `filteredDrivers`).
- **Feedback:** Flash messages are dispatched via Livewire events and caught by the parent list components.

## 8. Code Patterns
- **Eloquent Scopes:** High usage of local scopes (`scopeSearch`, `scopeStatus`, `scopeActive`) inside Models to keep Livewire `render()` methods clean.
- **Livewire Computed Properties:** `#[Computed]` attributes are used for efficient filtering of pre-loaded dropdown lists.
- **Event Dispatching:** Loosely coupled components communicate via `$this->dispatch()`. For instance, saving a trip dispatches a `closeOffcanvas` event and a `tripAdded` event to refresh the table.

## 9. Performance Observations
- **Strengths:** 
  - Good use of Eloquent Eager Loading (`with(['party', 'truck'])`) in list views to prevent N+1 queries.
  - Using computed properties caches values during the request lifecycle.
- **Potential Issues:**
  - **Preloaded Lists:** In `AddTrip::mount()`, `Party::active()->get()` loads all active parties into an array. As the database grows, loading thousands of records into memory on every component mount will degrade performance. A shift to async AJAX/Livewire search (fetching from DB on keystroke) is recommended for large datasets.
  - **Livewire Overuse:** Relying on Livewire for simple UI interactions (like showing/hiding dropdowns) causes unnecessary server round-trips. Alpine.js could be used to handle frontend state more efficiently.

## 10. Future Modules Design

### Bilty (Consignment Note/LR)
- **Purpose:** Document acknowledging the receipt of goods.
- **Required Tables:** `builties` (`id, trip_id, bilty_number, date, consigner_details, consignee_details, items, weight, status`).
- **Relationships:** `belongsTo(Trip)`.
- **Flow:** Generated automatically from a Trip. Can be downloaded as PDF.

### Invoice
- **Purpose:** Billing a Party for one or multiple trips.
- **Required Tables:** 
  - `invoices` (`id, party_id, invoice_number, total_amount, status, due_date`).
  - `invoice_items` (`id, invoice_id, trip_id, amount`).
- **Relationships:** `belongsTo(Party)`, `hasMany(Trips)` through `invoice_items`.
- **Flow:** Admin selects a Party -> System lists un-invoiced completed Trips -> Admin selects Trips to include -> Invoice generated -> Updates `pending_freight_amount` logic.

### Report
- **Purpose:** Analytics and summaries.
- **Required Tables:** None (Data derived from Trips, Expenses, Payments).
- **Flow:** Purely analytical Livewire components with complex querying. Example reports: Profit/Loss per Trip, Truck utilization, Pending balances by Party.

### Profile
- **Purpose:** Admin/User settings.
- **Required Tables:** Existing `users` table. Maybe a new `settings` table for global configurations.
- **Flow:** Simple form to update name, email, password, and application preferences (like default billing type, company logo for invoices).

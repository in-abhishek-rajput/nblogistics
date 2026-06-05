# Project Understanding Document

## 1. Project Overview
This project is a transportation and logistics management system built with **Laravel 12**, **PHP 8.2+**, and **Livewire 3**. It is designed to manage the core operations of a transport business, including customers (Parties), vehicles (Trucks), staff (Drivers), and operations (Trips, Expenses, Finances).

## 2. Architecture
The application follows a modernized Laravel architecture heavily relying on Livewire for frontend reactivity:
- **Directory Structure:**
  - `app/Models/`: Contains Eloquent models and business logic via model scopes and event hooks (boot methods).
  - `app/Http/Controllers/Admin/`: Very thin controllers (e.g., `TripsController`, `BiltyController`, `InvoicesController`) that primarily return initial Blade views or printable PDF-style templates.
  - `app/Livewire/Admin/`: The core application controllers. Components like `AddTrip`, `ListTrips`, `ViewTrip`, `Attendance`, `Salary`, `DriversReport`, etc. handle form submissions, validation, real-time UI updates, and data fetching.
  - `resources/views/livewire/admin/`: Blade views for the Livewire components, often utilizing Modals and Offcanvas elements for CRUD operations.
  - `resources/views/admin/bill/`: Standalone printable Bill/Tax Invoice templates (not using the admin layout).
  - `resources/views/admin/bilty/`: Standalone printable Bilty/LR (Lorry Receipt) templates.
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
- **Fields:** `name, email, mobile, status, opening_balance, base_salary`.
- **Relationships:** Has one Truck (assigned), has many Trips, has many DriverAttendances.
- **Flow:** CRUD operations. Similar to Trucks, their status dynamically changes based on active trips. Now also tracks `base_salary` (added via migration `2026_05_23_005232`), which is used for monthly salary calculation.

### Trip
- **Purpose:** The core operational module connecting a Party, a Truck, and a Driver to transport goods from an origin to a destination.
- **Fields:** `party_id, truck_id, driver_id, origin, destination, billing_type, per_unit_amount, unit, freight_amount, pending_freight_amount, profit, start_date, start_km, end_date, end_km, status, lr_number, material_name, note, completed_date, pod_receipt, pod_received_date, pod_submitted_date, settled_date, created_by, updated_by, deleted_by, [manual_entry flags]`.
- **Relationships:** Belongs to Party, Truck, Driver. Has many TripAdvances, TripCharges, TripExpenses, TripPayments.
- **Flow:** Created with complex validation. Allows for "manual entry" of missing entities. Automatically manages the status of assigned Drivers and Trucks.
- **Status Lifecycle (sequential, enforced):**
  ```
  pending → start → completed → pod_received → pod_submitted → settled
  ```
  Each transition records a timestamp: `start_date`, `completed_date`, `pod_received_date`, `pod_submitted_date`, `settled_date`.

### Trip View (`ViewTrip` Livewire Component)
- **Purpose:** The most complex component — manages the complete lifecycle of a single trip on one page.
- **Features:**
  - Displays all trip details: party, truck, driver, route, billing, KM readings, dates, status.
  - A **status tracker UI** shows progress through the pipeline.
  - Sequential status transitions via Bootstrap confirmation modals.
  - **"Complete Trip"** action: requires `end_date` + `end_km` inputs (validated against start values).
  - **"POD Received"** action: requires file upload (PDF/JPG/PNG, max 5MB), stored in `storage/public/POCReceipt/TripX/`.
  - **"Settled"** action: supports **partial payment** — deducts a `paid_amount` from `pending_freight_amount`; trip status only advances to `settled` if fully cleared.
  - Inline CRUD forms for: **Advances**, **Charges** (add-to-bill / reduce-from-bill), **Payments**, **Expenses**.
  - Auto-recalculates `pending_freight_amount` and `profit` after every financial change.
- **Component:** `app/Livewire/Admin/Trip/ViewTrip.php` (1009 lines).

### Expense (Trip Financials)
- **Purpose:** Manages financial transactions occurring during a trip.
- **Models:** Broken down into `TripExpense`, `TripAdvance`, `TripCharge`, and `TripPayment`.
- **Fields (TripExpense):** `trip_id, truck_id, expense_type, expense_category, amount, expense_date, payment_mode, add_to_party_bill, notes`.
- **Relationships:** All belong to a Trip.
- **Flow:** Managed inline within the Trip View (`ViewTrip`), and also via standalone Expense modals (`AddExpense`, `EditExpense`) in the Trip Expenses global list.

### Trip Expenses Global List (`ListExpenses` Livewire Component)
- **Purpose:** A dedicated cross-trip view of all expenses.
- **Route:** `GET /trip-expenses` → `admin.trip-expenses.list`.
- **Features:**
  - Filter by: month, payment mode, expense category, text search (type, notes, party, route).
  - Sort by any column.
  - Shows **total expenses for the selected month** as a summary stat.
  - Delete expenses with confirmation.
  - Integrates with `AddExpense` and `EditExpense` modals.

### Trip Document Wizard
- **Purpose:** A unified 3-step wizard to manage Bilty, Invoice, and Receipt documents for a single trip.
- **Route:** `GET /trips/{tripId}/documents/{step?}` → `admin.trip.documents` wrapper view.
- **Livewire Component:** `app/Livewire/Admin/Trip/DocumentWizard.php`.
- **Features:**
  - **Step 1 (Bilty):** Collects consignment details. Defaults are pulled from the Trip.
  - **Step 2 (Invoice):** Collects tax invoice details. Data cascades intelligently from the Bilty step. Auto-generates invoice numbers.
  - **Step 3 (Receipt):** Collects payment receipt details. Data cascades from the Invoice step. Auto-generates receipt numbers.
  - **Data Storage:** Saves flexible JSON data into the `trip_documents` table via the `TripDocument` model.
  - **Live Preview:** Allows previewing the printable document at any step.

### Bilty (Lorry Receipt / LR)
- **Purpose:** A printable consignment note (Lorry Receipt) generated from a Trip and Document Wizard.
- **Routes:** 
  - `GET /builty/{id}` → `BiltyController@show` → `admin.bilty.template`
  - `GET /builty/{id}/print` & `/download` available.
- **Template:** `resources/views/admin/bilty/template.blade.php` — A full A4-styled printable document.
- **Contents:** Pulls JSON data from `TripDocument` (Bilty type).
- **Print Support:** Includes CSS `@media print` rules.

### Invoice / Bill (Tax Invoice)
- **Purpose:** A printable Tax Invoice for billing a Party for a trip.
- **Routes:** 
  - `GET /invoices/{id}` → `InvoicesController@show` → `admin.bill.template`
  - `GET /invoices/{id}/print` & `/download` available.
- **Template:** `resources/views/admin/bill/template.blade.php` — A full A4-styled Tax Invoice document.
- **Contents:** Pulls JSON data from `TripDocument` (Invoice type).
- **Print Support:** Includes CSS `@media print` rules.

### Money Receipt
- **Purpose:** A printable Money Receipt confirming payment received for a trip.
- **Routes:** `GET /receipts/{id}` → `ReceiptController@show` → `admin.receipt.template`
- **Template:** `resources/views/admin/receipt/template.blade.php`
- **Contents:** Pulls JSON data from `TripDocument` (Receipt type). Includes payment amounts, cheque details, and references to Invoice/LR.

### Driver Attendance
- **Purpose:** Tracks daily attendance (present/absent/half-day/holiday) for all drivers on a monthly grid.
- **Model:** `DriverAttendance` — fields: `driver_id, attendance_date, status` (enum: `present`, `half_day`, `absent`, `holiday`). Unique constraint on `(driver_id, attendance_date)`.
- **DB Table:** `driver_attendances` (migration: `2026_05_22_183522`).
- **Routes:**
  - `GET /drivers-attendance` → `admin.driver.attendance` (wrapper view embedding Livewire).
- **Livewire Component:** `app/Livewire/Admin/Driver/Attendance.php`
- **Flow:**
  - Month/Year pickers update the displayed grid.
  - Each day cell is a checkbox; toggling calls `toggleAttendance($driverId, $day, $isChecked)`.
  - **Backend security:** Modifications to past months, future months, or future dates within the current month are rejected server-side.
  - Uses `DriverAttendance::updateOrCreate()` to upsert records.
  - Displays per-driver present-day totals.
  - Eager loads attendances for the selected month to avoid N+1 queries.

### Driver Salary
- **Purpose:** Calculates and records monthly salary for each driver based on attendance and `base_salary`.
- **Model:** `DriverSalaryRecord` — fields: `driver_id, month (Y-m), total_days, present_days, absent_days, half_days, gross_salary, advance_deduction, net_salary, status`.
- **Livewire Component:** `app/Livewire/Admin/Driver/Salary.php`
- **Routes:**
  - `GET /drivers-salary` → `admin.driver.salary` (wrapper view embedding Livewire).
- **Calculation Logic:**
  - `paidDays = presentDays + holidays + (halfDays * 0.5)`
  - `grossSalary = (base_salary / totalDaysInMonth) * paidDays`
  - `netSalary = grossSalary - advance_deduction`
  - Admin inputs `advance_deduction` per driver inline.
  - Salary is saved/updated via `DriverSalaryRecord::updateOrCreate()`.
  - Records can be marked as **PAID** (`status = 'PAID'`).

### Reports
- **Purpose:** Analytics dashboards for Trips, Trucks, and Drivers.
- **Routes:**
  - `GET /reports/trips` → `ReportsController@trips`
  - `GET /reports/trucks` → `ReportsController@trucks`
  - `GET /reports/drivers` → `ReportsController@drivers`
- **Livewire Components:** `app/Livewire/Admin/Reports/` (TripsReport, TrucksReport, DriversReport)
- **Common Features (all reports):**
  - Month/Year filter pickers (defaults to current month).
  - Re-fetches data reactively on filter change.
  - **Print** via `dispatch('printReport')` → `window.print()`.
  - **Export to Excel** (`.xlsx`) using `maatwebsite/excel` — inline anonymous class implementing `FromCollection`, `WithHeadings`, `WithStyles`.

#### Trips Report
- **Metrics:** Total/Completed/Ongoing/Cancelled trips, Total Freight, Total Expenses, Profit/Loss, Average Trip Profit, Top Route (by count).
- **Expense Breakdown:** Groups TripExpenses by type for a detailed breakdown table.
- **Per-Trip Table:** Shows date, party, truck, driver, origin, destination, freight, expenses, profit, status.
- **Export columns:** #, Date, Party, Truck, Driver, Origin, Destination, Freight, Expenses, Profit, Status.

#### Trucks Report
- **Metrics:** Total/Active trucks, used vs idle trucks, utilization percentage.
- **Per-Truck Table:** trips count, income (freight), expenses (trip expenses linked to truck), maintenance expenses (TripExpenses with null trip_id), profit, utilization %.
- **Top Earning Truck** highlighted.
- **Export columns:** Truck Number, Type, Assigned Driver, Trips Count, Income, Expenses, Maintenance, Profit, Utilization %.

#### Drivers Report
- **Metrics:** Total/Active drivers, drivers assigned to trips.
- **Per-Driver Table:** trips count, total earnings (freight), average earnings per trip, completed trips, ongoing trips.
- **Top Performing Driver** (by earnings) highlighted.
- **Driver Expenses** (TripExpenses paid by driver) grouped and surfaced.
- **Export columns:** Driver Name, Mobile, Truck Assigned, Trips Count, Earnings, Avg. Earnings/Trip, Completed Trips, Ongoing Trips.

### Dashboard
- **Purpose:** Admin overview with static charts (Phase 1 — static data placeholder for Phase 2 dynamic loading).
- **Livewire Component:** `app/Livewire/Admin/Dashboard.php`
- **Charts (Chart.js):**
  - **Revenue vs Expense vs Profit** — Grouped Bar chart (6 months of sample data).
  - **Trip Status Distribution** — Doughnut chart (Active, Completed, Pending, Cancelled).
- **Implementation notes:** Charts are initialized after `livewire:initialized` event. Existing chart instances are destroyed before re-initializing to prevent duplication. A `refreshCharts` Livewire event listener is wired for Phase 2 dynamic reload.

### Profile
- **Purpose:** Admin/User settings.
- **Route:** `GET /profile` → `ProfileController`.
- **Status:** Scaffolded but not yet implemented beyond the route/controller stub.

## 4. Data Flow (Core Flow)
1. **Initiation:** A `Party` requests a shipment.
2. **Trip Creation:** An admin creates a `Trip` via the `AddTrip` Livewire component. They assign a `Truck` and a `Driver`. If the entities don't exist in the DB, the system accepts them as "manual entries".
3. **Resource Locking:** Upon saving the Trip, the `Trip` model's `boot()` method fires. It marks the assigned `Driver` and `Truck` statuses as `not_available`.
4. **Freight Calculation:** Based on the `billing_type` (fixed vs per_unit), the `freight_amount` is calculated (e.g., `per_unit_amount * unit`). The `pending_freight_amount` is initialized.
5. **Ongoing Operations:** During the trip, `TripAdvances`, `TripCharges`, `TripExpenses`, and `TripPayments` are logged via the `ViewTrip` component. Each change auto-recalculates `pending_freight_amount` and `profit`.
6. **Documentation:** Admin can print a **Bilty (LR)** from the trip for the consignment note and a **Bill (Tax Invoice)** for billing the party.
7. **Status Progression:** Trip advances through: `pending → start → completed → pod_received → pod_submitted → settled`. Each step records a timestamp.
8. **Completion:** When the trip reaches a completed phase (or is fully paid), the `boot()` method frees up the `Driver` and `Truck` by setting them back to `available`.
9. **Salary Cycle:** Monthly attendance is recorded in Driver Attendance. At month end, admin calculates salary via Driver Salary, entering advance deductions, and marks salaries as PAID.

## 5. DB Relationships
- **Trip:**
  - `belongsTo(Party::class)`
  - `belongsTo(Truck::class)`
  - `belongsTo(Driver::class)`
  - `hasMany(TripAdvance::class)`
  - `hasMany(TripCharge::class)`
  - `hasMany(TripPayment::class)`
  - `hasMany(TripExpense::class)`
  - `hasMany(TripDocument::class)`
- **TripDocument:**
  - `belongsTo(Trip::class)`
  - `belongsTo(User::class, 'created_by')`
  - `belongsTo(User::class, 'updated_by')`
- **Truck:**
  - `belongsTo(Driver::class)`
- **Driver:**
  - `hasOne(Truck::class)`
  - `hasMany(Trip::class)`
  - `hasMany(DriverAttendance::class)`
- **DriverAttendance:**
  - `belongsTo(Driver::class)`
- **DriverSalaryRecord:**
  - `belongsTo(Driver::class)`

## 6. Business Logic
- **Freight Calculation:** Done synchronously in Livewire components (`AddTrip.php`). `freight_amount = per_unit_amount * unit`.
- **Pending Freight & Profit Recalculation:** Handled in `ViewTrip::updatePendingFreightAmount()`. Formula: `pending_freight = freight_amount - total_advances - total_payments ± total_charges`. `profit = pending_freight - total_expenses`.
- **Status Flow:** State machines for Trucks and Drivers are fully automated inside `app/Models/Trip.php` using Eloquent lifecycle hooks (`static::saving`). If a trip driver changes during an update, the old driver is freed, and the new driver is marked busy. A trip is considered "finished" if its status is in `[completed, pod_received, pod_submitted, settled]` OR if `pending_freight_amount <= 0`.
- **Partial Settlement:** The `settled` action allows a partial `paid_amount`. The pending amount is reduced but the status only transitions to `settled` when the full amount is cleared.
- **Validation:** Deeply integrated into Livewire components with dynamic rules. E.g., either a `party_id` (from DB) OR a `party_name` (manual entry string) must be provided.
- **Soft Deletes:** Used on Trips (`$trip->deleted_by = auth()->id(); $trip->delete();`) to preserve historical data.
- **Attendance Guard:** The `toggleAttendance` method in `Attendance.php` validates server-side that attendance cannot be modified for past months, future months, or future dates within the current month.
- **Salary Calculation:** `gross_salary = (base_salary / days_in_month) * paid_days`, where `paid_days = present_days + holiday_days + (half_days * 0.5)`.

## 7. UI Flow
- **Layout:** Standard admin dashboard shell (Bootstrap-based).
- **CRUD Flow:** 
  - List views (`ListTrips`, `ListDrivers`, `ListExpenses`) provide robust DataTables-like features (Pagination, Search, Column Sorting, Date Range/Month Filtering) powered by Livewire.
  - Adding/Editing relies heavily on UI Offcanvas/Modals rather than full page reloads.
  - Dropdown dependencies use Livewire computed properties for autocomplete filtering (e.g., `filteredParties`, `filteredDrivers`).
- **Feedback:** Flash messages are dispatched via Livewire events and caught by the parent list components.
- **Printing:** Bilty and Bill templates are standalone printable pages opened in a new browser tab/window, with `@media print` CSS for clean output.
- **Export:** Report pages have an "Export" button that triggers an Excel file download via `maatwebsite/excel`.

## 8. Code Patterns
- **Eloquent Scopes:** High usage of local scopes (`scopeSearch`, `scopeStatus`, `scopeActive`, `scopeBillingType`) inside Models to keep Livewire `render()` methods clean.
- **Livewire Computed Properties:** `#[Computed]` attributes are used for efficient filtering of pre-loaded dropdown lists.
- **Event Dispatching:** Loosely coupled components communicate via `$this->dispatch()`. For instance, saving a trip dispatches a `closeOffcanvas` event and a `tripAdded` event to refresh the table.
- **Anonymous Excel Export Classes:** Reports use inline anonymous classes implementing `Maatwebsite\Excel` interfaces directly within the Livewire component, avoiding separate export class files.
- **DB Transactions:** Critical state changes (status transitions, POD upload) are wrapped in `DB::transaction()`.
- **File Uploads:** `ViewTrip` uses Livewire's `WithFileUploads` trait for POD file uploads, storing files in `storage/public/POCReceipt/Trip{id}/`.

## 9. Performance Observations
- **Strengths:** 
  - Good use of Eloquent Eager Loading (`with(['party', 'truck'])`) in list views to prevent N+1 queries.
  - Using computed properties caches values during the request lifecycle.
  - The `Attendance` component uses a single eager-loaded query for all driver attendances in the month.
  - Reports use `withCount` and `withSum` aggregates on the main query to minimize round-trips.
- **Potential Issues:**
  - **Preloaded Lists:** In `AddTrip::mount()`, `Party::active()->get()` loads all active parties into an array. As the database grows, loading thousands of records into memory on every component mount will degrade performance. A shift to async AJAX/Livewire search (fetching from DB on keystroke) is recommended for large datasets.
  - **Livewire Overuse:** Relying on Livewire for simple UI interactions (like showing/hiding dropdowns) causes unnecessary server round-trips. Alpine.js could be used to handle frontend state more efficiently.
  - **Dashboard Static Data:** The dashboard charts currently use hardcoded data arrays in the `Dashboard.php` Livewire component. Phase 2 should replace these with live DB queries.
  - **Reports In-Memory Grouping:** `DriversReport` and `TrucksReport` load all trips for the month in memory and group in PHP. For large datasets, DB-side aggregation would be more efficient.

## 10. Implemented Modules Summary

| Module | Status | Key Files |
|---|---|---|
| Party (Customer) | ✅ Complete | `Party.php`, `ListParties`, `AddParty`, `EditParty` |
| Truck | ✅ Complete | `Truck.php`, `ListTrucks`, `AddTruck`, `EditTruck` |
| Driver | ✅ Complete | `Driver.php`, `ListDrivers`, `AddDriver`, `EditDriver` |
| Trip (Create/Edit/List) | ✅ Complete | `Trip.php`, `AddTrip`, `EditTrip`, `ListTrips` |
| Trip View & Status Flow | ✅ Complete | `ViewTrip.php` (1009 lines) |
| Trip Expenses (inline) | ✅ Complete | Inline in `ViewTrip`, `TripExpenseModal` |
| Trip Expenses (global list) | ✅ Complete | `ListExpenses`, `AddExpense`, `EditExpense` |
| Trip Document Wizard | ✅ Complete | `DocumentWizard.php`, `TripDocument.php`, `documents.blade.php` |
| Bilty / LR Template | ✅ Live | `BiltyController`, `admin/bilty/template.blade.php` |
| Invoice / Bill Template | ✅ Live | `InvoicesController`, `admin/bill/template.blade.php` |
| Money Receipt Template | ✅ Live | `ReceiptController`, `admin/receipt/template.blade.php` |
| Driver Attendance | ✅ Complete | `Attendance.php`, `driver_attendances` table |
| Driver Salary | ✅ Complete | `Salary.php`, `DriverSalaryRecord.php` |
| Reports (Trips/Trucks/Drivers) | ✅ Complete | `TripsReport`, `TrucksReport`, `DriversReport` |
| Dashboard Charts | ✅ Phase 1 (static data) | `Dashboard.php`, Chart.js |
| Profile | 🚧 Scaffolded | `ProfileController` stub |
| Multi-trip Invoice Management | 🚧 Scaffolded | `InvoicesController` (index/store not implemented) |

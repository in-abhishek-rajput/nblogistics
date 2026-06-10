<?php

namespace App\Livewire\Admin\Truck;

use App\Models\Party;
use App\Models\Trip;
use App\Models\Truck;
use App\Models\Driver;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class TripBook extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public int $truckId;
    public Truck $truck;

    public string $monthFilter = 'all';
    public string $custom_from = '';
    public string $custom_to = '';

    public ?int $viewingTripId = null;
    public ?int $editingTripId = null;

    public string $partySearch = '';
    public string $driverSearch = '';

    public $party_id = null;
    public $driver_id = null;
    public ?int $editingId = null;

    public string $origin = '';
    public string $destination = '';
    public string $billing_type = 'fixed';
    public string $freight_amount = '';
    public string $per_unit_amount = '';
    public string $unit = '';
    public string $start_date = '';
    public string $start_km = '';
    public string $lr_number = '';
    public string $material_name = '';
    public string $note = '';

    public int $perPage = 8;
    public bool $showPartyDropdown = false;
    public bool $showDriverDropdown = false;

    public array $monthOptions = [
        'all' => 'All Months',
        'current' => 'Current Month',
        'previous' => 'Previous Month',
        'three' => 'Last 3 Months',
        'six' => 'Last 6 Months',
        'custom' => 'Custom Range',
    ];

    public array $billingTypes;
    public array $partyList = [];
    public array $driverList = [];

    protected $listeners = [
        'openTripBookPanel' => 'openPanel',
        'editTrip' => 'editTrip',
        'deleteTrip' => 'deleteTrip',
    ];

    public function mount(int $truckId)
    {
        $this->truckId = $truckId;
        $this->billingTypes = config('trip.billing_types');
        $this->partyList = Party::active()
            ->orderBy('name')
            ->get(['id', 'name'])
            ->toArray();
        $this->driverList = Driver::orderBy('name')
            ->get(['id', 'name'])
            ->toArray();
        $this->refreshData();
    }

    public function refreshData(): void
    {
        $this->truck = Truck::with('driver')->findOrFail($this->truckId);
        $this->resetPage();
    }

    public function openPanel(): void
    {
        $this->refreshData();
        $this->dispatch('openTripBookOffcanvas');
    }

    public function updatingMonthFilter(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function filteredParties()
    {
        if (!$this->partySearch) {
            return $this->partyList;
        }

        $search = strtolower($this->partySearch);
        return array_filter($this->partyList, function ($party) use ($search) {
            return strpos(strtolower($party['name']), $search) !== false;
        });
    }

    #[Computed]
    public function filteredDrivers()
    {
        if (!$this->driverSearch) {
            return $this->driverList;
        }

        $search = strtolower($this->driverSearch);
        return array_filter($this->driverList, function ($driver) use ($search) {
            return strpos(strtolower($driver['name']), $search) !== false;
        });
    }

    public function showAddTripModal(): void
    {
        $this->reset([
            'party_id',
            'partySearch',
            'driver_id',
            'driverSearch',
            'origin',
            'destination',
            'billing_type',
            'freight_amount',
            'per_unit_amount',
            'unit',
            'start_date',
            'start_km',
            'lr_number',
            'material_name',
            'note',
            'editingId',
        ]);

        $this->billing_type = 'fixed';
        $this->showPartyDropdown = false;
        $this->showDriverDropdown = false;
        $this->dispatch('showAddTripModal');
    }

    public function createTrip(): void
    {
        $this->validate($this->tripValidationRules());

        $partyName = null;
        if (!$this->party_id) {
            $partyName = $this->partySearch;
        }

        $driverName = null;
        if (!$this->driver_id) {
            $driverName = $this->driverSearch;
        }

        $freight = 0;
        if ($this->billing_type === 'fixed') {
            $freight = (float) $this->freight_amount;
        } else {
            $freight = (float) ($this->per_unit_amount ?? 0) * (float) ($this->unit ?? 0);
        }

        $trip = Trip::create([
            'truck_id' => $this->truckId,
            'truck_name' => $this->truck->truck_number,
            'truck_manual_entry' => false,
            'party_id' => $this->party_id ?: null,
            'party_name' => $partyName,
            'party_manual_entry' => !$this->party_id,
            'driver_id' => $this->driver_id ?: null,
            'driver_name' => $driverName,
            'driver_manual_entry' => !$this->driver_id,
            'origin' => $this->origin,
            'destination' => $this->destination,
            'billing_type' => $this->billing_type,
            'freight_amount' => $freight,
            'pending_freight_amount' => $freight,
            'per_unit_amount' => $this->per_unit_amount ?: null,
            'unit' => $this->unit ?: null,
            'start_date' => Carbon::parse($this->start_date),
            'start_km' => (int) $this->start_km,
            'lr_number' => $this->lr_number ?: null,
            'material_name' => $this->material_name ?: null,
            'note' => $this->note ?: null,
            'status' => 'pending',
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        $this->dispatch('tripBookUpdated');
        $this->dispatch('closeModal', 'addTripModal');
        $this->dispatch('openTripBookOffcanvas');
        $this->refreshData();
    }

    public function editTrip(int $tripId): void
    {
        $trip = Trip::findOrFail($tripId);

        $this->editingId = $trip->id;
        $this->origin = $trip->origin;
        $this->destination = $trip->destination;
        $this->billing_type = $trip->billing_type;
        $this->freight_amount = (string) $trip->freight_amount;
        $this->per_unit_amount = $trip->per_unit_amount ? (string) $trip->per_unit_amount : '';
        $this->unit = $trip->unit ? (string) $trip->unit : '';
        $this->start_date = $trip->start_date?->format('Y-m-d');
        $this->start_km = $trip->start_km ? (string) $trip->start_km : '';
        $this->lr_number = $trip->lr_number ?? '';
        $this->material_name = $trip->material_name ?? '';
        $this->note = $trip->note ?? '';

        if ($trip->party_id) {
            $this->party_id = $trip->party_id;
            $this->partySearch = Party::find($trip->party_id)?->name ?? '';
        } else {
            $this->party_id = null;
            $this->partySearch = $trip->party_name ?? '';
        }

        if ($trip->driver_id) {
            $this->driver_id = $trip->driver_id;
            $this->driverSearch = Driver::find($trip->driver_id)?->name ?? '';
        } else {
            $this->driver_id = null;
            $this->driverSearch = $trip->driver_name ?? '';
        }

        $this->showPartyDropdown = false;
        $this->showDriverDropdown = false;
        $this->dispatch('showEditTripModal');
    }

    public function updateTrip(): void
    {
        $this->validate($this->tripValidationRules());

        $trip = Trip::findOrFail($this->editingId);

        $partyName = null;
        if (!$this->party_id) {
            $partyName = $this->partySearch;
        }

        $driverName = null;
        if (!$this->driver_id) {
            $driverName = $this->driverSearch;
        }

        $freight = 0;
        if ($this->billing_type === 'fixed') {
            $freight = (float) $this->freight_amount;
        } else {
            $freight = (float) ($this->per_unit_amount ?? 0) * (float) ($this->unit ?? 0);
        }

        $trip->update([
            'party_id' => $this->party_id ?: null,
            'party_name' => $partyName,
            'party_manual_entry' => !$this->party_id,
            'driver_id' => $this->driver_id ?: null,
            'driver_name' => $driverName,
            'driver_manual_entry' => !$this->driver_id,
            'origin' => $this->origin,
            'destination' => $this->destination,
            'billing_type' => $this->billing_type,
            'freight_amount' => $freight,
            'pending_freight_amount' => $freight,
            'per_unit_amount' => $this->per_unit_amount ?: null,
            'unit' => $this->unit ?: null,
            'start_date' => Carbon::parse($this->start_date),
            'start_km' => (int) $this->start_km,
            'lr_number' => $this->lr_number ?: null,
            'material_name' => $this->material_name ?: null,
            'note' => $this->note ?: null,
            'updated_by' => auth()->id(),
        ]);

        $this->dispatch('tripBookUpdated');
        $this->dispatch('closeModal', 'editTripModal');
        $this->dispatch('openTripBookOffcanvas');
        $this->refreshData();
    }

    public function deleteTrip(int $tripId): void
    {
        $trip = Trip::findOrFail($tripId);
        $trip->delete();

        $this->dispatch('tripBookUpdated');
        $this->dispatch('openTripBookOffcanvas');
        $this->refreshData();
    }

    public function viewTrip(int $tripId): void
    {
        $this->dispatch('viewTripFromBook', tripId: $tripId)->to(\App\Livewire\Admin\Truck\ViewTruck::class);
    }

    protected function tripValidationRules(): array
    {
        $rules = [
            'origin' => 'required|string|max:255',
            'destination' => 'required|string|max:255',
            'billing_type' => 'required|in:' . implode(',', array_keys($this->billingTypes)),
            'start_date' => 'required|date',
            'start_km' => 'required|integer|min:0',
            'lr_number' => 'nullable|string|max:255',
            'material_name' => 'nullable|string|max:255',
            'note' => 'nullable|string',
        ];

        if ($this->billing_type === 'fixed') {
            $rules['freight_amount'] = 'required|numeric|min:0';
        } else {
            $rules['per_unit_amount'] = 'required|numeric|min:0';
            $rules['unit'] = 'required|numeric|min:0';
        }

        return $rules;
    }

    public function getTripsProperty()
    {
        return $this->tripsQuery()
            ->with('party')
            ->orderByDesc('start_date')
            ->paginate($this->perPage);
    }

    public function getTripSummaryProperty()
    {
        $trips = $this->tripsQuery()->get();

        return [
            'count' => $trips->count(),
            'revenue' => $trips->sum('freight_amount'),
        ];
    }

    protected function tripsQuery()
    {
        $query = Trip::where('truck_id', $this->truckId);
        $today = Carbon::today();

        if ($this->monthFilter === 'current') {
            $query->whereYear('start_date', $today->year)
                ->whereMonth('start_date', $today->month);
        } elseif ($this->monthFilter === 'previous') {
            $previous = $today->copy()->subMonth();
            $query->whereYear('start_date', $previous->year)
                ->whereMonth('start_date', $previous->month);
        } elseif ($this->monthFilter === 'three') {
            $query->whereBetween('start_date', [$today->copy()->subMonths(3)->startOfMonth(), $today->endOfMonth()]);
        } elseif ($this->monthFilter === 'six') {
            $query->whereBetween('start_date', [$today->copy()->subMonths(6)->startOfMonth(), $today->endOfMonth()]);
        } elseif ($this->monthFilter === 'custom') {
            if ($this->custom_from) {
                $query->whereDate('start_date', '>=', $this->custom_from);
            }
            if ($this->custom_to) {
                $query->whereDate('start_date', '<=', $this->custom_to);
            }
        }

        return $query;
    }

    public function getTypesProperty()
    {
        return config('truck.types');
    }

    public function getStatusesProperty()
    {
        return config('trip.statuses');
    }

    public function render()
    {
        return view('livewire.admin.truck.trip-book', [
            'trips' => $this->trips,
            'summary' => $this->tripSummary,
            'types' => $this->types,
            'statuses' => $this->statuses,
            'billingTypes' => $this->billingTypes,
            'filteredParties' => $this->filteredParties,
            'filteredDrivers' => $this->filteredDrivers,
            'monthOptions' => $this->monthOptions,
        ]);
    }
}
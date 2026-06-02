<?php

namespace App\Livewire\Admin\Documents;

use App\Models\TripDocument;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;

abstract class DocumentListBase extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $statusFilter = '';

    #[Url]
    public ?string $from_date = null;

    #[Url]
    public ?string $to_date = null;

    #[Url]
    public string $selectedDateFilter = 'all_months';

    #[Url]
    public string $sortColumn = 'document_date';

    #[Url]
    public string $sortDirection = 'desc';
    public int $perPage = 10;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingFromDate(): void
    {
        $this->resetPage();
    }

    public function updatingToDate(): void
    {
        $this->resetPage();
    }

    public function updatedSelectedDateFilter($value): void
    {
        if ($value !== 'custom') {
            $this->from_date = null;
            $this->to_date = null;
        }

        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'statusFilter', 'from_date', 'to_date', 'selectedDateFilter']);
        $this->selectedDateFilter = 'all_months';
        $this->sortColumn = 'document_date';
        $this->sortDirection = 'desc';
        $this->resetPage();
    }

    public function sortBy(string $column): void
    {
        if ($this->sortColumn === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortColumn = $column;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
    }

    public function getStatusesProperty(): array
    {
        return config('trip_documents.statuses', []);
    }

    public function getDateFiltersProperty(): array
    {
        return config('trip_documents.date_filters', []);
    }

    protected function baseQuery(): Builder
    {
        return TripDocument::query()
            ->select([
                'trip_documents.*',
                'trips.lr_number as trip_number',
                DB::raw('COALESCE(trucks.truck_number, trips.truck_name) as vehicle_number'),
                DB::raw('COALESCE(drivers.name, trips.driver_name) as driver_name'),
                DB::raw('COALESCE(parties.name, trips.party_name) as customer_name'),
                'trips.origin as source_location',
                'trips.destination as destination_location',
                DB::raw('COALESCE(users.name, \'\') as creator_name'),
            ])
            ->leftJoin('trips', 'trips.id', '=', 'trip_documents.trip_id')
            ->leftJoin('parties', 'parties.id', '=', 'trips.party_id')
            ->leftJoin('trucks', 'trucks.id', '=', 'trips.truck_id')
            ->leftJoin('drivers', 'drivers.id', '=', 'trips.driver_id')
            ->leftJoin('users', 'users.id', '=', 'trip_documents.created_by')
            ->with(['trip.party', 'trip.truck', 'trip.driver', 'createdBy'])
            ->where('trip_documents.document_type', $this->documentType());
    }

    protected function applySearch(Builder $query): Builder
    {
        if (!$this->search) {
            return $query;
        }

        $search = trim($this->search);

        return $query->where(function (Builder $builder) use ($search) {
            $builder
                ->where('trip_documents.document_number', 'like', "%{$search}%")
                ->orWhere('trips.lr_number', 'like', "%{$search}%")
                ->orWhere('trips.truck_name', 'like', "%{$search}%")
                ->orWhere('trucks.truck_number', 'like', "%{$search}%")
                ->orWhere('trips.driver_name', 'like', "%{$search}%")
                ->orWhere('drivers.name', 'like', "%{$search}%")
                ->orWhere('trips.party_name', 'like', "%{$search}%")
                ->orWhere('parties.name', 'like', "%{$search}%")
                ->orWhere('trips.origin', 'like', "%{$search}%")
                ->orWhere('trips.destination', 'like', "%{$search}%");
        });
    }

    protected function applyDateFilter(Builder $query): Builder
    {
        if ($this->selectedDateFilter === 'custom') {
            if ($this->from_date && $this->to_date) {
                return $query->whereBetween('trip_documents.document_date', [
                    Carbon::parse($this->from_date)->startOfDay(),
                    Carbon::parse($this->to_date)->endOfDay(),
                ]);
            }

            if ($this->from_date) {
                return $query->whereDate('trip_documents.document_date', $this->from_date);
            }

            return $query;
        }

        return match ($this->selectedDateFilter) {
            'today' => $query->whereDate('trip_documents.document_date', Carbon::today()),
            'this_week' => $query->whereBetween('trip_documents.document_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]),
            'last_week' => $query->whereBetween('trip_documents.document_date', [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()]),
            'this_month' => $query->whereMonth('trip_documents.document_date', Carbon::now()->month)->whereYear('trip_documents.document_date', Carbon::now()->year),
            'last_month' => $query->whereMonth('trip_documents.document_date', Carbon::now()->subMonth()->month)->whereYear('trip_documents.document_date', Carbon::now()->subMonth()->year),
            'last_3_months' => $query->where('trip_documents.document_date', '>=', Carbon::now()->subMonths(3)),
            'this_year' => $query->whereYear('trip_documents.document_date', Carbon::now()->year),
            default => $query,
        };
    }

    protected function orderByQuery(Builder $query): Builder
    {
        $columnMap = [
            'document_number' => 'trip_documents.document_number',
            'trip_number' => 'trips.lr_number',
            'vehicle_number' => DB::raw('COALESCE(trucks.truck_number, trips.truck_name)'),
            'driver_name' => DB::raw('COALESCE(drivers.name, trips.driver_name)'),
            'customer_name' => DB::raw('COALESCE(parties.name, trips.party_name)'),
            'source_location' => 'trips.origin',
            'destination_location' => 'trips.destination',
            'document_date' => 'trip_documents.document_date',
            'status' => 'trip_documents.status',
            'created_by' => DB::raw('COALESCE(users.name, \'\')'),
            'created_at' => 'trip_documents.created_at',
        ];

        $column = $columnMap[$this->sortColumn] ?? 'trip_documents.document_date';
        if ($column instanceof \Illuminate\Database\Query\Expression) {
            return $query->orderByRaw($column->getValue(DB::connection()->getQueryGrammar()) . ' ' . $this->sortDirection);
        }

        return $query->orderBy($column, $this->sortDirection);
    }

    public function changeStatus(int $id, string $status): void
    {
        if (!array_key_exists($status, config('trip_documents.statuses', []))) {
            return;
        }

        TripDocument::query()
            ->whereKey($id)
            ->where('document_type', $this->documentType())
            ->update([
                'status' => $status,
                'updated_by' => auth()->id(),
                'updated_at' => now(),
            ]);

        session()->flash('success', ucfirst($this->documentType()) . ' status updated.');
    }

    public function deleteDocument(int $id): void
    {
        TripDocument::query()
            ->whereKey($id)
            ->where('document_type', $this->documentType())
            ->delete();

        session()->flash('success', ucfirst($this->documentType()) . ' deleted successfully.');
    }

    public function exportList()
    {
        $rows = $this->filteredCollection()->map(function ($document) {
            return [
                $document->document_number,
                $document->trip_number,
                $document->vehicle_number,
                $document->driver_name,
                $document->customer_name,
                $document->source_location,
                $document->destination_location,
                optional($document->document_date)->format('d M Y'),
                $document->status_label,
                $document->creator_name,
            ];
        })->values()->all();

        return \Maatwebsite\Excel\Facades\Excel::download(new class($rows) implements FromCollection, WithHeadings, WithStyles {
            public function __construct(protected array $rows)
            {
            }

            public function collection()
            {
                return collect($this->rows);
            }

            public function headings(): array
            {
                return [
                    'Document Number',
                    'Trip Number',
                    'Vehicle Number',
                    'Driver Name',
                    'Customer Name',
                    'Source Location',
                    'Destination Location',
                    'Document Date',
                    'Status',
                    'Created By',
                ];
            }

            public function styles($sheet)
            {
                $sheet->getStyle('1:1')->getFont()->setBold(true);
                foreach ($sheet->getColumnIterator() as $column) {
                    $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
                }
            }
        }, $this->documentType() . '-list.xlsx');
    }

    protected function filteredCollection()
    {
        return $this->orderByQuery(
            $this->applyDateFilter(
                $this->applySearch($this->baseQuery())
            )
        )->get();
    }

    public function printList(): void
    {
        $query = array_filter([
            'search' => $this->search,
            'statusFilter' => $this->statusFilter,
            'from_date' => $this->from_date,
            'to_date' => $this->to_date,
            'selectedDateFilter' => $this->selectedDateFilter,
            'sortColumn' => $this->sortColumn,
            'sortDirection' => $this->sortDirection,
        ], fn ($value) => $value !== null && $value !== '');

        $this->dispatch('open-document-list-print', url: route($this->routeName() . '.index', $query));
    }

    public function getDocumentsProperty()
    {
        $query = $this->baseQuery();
        $query = $this->applySearch($query);

        if ($this->statusFilter) {
            $query->where('trip_documents.status', $this->statusFilter);
        }

        $query = $this->applyDateFilter($query);
        $query = $this->orderByQuery($query);

        return $query->paginate($this->perPage);
    }

    public function getDocumentCountProperty(): int
    {
        return $this->baseQuery()->count();
    }

    protected function documentType(): string
    {
        return 'invoice';
    }

    protected function routeName(): string
    {
        return 'invoices';
    }

    protected function moduleLabel(): string
    {
        return config("trip_documents.documents.{$this->documentType()}.label", 'Document');
    }

    protected function listPageTitle(): string
    {
        return config("trip_documents.documents.{$this->documentType()}.list_title", $this->moduleLabel() . ' List');
    }

    public function render()
    {
        return view('livewire.admin.documents.document-list', [
            'documents' => $this->documents,
            'statuses' => $this->statuses,
            'dateFilters' => $this->dateFilters,
            'pageTitle' => $this->listPageTitle(),
            'documentType' => $this->documentType(),
            'routeName' => $this->routeName(),
        ]);
    }
}

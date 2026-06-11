<?php

namespace App\Livewire\Admin\Reports;

use App\Models\Party;
use Carbon\Carbon;
use Livewire\Component;

class PartiesReport extends Component
{
    public int $month;
    public int $year;

    public $parties;
    public $summary = [];

    public function mount()
    {
        $this->month = Carbon::now()->month;
        $this->year = Carbon::now()->year;
        $this->fetchData();
    }

    public function updatedMonth()
    {
        $this->fetchData();
    }

    public function updatedYear()
    {
        $this->fetchData();
    }

    public function fetchData()
    {
        // Get all parties from master table
        $parties = Party::orderBy('name')->get();

        $monthStart = Carbon::createFromDate($this->year, $this->month, 1)->startOfMonth();
        $monthEnd = Carbon::createFromDate($this->year, $this->month, 1)->endOfMonth();

        $partyPerformance = $parties->map(function ($party) {
            $tripsQuery = \App\Models\Trip::where('party_id', $party->id)
                ->whereMonth('start_date', $this->month)
                ->whereYear('start_date', $this->year);
                
            $completedTrips = (clone $tripsQuery)->whereIn('status', ['completed', 'pod_received', 'pod_submitted', 'settled'])->count();
            $ongoingTrips = (clone $tripsQuery)->whereIn('status', ['pending', 'start'])->count();
            $cancelledTrips = (clone $tripsQuery)->where('status', 'cancelled')->count();
            $totalTrips = (clone $tripsQuery)->count();
            $totalFreight = (clone $tripsQuery)->sum('freight_amount');
            $pendingFreight = (clone $tripsQuery)->sum('pending_freight_amount');

            return [
                'id' => $party->id,
                'name' => $party->name,
                'email' => $party->email,
                'mobile' => $party->mobile,
                'status' => ucwords(str_replace('_', ' ', $party->status ?? '-')),
                'opening_balance' => round((float) ($party->opening_balance ?? 0), 2),
                'total_trips' => $totalTrips,
                'completed_trips' => $completedTrips,
                'ongoing_trips' => $ongoingTrips,
                'cancelled_trips' => $cancelledTrips,
                'total_freight' => round((float)$totalFreight, 2),
                'pending_freight' => round((float)$pendingFreight, 2),
            ];
        });

        $this->summary = [
            'total_parties' => $parties->count(),
            'active_parties' => $parties->where('status', 'active')->count(),
            'parties_performance' => $partyPerformance,
        ];
    }

    public function printReport()
    {
        $this->dispatch('printReport');
    }

    public function exportReport()
    {
        $data = [];
        foreach ($this->summary['parties_performance'] as $party) {
            $data[] = [
                $party['name'],
                $party['email'],
                $party['mobile'],
                $party['status'],
                $party['opening_balance'],
                $party['total_trips'],
                $party['completed_trips'],
                $party['ongoing_trips'],
                $party['cancelled_trips'],
                $party['total_freight'],
                $party['pending_freight'],
            ];
        }

        return \Maatwebsite\Excel\Facades\Excel::download(new class($data) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings, \Maatwebsite\Excel\Concerns\WithStyles {
            protected $data;

            public function __construct($data)
            {
                $this->data = $data;
            }

            public function collection()
            {
                return collect($this->data);
            }

            public function headings(): array
            {
                return [
                    'Party Name', 'Email', 'Mobile', 'Status', 'Opening Balance', 
                    'Total Trips', 'Completed Trips', 'Ongoing Trips', 'Cancelled Trips', 
                    'Total Freight', 'Pending Freight',
                ];
            }

            public function styles($sheet)
            {
                $sheet->getStyle('1:1')->getFont()->setBold(true);
                foreach ($sheet->getColumnIterator() as $column) {
                    $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
                }
            }
        }, 'parties-report.xlsx');
    }

    public function render()
    {
        $monthNames = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
        ];

        $currentYear = Carbon::now()->year;

        return view('livewire.admin.reports.parties-report', [
            'monthNames' => $monthNames,
            'years' => range($currentYear - 5, $currentYear + 5),
            'selectedMonth' => $this->month,
            'selectedYear' => $this->year,
            'summary' => $this->summary,
        ]);
    }
}

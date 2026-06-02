<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Trip;
use App\Models\TripDocument;
use Illuminate\Http\Request;

class BiltyController extends Controller
{
    public function index()
    {
        return view('admin.bilty.list');
    }

    public function create()
    {
        if (!request()->filled('trip_id')) {
            return redirect()->route('trips.index');
        }

        return view('admin.trip.documents', [
            'tripId' => (int) request('trip_id', 0),
            'step' => 1,
        ]);
    }

    public function store(Request $request)
    {
        //
    }

    public function show(string $id)
    {
        $trip = Trip::with(['party', 'truck', 'driver', 'advances'])->findOrFail($id);
        $document = TripDocument::bilty()->where('trip_id', $id)->first() ?? new TripDocument(['data' => []]);

        return view('admin.bilty.template', compact('trip', 'document'));
    }

    public function edit(string $id)
    {
        if (!$id) {
            return redirect()->route('trips.index');
        }

        return view('admin.trip.documents', [
            'tripId' => (int) $id,
            'step' => 1,
        ]);
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function print(string $id)
    {
        return $this->show($id);
    }

    public function download(string $id)
    {
        $trip = Trip::with(['party', 'truck', 'driver', 'advances'])->findOrFail($id);
        $document = TripDocument::bilty()->where('trip_id', $id)->first() ?? new TripDocument(['data' => []]);

        if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            return \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.bilty.template', compact('trip', 'document'))
                ->download(($document->document_number ?? 'bilty') . '.pdf');
        }

        return view('admin.bilty.template', compact('trip', 'document'));
    }

    public function destroy(string $id)
    {
        //
    }
}

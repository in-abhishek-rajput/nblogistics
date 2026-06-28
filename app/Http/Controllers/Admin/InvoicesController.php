<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Trip;
use App\Models\TripDocument;
use Illuminate\Http\Request;

class InvoicesController extends Controller
{
    public function index()
    {
        return view('admin.invoice.list');
    }

    public function create()
    {
        if (!request()->filled('trip_id')) {
            return redirect()->route('trips.index');
        }

        return view('admin.trip.documents', [
            'tripId' => (int) request('trip_id', 0),
            'step' => 2,
        ]);
    }

    public function store(Request $request)
    {
        //
    }

    public function show(string $id)
    {
        $trip = Trip::with(['party', 'truck', 'driver', 'advances'])->findOrFail($id);
        $document = TripDocument::invoice()->where('trip_id', $id)->first() ?? new TripDocument(['data' => []]);

        return view('admin.bill.template', compact('trip', 'document'));
    }

    public function edit(string $id)
    {
        if (!$id) {
            return redirect()->route('trips.index');
        }

        return view('admin.trip.documents', [
            'tripId' => (int) $id,
            'step' => 2,
        ]);
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function print(string $id)
    {
        $trip = Trip::with(['party', 'truck', 'driver', 'advances'])->findOrFail($id);
        $document = TripDocument::invoice()->where('trip_id', $id)->first() ?? new TripDocument(['data' => []]);

        return view('admin.bill.template', compact('trip', 'document'))->with('autoPrint', true);
    }

    public function destroy(string $id)
    {
        //
    }
}

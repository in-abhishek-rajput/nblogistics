<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Trip;
use Illuminate\Http\Request;

class TripsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $total_trips = Trip::count();
        $pending_trips = Trip::where('status', 'pending')->count();
        $ongoing_trips = Trip::where('status', 'ongoing')->count();
        $completed_trips = Trip::where('status', 'completed')->count();
        return view('admin.trip.list', compact('total_trips', 'pending_trips', 'ongoing_trips', 'completed_trips'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Generate the digital invoice for the trip
     */
    public function digitalInvoice(string $id)
    {
        $trip = Trip::with(['party', 'truck', 'driver', 'advances', 'charges', 'payments'])->findOrFail($id);
        
        $totalAdvances = $trip->advances->sum('amount');
        $totalCharges = $trip->charges->where('charge_direction', 'add_to_bill')->sum('amount') - $trip->charges->where('charge_direction', 'reduce_from_bill')->sum('amount');
        $totalPayments = $trip->payments->sum('amount');
        $pendingBalance = ($trip->freight_amount ?? 0) - $totalAdvances - $totalPayments + $totalCharges;

        return view('admin.trip.digital-invoice', compact('trip', 'totalAdvances', 'totalCharges', 'totalPayments', 'pendingBalance'))->with('autoPrint', true);
    }
    public function podPrint(string $id)
    {
        $trip = Trip::findOrFail($id);
        
        if (!$trip->pod_receipt) {
            abort(404, 'POD not found');
        }

        return view('admin.trip.pod-print', compact('trip'))->with('autoPrint', true);
    }
}

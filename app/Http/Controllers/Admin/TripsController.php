<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Trip;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

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
    public function shareWhatsappInvoice(string $id)
    {
        $trip = Trip::with(['party', 'truck', 'driver', 'advances', 'charges', 'payments'])->findOrFail($id);

        $totalAdvances  = $trip->advances->sum('amount');
        $totalCharges   = $trip->charges->where('charge_direction', 'add_to_bill')->sum('amount')
                        - $trip->charges->where('charge_direction', 'reduce_from_bill')->sum('amount');
        $totalPayments  = $trip->payments->sum('amount');
        $pendingBalance = ($trip->freight_amount ?? 0) - $totalAdvances - $totalPayments + $totalCharges;

        // Use a dedicated dompdf-optimised blade (table layout, absolute image paths, @page margins)
        $pdf = Pdf::loadView('admin.trip.digital-invoice-pdf', [
            'trip'           => $trip,
            'totalAdvances'  => $totalAdvances,
            'totalCharges'   => $totalCharges,
            'totalPayments'  => $totalPayments,
            'pendingBalance' => $pendingBalance,
        ])
        ->setPaper('a4', 'portrait')  // explicit A4 portrait
        ->setOption('dpi', 150)       // higher DPI for sharper text & images
        ->setOption('isRemoteEnabled', true)  // allow loading images from storage
        ->setOption('chroot', base_path()); // restrict to project root for security

        // Save to storage so it can be shared via WhatsApp link
        $filename  = 'trip_invoice_' . $id . '.pdf';
        $storagePath = 'invoices/' . $filename;
        Storage::disk('public')->put($storagePath, $pdf->output());

        // Stream the PDF directly to the browser (also usable as a shareable URL)
        return $pdf->stream($filename);
    }
}

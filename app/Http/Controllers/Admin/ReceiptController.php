<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReceiptController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
     * Display the specified resource (printable Money Receipt).
     */
    public function show(string $id)
    {
        $trip = \App\Models\Trip::with(['party', 'advances'])->find($id);
        $document = \App\Models\TripDocument::receipt()->where('trip_id', $id)->first() ?? new \App\Models\TripDocument(['data' => []]);

        return view('admin.receipt.template', compact('trip', 'document'));
    }

    /**
     * Print the specified resource (Money Receipt).
     */
    public function print(string $id)
    {
        $trip = \App\Models\Trip::with(['party', 'advances'])->find($id);
        $document = \App\Models\TripDocument::receipt()->where('trip_id', $id)->first() ?? new \App\Models\TripDocument(['data' => []]);

        return view('admin.receipt.template', compact('trip', 'document'))->with('autoPrint', true);
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
}

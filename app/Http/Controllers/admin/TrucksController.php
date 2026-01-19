<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Truck;
use Illuminate\Http\Request;

class TrucksController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $total_trucks = Truck::all()->count();
        $total_self_trucks = Truck::where('ownership','self')->count();
        $total_market_trucks = Truck::where('ownership','market')->count();
        return view('admin.truck.list',compact('total_trucks','total_self_trucks','total_market_trucks'));
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
}

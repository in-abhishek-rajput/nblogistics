<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    /**
     * Show the daily freight report.
     */
    public function dailyFreight()
    {
        return view('admin.reports.daily-freight');
    }

    /**
     * Print the daily freight report.
     */
    public function dailyFreightPrint($date)
    {
        $trips = \App\Models\Trip::with(['party', 'truck', 'driver'])
            ->whereDate('start_date', $date)
            ->orderBy('id', 'asc')
            ->get();
            
        return view('admin.reports.print.daily-freight', compact('trips', 'date'));
    }

    public function trips()
    {
        return view('admin.reports.trips');
    }

    /**
     * Show the trucks report.
     */
    public function trucks()
    {
        return view('admin.reports.trucks');
    }

    /**
     * Show the drivers report.
     */
    public function drivers()
    {
        return view('admin.reports.drivers');
    }

    /**
     * Show the parties report.
     */
    public function parties()
    {
        return view('admin.reports.parties');
    }
}

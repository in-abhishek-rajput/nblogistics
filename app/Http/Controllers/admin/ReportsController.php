<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    /**
     * Show the trips report.
     */
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
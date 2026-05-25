<?php

namespace App\Livewire\Admin;

use Livewire\Component;

class Dashboard extends Component
{
    // Static data for Revenue vs Expense vs Profit Chart
    public $revenueExpenseChartData = [
        'months' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        'revenue' => [50000, 70000, 65000, 90000, 85000, 100000],
        'expense' => [30000, 40000, 35000, 50000, 45000, 55000],
        'profit' => [20000, 30000, 30000, 40000, 40000, 45000],
    ];

    // Static data for Trip Status Donut Chart
    public $tripStatusChartData = [
        'labels' => ['Active', 'Completed', 'Pending', 'Cancelled'],
        'data' => [25, 70, 10, 5]
    ];

    public function render()
    {
        return view('livewire.admin.dashboard');
    }
}

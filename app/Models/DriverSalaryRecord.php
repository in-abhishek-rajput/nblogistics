<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverSalaryRecord extends Model
{
    protected $fillable = [
        'driver_id',
        'month',
        'total_days',
        'present_days',
        'absent_days',
        'half_days',
        'gross_salary',
        'advance_deduction',
        'net_salary',
        'status',
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }
}

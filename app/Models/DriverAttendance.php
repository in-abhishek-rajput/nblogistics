<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverAttendance extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'driver_id',
        'attendance_date',
        'status',
    ];

    /**
     * Get the driver that owns the attendance.
     */
    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }
}

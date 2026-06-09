<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverAdvance extends Model
{
    protected $fillable = [
        'driver_id',
        'advance_date',
        'amount',
        'remarks',
        'created_by',
    ];

    /**
     * Get the driver that owns the advance.
     */
    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    /**
     * Get the user who created the advance.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

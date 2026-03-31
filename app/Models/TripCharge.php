<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TripCharge extends Model
{
    protected $fillable = [
        'trip_id',
        'charge_direction',
        'charge_type',
        'amount',
        'date',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'date',
    ];

    /**
     * Relationship with Trip.
     */
    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }
}

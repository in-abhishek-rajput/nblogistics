<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TripAdvance extends Model
{
    protected $fillable = [
        'trip_id',
        'amount',
        'payment_method',
        'payment_date',
        'received_by_driver',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
        'received_by_driver' => 'boolean',
    ];

    /**
     * Relationship with Trip.
     */
    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }
}

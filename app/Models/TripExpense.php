<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TripExpense extends Model
{
    protected $fillable = [
        'trip_id',
        'expense_type',
        'amount',
        'expense_date',
        'payment_mode',
        'add_to_party_bill',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
        'add_to_party_bill' => 'boolean',
    ];

    /**
     * Relationship with Trip.
     */
    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }
}
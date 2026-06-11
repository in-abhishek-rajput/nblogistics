<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TruckEmiPayment extends Model
{
    protected $fillable = [
        'truck_emi_id',
        'due_date',
        'amount',
        'payment_date',
        'status',
        'remarks',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'due_date' => 'date',
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function emi(): BelongsTo
    {
        return $this->belongsTo(TruckEmi::class, 'truck_emi_id');
    }
}

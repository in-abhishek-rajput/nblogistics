<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TruckEmi extends Model
{
    protected $fillable = [
        'truck_id',
        'finance_company',
        'monthly_emi',
        'due_day',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'monthly_emi' => 'decimal:2',
        'due_day' => 'integer',
    ];

    public function truck(): BelongsTo
    {
        return $this->belongsTo(Truck::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(TruckEmiPayment::class, 'truck_emi_id');
    }
}

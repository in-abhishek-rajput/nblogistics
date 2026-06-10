<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TruckFuelExpense extends Model
{
    protected $fillable = [
        'truck_id',
        'expense_amount',
        'fuel_quantity',
        'rate_per_litre',
        'is_full_tank',
        'current_km_reading',
        'payment_mode',
        'shop_name',
        'driver_id',
        'driver_name',
        'transaction_id',
        'diesel_pump_name',
        'expense_date',
        'bill_file',
        'remarks',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'expense_amount' => 'decimal:2',
        'fuel_quantity' => 'decimal:2',
        'rate_per_litre' => 'decimal:2',
        'is_full_tank' => 'boolean',
        'current_km_reading' => 'integer',
        'expense_date' => 'date',
    ];

    public function truck(): BelongsTo
    {
        return $this->belongsTo(Truck::class);
    }
}

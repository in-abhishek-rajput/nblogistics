<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TruckMaintenanceExpense extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'truck_id',
        'expense_type',
        'amount',
        'expense_date',
        'payment_mode',
        'shop_name',
        'driver_id',
        'driver_name',
        'transaction_id',
        'current_km_reading',
        'notes',
        'expense_image',
        'due_date',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
        'due_date' => 'date',
        'current_km_reading' => 'integer',
    ];

    public function truck(): BelongsTo
    {
        return $this->belongsTo(Truck::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }
}

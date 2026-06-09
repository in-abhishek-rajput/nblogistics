<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Truck extends Model
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     * These fields can be filled via mass assignment for flexibility and security.
     */
    protected $fillable = [
        'truck_number',
        'truck_type',
        'ownership',
        'status',
        'driver_id',
        'deleted_by',
    ];

    /**
     * Scope for searching trucks by name or number.
     * This allows dynamic filtering across multiple fields.
     */
    public function scopeSearch(Builder $query, string $search = null): Builder
    {
        if ($search) {
            return $query->where('truck_number', 'like', "%{$search}%");
        }
        return $query;
    }

    /**
     * Scope for filtering by status.
     * Allows filtering trucks by their current status.
     */
    public function scopeStatus(Builder $query, string $status = null): Builder
    {
        if ($status) {
            return $query->where('status', $status);
        }
        return $query;
    }

    /**
     * Scope for filtering by type.
     * Allows filtering trucks by their type.
     */
    public function scopeType(Builder $query, string $type = null): Builder
    {
        if ($type) {
            return $query->where('truck_type', $type);
        }
        return $query;
    }

    public function scopeOwnership(Builder $query, string $ownership = null): Builder
    {
        if ($ownership) {
            return $query->where('ownership', $ownership);
        }
        return $query;
    }

    /**
     * Scope for active trucks (status = 'available').
     * Convenient method to get only available trucks.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'available');
    }

    /**
     * Relationship with Driver.
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    /**
     * Relationship with Trips.
     */
    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class);
    }

    /**
     * Relationship with Truck EMIs.
     */
    public function truckEmis(): HasMany
    {
        return $this->hasMany(TruckEmi::class);
    }

    /**
     * Relationship with Truck EMI payments through the EMI master record.
     */
    public function truckEmiPayments(): HasManyThrough
    {
        return $this->hasManyThrough(
            TruckEmiPayment::class,
            TruckEmi::class,
            'truck_id',
            'truck_emi_id',
            'id',
            'id'
        );
    }
}

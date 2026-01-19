<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Truck extends Model
{
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
}

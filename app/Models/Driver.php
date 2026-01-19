<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Driver extends Model
{
    /**
     * The attributes that are mass assignable.
     * These fields can be filled via mass assignment for flexibility and security.
     */
    protected $fillable = [
        'name',
        'email',
        'mobile',
        'status',
        'opening_balance',
    ];

    /**
     * Scope for searching drivers by name, email, or mobile.
     * This allows dynamic filtering across multiple fields.
     */
    public function scopeSearch(Builder $query, string $search = null): Builder
    {
        if ($search) {
            return $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%");
            });
        }
        return $query;
    }

    /**
     * Scope for filtering by status.
     * Allows filtering drivers by their current status.
     */
    public function scopeStatus(Builder $query, string $status = null): Builder
    {
        if ($status) {
            return $query->where('status', $status);
        }
        return $query;
    }

    /**
     * Scope for active drivers (status = 'available').
     * Convenient method to get only available drivers.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'available');
    }

    public function truck()
    {
        return $this->hasOne(Truck::class,'driver_id');
    }
}


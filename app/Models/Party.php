<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Party extends Model
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
        'opening_balance_date',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'opening_balance_date' => 'date',
    ];

    /**
     * Scope for searching parties by name, email, or mobile.
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
     * Allows filtering parties by their current status.
     */
    public function scopeStatus(Builder $query, string $status = null): Builder
    {
        if ($status) {
            return $query->where('status', $status);
        }
        return $query;
    }

    /**
     * Scope for active parties (status = 'active').
     * Convenient method to get only active parties.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }
}

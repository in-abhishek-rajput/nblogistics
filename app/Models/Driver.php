<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Trip;

class Driver extends Model
{
    use SoftDeletes;
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
        'base_salary',
        'deleted_by',
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

    /**
     * Get the attendances for the driver.
     */
    public function attendances()
    {
        return $this->hasMany(DriverAttendance::class);
    }

    /**
     * Get the trips for the driver.
     */
    public function trips()
    {
        return $this->hasMany(Trip::class);
    }

    /**
     * Get the advances for the driver.
     */
    public function driverSalaryRecords()
    {
        return $this->hasMany(DriverSalaryRecord::class);
    }

    /**
     * Get the advances associated with the driver.
     */
    public function advances()
    {
        return $this->hasMany(DriverAdvance::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Driver;
use App\Models\Truck;

class Trip extends Model
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     * These fields can be filled via mass assignment for flexibility and security.
     */
    protected $fillable = [
        'party_id',
        'truck_id',
        'driver_id',
        'origin',
        'destination',
        'billing_type',
        'freight_amount',
        'start_date',
        'start_km',
        'end_date',
        'end_km',
        'status',
        'lr_number',
        'material_name',
        'note',
        'pod_received_date',
        'pod_submitted_date',
        'settled_date',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'freight_amount' => 'decimal:2',
    ];

    /**
     * Scope for searching trips by party name, truck number, origin, or destination.
     * This allows dynamic filtering across multiple fields.
     */
    public function scopeSearch(Builder $query, string $search = null): Builder
    {
        if ($search) {
            return $query->where(function ($q) use ($search) {
                $q->whereHas('party', function ($partyQuery) use ($search) {
                    $partyQuery->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('truck', function ($truckQuery) use ($search) {
                    $truckQuery->where('truck_number', 'like', "%{$search}%");
                })
                ->orWhereHas('driver', function ($driverQuery) use ($search) {
                    $driverQuery->where('name', 'like', "%{$search}%");
                })
                ->orWhere('origin', 'like', "%{$search}%")
                ->orWhere('destination', 'like', "%{$search}%");
            });
        }
        return $query;
    }

    /**
     * Scope for filtering by status.
     * Allows filtering trips by their current status.
     */
    public function scopeStatus(Builder $query, string $status = null): Builder
    {
        if ($status) {
            return $query->where('status', $status);
        }
        return $query;
    }

    /**
     * Scope for filtering by billing type.
     * Allows filtering trips by their billing type.
     */
    public function scopeBillingType(Builder $query, string $billingType = null): Builder
    {
        if ($billingType) {
            return $query->where('billing_type', $billingType);
        }
        return $query;
    }

    /**
     * Scope for active trips (status not completed).
     * Convenient method to get only active trips.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', '!=', 'completed');
    }

    /**
     * Relationship with Party.
     */
    public function party(): BelongsTo
    {
        return $this->belongsTo(Party::class);
    }

    /**
     * Relationship with Truck.
     */
    public function truck(): BelongsTo
    {
        return $this->belongsTo(Truck::class);
    }

    /**
     * Relationship with Driver.
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    /**
     * Boot method to handle status synchronization.
     * Automatically updates driver and truck statuses based on trip status.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($trip) {
            // Determine the busy status for driver and truck
            $busyStatus = 'not_available'; // Using 'not_available' as busy/in use
            $availableStatus = 'available';

            // Get previous driver and truck if updating
            $originalDriverId = $trip->getOriginal('driver_id');
            $originalTruckId = $trip->getOriginal('truck_id');

            // If trip is active (not completed), mark driver and truck as busy
            if ($trip->status !== 'completed') {
                if ($trip->driver_id) {
                    $trip->driver()->update(['status' => $busyStatus]);
                }
                if ($trip->truck_id) {
                    $trip->truck()->update(['status' => $busyStatus]);
                }
            } else {
                // If trip is completed, mark driver and truck as available
                if ($trip->driver_id) {
                    $trip->driver()->update(['status' => $availableStatus]);
                }
                if ($trip->truck_id) {
                    $trip->truck()->update(['status' => $availableStatus]);
                }
            }

            // If driver changed, free up old driver
            if ($originalDriverId && $originalDriverId != $trip->driver_id) {
                Driver::where('id', $originalDriverId)->update(['status' => $availableStatus]);
            }

            // If truck changed, free up old truck
            if ($originalTruckId && $originalTruckId != $trip->truck_id) {
                Truck::where('id', $originalTruckId)->update(['status' => $availableStatus]);
            }
        });
    }
}

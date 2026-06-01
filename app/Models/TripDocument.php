<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TripDocument extends Model
{
    protected $fillable = [
        'trip_id',
        'document_type',
        'document_number',
        'document_date',
        'data',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'data' => 'array',
        'document_date' => 'date',
    ];

    /**
     * Scope for Bilty documents.
     */
    public function scopeBilty(Builder $query): Builder
    {
        return $query->where('document_type', 'bilty');
    }

    /**
     * Scope for Invoice documents.
     */
    public function scopeInvoice(Builder $query): Builder
    {
        return $query->where('document_type', 'invoice');
    }

    /**
     * Scope for Receipt documents.
     */
    public function scopeReceipt(Builder $query): Builder
    {
        return $query->where('document_type', 'receipt');
    }

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

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
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
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

    public function scopeStatus(Builder $query, string $status = null): Builder
    {
        if ($status) {
            return $query->where('status', $status);
        }

        return $query;
    }

    public function scopeForDateRange(Builder $query, ?string $from = null, ?string $to = null): Builder
    {
        if ($from && $to) {
            return $query->whereBetween('document_date', [$from, $to]);
        }

        if ($from) {
            return $query->whereDate('document_date', $from);
        }

        return $query;
    }

    public function getStatusLabelAttribute(): string
    {
        return config("trip_documents.statuses.{$this->status}.label", ucfirst((string) $this->status));
    }

    public function getStatusColorAttribute(): string
    {
        return config("trip_documents.statuses.{$this->status}.color", 'secondary');
    }

    public function getDocumentLabelAttribute(): string
    {
        return config("trip_documents.documents.{$this->document_type}.label", ucfirst((string) $this->document_type));
    }

    public function getPrintRouteAttribute(): string
    {
        return config("trip_documents.documents.{$this->document_type}.route_prefix", 'documents');
    }

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}

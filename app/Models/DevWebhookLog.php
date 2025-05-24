<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * DevWebhookLog Model
 * 
 * ⚠️ DEV ONLY MODEL - DO NOT USE IN PRODUCTION ⚠️
 * This model is specifically for development webhook testing and logging
 * 
 * @property int $id
 * @property string|null $webhook_id
 * @property string $object_type
 * @property string $object_id
 * @property string $event_status
 * @property float|null $amount
 * @property string|null $currency
 * @property string|null $gateway_reference
 * @property string|null $payment_reference
 * @property string|null $received_hashstring
 * @property string|null $calculated_hashstring
 * @property bool $hash_valid
 * @property array|null $webhook_headers
 * @property array $webhook_payload
 * @property string|null $processing_notes
 * @property string $processing_status
 * @property \Illuminate\Support\Carbon|null $processed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class DevWebhookLog extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'dev_webhook_logs';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'webhook_id',
        'object_type',
        'object_id',
        'event_status',
        'amount',
        'currency',
        'gateway_reference',
        'payment_reference',
        'received_hashstring',
        'calculated_hashstring',
        'hash_valid',
        'webhook_headers',
        'webhook_payload',
        'processing_notes',
        'processing_status',
        'processed_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'webhook_headers' => 'array',
        'webhook_payload' => 'array',
        'hash_valid' => 'boolean',
        'amount' => 'decimal:3',
        'processed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Scope for filtering by object type
     */
    public function scopeByObjectType($query, string $objectType)
    {
        return $query->where('object_type', $objectType);
    }

    /**
     * Scope for filtering by processing status
     */
    public function scopeByProcessingStatus($query, string $status)
    {
        return $query->where('processing_status', $status);
    }

    /**
     * Scope for recent webhooks
     */
    public function scopeRecent($query, int $hours = 24)
    {
        return $query->where('created_at', '>=', now()->subHours($hours));
    }

    /**
     * Get the formatted amount with currency
     */
    public function getFormattedAmountAttribute(): string
    {
        if (!$this->amount || !$this->currency) {
            return 'N/A';
        }
        
        return number_format($this->amount, $this->getDecimalPlaces(), '.', ',') . ' ' . $this->currency;
    }

    /**
     * Get decimal places for currency
     */
    private function getDecimalPlaces(): int
    {
        $threePlaceCurrencies = ['BHD', 'KWD', 'OMR'];
        return in_array($this->currency, $threePlaceCurrencies) ? 3 : 2;
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->processing_status) {
            'received' => 'bg-blue-100 text-blue-800',
            'validated' => 'bg-yellow-100 text-yellow-800',
            'processed' => 'bg-green-100 text-green-800',
            'failed' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Get event status color
     */
    public function getEventStatusColorAttribute(): string
    {
        return match($this->event_status) {
            'CAPTURED' => 'bg-green-100 text-green-800',
            'FAILED', 'DECLINED' => 'bg-red-100 text-red-800',
            'AUTHORIZED' => 'bg-blue-100 text-blue-800',
            'INITIATED', 'PENDING' => 'bg-yellow-100 text-yellow-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}

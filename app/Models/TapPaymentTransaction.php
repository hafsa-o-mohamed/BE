<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TapPaymentTransaction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'transaction_id',
        'charge_id',
        'token_id',
        'payment_agreement_id',
        'amount',
        'currency',
        'status',
        'payment_method',
        'payment_type',
        'card_brand',
        'card_last_four',
        'card_first_six',
        'user_id',
        'request_data',
        'response_data',
        'error_message',
        'idempotency_key',
        'ip_address',
        'user_agent',
        'is_live',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:3',
        'request_data' => 'array',
        'response_data' => 'array',
        'is_live' => 'boolean',
    ];

    /**
     * Get the user that owns the transaction.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

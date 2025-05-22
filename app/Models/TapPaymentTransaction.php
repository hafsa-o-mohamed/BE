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
        'amount',
        'currency',
        'description',
        'reference_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'payment_method',
        'card_brand',
        'card_last_four',
        'status',
        'request_payload',
        'response_payload',
        'webhook_payload',
        'error_code',
        'error_message',
        'ip_address',
        'user_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'request_payload' => 'array',
        'response_payload' => 'array',
        'webhook_payload' => 'array',
        'amount' => 'decimal:3',
    ];

    /**
     * Get the user that owns the transaction.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

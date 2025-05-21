<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ElectricityBill extends Model
{
    protected $fillable = [
        'default_balance',
        'current_balance',
        'subtracted_amount',
        'building_id'
    ];

    protected $casts = [
        'default_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'subtracted_amount' => 'decimal:2',
    ];

    protected $with = ['building']; // Eager load building relationship

    public function building()
    {
        return $this->belongsTo(Building::class);
    }
}
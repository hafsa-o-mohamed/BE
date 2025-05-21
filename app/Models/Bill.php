<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    protected $fillable = [
        'owner_id',
        'bill_type',
        'due_amount',
        'status',
        'description',
        'due_date',
        'reference_id',
        'reference_type'
    ];

    // Relationship with User (owner)
    public function owner()
    {
        return $this->belongsTo(ApartmentOwner::class, 'owner_id');
    }
    public function building()
    {
        return $this->belongsTo(Building::class);
    }
    public function apartment()
    {
        return $this->belongsTo(Apartment::class);
    }
}

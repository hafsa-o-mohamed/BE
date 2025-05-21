<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApartmentOwner extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'user_id'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function apartments()
    {
        return $this->hasMany(Apartment::class, 'owner_id');
    }

    public function building()
    {
        return $this->belongsTo(Building::class, 'building_id');
    }

    public function bills()
    {
        return $this->hasMany(Bill::class, 'owner_id');
    }

    public function serviceRequests()
    {
        return $this->hasMany(ServiceRequest::class, 'owner_id');
    }
    
}
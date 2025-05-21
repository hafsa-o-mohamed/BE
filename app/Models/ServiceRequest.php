<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceRequest extends Model
{
    protected $fillable = [
        'owner_id',
        'apartment_id',
        'service_id',
        'due_price',
        'request_date',
        'status',
        'payment_status'
    ];

    protected $casts = [
        'request_date' => 'date'
    ];


    public function owner()
    {
        return $this->belongsTo(ApartmentOwner::class, 'owner_id');
    }

    public function apartment()
    {
        return $this->belongsTo(Apartment::class, 'apartment_id');
    }

    public function service()
    {
        return $this->belongsTo(MaintenanceService::class, 'service_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

 
}
<?php
    
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Apartment extends Model
{
    protected $primaryKey = 'id';
    protected $fillable = [
        'building_id',
        'floor_number',
        'apartment_number',
        'owner_id',
        'owner_number'
    ];

    public function building()
    {
        return $this->belongsTo(Building::class, 'building_id');
    }
    

    public function owner()
    {
        return $this->belongsTo(ApartmentOwner::class, 'owner_id');

    }

    public function serviceRequests()
    {
        return $this->hasMany(ServiceRequest::class, 'apartment_id');
    }

    public function providedServices()
    {
        return $this->hasMany(ProvidedService::class, 'apartment_id');
    }

    
} 
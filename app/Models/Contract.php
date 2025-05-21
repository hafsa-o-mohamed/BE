<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    protected $primaryKey = 'id';
    protected $fillable = [
        'contract_type',
        'building_id',
        'duration',
        'start_date',
        'end_date',
        'status',
        'apartment_id',
        'yearly_price' 
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date'
    ];

    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    public function services()
    {
        return $this->hasMany(ContractService::class);
    }

    public function contractServices()
    {
        return $this->hasMany(ContractService::class);
    }

    public function apartment()
    {
        return $this->belongsTo(Apartment::class);
    }

    public function owner()
    {
        return $this->belongsTo(ApartmentOwner::class, 'apartment_id', 'apartment_id');
    }
} 
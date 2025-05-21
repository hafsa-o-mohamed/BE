<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Building extends Model
{
    protected $primaryKey = 'id';
    protected $fillable = [
        'project_id',
        'building_name',
        'number_of_floors',
        'number_of_apartments'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function contract()
    {
        return $this->hasOne(Contract::class, 'building_id');
    }

    public function apartments()
    {
        return $this->hasMany(Apartment::class, 'building_id');
    }

    public function waterBills()
    {
        return $this->hasMany(WaterBill::class, 'building_id');
    }   

    public function electricityBills()
    {
        return $this->hasMany(ElectricityBill::class, 'building_id');
    }

} 

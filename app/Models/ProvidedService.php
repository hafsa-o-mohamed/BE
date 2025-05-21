<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProvidedService extends Model
{
    protected $primaryKey = 'provided_id';
    protected $fillable = [
        'apartment_id',
        'service_name',
        'date_provided'
    ];

    protected $casts = [
        'date_provided' => 'date'
    ];

    public function apartment()
    {
        return $this->belongsTo(Apartment::class, 'apartment_id');
    }
}
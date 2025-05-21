<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $table = 'services'; // Specify the table name if it doesn't follow Laravel's naming convention
    protected $fillable = ['service_name', 'description', 'image_url']; // Specify the fillable fields


    public function contracts()
    {
        return $this->belongsToMany(Contract::class);
    }

    public function contractServices()
    {
        return $this->hasMany(ContractService::class);
    }
}

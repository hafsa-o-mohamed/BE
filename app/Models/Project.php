<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $primaryKey = 'id';
    protected $fillable = ['project_name', 'address'];

    public function buildings()
    {
        return $this->hasMany(Building::class);
    }
    
    
} 
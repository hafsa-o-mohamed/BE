<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
        'role',
        'fcm_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function owner()
    {
        return $this->hasOne(ApartmentOwner::class, 'user_id');
    }



    public function serviceRequests()
    {
        return $this->hasMany(ServiceRequest::class, 'owner_id');
    }
    public function hasAccessToDashboard()
    {
        return in_array($this->role, ['admin', 'accountant']);
    }
} 
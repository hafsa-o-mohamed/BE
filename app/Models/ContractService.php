<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContractService extends Model
{
    protected $table = 'contract_services';
    protected $primaryKey = 'id';
    protected $fillable = [
        'contract_id',
        'service_id',
        'frequency'
    ];

    const FREQUENCIES = [
        'monthly' => 'مرة في الشهر',
        'yearly' => 'مرة في السنة',
        'quarterly' => 'كل 3 شهور',
        'daily' => 'يومياً',
        'biannually' => 'مرتين في السنة'
    ];

    protected $casts = [
        'frequency' => 'string'
    ];

    public function getFrequencyTextAttribute()
    {
        return self::FREQUENCIES[$this->frequency] ?? '';
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
} 
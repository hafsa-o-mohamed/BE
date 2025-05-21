<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\SuggestionReply;

class Suggestion extends Model
{
    protected $fillable = [
        'user_id',
        'type',      // 'suggestion','complaint'
        'content',
        'status'     // 'pending','reviewed','resolved'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationship with User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function replies()
    {
        return $this->hasMany(SuggestionReply::class, 'suggestion_id');
    }
} 
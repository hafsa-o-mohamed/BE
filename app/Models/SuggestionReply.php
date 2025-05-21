<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuggestionReply extends Model
{
    use HasFactory;


    protected $fillable = [
        'suggestion_id',
        'reply',
        'user_id',
    ];

    // Relationship to user (who replied)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relationship to suggestion
    public function suggestion()
    {
        return $this->belongsTo(Suggestion::class, 'suggestion_id');
    }
}

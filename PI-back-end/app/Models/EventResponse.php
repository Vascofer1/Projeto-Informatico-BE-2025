<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventResponse extends Model
{
    protected $fillable = [
        'event_question_id',
        'response',
    ];

    public function eventQuestion()
    {
        return $this->belongsTo(EventQuestion::class);
    }
    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }
}

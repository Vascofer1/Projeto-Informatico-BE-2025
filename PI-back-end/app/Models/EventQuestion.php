<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventQuestion extends Model
{
    use HasFactory;

    protected $table = 'event_questions';

    protected $fillable = [
        'event_id',
        'question_id',
        'mandatory',
    ];

    public function events()
    {
        return $this->belongsTo(Event::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id');
    }

    public function eventResponses()
    {
        return $this->hasMany(EventResponse::class);
    }
}

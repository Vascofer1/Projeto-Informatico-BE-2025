<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Participant;
use App\Models\Event_questions;

class Event extends Model
{
    
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'start_date',
        'end_date',
        'location',
        'limit_participants',
        'category',
        'type',
        "start_time",
        "end_time",
        'image',

    ];

    public function participants()
    {
        return $this->hasMany(Participant::class);
    }

    public function event_questions()
    {
        return $this->hasMany(Event_questions::class);
    }
}

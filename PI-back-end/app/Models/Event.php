<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Participant;
use App\Models\EventQuestion;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{

    use HasFactory;
    use SoftDeletes;

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

    public function questions()
    {
        return $this->belongsToMany(Question::class, 'event_questions')
            ->withPivot('mandatory')
            ->withTimestamps();
    }

    public function participants()
    {
        return $this->hasMany(Participant::class);
    }

}

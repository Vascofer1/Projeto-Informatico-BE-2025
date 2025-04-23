<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\EventQuestion;

class Question extends Model
{
    //
    protected $fillable = [
        'name',
        'description',
        'options',
        'category',
    ];
    protected $casts = [
        'options' => 'array',
    ];

    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_questions')
            ->withPivot('mandatory')
            ->withTimestamps();
    }

    public function event_question()
    {
        return $this->hasMany(EventQuestion::class);
    }
}

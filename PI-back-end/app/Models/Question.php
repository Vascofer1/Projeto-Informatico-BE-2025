<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Event_questions;

class Question extends Model
{
    //
    protected $fillable = [
        'question',
        'description',
        'question_options',
    ];
    protected $casts = [
        'question_options' => 'array',
    ];

    public function event_questions()
    {
        return $this->hasMany(Event_questions::class);
    }
}

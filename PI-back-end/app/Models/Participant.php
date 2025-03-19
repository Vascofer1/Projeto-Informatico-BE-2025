<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Event;

class Participant extends Model
{
    
    protected $fillable = [
        'name',
        'email',
        'phone',
        'event_id',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}

<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Participant;

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
        'start_time',
        'end_time',
        'image',

    ];

    public function participants()
    {
        return $this->hasMany(Participant::class);
    }
}

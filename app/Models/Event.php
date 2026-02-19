<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;
    
    protected $attributes = [
        'adult_status'   => 0,
        'child_above_10' => 0,
        'child_below_5'  => 0,
        'child_5_to_10'  => 0,
        'feedback_status'=> 0,
    ];

    protected $table = 'events';

    public function registrations()
    {
        return $this->hasMany(Eventregister::class, 'event_id');
    }

    public function eventFeedbacks()
    {
        return $this->hasMany(EventFeedback::class, 'event_id');
    }


   
}
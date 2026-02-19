<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventFeedback extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'event_id',
        'user_id',
        'role',
        'question_id',
        'answer',
        'suggestion',
        'created_at',
        'updated_at',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function question()
    {
        return $this->belongsTo(EventFeedBackQuestion::class, 'question_id');
    }

     public function eventFeedbackQuestions()
    {
        return $this->hasMany(EventFeedbackQuestion::class, 'event_id');
    }
    

}
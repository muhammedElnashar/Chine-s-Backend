<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyTextQuestionAnswer extends Model
{
    protected $fillable =['daily_text_question_id', 'answer_text', 'is_correct'];
    public function question()
    {
        return $this->belongsTo(DailyTextQuestion::class, 'daily_text_question_id');
    }

}

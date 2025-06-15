<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamQuestion extends Model
{
    protected $fillable = ['exam_id', 'question_text','question_type','question_media_url','explanation'];
    public function exam()
    {
        return $this->belongsTo(Exam::class, 'exam_id');
    }

    public function answers()
    {
        return $this->hasMany(ExamAnswer::class, 'question_id');
    }
}

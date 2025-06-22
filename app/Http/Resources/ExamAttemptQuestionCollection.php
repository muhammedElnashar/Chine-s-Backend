<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ExamAttemptQuestionCollection extends ResourceCollection
{
    protected $studentAnswers;
    public function __construct($resource, $studentAnswers)
    {
        parent::__construct($resource);
        $this->studentAnswers = $studentAnswers;
    }
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray($request): array
    {
        return $this->collection->map(function ($question) {
            $studentAnswer = $this->studentAnswers->firstWhere('question_id', $question->id);
            return new QuestionWithAttemptResource($question->setRelation('studentAnswer', $studentAnswer));
        })->toArray();
    }}

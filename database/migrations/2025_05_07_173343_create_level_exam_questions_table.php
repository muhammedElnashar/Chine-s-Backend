<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('level_exam_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('level_exam_id')->constrained('level_exams')->onDelete('cascade'); // رابط مع level_exams
            $table->string('question_text');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('level_exam_questions');
    }
};

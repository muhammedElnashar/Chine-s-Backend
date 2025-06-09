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
        Schema::create('daily_text_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_exercise_id')->constrained()->onDelete('cascade');
            $table->string('question_type')->default('text'); // text, image, video, audio
            $table->text('question_text')->nullable();
            $table->string('question_media_url')->nullable();
            $table->text('explanation')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_text_questions');
    }
};

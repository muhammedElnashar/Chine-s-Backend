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
        Schema::create('daily_audio_words', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_exercise_id')->constrained()->onDelete('cascade');
            $table->string('audio_file');
            $table->string('word_meaning');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_audio_words');
    }
};

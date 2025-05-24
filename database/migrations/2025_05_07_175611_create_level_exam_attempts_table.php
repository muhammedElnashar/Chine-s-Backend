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
        Schema::create('level_exam_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('level_exam_id')->constrained('level_exams')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->integer('score')->nullable();
            $table->timestamps();

            $table->unique(['level_exam_id', 'student_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('level_exam_attempts');
    }
};

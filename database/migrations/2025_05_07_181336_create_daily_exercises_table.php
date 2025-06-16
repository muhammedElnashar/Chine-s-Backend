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
        Schema::create('daily_exercises', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('type');
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->unique(['date', 'type']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_exercises');
    }
};

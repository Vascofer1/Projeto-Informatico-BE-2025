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
        Schema::create('event_response', function (Blueprint $table) {
            $table->id();
            $table->foreignId('participant_id')->constrained()->onDelete('cascade');
            $table->foreignId('event_question_id')->constrained('event_question')->onDelete('cascade');
            $table->text('response')->nullable();
            $table->boolean('answered')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_response');
    }
};

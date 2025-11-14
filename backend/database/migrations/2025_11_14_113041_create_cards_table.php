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
        Schema::create('cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deck_id')->constrained('decks')->cascadeOnDelete();
            $table->text('question');
            $table->text('answer');
            // tags as JSON for future flexibility
            $table->json('tags')->nullable();
            $table->unsignedTinyInteger('box_level')->default(1); // 1..5
            $table->unsignedInteger('repetitions')->default(0);
            $table->float('easiness_factor')->default(2.5);
            $table->unsignedInteger('interval_days')->default(0);
            $table->timestamp('next_review_at')->nullable();
            $table->timestamp('last_reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cards');
    }
};

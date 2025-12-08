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
        Schema::create('verification_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('contact')->nullable(); // email or phone at time of sending
            $table->string('code', 10);
            $table->string('method', 10); // 'email' or 'sms'
            $table->string('purpose', 20)->default('login'); // extensible: login, reset, etc.
            $table->string('token', 64)->unique(); // random token to reference this verification session
            $table->timestamp('expires_at');
            $table->boolean('used')->default(false);
            $table->timestamps();
            $table->index(['user_id','purpose']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verification_codes');
    }
};

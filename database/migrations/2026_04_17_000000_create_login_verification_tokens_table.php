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
        Schema::create('login_verification_tokens', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->string('token', 255)->unique(); // Hashed token
            $table->string('plain_token', 255)->unique(); // Plain token for email link
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('expires_at')->index(); // Token expiration time
            $table->timestamp('verified_at')->nullable(); // When user verified
            $table->boolean('used')->default(false)->index(); // One-time use tracking
            $table->timestamp('created_at')->useCurrent();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('login_verification_tokens');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('webrtc_signals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('consultation_id');
            $table->unsignedBigInteger('sender_id');
            $table->string('sender_role', 20);
            $table->string('type', 20);
            $table->json('payload');
            $table->timestamps();

            $table->index(['consultation_id', 'id']);
            $table->foreign('consultation_id')->references('id')->on('consultations')->cascadeOnDelete();
            $table->foreign('sender_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webrtc_signals');
    }
};

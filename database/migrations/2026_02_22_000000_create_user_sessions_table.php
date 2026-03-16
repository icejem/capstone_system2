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
        Schema::create('user_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('device_identifier')->nullable()->comment('Browser/Device unique identifier');
            $table->dateTime('login_at')->comment('Session start time');
            $table->dateTime('logout_at')->nullable()->comment('Session end time');
            $table->integer('active_minutes')->default(0)->comment('Duration in minutes');
            $table->string('device_type')->nullable()->comment('Device OS/Browser info');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['user_id', 'logout_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_sessions');
    }
};

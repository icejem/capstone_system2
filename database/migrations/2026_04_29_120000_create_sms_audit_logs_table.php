<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sms_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('provider', 30)->index();
            $table->string('stage', 30)->nullable();
            $table->string('status', 20)->index();
            $table->string('phone_number_input', 30)->nullable();
            $table->string('phone_number_normalized', 20)->nullable()->index();
            $table->text('message');
            $table->json('context')->nullable();
            $table->unsignedSmallInteger('provider_http_status')->nullable()->index();
            $table->text('provider_response')->nullable();
            $table->text('provider_error')->nullable();
            $table->text('result_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_audit_logs');
    }
};

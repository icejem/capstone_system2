<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trusted_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('fingerprint_hash', 64);
            $table->string('device_label')->nullable();
            $table->string('device_type')->nullable();
            $table->string('browser')->nullable();
            $table->string('operating_system')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('location')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('trusted_at')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'fingerprint_hash']);
            $table->index(['user_id', 'revoked_at']);
        });

        Schema::table('login_verifications', function (Blueprint $table) {
            $table->string('device_fingerprint_hash', 64)->nullable()->after('device_label');
            $table->foreignId('trusted_device_id')->nullable()->after('device_fingerprint_hash')->constrained('trusted_devices')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('login_verifications', function (Blueprint $table) {
            $table->dropConstrainedForeignId('trusted_device_id');
            $table->dropColumn('device_fingerprint_hash');
        });

        Schema::dropIfExists('trusted_devices');
    }
};

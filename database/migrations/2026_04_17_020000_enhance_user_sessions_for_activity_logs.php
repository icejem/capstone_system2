<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_sessions', function (Blueprint $table) {
            $table->string('browser')->nullable()->after('device_type');
            $table->string('operating_system')->nullable()->after('browser');
            $table->string('ip_address', 45)->nullable()->after('operating_system');
            $table->string('location')->nullable()->after('ip_address');
            $table->text('user_agent')->nullable()->after('location');
            $table->dateTime('last_activity_at')->nullable()->after('logout_at');
            $table->string('logout_reason')->nullable()->after('active_minutes');

            $table->index(['login_at', 'logout_at']);
            $table->index('last_activity_at');
        });
    }

    public function down(): void
    {
        Schema::table('user_sessions', function (Blueprint $table) {
            $table->dropIndex(['login_at', 'logout_at']);
            $table->dropIndex(['last_activity_at']);
            $table->dropColumn([
                'browser',
                'operating_system',
                'ip_address',
                'location',
                'user_agent',
                'last_activity_at',
                'logout_reason',
            ]);
        });
    }
};

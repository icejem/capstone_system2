<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('webrtc_signals', function (Blueprint $table) {
            $table->string('device_session_id', 100)->nullable()->after('sender_role');
        });
    }

    public function down(): void
    {
        Schema::table('webrtc_signals', function (Blueprint $table) {
            $table->dropColumn('device_session_id');
        });
    }
};

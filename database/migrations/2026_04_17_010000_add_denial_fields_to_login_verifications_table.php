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
        Schema::table('login_verifications', function (Blueprint $table) {
            $table->timestamp('denied_at')->nullable()->after('verified_at');
            $table->string('denied_reason')->nullable()->after('denied_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('login_verifications', function (Blueprint $table) {
            $table->dropColumn(['denied_at', 'denied_reason']);
        });
    }
};

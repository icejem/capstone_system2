<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('consultations', function (Blueprint $table) {
            if (! Schema::hasColumn('consultations', 'reminder_30_sent_at')) {
                $table->timestamp('reminder_30_sent_at')->nullable()->after('reminder_sent_at');
            }

            if (! Schema::hasColumn('consultations', 'reminder_10_sent_at')) {
                $table->timestamp('reminder_10_sent_at')->nullable()->after('reminder_30_sent_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('consultations', function (Blueprint $table) {
            if (Schema::hasColumn('consultations', 'reminder_10_sent_at')) {
                $table->dropColumn('reminder_10_sent_at');
            }

            if (Schema::hasColumn('consultations', 'reminder_30_sent_at')) {
                $table->dropColumn('reminder_30_sent_at');
            }
        });
    }
};

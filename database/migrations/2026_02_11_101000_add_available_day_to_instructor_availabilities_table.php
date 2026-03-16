<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('instructor_availabilities', function (Blueprint $table) {
            $table->string('available_day')->nullable()->after('instructor_id');
            $table->date('available_date')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('instructor_availabilities', function (Blueprint $table) {
            $table->dropColumn('available_day');
            $table->date('available_date')->nullable(false)->change();
        });
    }
};

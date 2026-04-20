<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_registration_rosters', function (Blueprint $table) {
            $table->string('year_level', 16)->nullable()->after('last_name');
        });
    }

    public function down(): void
    {
        Schema::table('student_registration_rosters', function (Blueprint $table) {
            $table->dropColumn('year_level');
        });
    }
};

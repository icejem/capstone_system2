<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('instructor_availabilities', function (Blueprint $table) {
            $table->string('semester', 20)->nullable()->after('instructor_id');
            $table->string('academic_year', 15)->nullable()->after('semester');
        });
    }

    public function down(): void
    {
        Schema::table('instructor_availabilities', function (Blueprint $table) {
            $table->dropColumn(['semester', 'academic_year']);
        });
    }
};

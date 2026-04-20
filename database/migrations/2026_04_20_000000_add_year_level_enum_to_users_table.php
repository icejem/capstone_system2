<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'year_level')) {
            Schema::table('users', function (Blueprint $table) {
                $table->enum('year_level', ['1st', '2nd', '3rd', '4th'])
                    ->nullable()
                    ->after('student_id');
            });
        }

        if (Schema::hasColumn('users', 'yearlevel')) {
            DB::table('users')->where('yearlevel', '1st Year')->update(['year_level' => '1st']);
            DB::table('users')->where('yearlevel', '2nd Year')->update(['year_level' => '2nd']);
            DB::table('users')->where('yearlevel', '3rd Year')->update(['year_level' => '3rd']);
            DB::table('users')->where('yearlevel', '4th Year')->update(['year_level' => '4th']);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'year_level')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('year_level');
            });
        }
    }
};

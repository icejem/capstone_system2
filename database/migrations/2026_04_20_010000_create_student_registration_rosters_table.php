<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_registration_rosters', function (Blueprint $table) {
            $table->id();
            $table->string('batch_token', 64)->index();
            $table->string('academic_year', 9)->index();
            $table->enum('semester', ['first', 'second'])->index();
            $table->string('student_id', 32)->index();
            $table->string('first_name');
            $table->string('last_name');
            $table->foreignId('imported_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['academic_year', 'semester', 'student_id'], 'student_registration_rosters_term_student_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_registration_rosters');
    }
};

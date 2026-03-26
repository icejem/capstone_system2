<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name')->nullable()->after('name');
            $table->string('last_name')->nullable()->after('first_name');
            $table->string('middle_name')->nullable()->after('last_name');
        });

        // Keep the backfill database-agnostic so SQLite, MySQL, and Postgres all behave the same.
        DB::table('users')
            ->select(['id', 'name'])
            ->whereNull('first_name')
            ->whereNotNull('name')
            ->orderBy('id')
            ->lazyById()
            ->each(function (object $user): void {
                $name = trim((string) $user->name);

                if ($name === '') {
                    return;
                }

                $parts = preg_split('/\s+/', $name, -1, PREG_SPLIT_NO_EMPTY) ?: [];
                $firstName = array_shift($parts);
                $lastName = count($parts) > 0 ? implode(' ', $parts) : null;

                DB::table('users')
                    ->where('id', $user->id)
                    ->update([
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                    ]);
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['first_name', 'last_name', 'middle_name']);
        });
    }
};

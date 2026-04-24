<?php

namespace App\Services;

use App\Models\StudentRegistrationRoster;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class StudentSemesterAccountService
{
    private const TERM_SYNC_CACHE_KEY = 'student_accounts.last_term_sync_key';

    public static function deactivateAllStudents(): int
    {
        return User::query()
            ->where('user_type', 'student')
            ->where(function ($query) {
                $query->whereNull('account_status')
                    ->orWhere('account_status', '!=', 'suspended');
            })
            ->update([
                'account_status' => 'inactive',
                'updated_at' => now(),
            ]);
    }

    public static function currentSemester(?Carbon $date = null): ?string
    {
        $date ??= Carbon::now('Asia/Manila');
        $month = (int) $date->copy()->timezone('Asia/Manila')->month;

        return match (true) {
            $month >= 8 && $month <= 12 => 'first',
            $month >= 1 && $month <= 5 => 'second',
            default => null,
        };
    }

    public static function currentAcademicYear(?Carbon $date = null): ?string
    {
        $date ??= Carbon::now('Asia/Manila');
        $date = $date->copy()->timezone('Asia/Manila');
        $year = (int) $date->year;
        $semester = self::currentSemester($date);

        return match ($semester) {
            'first' => $year . '-' . ($year + 1),
            'second' => ($year - 1) . '-' . $year,
            default => null,
        };
    }

    /**
     * @param array<int, array<string, mixed>> $rowsToImport
     * @return array{deactivated:int, activated:int}
     */
    public static function syncAccountsForImportedRoster(array $rowsToImport): array
    {
        return DB::transaction(function () use ($rowsToImport): array {
            $deactivatedCount = self::deactivateAllStudents();
            $activatedCount = 0;

            foreach ($rowsToImport as $row) {
                $activatedCount += User::query()
                    ->where('user_type', 'student')
                    ->where('student_id', (string) ($row['student_id'] ?? ''))
                    ->where(function ($query) {
                        $query->whereNull('account_status')
                            ->orWhere('account_status', '!=', 'suspended');
                    })
                    ->update([
                        'account_status' => 'active',
                        'year_level' => $row['year_level'] ?? null,
                        'yearlevel' => User::legacyYearLevelValue($row['year_level'] ?? null),
                        'updated_at' => now(),
                    ]);
            }

            return [
                'deactivated' => $deactivatedCount,
                'activated' => $activatedCount,
            ];
        });
    }

    /**
     * @return array{deactivated:int, activated:int, semester:?string, academic_year:?string}
     */
    public static function syncCurrentTermAccounts(?Carbon $date = null, bool $force = false): array
    {
        $date ??= Carbon::now('Asia/Manila');
        $semester = self::currentSemester($date);
        $academicYear = self::currentAcademicYear($date);
        $syncKey = ($academicYear ?? 'none') . ':' . ($semester ?? 'none') . ':' . $date->copy()->timezone('Asia/Manila')->toDateString();

        if (! $force && Cache::get(self::TERM_SYNC_CACHE_KEY) === $syncKey) {
            return [
                'deactivated' => 0,
                'activated' => 0,
                'semester' => $semester,
                'academic_year' => $academicYear,
            ];
        }

        if ($semester === null || $academicYear === null) {
            $deactivated = self::deactivateAllStudents();
            Cache::forever(self::TERM_SYNC_CACHE_KEY, $syncKey);

            return [
                'deactivated' => $deactivated,
                'activated' => 0,
                'semester' => null,
                'academic_year' => null,
            ];
        }

        $rowsToImport = StudentRegistrationRoster::query()
            ->where('academic_year', $academicYear)
            ->where('semester', $semester)
            ->get(['student_id', 'year_level'])
            ->map(fn (StudentRegistrationRoster $row): array => [
                'student_id' => (string) $row->student_id,
                'year_level' => $row->year_level,
            ])
            ->all();

        $result = $rowsToImport === []
            ? ['deactivated' => self::deactivateAllStudents(), 'activated' => 0]
            : self::syncAccountsForImportedRoster($rowsToImport);

        Cache::forever(self::TERM_SYNC_CACHE_KEY, $syncKey);

        return [
            'deactivated' => $result['deactivated'],
            'activated' => $result['activated'],
            'semester' => $semester,
            'academic_year' => $academicYear,
        ];
    }
}

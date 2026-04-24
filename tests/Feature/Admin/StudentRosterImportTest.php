<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class StudentRosterImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_import_deactivates_old_student_accounts_and_reactivates_students_in_new_roster(): void
    {
        $admin = User::factory()->create([
            'user_type' => 'admin',
            'account_status' => 'active',
            'student_id' => null,
        ]);

        $existingListedStudent = User::factory()->create([
            'student_id' => '11111',
            'account_status' => 'active',
            'year_level' => '1st',
            'yearlevel' => '1st Year',
        ]);

        $inactiveListedStudent = User::factory()->create([
            'student_id' => '22222',
            'account_status' => 'inactive',
            'year_level' => '1st',
            'yearlevel' => '1st Year',
        ]);

        $studentMissingFromRoster = User::factory()->create([
            'student_id' => '33333',
            'account_status' => 'active',
        ]);

        $suspendedListedStudent = User::factory()->create([
            'student_id' => '44444',
            'account_status' => 'suspended',
        ]);

        $instructor = User::factory()->create([
            'user_type' => 'instructor',
            'account_status' => 'active',
            'student_id' => null,
        ]);

        $csv = implode("\n", [
            'student_id,first_name,last_name,year_level',
            '11111,John,Listed,3rd Year',
            '22222,Jane,Inactive,2nd Year',
            '44444,Sam,Suspended,4th Year',
        ]);

        $file = UploadedFile::fake()->createWithContent('students.csv', $csv);

        $response = $this->actingAs($admin)->postJson(route('admin.students.import-csv'), [
            'academic_year' => '2026-2027',
            'semester' => 'first',
            'csv_file' => $file,
        ]);

        $response->assertOk()
            ->assertJsonFragment([
                'created' => 3,
                'skipped' => 0,
            ]);

        $this->assertSame('active', $existingListedStudent->fresh()->account_status);
        $this->assertSame('3rd', $existingListedStudent->fresh()->year_level);

        $this->assertSame('active', $inactiveListedStudent->fresh()->account_status);
        $this->assertSame('2nd', $inactiveListedStudent->fresh()->year_level);

        $this->assertSame('inactive', $studentMissingFromRoster->fresh()->account_status);
        $this->assertSame('suspended', $suspendedListedStudent->fresh()->account_status);
        $this->assertSame('active', $instructor->fresh()->account_status);

        $this->assertDatabaseHas('student_registration_rosters', [
            'academic_year' => '2026-2027',
            'semester' => 'first',
            'student_id' => '11111',
            'year_level' => '3rd',
        ]);
    }
}

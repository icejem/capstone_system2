<?php

namespace Tests\Unit;

use App\Models\StudentRegistrationRoster;
use App\Models\User;
use App\Services\StudentSemesterAccountService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class StudentSemesterAccountServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        Cache::flush();

        parent::tearDown();
    }

    public function test_it_maps_months_to_the_expected_semester_and_academic_year(): void
    {
        $augustDate = Carbon::create(2026, 8, 10, 9, 0, 0, 'Asia/Manila');
        $februaryDate = Carbon::create(2027, 2, 10, 9, 0, 0, 'Asia/Manila');
        $juneDate = Carbon::create(2027, 6, 10, 9, 0, 0, 'Asia/Manila');

        $this->assertSame('first', StudentSemesterAccountService::currentSemester($augustDate));
        $this->assertSame('2026-2027', StudentSemesterAccountService::currentAcademicYear($augustDate));

        $this->assertSame('second', StudentSemesterAccountService::currentSemester($februaryDate));
        $this->assertSame('2026-2027', StudentSemesterAccountService::currentAcademicYear($februaryDate));

        $this->assertNull(StudentSemesterAccountService::currentSemester($juneDate));
        $this->assertNull(StudentSemesterAccountService::currentAcademicYear($juneDate));
    }

    public function test_it_deactivates_students_when_current_month_is_outside_active_semesters(): void
    {
        Carbon::setTestNow(Carbon::create(2027, 6, 15, 8, 0, 0, 'Asia/Manila'));

        $activeStudent = User::factory()->create([
            'account_status' => 'active',
        ]);

        $suspendedStudent = User::factory()->create([
            'account_status' => 'suspended',
        ]);

        $result = StudentSemesterAccountService::syncCurrentTermAccounts(force: true);

        $this->assertNull($result['semester']);
        $this->assertNull($result['academic_year']);
        $this->assertSame('inactive', $activeStudent->fresh()->account_status);
        $this->assertSame('suspended', $suspendedStudent->fresh()->account_status);
    }

    public function test_it_uses_current_term_roster_to_activate_only_listed_students(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 9, 2, 8, 0, 0, 'Asia/Manila'));

        $listedStudent = User::factory()->create([
            'student_id' => '12345',
            'account_status' => 'inactive',
        ]);

        $missingStudent = User::factory()->create([
            'student_id' => '54321',
            'account_status' => 'active',
        ]);

        StudentRegistrationRoster::create([
            'batch_token' => 'batch-1',
            'academic_year' => '2026-2027',
            'semester' => 'first',
            'student_id' => '12345',
            'first_name' => 'listed',
            'last_name' => 'student',
            'year_level' => '4th',
            'imported_by' => null,
        ]);

        $result = StudentSemesterAccountService::syncCurrentTermAccounts(force: true);

        $this->assertSame('first', $result['semester']);
        $this->assertSame('2026-2027', $result['academic_year']);
        $this->assertSame('active', $listedStudent->fresh()->account_status);
        $this->assertSame('4th', $listedStudent->fresh()->year_level);
        $this->assertSame('inactive', $missingStudent->fresh()->account_status);
    }
}

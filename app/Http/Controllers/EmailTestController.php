<?php

namespace App\Http\Controllers;

use App\Mail\PasswordResetMail;
use App\Mail\ConsultationRequest;
use App\Mail\ConsultationStatusUpdate;
use App\Mail\InstructorCallingMail;
use App\Mail\StudentCancellationMail;
use App\Mail\AdminActionMail;
use App\Models\User;
use App\Models\Consultation;
use Illuminate\Support\Facades\Mail;

class EmailTestController extends Controller
{
    /**
     * Test sending a password reset email
     */
    public function testPasswordReset()
    {
        try {
            $resetUrl = route('password.reset', ['token' => 'test-token-12345']);
            Mail::to('test@example.com')->send(new PasswordResetMail($resetUrl, 'Test User'));
            
            return response()->json([
                'status' => 'success',
                'message' => 'Password reset email sent successfully to test@example.com',
                'template' => 'password_reset',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'hint' => 'Check your MAIL_USERNAME and MAIL_PASSWORD in .env file',
            ], 500);
        }
    }

    /**
     * Test sending a consultation request email
     */
    public function testConsultationRequest()
    {
        try {
            // Get or create test users
            $student = User::where('user_type', 'student')->first() ?? 
                       User::factory()->create(['user_type' => 'student']);
            
            $instructor = User::where('user_type', 'instructor')->first() ?? 
                         User::factory()->create(['user_type' => 'instructor']);

            // Get or create a consultation
            $consultation = Consultation::first() ?? 
                           Consultation::factory()->create([
                               'student_id' => $student->id,
                               'instructor_id' => $instructor->id,
                           ]);

            Mail::to($instructor->email)->send(
                new ConsultationRequest($consultation, $student, $instructor)
            );

            return response()->json([
                'status' => 'success',
                'message' => "Consultation request email sent to {$instructor->email}",
                'template' => 'consultation_request',
                'recipient' => $instructor->email,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Test sending a consultation status update email
     */
    public function testConsultationStatusUpdate($status = 'approved')
    {
        try {
            // Validate status
            if (!in_array($status, ['approved', 'declined'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid status. Use: approved or declined',
                ], 400);
            }

            // Get or create test users
            $student = User::where('user_type', 'student')->first() ?? 
                       User::factory()->create(['user_type' => 'student']);
            
            $instructor = User::where('user_type', 'instructor')->first() ?? 
                         User::factory()->create(['user_type' => 'instructor']);

            // Get or create a consultation
            $consultation = Consultation::first() ?? 
                           Consultation::factory()->create([
                               'student_id' => $student->id,
                               'instructor_id' => $instructor->id,
                               'status' => $status,
                           ]);

            Mail::to($student->email)->send(
                new ConsultationStatusUpdate($consultation, $student, $instructor, $status)
            );

            return response()->json([
                'status' => 'success',
                'message' => "Consultation status update email ({$status}) sent to {$student->email}",
                'template' => 'consultation_status_update',
                'recipient' => $student->email,
                'update_status' => $status,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show email configuration status
     */
    public function status()
    {
        return response()->json([
            'mailer' => config('mail.mailer'),
            'host' => config('mail.host'),
            'port' => config('mail.port'),
            'encryption' => config('mail.encryption'),
            'from_address' => config('mail.from.address'),
            'from_name' => config('mail.from.name'),
            'username' => config('mail.username') ? '***configured***' : 'not configured',
            'password' => config('mail.password') ? '***configured***' : 'not configured',
        ]);
    }

    /**
     * Test sending an instructor calling email
     */
    public function testInstructorCalling()
    {
        try {
            // Get or create test users
            $student = User::where('user_type', 'student')->first() ?? 
                       User::factory()->create(['user_type' => 'student']);
            
            $instructor = User::where('user_type', 'instructor')->first() ?? 
                         User::factory()->create(['user_type' => 'instructor']);

            // Get or create an approved consultation
            $consultation = Consultation::where('status', 'approved')->first() ?? 
                           Consultation::factory()->create([
                               'student_id' => $student->id,
                               'instructor_id' => $instructor->id,
                               'status' => 'approved',
                               'consultation_date' => now()->addDays(1)->format('Y-m-d'),
                               'consultation_time' => '10:00:00',
                               'consultation_end_time' => '11:00:00',
                           ]);

            Mail::to($student->email)->send(new InstructorCallingMail(
                $instructor->name,
                $consultation->consultation_date,
                $consultation->consultation_time,
                $consultation->consultation_end_time,
                $consultation->consultation_type ?? 'Video Call',
                1 // First attempt
            ));

            return response()->json([
                'status' => 'success',
                'message' => "Instructor calling email sent to {$student->email}",
                'template' => 'instructor_calling',
                'recipient' => $student->email,
                'instructor' => $instructor->name,
                'consultation_date' => $consultation->consultation_date,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Test sending a student cancellation email
     */
    public function testStudentCancellation()
    {
        try {
            // Get or create test users
            $student = User::where('user_type', 'student')->first() ?? 
                       User::factory()->create(['user_type' => 'student']);
            
            $instructor = User::where('user_type', 'instructor')->first() ?? 
                         User::factory()->create(['user_type' => 'instructor']);

            // Get or create a pending consultation
            $consultation = Consultation::where('status', 'pending')->first() ?? 
                           Consultation::factory()->create([
                               'student_id' => $student->id,
                               'instructor_id' => $instructor->id,
                               'status' => 'pending',
                               'consultation_date' => now()->addDays(2)->format('Y-m-d'),
                               'consultation_time' => '14:00:00',
                               'consultation_end_time' => '15:00:00',
                           ]);

            Mail::to($instructor->email)->send(new StudentCancellationMail(
                $student->name,
                $instructor->name,
                $consultation->consultation_date,
                $consultation->consultation_time,
                $consultation->consultation_end_time,
                $consultation->consultation_type ?? 'Consultation'
            ));

            return response()->json([
                'status' => 'success',
                'message' => "Student cancellation email sent to {$instructor->email}",
                'template' => 'student_cancellation',
                'recipient' => $instructor->email,
                'student' => $student->name,
                'consultation_date' => $consultation->consultation_date,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Test sending an admin action email
     */
    public function testAdminAction()
    {
        try {
            // Get or create test users
            $student = User::where('user_type', 'student')->first() ?? 
                       User::factory()->create(['user_type' => 'student']);
            
            $instructor = User::where('user_type', 'instructor')->first() ?? 
                         User::factory()->create(['user_type' => 'instructor']);

            $admin = User::where('user_type', 'admin')->first() ?? 
                    User::factory()->create(['user_type' => 'admin']);

            // Get or create a consultation
            $consultation = Consultation::first() ?? 
                           Consultation::factory()->create([
                               'student_id' => $student->id,
                               'instructor_id' => $instructor->id,
                               'consultation_date' => now()->addDays(3)->format('Y-m-d'),
                               'consultation_time' => '09:00:00',
                               'consultation_end_time' => '10:00:00',
                           ]);

            Mail::to($admin->email)->send(new AdminActionMail(
                'submitted',
                $student->name,
                'student',
                $instructor->name,
                'instructor',
                [
                    'date' => $consultation->consultation_date,
                    'time' => '09:00',
                    'type' => $consultation->consultation_type ?? 'Consultation',
                    'mode' => $consultation->consultation_mode ?? 'Video Call',
                ],
                $student->name . ' submitted a new consultation request with ' . $instructor->name . ' for ' . $consultation->consultation_date . ' at 09:00.',
                now()->format('Y-m-d H:i:s')
            ));

            return response()->json([
                'status' => 'success',
                'message' => "Admin action email sent to {$admin->email}",
                'template' => 'admin_action',
                'recipient' => $admin->email,
                'action_type' => 'submitted',
                'performed_by' => $student->name,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}

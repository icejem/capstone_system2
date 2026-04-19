<?php

use App\Http\Controllers\ProfileController;
use App\Mail\ConsultationRequest;
use App\Mail\ConsultationStatusUpdate;
use App\Mail\InstructorCallingMail;
use App\Mail\StudentCancellationMail;
use App\Mail\AdminActionMail;
use App\Models\Consultation;
use App\Models\Feedback;
use App\Models\InstructorAvailability;
use App\Models\User;
use App\Models\UserNotification;
use App\Models\UserSession;
use App\Services\ConsultationNotificationService;
use App\Services\SmsNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;

if (! function_exists('notifyAdmins')) {
    function notifyAdmins(string $title, string $message, string $type = 'admin_consultation'): void
    {
        $adminIds = User::where('user_type', 'admin')->pluck('id');

        foreach ($adminIds as $adminId) {
            UserNotification::create([
                'user_id' => $adminId,
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'is_read' => false,
            ]);
        }
    }
}

if (! function_exists('buildInstructorConsultationSummaryPayload')) {
    function buildInstructorConsultationSummaryPayload(User $user): array
    {
        $consultations = Consultation::with('student')
            ->where('instructor_id', $user->id)
            ->orderByRaw("
                CASE
                    WHEN status = 'pending' THEN 0
                    WHEN status = 'approved' THEN 1
                    WHEN status IN ('in_progress', 'completed') THEN 2
                    WHEN status = 'declined' THEN 3
                    ELSE 4
                END
            ")
            ->orderByDesc('created_at')
            ->orderByDesc('consultation_date')
            ->orderByDesc('consultation_time')
            ->get();

        $notifications = UserNotification::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->get();
        $latestUnreadNotification = $notifications->firstWhere('is_read', false);

        $stats = [
            'total' => $consultations->count(),
            'pending' => $consultations->where('status', 'pending')->count(),
            'approved' => $consultations->where('status', 'approved')->count(),
            'completed' => $consultations->where('status', 'completed')->count(),
        ];

        $formatManilaTime = function (?string $time): string {
            if (! $time) {
                return '--';
            }
            $value = strlen($time) === 5 ? $time . ':00' : $time;
            return \Illuminate\Support\Carbon::createFromFormat('H:i:s', $value, 'Asia/Manila')
                ->setTimezone('Asia/Manila')
                ->format('g:i A');
        };

        $formatManilaRange = function (?string $start, ?string $end) use ($formatManilaTime) {
            if (! $start && ! $end) {
                return '--';
            }
            if (! $end && $start) {
                $startValue = strlen($start) === 5 ? $start . ':00' : $start;
                $endValue = \Illuminate\Support\Carbon::createFromFormat('H:i:s', $startValue, 'Asia/Manila')
                    ->copy()
                    ->addHour()
                    ->format('H:i:s');
                return $formatManilaTime($start) . ' to ' . $formatManilaTime($endValue);
            }
            return $formatManilaTime($start) . ' to ' . $formatManilaTime($end);
        };

        $formatManilaRangeDash = function (?string $start, ?string $end) use ($formatManilaTime) {
            if (! $start && ! $end) {
                return '--';
            }
            if (! $end && $start) {
                $startValue = strlen($start) === 5 ? $start . ':00' : $start;
                $endValue = \Illuminate\Support\Carbon::createFromFormat('H:i:s', $startValue, 'Asia/Manila')
                    ->copy()
                    ->addHour()
                    ->format('H:i:s');
                return $formatManilaTime($start) . ' - ' . $formatManilaTime($endValue);
            }
            return $formatManilaTime($start) . ' - ' . $formatManilaTime($end);
        };

        $formatRelativeDay = function (?string $date): string {
            if (! $date) {
                return 'Unknown day';
            }
            try {
                $dateObj = \Illuminate\Support\Carbon::parse($date, 'Asia/Manila');
            } catch (\Throwable $e) {
                return 'Unknown day';
            }
            $today = \Illuminate\Support\Carbon::now('Asia/Manila')->startOfDay();
            $diffDays = $dateObj->copy()->startOfDay()->diffInDays($today, false);
            if ($diffDays === 0) {
                return 'Today';
            }
            if ($diffDays === -1) {
                return 'Tomorrow';
            }
            if ($diffDays === 1) {
                return 'Yesterday';
            }
            return $dateObj->format('M d');
        };

        $historyConsultations = $consultations
            ->filter(function ($consultation) {
                $status = strtolower((string) ($consultation->status ?? ''));
                return in_array($status, ['completed', 'incompleted'], true);
            })
            ->values()
            ->map(function ($consultation) use ($formatManilaRangeDash) {
                return [
                    'id' => $consultation->id,
                    'student' => (string) ($consultation->student?->name ?? 'Student'),
                    'studentId' => (string) ($consultation->student?->student_id ?? '--'),
                    'date' => (string) ($consultation->consultation_date ?? '--'),
                    'time' => $formatManilaRangeDash($consultation->consultation_time, $consultation->consultation_end_time),
                    'type' => (string) ($consultation->type_label ?? '--'),
                    'mode' => (string) ($consultation->consultation_mode ?? '--'),
                    'duration' => $consultation->duration_minutes !== null
                        ? ((int) $consultation->duration_minutes . ' min')
                        : '--',
                    'summary' => (string) ($consultation->summary_text ?? ''),
                    'transcript' => (string) ($consultation->transcript_text ?? ''),
                ];
            });

        return [
            'stats' => $stats,
            'unreadNotifications' => $notifications->where('is_read', false)->count(),
            'notifications' => $notifications
                ->take(20)
                ->map(function ($notification) {
                    return [
                        'id' => $notification->id,
                        'title' => (string) $notification->title,
                        'message' => (string) $notification->message,
                        'is_read' => (bool) $notification->is_read,
                        'created_at' => optional($notification->created_at)?->toIso8601String(),
                        'created_at_human' => $notification->created_at?->diffForHumans(),
                    ];
                })
                ->values(),
            'latestUnreadNotification' => $latestUnreadNotification
                ? [
                    'id' => $latestUnreadNotification->id,
                    'title' => (string) $latestUnreadNotification->title,
                    'message' => (string) $latestUnreadNotification->message,
                    'created_at' => optional($latestUnreadNotification->created_at)?->toIso8601String(),
                ]
                : null,
            'consultations' => $consultations->map(function ($c) use ($formatManilaRange) {
                $modeValue = strtolower((string) $c->consultation_mode);
                $isFace = str_contains($modeValue, 'face');
                return [
                    'id' => $c->id,
                    'student_name' => $c->student?->name ?? 'Student',
                    'student_email' => $c->student?->email ?? '',
                    'student_id' => $c->student?->student_id ?? '--',
                    'status' => $c->status,
                    'consultation_date' => $c->consultation_date,
                    'consultation_time' => substr((string) $c->consultation_time, 0, 5),
                    'time_range' => $formatManilaRange($c->consultation_time, $c->consultation_end_time),
                    'type_label' => $c->type_label ?? '',
                    'consultation_mode' => $c->consultation_mode ?? '',
                    'student_notes' => $c->student_notes ?? '',
                    'is_face_to_face' => $isFace,
                    'call_attempts' => (int) ($c->call_attempts ?? 0),
                    'started_at' => optional($c->started_at)?->toIso8601String(),
                    'updated_label' => $c->updated_at?->diffForHumans() ?? 'just now',
                    'duration_minutes' => $c->duration_minutes,
                    'summary_text' => (string) ($c->summary_text ?? ''),
                    'transcript_text' => (string) ($c->transcript_text ?? ''),
                ];
            }),
            'historyConsultations' => $historyConsultations,
            'recentConsultations' => $consultations
                ->sortByDesc(function ($consultation) {
                    return sprintf(
                        '%s %s',
                        (string) ($consultation->consultation_date ?? '0000-00-00'),
                        (string) ($consultation->consultation_time ?? '00:00:00')
                    );
                })
                ->take(4)
                ->values()
                ->map(function ($consultation) use ($formatRelativeDay, $formatManilaRangeDash) {
                    return [
                        'title' => (string) ($consultation->type_label ?: 'Consultation Session'),
                        'status' => strtolower((string) ($consultation->status ?? 'pending')),
                        'student' => (string) ($consultation->student?->name ?? 'Student'),
                        'date_label' => $formatRelativeDay($consultation->consultation_date),
                        'time_label' => $formatManilaRangeDash($consultation->consultation_time, $consultation->consultation_end_time),
                    ];
                }),
        ];
    }
}

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return view('welcome');
})->name('home');

Route::get('/dashboard', function () {
    $user = auth()->user();

    if (! $user) {
        return redirect()->route('login');
    }

    if (! $user->hasActiveAccount()) {
        $message = $user->normalizedAccountStatus() === 'suspended'
            ? 'Access denied. Your account is suspended. Please contact the administrator.'
            : 'Access denied. Your account is deactivated. Please contact the administrator.';

        Auth::guard('web')->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('login')->with('status', $message);
    }

    return match ($user->user_type) {
        'admin' => redirect()->route('admin.dashboard'),
        'instructor' => redirect()->route('instructor.dashboard'),
        default => redirect()->route('student.dashboard'),
    };
})->middleware('auth')->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/student/dashboard', function () {
    $user = auth()->user();
    if ($user && ! $user->hasActiveAccount()) {
        $message = $user->normalizedAccountStatus() === 'suspended'
            ? 'Access denied. Your account is suspended. Please contact the administrator.'
            : 'Access denied. Your account is deactivated. Please contact the administrator.';

        Auth::guard('web')->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('login')->with('status', $message);
    }

    if (! $user || ! in_array($user->user_type, ['student', 'admin'], true)) {
        abort(403);
    }

    \App\Services\ConsultationOverdueService::markOverdueAsIncompleted();

    $userId = $user->id;

    $consultations = Consultation::with('instructor')
        ->where('student_id', $userId)
        ->orderByDesc('created_at')
        ->get();

    $instructors = User::where('user_type', 'instructor')
        ->where(function ($query) {
            $query->whereNull('account_status')
                ->orWhere('account_status', 'active');
        })
        ->orderBy('name')
        ->get();

    $activeInstructorIds = $instructors->pluck('id');

    $allAvailabilities = InstructorAvailability::where('is_active', true)
        ->whereIn('instructor_id', $activeInstructorIds)
        ->orderByDesc('updated_at')
        ->get();

    $latestTagByInstructor = $allAvailabilities
        ->filter(fn ($slot) => $slot->semester && $slot->academic_year)
        ->groupBy('instructor_id')
        ->map(function ($slots) {
            $latest = $slots->first();
            return [
                'semester' => $latest->semester,
                'academic_year' => $latest->academic_year,
            ];
        });

    $availabilities = $allAvailabilities
        ->groupBy('instructor_id')
        ->map(function ($slots, $instructorId) use ($latestTagByInstructor) {
            $tag = $latestTagByInstructor->get($instructorId);
            if ($tag) {
                return $slots->filter(fn ($slot) =>
                    $slot->semester === $tag['semester'] &&
                    $slot->academic_year === $tag['academic_year']
                )
                    ->sortBy(fn ($slot) => ($slot->available_day ?? '') . '-' . ($slot->start_time ?? ''))
                    ->values();
            }
            return $slots
                ->sortBy(fn ($slot) => ($slot->available_day ?? '') . '-' . ($slot->start_time ?? ''))
                ->values();
        });

    $bookedSlots = Consultation::whereIn('status', ['pending', 'approved'])
        ->whereIn('instructor_id', $activeInstructorIds)
        ->whereDate('consultation_date', '>=', today())
        ->get(['instructor_id', 'consultation_date', 'consultation_time'])
        ->groupBy('instructor_id')
        ->map(function ($items) {
            return $items->groupBy('consultation_date')
                ->map(fn ($perDate) => $perDate->pluck('consultation_time')
                    ->map(fn ($time) => substr((string) $time, 0, 5))
                    ->values());
        });

    $notifications = UserNotification::where('user_id', $userId)
        ->orderByDesc('created_at')
        ->get();

    $stats = [
        [
            'count' => $consultations->count(),
            'label' => 'Total Consultations',
            'color' => '#f59e0b',
        ],
        [
            'count' => $consultations->where('status', 'approved')->count(),
            'label' => 'Approved Sessions',
            'color' => '#8b5cf6',
        ],
        [
            'count' => $consultations->where('status', 'completed')->count(),
            'label' => 'Completed Sessions',
            'color' => '#10b981',
        ],
    ];

    // Get online instructor IDs
    $onlineInstructorIds = \App\Services\UserSessionService::getOnlineUserIds('instructor');

    // Get last active minutes for each instructor
    $instructorActiveMinutes = [];
    foreach ($instructors as $instructor) {
        $lastMinutes = \App\Services\UserSessionService::getLastActiveMinutes($instructor->id);
        if ($lastMinutes !== null) {
            $instructorActiveMinutes[$instructor->id] = [
                'last_active_minutes' => $lastMinutes,
            ];
        }
    }

    return view('student.dashboard', compact(
        'consultations',
        'notifications',
        'stats',
        'instructors',
        'availabilities',
        'bookedSlots',
        'onlineInstructorIds',
        'instructorActiveMinutes'
    ));
})->name('student.dashboard')->middleware('auth');

// Endpoint for student dashboard to poll for incoming sessions
Route::get('/student/incoming-session', function () {
    $user = auth()->user();
    if (! $user || ! in_array($user->user_type, ['student', 'admin'], true)) {
        abort(403);
    }

    $consultation = \App\Models\Consultation::with('instructor')
        ->where('student_id', $user->id)
        ->where('status', 'in_progress')
        ->orderByDesc('started_at')
        ->first();

    if (! $consultation) {
        return response()->json(['consultation' => null]);
    }

    return response()->json([
        'consultation' => [
            'id' => $consultation->id,
            'instructor_name' => $consultation->instructor?->name ?? 'Instructor',
            'instructor_initials' => collect(explode(' ', ($consultation->instructor?->name ?? '')))->map(fn($p) => strtoupper(substr($p,0,1)))->slice(0,2)->join(''),
            'mode' => $consultation->consultation_mode,
            'date' => (string) $consultation->consultation_date,
            'time' => substr((string) $consultation->consultation_time, 0, 5),
        ],
    ]);
})->middleware('auth');

Route::get('/student/request-consultation', function () {
    return redirect()->to(route('student.dashboard') . '#request-consultation');
})->name('student.consultation.create')->middleware('auth');

Route::get('/student/instructors/{user}/availability-status', function (User $user) {
    $authUser = auth()->user();
    if (! $authUser || ! in_array($authUser->user_type, ['student', 'admin'], true)) {
        abort(403);
    }

    if ($user->user_type !== 'instructor') {
        abort(404);
    }

    if (! $user->hasActiveAccount()) {
        return response()->json([
            'available' => false,
            'message' => 'This instructor account is not available.',
        ], 409);
    }

    return response()->json([
        'available' => true,
    ]);
})->name('student.instructor.availability')->middleware('auth');

Route::post('/student/request-consultation', function (Request $request) {
    $user = auth()->user();
    if (! $user || ! in_array($user->user_type, ['student', 'admin'], true)) {
        abort(403);
    }
    $expectsJson = $request->expectsJson() || $request->ajax();

    // Backend fallback: auto-pick the first available time slot when the
    // hidden consultation_time is still empty on submit.
    if (
        ! $request->filled('consultation_time')
        && $request->filled('instructor_id')
        && $request->filled('consultation_date')
    ) {
        try {
            $dayName = strtolower(\Illuminate\Support\Carbon::parse($request->consultation_date)->format('l'));
            $instructorId = (int) $request->input('instructor_id');
            $dateValue = (string) $request->input('consultation_date');

            $latestTaggedAvailability = InstructorAvailability::where('instructor_id', $instructorId)
                ->whereNotNull('semester')
                ->whereNotNull('academic_year')
                ->orderByDesc('updated_at')
                ->first();

            $slotQuery = InstructorAvailability::where('instructor_id', $instructorId)
                ->where('is_active', true)
                ->where('available_day', $dayName);

            if ($latestTaggedAvailability) {
                $slotQuery
                    ->where('semester', $latestTaggedAvailability->semester)
                    ->where('academic_year', $latestTaggedAvailability->academic_year);
            }

            $candidateSlots = $slotQuery
                ->orderBy('start_time')
                ->get(['start_time'])
                ->map(fn ($slot) => substr((string) $slot->start_time, 0, 5))
                ->filter()
                ->values();

            foreach ($candidateSlots as $candidateTime) {
                $isTaken = Consultation::where('instructor_id', $instructorId)
                    ->whereDate('consultation_date', $dateValue)
                    ->whereTime('consultation_time', $candidateTime . ':00')
                    ->whereIn('status', ['pending', 'approved', 'in_progress'])
                    ->exists();

                if (! $isTaken) {
                    $request->merge(['consultation_time' => $candidateTime]);
                    break;
                }
            }
        } catch (\Throwable $e) {
            // Keep default validation flow if auto-resolution fails.
        }
    }

    $request->validate([
        'instructor_id' => 'required|exists:users,id',
        'consultation_date' => 'required|date|after_or_equal:today',
        'consultation_time' => 'required|date_format:H:i',
        'consultation_category' => 'required|string|max:255',
        'consultation_type' => 'required|string|max:255',
        'consultation_type_other' => 'required_if:consultation_type,Others|nullable|string|max:255|regex:/\\S/',
        'consultation_priority' => 'nullable|string|max:100',
        'consultation_mode' => 'required|string|max:255',
        'student_notes' => 'nullable|string|max:2000',
    ]);

    $selectedInstructor = User::whereKey($request->instructor_id)
        ->where('user_type', 'instructor')
        ->first();

    if (! $selectedInstructor || ! $selectedInstructor->hasActiveAccount()) {
        if ($expectsJson) {
            return response()->json([
                'ok' => false,
                'message' => 'This instructor account is not available.',
                'errors' => [
                    'instructor_id' => ['This instructor account is not available.'],
                ],
            ], 422);
        }

        return redirect()->to(route('student.dashboard') . '#request-consultation')
            ->withErrors([
                'instructor_id' => 'This instructor account is not available.',
            ])->withInput();
    }

    $dayName = strtolower(\Illuminate\Support\Carbon::parse($request->consultation_date)->format('l'));
    $latestTaggedAvailability = InstructorAvailability::where('instructor_id', $request->instructor_id)
        ->whereNotNull('semester')
        ->whereNotNull('academic_year')
        ->orderByDesc('updated_at')
        ->first();

    $availabilityQuery = InstructorAvailability::where('instructor_id', $request->instructor_id)
        ->where('is_active', true)
        ->where('available_day', $dayName)
        ->whereTime('start_time', $request->consultation_time . ':00');

    if ($latestTaggedAvailability) {
        $availabilityQuery
            ->where('semester', $latestTaggedAvailability->semester)
            ->where('academic_year', $latestTaggedAvailability->academic_year);
    }

    $availability = $availabilityQuery->orderByDesc('updated_at')->first();
    if (! $availability) {
        if ($expectsJson) {
            return response()->json([
                'ok' => false,
                'message' => 'Selected time is not available for this instructor.',
                'errors' => [
                    'consultation_time' => ['Selected time is not available for this instructor.'],
                ],
            ], 422);
        }
        return back()->withErrors([
            'consultation_time' => 'Selected time is not available for this instructor.',
        ])->withInput();
    }

    $isTaken = Consultation::where('instructor_id', $request->instructor_id)
        ->whereDate('consultation_date', $request->consultation_date)
        ->whereTime('consultation_time', $request->consultation_time . ':00')
        ->whereIn('status', ['pending', 'approved', 'in_progress'])
        ->exists();

    if ($isTaken) {
        if ($expectsJson) {
            return response()->json([
                'ok' => false,
                'message' => 'This time slot is already booked. Please choose another.',
                'errors' => [
                    'consultation_time' => ['This time slot is already booked. Please choose another.'],
                ],
            ], 422);
        }
        return back()->withErrors([
            'consultation_time' => 'This time slot is already booked. Please choose another.',
        ])->withInput();
    }

    $consultationEndTime = $availability->end_time
        ?? \Illuminate\Support\Carbon::createFromFormat('H:i', $request->consultation_time, 'Asia/Manila')
            ->addHour()
            ->format('H:i:s');

    // Combine category + topic (and optionally priority) so instructor sees the full context
    $topicValue = (string) ($request->consultation_type ?? '');
    if ($topicValue === 'Others') {
        $topicValue = trim((string) $request->input('consultation_type_other', ''));
    }

    $typeLabel = $topicValue;
    if (!empty($request->consultation_category)) {
        $typeLabel = ($request->consultation_category ?: '') . ($typeLabel ? ' - ' . $typeLabel : '');
    }
    if (!empty($request->consultation_priority)) {
        $typeLabel .= ' (' . $request->consultation_priority . ')';
    }

    $consultation = Consultation::create([
        'student_id' => $user->id,
        'instructor_id' => $request->instructor_id,
        'consultation_date' => $request->consultation_date,
        'consultation_time' => $request->consultation_time . ':00',
        'consultation_end_time' => $consultationEndTime,
        // Save structured fields separately for future queries
        'consultation_type' => $typeLabel,
        'consultation_category' => $request->consultation_category,
        'consultation_topic' => $topicValue,
        'consultation_priority' => $request->consultation_priority,
        'consultation_mode' => $request->consultation_mode,
        'student_notes' => $request->student_notes,
        'status' => 'pending',
    ]);

    $studentName = $user->name ?? 'Student';
    $startLabel = substr((string) $consultation->consultation_time, 0, 5);
    $endLabel = substr((string) $consultation->consultation_end_time, 0, 5);
    $timeLabel = $endLabel ? ($startLabel . ' to ' . $endLabel) : $startLabel;
    UserNotification::create([
        'user_id' => $consultation->instructor_id,
        'title' => 'New Consultation Request',
        'message' => $studentName . ' requested a consultation on ' .
            $consultation->consultation_date . ' at ' . $timeLabel . '.',
        'type' => 'consultation_request',
        'is_read' => false,
    ]);

    // Create notification for student
    UserNotification::create([
        'user_id' => $user->id,
        'title' => 'Consultation Request Submitted',
        'message' => 'Your consultation request with ' . ($consultation->instructor?->name ?? 'Instructor') . ' has been submitted successfully.',
        'type' => 'consultation_submitted',
        'is_read' => false,
    ]);

    notifyAdmins(
        'New Consultation Request',
        $studentName . ' requested a consultation with ' . ($consultation->instructor?->name ?? 'Instructor') . ' on ' .
            $consultation->consultation_date . ' at ' . $timeLabel . '.',
        'consultation_request'
    );

    // Keep submit one-click fast, while still emailing instructor after submission.
    $consultationIdForMail = (int) $consultation->id;
    $studentIdForMail = (int) $user->id;
    $instructorIdForMail = (int) $consultation->instructor_id;
    $sendAdminEmails = ! $expectsJson;
    dispatch(function () use ($consultationIdForMail, $studentIdForMail, $instructorIdForMail, $studentName, $timeLabel, $sendAdminEmails) {
        try {
            $consultationForMail = Consultation::find($consultationIdForMail);
            $studentForMail = User::find($studentIdForMail);
            $instructorForMail = User::find($instructorIdForMail);

            if (! $consultationForMail || ! $studentForMail) {
                return;
            }

            if ($instructorForMail && $instructorForMail->email) {
                try {
                    Mail::to($instructorForMail->email)->send(new ConsultationRequest(
                        $consultationForMail,
                        $studentForMail,
                        $instructorForMail
                    ));
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning('Failed to send ConsultationRequest email after response.', [
                        'consultation_id' => $consultationIdForMail,
                        'instructor_id' => $instructorIdForMail,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            if ($instructorForMail) {
                SmsNotificationService::sendConsultationRequest(
                    $consultationForMail,
                    $studentForMail,
                    $instructorForMail
                );
            }

            if ($sendAdminEmails) {
                $admins = User::where('user_type', 'admin')->whereNotNull('email')->get();
                foreach ($admins as $admin) {
                    try {
                        Mail::to($admin->email)->send(new AdminActionMail(
                            'submitted',
                            $studentName,
                            'student',
                            $instructorForMail?->name ?? 'Instructor',
                            'instructor',
                            [
                                'date' => $consultationForMail->consultation_date,
                                'time' => $timeLabel,
                                'type' => $consultationForMail->consultation_type ?? 'Consultation',
                                'mode' => $consultationForMail->consultation_mode ?? 'N/A',
                            ],
                            $studentName . ' submitted a new consultation request with ' . ($instructorForMail?->name ?? 'Instructor') . ' for ' . $consultationForMail->consultation_date . ' at ' . $timeLabel . '.',
                            now()->format('Y-m-d H:i:s')
                        ));
                    } catch (\Throwable $e) {
                        \Illuminate\Support\Facades\Log::warning('Failed to send AdminActionMail (submitted) after response.', [
                            'consultation_id' => $consultationIdForMail,
                            'admin_id' => $admin->id,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Deferred consultation email dispatch failed.', [
                'consultation_id' => $consultationIdForMail,
                'error' => $e->getMessage(),
            ]);
        }
    })->afterResponse();

    if ($expectsJson) {
        return response()->json([
            'ok' => true,
            'message' => 'Your consultation is done.',
        ]);
    }

    return redirect()->to(route('student.dashboard') . '#request-consultation')
        ->with('success', 'Your consultation is done.');
})->name('student.consultation.store')->middleware('auth');

Route::post('/student/consultations/{consultation}/cancel', function (Consultation $consultation) {
    $user = auth()->user();
    $myConsultationsRedirect = redirect()->to(route('student.dashboard') . '#my-consultations');
    if (! $user || ! in_array($user->user_type, ['student', 'admin'], true)) {
        abort(403);
    }

    if ((int) $consultation->student_id !== (int) $user->id) {
        abort(403);
    }

    if ($consultation->status !== 'pending') {
        return $myConsultationsRedirect->withErrors([
            'consultation_cancel' => 'Only pending requests can be cancelled.',
        ]);
    }

    $consultation->update(['status' => 'cancelled']);

    $studentName = $user->name ?? 'Student';
    $startLabel = substr((string) $consultation->consultation_time, 0, 5);
    $endLabel = substr((string) $consultation->consultation_end_time, 0, 5);
    $timeLabel = $endLabel ? ($startLabel . ' to ' . $endLabel) : $startLabel;

    UserNotification::create([
        'user_id' => $consultation->instructor_id,
        'title' => 'Consultation Request Cancelled',
        'message' => $studentName . ' cancelled the consultation request on ' .
            $consultation->consultation_date . ' at ' . $timeLabel . '.',
        'type' => 'consultation_cancelled',
        'is_read' => false,
    ]);

    notifyAdmins(
        'Consultation Cancelled',
        $studentName . ' cancelled a consultation with ' . ($consultation->instructor?->name ?? 'Instructor') . ' on ' .
            $consultation->consultation_date . ' at ' . $timeLabel . '.',
        'consultation_cancelled'
    );

    $instructor = $consultation->instructor;
    $admins = User::where('user_type', 'admin')->get();
    app()->terminating(function () use ($instructor, $admins, $studentName, $consultation, $timeLabel) {
        if ($instructor && $instructor->email) {
            Mail::to($instructor->email)->send(new StudentCancellationMail(
                $studentName,
                $instructor->name ?? 'Instructor',
                $consultation->consultation_date,
                $consultation->consultation_time,
                $consultation->consultation_end_time,
                $consultation->consultation_type ?? 'Consultation'
            ));
        }

        if ($instructor) {
            SmsNotificationService::sendStudentCancellation(
                $instructor,
                $studentName,
                (string) $consultation->consultation_date,
                (string) $consultation->consultation_time,
                $consultation->consultation_end_time,
                $consultation->consultation_type
            );
        }

        foreach ($admins as $admin) {
            if ($admin->email) {
                Mail::to($admin->email)->send(new AdminActionMail(
                    'cancelled',
                    $studentName,
                    'student',
                    $instructor?->name ?? 'Instructor',
                    'instructor',
                    [
                        'date' => $consultation->consultation_date,
                        'time' => $timeLabel,
                        'type' => $consultation->consultation_type ?? 'Consultation',
                        'mode' => $consultation->consultation_mode ?? 'N/A',
                    ],
                    $studentName . ' cancelled a consultation request scheduled for ' . $consultation->consultation_date . ' at ' . $timeLabel . '.',
                    now()->format('Y-m-d H:i:s')
                ));
            }
        }
    });

    if (request()->expectsJson() || request()->wantsJson() || request()->ajax()) {
        return response()->json([
            'message' => 'Consultation request cancelled.',
            'consultation' => [
                'id' => $consultation->id,
                'status' => $consultation->status,
                'consultation_mode' => $consultation->consultation_mode,
                'consultation_date' => $consultation->consultation_date,
                'time_range' => $timeLabel,
                'type_label' => $consultation->type_label,
                'duration_minutes' => $consultation->duration_minutes,
                'summary_text' => $consultation->summary_text,
                'transcript_text' => $consultation->transcript_text,
                'updated_at_human' => $consultation->updated_at?->diffForHumans(),
                'instructor_name' => $consultation->instructor?->name ?? 'Instructor',
            ],
        ]);
    }

    return $myConsultationsRedirect->with('success', 'Consultation request cancelled.');
})->name('student.consultation.cancel')->middleware('auth');

Route::post('/student/feedback', function (Request $request) {
    $user = auth()->user();
    if (! $user || ! in_array($user->user_type, ['student', 'admin'], true)) {
        abort(403);
    }

    $request->validate([
        'consultation_id' => 'required|exists:consultations,id',
        'rating' => 'required|integer|min:1|max:5',
        'comments' => 'nullable|string|max:2000',
    ]);

    $consultation = Consultation::findOrFail($request->consultation_id);
    if ((int) $consultation->student_id !== (int) $user->id) {
        abort(403);
    }

    $feedback = Feedback::updateOrCreate(
        [
            'consultation_id' => $consultation->id,
            'student_id' => $user->id,
            'instructor_id' => $consultation->instructor_id,
        ],
        [
            'rating' => $request->rating,
            'comments' => $request->comments,
        ]
    );

    UserNotification::create([
        'user_id' => $consultation->instructor_id,
        'title' => 'New Student Feedback',
        'message' => ($user->name ?? 'Student') . ' sent feedback (' . $feedback->rating . '/5) for ' . ($consultation->consultation_type ?? 'your session') . '.',
        'type' => 'feedback',
        'is_read' => false,
    ]);

    return redirect()->route('student.dashboard')->with('success', 'Feedback submitted.');
})->name('student.dashboard.submit')->middleware('auth');

Route::get('/admin/dashboard', function () {
    $user = auth()->user();
    if ($user && ! $user->hasActiveAccount()) {
        $message = $user->normalizedAccountStatus() === 'suspended'
            ? 'Access denied. Your account is suspended. Please contact the administrator.'
            : 'Access denied. Your account is deactivated. Please contact the administrator.';

        Auth::guard('web')->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('login')->with('status', $message);
    }

    if (! $user || $user->user_type !== 'admin') {
        abort(403);
    }

    $consultations = Consultation::with(['student', 'instructor'])
        ->orderByDesc('created_at')
        ->get();

    $notifications = UserNotification::where('user_id', $user->id)
        ->orderByDesc('created_at')
        ->get();

    $students = User::where('user_type', 'student')
        ->orderBy('name')
        ->get();

    $instructors = User::where('user_type', 'instructor')
        ->orderBy('name')
        ->get();

    // Fetch online student and instructor IDs
    $onlineStudentIds = \App\Services\UserSessionService::getOnlineUserIds('student');
    $onlineInstructorIds = \App\Services\UserSessionService::getOnlineUserIds('instructor');

    // Fetch active minutes for all students and instructors
    $studentActiveMinutes = [];
    foreach ($students as $student) {
        $lastActiveMinutes = \App\Services\UserSessionService::getLastActiveMinutes($student->id);
        if ($lastActiveMinutes !== null) {
            $studentActiveMinutes[$student->id] = ['last_active_minutes' => $lastActiveMinutes];
        }
    }

    $instructorActiveMinutes = [];
    foreach ($instructors as $instructor) {
        $lastActiveMinutes = \App\Services\UserSessionService::getLastActiveMinutes($instructor->id);
        if ($lastActiveMinutes !== null) {
            $instructorActiveMinutes[$instructor->id] = ['last_active_minutes' => $lastActiveMinutes];
        }
    }

    $stats = [
        ['count' => $consultations->count(), 'label' => 'Total Consultations', 'color' => '#6f42c1'],
        ['count' => $consultations->where('status', 'pending')->count(), 'label' => 'Pending Requests', 'color' => '#f59e0b'],
        ['count' => $consultations->where('status', 'approved')->count(), 'label' => 'Approved Sessions', 'color' => '#8b5cf6'],
        ['count' => $consultations->where('status', 'completed')->count(), 'label' => 'Completed Sessions', 'color' => '#9ca3af'],
    ];

    \App\Services\UserSessionService::closeExpiredSessions();

    $systemLogs = UserSession::with('user')
        ->latest('login_at')
        ->take(200)
        ->get();

    return view('admin.dashboard', compact('consultations', 'notifications', 'stats', 'students', 'instructors', 'onlineStudentIds', 'onlineInstructorIds', 'studentActiveMinutes', 'instructorActiveMinutes', 'systemLogs'));
})->name('admin.dashboard')->middleware('auth');

Route::get('/admin/instructors', function () {
    $user = auth()->user();
    if (! $user || $user->user_type !== 'admin') {
        abort(403);
    }

    $instructors = User::where('user_type', 'instructor')->orderBy('name')->get();
    $students = User::where('user_type', 'student')->orderBy('name')->get();

    return view('admin.instructors', compact('instructors', 'students'));
})->name('admin.instructors')->middleware('auth');

Route::post('/admin/instructors', function (Request $request) {
    $admin = auth()->user();
    if (! $admin || $admin->user_type !== 'admin') {
        abort(403);
    }

    $validated = $request->validate([
        'first_name' => ['required', 'string', 'max:255'],
        'last_name' => ['required', 'string', 'max:255'],
        'middle_name' => ['nullable', 'string', 'max:255'],
        'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
        'phone_number' => [
            'required',
            'string',
            'max:20',
            'unique:users,phone_number',
            function (string $attribute, mixed $value, \Closure $fail) {
                if (! SmsNotificationService::normalizePhoneNumber((string) $value)) {
                    $fail('Please enter a valid Philippine mobile number (e.g. 09171234567).');
                }
            },
        ],
        'password' => ['required', 'confirmed', Rules\Password::defaults()],
    ]);

    $fullName = trim(
        $validated['first_name'] . ' ' .
        (! empty($validated['middle_name']) ? $validated['middle_name'] . ' ' : '') .
        $validated['last_name']
    );

    User::create([
        'name' => $fullName,
        'email' => $validated['email'],
        'phone_number' => SmsNotificationService::normalizePhoneNumber($validated['phone_number']),
        'password' => Hash::make($validated['password']),
        'user_type' => 'instructor',
        'student_id' => null,
        'yearlevel' => null,
    ]);

    return back()->with('success', 'Instructor account created.');
})->name('admin.instructors.store')->middleware('auth');

Route::post('/admin/instructors/{user}/promote', function (User $user) {
    $admin = auth()->user();
    if (! $admin || $admin->user_type !== 'admin') {
        abort(403);
    }

    $user->update(['user_type' => 'instructor']);
    return back()->with('success', 'User promoted to instructor.');
})->name('admin.instructors.promote')->middleware('auth');

Route::post('/admin/instructors/{user}/demote', function (User $user) {
    $admin = auth()->user();
    if (! $admin || $admin->user_type !== 'admin') {
        abort(403);
    }

    $user->update(['user_type' => 'student']);
    return back()->with('success', 'Instructor moved to student.');
})->name('admin.instructors.demote')->middleware('auth');

Route::post('/admin/users/{user}/status', function (Request $request, User $user) {
    $admin = auth()->user();
    if (! $admin || $admin->user_type !== 'admin') {
        abort(403);
    }

    $validated = $request->validate([
        'account_status' => ['required', 'in:active,inactive,suspended'],
    ]);

    $nextStatus = strtolower((string) $validated['account_status']);

    if (
        $user->user_type === 'admin'
        && $nextStatus !== 'active'
        && User::where('user_type', 'admin')
            ->where('account_status', 'active')
            ->whereKeyNot($user->id)
            ->count() === 0
    ) {
        $message = 'Cannot deactivate or suspend the last active admin account.';

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['message' => $message], 422);
        }

        return back()->withErrors(['account_status' => $message]);
    }

    $user->update([
        'account_status' => $nextStatus,
    ]);

    $message = match ($nextStatus) {
        'active' => 'Account activated successfully.',
        'suspended' => 'Account suspended successfully.',
        default => 'Account deactivated successfully.',
    };

    if ($request->expectsJson() || $request->ajax()) {
        return response()->json([
            'message' => $message,
            'user' => [
                'id' => $user->id,
                'account_status' => $user->normalizedAccountStatus(),
            ],
        ]);
    }

    return back()->with('success', $message);
})->name('admin.users.status')->middleware('auth');

Route::get('/instructor/dashboard', function () {
    $user = auth()->user();
    if ($user && ! $user->hasActiveAccount()) {
        $message = $user->normalizedAccountStatus() === 'suspended'
            ? 'Access denied. Your account is suspended. Please contact the administrator.'
            : 'Access denied. Your account is deactivated. Please contact the administrator.';

        Auth::guard('web')->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('login')->with('status', $message);
    }

    if (! $user || $user->user_type !== 'instructor') {
        abort(403);
    }

    \App\Services\ConsultationOverdueService::markOverdueAsIncompleted();

    $now = now();
    $currentYear = (int) $now->format('Y');
    $currentMonth = (int) $now->format('n');
    $defaultAcademicYear = $currentMonth >= 8
        ? $currentYear . '-' . ($currentYear + 1)
        : ($currentYear - 1) . '-' . $currentYear;
    $defaultSemester = $currentMonth >= 8 ? 'first' : 'second';
    $selectedSemester = request()->query('semester');
    $selectedAcademicYear = request()->query('academic_year');
    if (! $selectedSemester || ! $selectedAcademicYear) {
        $latestTagged = InstructorAvailability::where('instructor_id', $user->id)
            ->whereNotNull('semester')
            ->whereNotNull('academic_year')
            ->orderByDesc('updated_at')
            ->first();
        $selectedSemester = $selectedSemester
            ?? ($latestTagged?->semester ?? $defaultSemester);
        $selectedAcademicYear = $selectedAcademicYear
            ?? ($latestTagged?->academic_year ?? $defaultAcademicYear);
    }

    $consultations = Consultation::with('student')
        ->where('instructor_id', $user->id)
        ->orderByRaw("
            CASE
                WHEN status IN ('declined', 'decline') THEN 1
                ELSE 0
            END
        ")
        ->orderByDesc('created_at')
        ->orderByDesc('consultation_date')
        ->orderByDesc('consultation_time')
        ->get();

    $notifications = UserNotification::where('user_id', $user->id)
        ->orderByDesc('created_at')
        ->get();

    $stats = [
        'total' => $consultations->count(),
        'pending' => $consultations->where('status', 'pending')->count(),
        'approved' => $consultations->where('status', 'approved')->count(),
        'completed' => $consultations->where('status', 'completed')->count(),
    ];

    $availabilities = InstructorAvailability::where('instructor_id', $user->id)
        ->where('is_active', true)
        ->where('semester', $selectedSemester)
        ->where('academic_year', $selectedAcademicYear)
        ->orderBy('available_day')
        ->orderBy('start_time')
        ->get();

    $feedbacks = Feedback::with(['student', 'consultation'])
        ->where('instructor_id', $user->id)
        ->orderByDesc('created_at')
        ->get();

    $totalFeedback = $feedbacks->count();
    $positiveCount = $feedbacks->filter(fn ($feedback) => (int) $feedback->rating >= 4)->count();
    $feedbackStats = [
        'average_rating' => $totalFeedback ? round((float) $feedbacks->avg('rating'), 1) : 0,
        'total_feedback' => $totalFeedback,
        'positive_rate' => $totalFeedback ? (int) round(($positiveCount / $totalFeedback) * 100) : 0,
        'this_month' => $feedbacks->filter(fn ($feedback) => $feedback->created_at && $feedback->created_at->isSameMonth(now()))->count(),
    ];

    // Get online student IDs
    $onlineStudentIds = \App\Services\UserSessionService::getOnlineUserIds('student');

    // Get last active minutes for each consultation's student
    $consultationActiveMinutes = $consultations->map(function ($c) {
        return [
            'student_id' => $c->student_id,
            'last_active_minutes' => \App\Services\UserSessionService::getLastActiveMinutes($c->student_id),
        ];
    })->keyBy('student_id');

    return view('instructor.dashboard', compact(
        'consultations',
        'notifications',
        'stats',
        'availabilities',
        'feedbacks',
        'feedbackStats',
        'selectedSemester',
        'selectedAcademicYear',
        'onlineStudentIds',
        'consultationActiveMinutes'
    ));
})->name('instructor.dashboard')->middleware('auth');

Route::post('/webrtc/signal', function (Request $request) {
    $user = auth()->user();
    if (! $user) {
        abort(403);
    }

    $validated = $request->validate([
        'consultation_id' => ['required', 'integer', 'exists:consultations,id'],
        'type' => ['required', 'in:offer,answer,ice,disconnect,answered'],
        'payload' => ['required', 'array'],
        'device_session_id' => ['nullable', 'string', 'max:100'],
    ]);

    $consultation = Consultation::findOrFail($validated['consultation_id']);
    if ($consultation->student_id !== $user->id && $consultation->instructor_id !== $user->id) {
        abort(403);
    }

    $signalType = (string) $validated['type'];
    $signalPayload = (array) $validated['payload'];

    // If either participant explicitly ends the call, persist completion
    // immediately so status/history update without waiting for extra requests.
    if ($signalType === 'disconnect') {
        $reason = Str::lower((string) ($signalPayload['reason'] ?? ''));
        $isCompletableStatus = (string) $consultation->status === 'in_progress';
        $isAlreadyCompleted = (string) $consultation->status === 'completed';

        if ($reason === 'call_ended' && $isCompletableStatus && ! $isAlreadyCompleted) {
            $endedAt = now();
            $startedAt = $consultation->started_at ?: $endedAt;
            $durationMinutes = max(0, (int) $startedAt->diffInMinutes($endedAt));

            $consultation->update([
                'status' => 'completed',
                'ended_at' => $endedAt,
                'duration_minutes' => $durationMinutes,
                'transcript_active' => false,
            ]);

            $otherPartyId = (int) $user->id === (int) $consultation->student_id
                ? $consultation->instructor_id
                : $consultation->student_id;

            if ($otherPartyId) {
                UserNotification::create([
                    'user_id' => $otherPartyId,
                    'title' => 'Session Completed',
                    'message' => 'The video call session has ended.',
                    'type' => 'session',
                    'is_read' => false,
                ]);
            }

            notifyAdmins(
                'Session Completed',
                ($consultation->instructor?->name ?? 'Instructor') . ' completed the video call session with ' .
                    ($consultation->student?->name ?? 'Student') . '.',
                'session'
            );
        }
    }

    $senderRole = $consultation->instructor_id === $user->id ? 'instructor' : 'student';
    $deviceSessionId = $validated['device_session_id'];

    // Check if another device session is already active for this user
    if ($validated['type'] === 'offer' && $deviceSessionId) {
        $lastActiveSession = DB::table('webrtc_signals')
            ->where('consultation_id', $consultation->id)
            ->where('sender_id', $user->id)
            ->where('sender_role', $senderRole)
            ->where('device_session_id', '!=', $deviceSessionId)
            ->where('type', '!=', 'disconnect')
            ->latest('id')
            ->first(['device_session_id']);

        if ($lastActiveSession && $lastActiveSession->device_session_id) {
            // Send a disconnect signal to the old device
            DB::table('webrtc_signals')->insertGetId([
                'consultation_id' => $consultation->id,
                'sender_id' => $user->id,
                'sender_role' => $senderRole,
                'device_session_id' => $lastActiveSession->device_session_id,
                'type' => 'disconnect',
                'payload' => json_encode(['reason' => 'another_device_joined']),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    $id = DB::table('webrtc_signals')->insertGetId([
        'consultation_id' => $consultation->id,
        'sender_id' => $user->id,
        'sender_role' => $senderRole,
        'device_session_id' => $deviceSessionId,
        'type' => $signalType,
        'payload' => json_encode($signalPayload),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return response()->json(['id' => $id]);
})->middleware('auth');

Route::get('/webrtc/poll', function (Request $request) {
    $user = auth()->user();
    if (! $user) {
        abort(403);
    }

    $validated = $request->validate([
        'consultation_id' => ['required', 'integer', 'exists:consultations,id'],
        'after' => ['nullable', 'integer', 'min:0'],
        'device_session_id' => ['nullable', 'string', 'max:100'],
    ]);

    $consultation = Consultation::findOrFail($validated['consultation_id']);
    if ($consultation->student_id !== $user->id && $consultation->instructor_id !== $user->id) {
        abort(403);
    }

    $after = (int) ($validated['after'] ?? 0);
    $deviceSessionId = $validated['device_session_id'];

    $query = DB::table('webrtc_signals')
        ->where('consultation_id', $consultation->id)
        ->where('id', '>', $after)
        ->where('sender_id', '!=', $user->id)
        ->orderBy('id');

    // If polling with a device_session_id, allow all regular signals and
    // disconnect signals that are either broadcast (null device_session_id)
    // or targeted to this specific device session.
    if ($deviceSessionId) {
        $query->where(function ($q) use ($deviceSessionId) {
            $q->where('type', '!=', 'disconnect')
              ->orWhere(function ($subQ) use ($deviceSessionId) {
                  $subQ->where('type', 'disconnect')
                       ->where(function ($deviceQ) use ($deviceSessionId) {
                           $deviceQ->whereNull('device_session_id')
                               ->orWhere('device_session_id', $deviceSessionId);
                       });
              });
        });
    }

    $signals = $query->get()
        ->map(function ($signal) {
            return [
                'id' => $signal->id,
                'type' => $signal->type,
                'payload' => json_decode($signal->payload, true),
            ];
        });

    return response()->json([
        'signals' => $signals,
        'consultation' => [
            'id' => (int) $consultation->id,
            'status' => (string) ($consultation->status ?? ''),
            'started_at' => optional($consultation->started_at)?->toIso8601String(),
            'ended_at' => optional($consultation->ended_at)?->toIso8601String(),
            'duration_minutes' => $consultation->duration_minutes !== null
                ? (int) $consultation->duration_minutes
                : null,
        ],
    ]);
})->middleware('auth');

Route::get('/webrtc/last-signal-id', function (Request $request) {
    $user = auth()->user();
    if (! $user) {
        abort(403);
    }

    $validated = $request->validate([
        'consultation_id' => ['required', 'integer', 'exists:consultations,id'],
    ]);

    $consultation = Consultation::findOrFail($validated['consultation_id']);
    if ($consultation->student_id !== $user->id && $consultation->instructor_id !== $user->id) {
        abort(403);
    }

    // Get the latest signal ID for this consultation
    $lastSignal = DB::table('webrtc_signals')
        ->where('consultation_id', $consultation->id)
        ->where('sender_id', '!=', $user->id)
        ->latest('id')
        ->first(['id']);

    $lastSignalId = $lastSignal ? $lastSignal->id : 0;

    return response()->json(['lastSignalId' => $lastSignalId]);
})->middleware('auth');

Route::get('/consultations/{consultation}/agora-token', function (Consultation $consultation) {
    $user = auth()->user();
    if (! $user) {
        abort(403);
    }

    $isParticipant = (int) $consultation->student_id === (int) $user->id
        || (int) $consultation->instructor_id === (int) $user->id;

    if (! $isParticipant) {
        abort(403);
    }

    $appId = (string) config('services.agora.app_id');
    $appCertificate = (string) config('services.agora.app_certificate');
    $tokenExpireSeconds = max(60, (int) config('services.agora.token_expire_seconds', 3600));

    if ($appId === '' || $appCertificate === '') {
        return response()->json([
            'message' => 'Agora credentials are incomplete.',
        ], 500);
    }

    $channelName = 'consultation-' . $consultation->id;
    $uid = (string) $user->id;
    $token = \BoogieFromZk\AgoraToken\RtcTokenBuilder2::buildTokenWithUserAccount(
        $appId,
        $appCertificate,
        $channelName,
        $uid,
        \BoogieFromZk\AgoraToken\RtcTokenBuilder2::ROLE_PUBLISHER,
        $tokenExpireSeconds,
        $tokenExpireSeconds
    );

    return response()->json([
        'app_id' => $appId,
        'channel' => $channelName,
        'token' => $token,
        'uid' => $uid,
        'expires_in' => $tokenExpireSeconds,
    ]);
})->middleware('auth')->name('consultations.agora-token');

Route::get('/instructor/consultations/history', function (Request $request) {
    $user = auth()->user();
    if (! $user || $user->user_type !== 'instructor') {
        abort(403);
    }

    $query = Consultation::with('student')
        ->where('instructor_id', $user->id)
        ->where('status', 'completed');

    if ($request->filled('type')) {
        $query->where('consultation_type', $request->string('type')->toString());
    }

    if ($request->filled('mode')) {
        $query->where('consultation_mode', $request->string('mode')->toString());
    }

    if ($request->filled('semester')) {
        $semester = $request->string('semester')->toString();
        if ($semester === 'first') {
            $query->whereMonth('consultation_date', '>=', 8)
                ->whereMonth('consultation_date', '<=', 12);
        } elseif ($semester === 'second') {
            $query->whereMonth('consultation_date', '>=', 1)
                ->whereMonth('consultation_date', '<=', 5);
        }
    }

    if ($request->filled('academic_year')) {
        $academicYear = $request->string('academic_year')->toString();
        if (preg_match('/^\d{4}-\d{4}$/', $academicYear)) {
            $startYear = (int) substr($academicYear, 0, 4);
            $endYear = (int) substr($academicYear, 5, 4);
            $startDate = \Illuminate\Support\Carbon::create($startYear, 8, 1)->toDateString();
            $endDate = \Illuminate\Support\Carbon::create($endYear, 5, 31)->toDateString();
            $query->whereBetween('consultation_date', [$startDate, $endDate]);
        }
    }

    if ($request->filled('search')) {
        $needle = strtolower(trim((string) $request->search));
        $query->where(function ($q) use ($needle) {
            $q->whereRaw('LOWER(consultation_type) like ?', ["%{$needle}%"])
                ->orWhereRaw('LOWER(consultation_mode) like ?', ["%{$needle}%"])
                ->orWhereHas('student', function ($studentQ) use ($needle) {
                    $studentQ->whereRaw('LOWER(name) like ?', ["%{$needle}%"]);
                });
        });
    }

    $consultations = $query
        ->orderByDesc('consultation_date')
        ->orderByDesc('consultation_time')
        ->get();

    $typeOptions = Consultation::where('instructor_id', $user->id)
        ->where('status', 'completed')
        ->pluck('consultation_type')
        ->filter()
        ->unique()
        ->sort()
        ->values();

    $modeOptions = Consultation::where('instructor_id', $user->id)
        ->where('status', 'completed')
        ->pluck('consultation_mode')
        ->filter()
        ->unique()
        ->sort()
        ->values();

    $academicYearOptions = Consultation::where('instructor_id', $user->id)
        ->where('status', 'completed')
        ->pluck('consultation_date')
        ->filter()
        ->map(function ($date) {
            try {
                $parsed = \Illuminate\Support\Carbon::parse($date);
            } catch (\Exception $e) {
                return null;
            }
            $month = (int) $parsed->format('n');
            $year = (int) $parsed->format('Y');
            if ($month >= 8) {
                return $year . '-' . ($year + 1);
            }
            if ($month <= 5) {
                return ($year - 1) . '-' . $year;
            }
            return null;
        })
        ->filter()
        ->unique()
        ->sort()
        ->values();

    $filters = [
        'type' => (string) $request->input('type', ''),
        'mode' => (string) $request->input('mode', ''),
        'search' => (string) $request->input('search', ''),
        'semester' => (string) $request->input('semester', ''),
        'academic_year' => (string) $request->input('academic_year', ''),
    ];

    return view('instructor.consultation-history', compact(
        'consultations',
        'typeOptions',
        'modeOptions',
        'academicYearOptions',
        'filters'
    ));
})->name('instructor.consultations.history')->middleware('auth');

Route::post('/instructor/availability', function (Request $request) {
    $user = auth()->user();
    if (! $user || $user->user_type !== 'instructor') {
        abort(403);
    }

    $request->validate([
        'semester' => 'required|in:first,second',
        'academic_year' => ['required', 'regex:/^\\d{4}-\\d{4}$/'],
        'days' => 'required|array|min:1',
        'days.*' => 'in:monday,tuesday,wednesday,thursday,friday,saturday',
        'slot_times' => 'required|array',
        'end_times' => 'nullable|array',
    ]);

    $semester = $request->input('semester');
    $academicYear = $request->input('academic_year');

    InstructorAvailability::where('instructor_id', $user->id)
        ->where('semester', $semester)
        ->where('academic_year', $academicYear)
        ->delete();

    foreach ($request->input('days', []) as $day) {
        $startTime = collect($request->input("slot_times.$day", []))
            ->filter()
            ->map(fn ($time) => substr((string) $time, 0, 5))
            ->first();

        if (! $startTime) {
            continue;
        }

        $endTime = collect($request->input("end_times.$day", []))
            ->filter()
            ->map(fn ($time) => substr((string) $time, 0, 5))
            ->first();

        $start = Carbon::createFromFormat('H:i', $startTime, 'Asia/Manila');
        $end = $endTime
            ? Carbon::createFromFormat('H:i', $endTime, 'Asia/Manila')
            : $start->copy()->addHour();

        InstructorAvailability::create([
            'instructor_id' => $user->id,
            'semester' => $semester,
            'academic_year' => $academicYear,
            'available_day' => $day,
            'start_time' => $start->format('H:i:s'),
            'end_time' => $end->format('H:i:s'),
            'is_active' => true,
        ]);
    }

    return redirect()
        ->route('instructor.dashboard', ['semester' => $semester, 'academic_year' => $academicYear])
        ->with('success', 'Availability updated successfully.');
})->name('instructor.availability.store')->middleware('auth');

Route::post('/instructor/consultations/{consultation}/approve', function (Consultation $consultation) {
    if ((int) $consultation->instructor_id !== (int) auth()->id()) {
        abort(403);
    }

    $expectsJson = request()->expectsJson() || request()->ajax();

    $consultation->update([
        'status' => 'approved',
        'reminder_sent_at' => null,
    ]);

    $startLabel = substr((string) $consultation->consultation_time, 0, 5);
    $endLabel = substr((string) $consultation->consultation_end_time, 0, 5);
    $timeLabel = $endLabel ? ($startLabel . ' to ' . $endLabel) : $startLabel;

    UserNotification::create([
        'user_id' => $consultation->student_id,
        'title' => 'Consultation Approved',
        'message' => 'Your consultation request has been approved for ' .
            $consultation->consultation_date . ' at ' . $timeLabel . '.',
        'type' => 'approved',
        'is_read' => false,
    ]);

    notifyAdmins(
        'Consultation Approved',
        ($consultation->instructor?->name ?? 'Instructor') . ' approved the consultation of ' .
            ($consultation->student?->name ?? 'Student') . ' for ' . $consultation->consultation_date . ' at ' . $timeLabel . '.',
        'approved'
    );

    $consultationIdForMail = (int) $consultation->id;
    $studentIdForMail = (int) $consultation->student_id;
    $instructorIdForMail = (int) $consultation->instructor_id;
    $approvedDateForMail = (string) $consultation->consultation_date;
    $approvedTypeForMail = (string) ($consultation->consultation_type ?? 'Consultation');
    $approvedModeForMail = (string) ($consultation->consultation_mode ?? 'N/A');

    dispatch(function () use (
        $consultationIdForMail,
        $studentIdForMail,
        $instructorIdForMail,
        $approvedDateForMail,
        $timeLabel,
        $approvedTypeForMail,
        $approvedModeForMail
    ) {
        try {
            $consultationForMail = Consultation::find($consultationIdForMail);
            $studentForMail = User::find($studentIdForMail);
            $instructorForMail = User::find($instructorIdForMail);

            if ($consultationForMail && $studentForMail && $studentForMail->email && $instructorForMail) {
                try {
                    Mail::to($studentForMail->email)->send(
                        new ConsultationStatusUpdate($consultationForMail, $studentForMail, $instructorForMail, 'approved')
                    );
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning('Failed to send ConsultationStatusUpdate (approved).', [
                        'consultation_id' => $consultationIdForMail,
                        'student_id' => $studentIdForMail,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            if ($consultationForMail && $studentForMail && $instructorForMail) {
                SmsNotificationService::sendStatusUpdate(
                    $consultationForMail,
                    $studentForMail,
                    $instructorForMail,
                    'approved'
                );
            }

            $admins = User::where('user_type', 'admin')->whereNotNull('email')->get();
            foreach ($admins as $admin) {
                try {
                    Mail::to($admin->email)->send(new AdminActionMail(
                        'approved',
                        $instructorForMail?->name ?? 'Instructor',
                        'instructor',
                        $studentForMail?->name ?? 'Student',
                        'student',
                        [
                            'date' => $approvedDateForMail,
                            'time' => $timeLabel,
                            'type' => $approvedTypeForMail,
                            'mode' => $approvedModeForMail,
                        ],
                        ($instructorForMail?->name ?? 'Instructor') . ' approved a consultation request from ' . ($studentForMail?->name ?? 'Student') . ' for ' . $approvedDateForMail . ' at ' . $timeLabel . '.',
                        now()->format('Y-m-d H:i:s')
                    ));
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning('Failed to send AdminActionMail (approved).', [
                        'consultation_id' => $consultationIdForMail,
                        'admin_id' => $admin->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Deferred approved email dispatch failed.', [
                'consultation_id' => $consultationIdForMail,
                'error' => $e->getMessage(),
            ]);
        }
    })->afterResponse();

    if ($expectsJson) {
        return response()->json([
            'ok' => true,
            'status' => 'approved',
            'message' => 'Consultation approved.',
            'consultation_id' => (int) $consultation->id,
        ]);
    }

    return back()->with('success', 'Consultation approved.');
})->name('instructor.consultations.approve')->middleware('auth');

Route::post('/instructor/consultations/{consultation}/decline', function (Consultation $consultation) {
    if ((int) $consultation->instructor_id !== (int) auth()->id()) {
        abort(403);
    }

    $expectsJson = request()->expectsJson() || request()->ajax();

    $consultation->update(['status' => 'declined']);

    $startLabel = substr((string) $consultation->consultation_time, 0, 5);
    $endLabel = substr((string) $consultation->consultation_end_time, 0, 5);
    $timeLabel = $endLabel ? ($startLabel . ' to ' . $endLabel) : $startLabel;

    UserNotification::create([
        'user_id' => $consultation->student_id,
        'title' => 'Consultation Declined',
        'message' => 'Your consultation request was declined.',
        'type' => 'declined',
        'is_read' => false,
    ]);

    notifyAdmins(
        'Consultation Declined',
        ($consultation->instructor?->name ?? 'Instructor') . ' declined the consultation of ' .
            ($consultation->student?->name ?? 'Student') . ' for ' . $consultation->consultation_date . ' at ' . $timeLabel . '.',
        'declined'
    );

    $consultationIdForMail = (int) $consultation->id;
    $studentIdForMail = (int) $consultation->student_id;
    $instructorIdForMail = (int) $consultation->instructor_id;
    $declinedDateForMail = (string) $consultation->consultation_date;
    $declinedTypeForMail = (string) ($consultation->consultation_type ?? 'Consultation');
    $declinedModeForMail = (string) ($consultation->consultation_mode ?? 'N/A');

    dispatch(function () use (
        $consultationIdForMail,
        $studentIdForMail,
        $instructorIdForMail,
        $declinedDateForMail,
        $timeLabel,
        $declinedTypeForMail,
        $declinedModeForMail
    ) {
        try {
            $consultationForMail = Consultation::find($consultationIdForMail);
            $studentForMail = User::find($studentIdForMail);
            $instructorForMail = User::find($instructorIdForMail);

            if ($consultationForMail && $studentForMail && $studentForMail->email && $instructorForMail) {
                try {
                    Mail::to($studentForMail->email)->send(
                        new ConsultationStatusUpdate($consultationForMail, $studentForMail, $instructorForMail, 'declined')
                    );
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning('Failed to send ConsultationStatusUpdate (declined).', [
                        'consultation_id' => $consultationIdForMail,
                        'student_id' => $studentIdForMail,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            if ($consultationForMail && $studentForMail && $instructorForMail) {
                SmsNotificationService::sendStatusUpdate(
                    $consultationForMail,
                    $studentForMail,
                    $instructorForMail,
                    'declined'
                );
            }

            $admins = User::where('user_type', 'admin')->whereNotNull('email')->get();
            foreach ($admins as $admin) {
                try {
                    Mail::to($admin->email)->send(new AdminActionMail(
                        'declined',
                        $instructorForMail?->name ?? 'Instructor',
                        'instructor',
                        $studentForMail?->name ?? 'Student',
                        'student',
                        [
                            'date' => $declinedDateForMail,
                            'time' => $timeLabel,
                            'type' => $declinedTypeForMail,
                            'mode' => $declinedModeForMail,
                        ],
                        ($instructorForMail?->name ?? 'Instructor') . ' declined a consultation request from ' . ($studentForMail?->name ?? 'Student') . ' for ' . $declinedDateForMail . ' at ' . $timeLabel . '.',
                        now()->format('Y-m-d H:i:s')
                    ));
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning('Failed to send AdminActionMail (declined).', [
                        'consultation_id' => $consultationIdForMail,
                        'admin_id' => $admin->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Deferred declined email dispatch failed.', [
                'consultation_id' => $consultationIdForMail,
                'error' => $e->getMessage(),
            ]);
        }
    })->afterResponse();

    if ($expectsJson) {
        return response()->json([
            'ok' => true,
            'status' => 'declined',
            'message' => 'Consultation declined.',
            'consultation_id' => (int) $consultation->id,
        ]);
    }

    return back()->with('success', 'Consultation declined.');
})->name('instructor.consultations.decline')->middleware('auth');

Route::post('/instructor/consultations/{consultation}/start', function (Request $request, Consultation $consultation) {
    if ((int) $consultation->instructor_id !== (int) auth()->id()) {
        abort(403);
    }

    $modeValue = Str::lower((string) $consultation->consultation_mode);
    if (Str::contains($modeValue, 'face')) {
        return $request->expectsJson()
            ? response()->json(['message' => 'Face-to-face consultations do not use video call.'], 422)
            : back()->withErrors(['consultation_start' => 'Face-to-face consultations do not use video call.']);
    }

    if (! in_array((string) $consultation->status, ['approved', 'in_progress'], true)) {
        return $request->expectsJson()
            ? response()->json(['message' => 'Session can only be started from approved consultations.'], 422)
            : back()->withErrors(['consultation_start' => 'Session can only be started from approved consultations.']);
    }

    $attempts = ((int) ($consultation->call_attempts ?? 0)) + 1;

    $consultation->update([
        'status' => 'in_progress',
        'started_at' => null,
        'ended_at' => null,
        'duration_minutes' => null,
        'transcript_active' => false,
        'call_attempts' => $attempts,
    ]);

    $startLabel = substr((string) $consultation->consultation_time, 0, 5);
    $endLabel = substr((string) $consultation->consultation_end_time, 0, 5);
    $timeLabel = $endLabel ? ($startLabel . ' to ' . $endLabel) : $startLabel;

    UserNotification::create([
        'user_id' => $consultation->student_id,
        'title' => 'Session Started',
        'message' => 'Instructor is calling for your consultation (' .
            $consultation->consultation_date . ' at ' . $timeLabel . ') - attempt #' . $attempts . '.',
        'type' => 'session',
        'is_read' => false,
    ]);

    notifyAdmins(
        'Video Call In Progress',
        ($consultation->instructor?->name ?? 'Instructor') . ' started a video call with ' .
            ($consultation->student?->name ?? 'Student') . ' for ' . $consultation->consultation_date . ' at ' . $timeLabel .
            ' (Attempt #' . $attempts . ').',
        'session'
    );

    // Send email to student/admin, but do not block session start on SMTP failures.
    $student = $consultation->student;
    if ($student && $student->email) {
        try {
            Mail::to($student->email)->send(new InstructorCallingMail(
                $consultation->instructor?->name ?? 'Instructor',
                $consultation->consultation_date,
                $consultation->consultation_time,
                $consultation->consultation_end_time,
                $consultation->consultation_type ?? 'Video Call',
                $attempts
            ));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Failed to send InstructorCallingMail.', [
                'consultation_id' => $consultation->id,
                'student_id' => $student->id,
                'mail_to' => $student->email,
                'error' => $e->getMessage(),
            ]);
        }
    }

    if ($student) {
        SmsNotificationService::sendInstructorCalling(
            $consultation,
            $student,
            $consultation->instructor,
            $attempts
        );
    }

    $admins = User::where('user_type', 'admin')->get();
    foreach ($admins as $admin) {
        if (! $admin->email) {
            continue;
        }

        try {
            Mail::to($admin->email)->send(new AdminActionMail(
                'call_started',
                $consultation->instructor?->name ?? 'Instructor',
                'instructor',
                $student?->name ?? 'Student',
                'student',
                [
                    'date' => $consultation->consultation_date,
                    'time' => $timeLabel,
                    'type' => $consultation->consultation_type ?? 'Consultation',
                    'mode' => $consultation->consultation_mode ?? 'N/A',
                ],
                ($consultation->instructor?->name ?? 'Instructor') . ' started a video call for consultation with ' . ($student?->name ?? 'Student') . ' (Attempt #' . $attempts . ').',
                now()->format('Y-m-d H:i:s')
            ));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Failed to send AdminActionMail (call_started).', [
                'consultation_id' => $consultation->id,
                'admin_id' => $admin->id,
                'mail_to' => $admin->email,
                'error' => $e->getMessage(),
            ]);
        }
    }

    if ($request->expectsJson()) {
        return response()->json([
            'status' => 'in_progress',
            'call_attempts' => $attempts,
        ]);
    }

    return back()->with('success', 'Session started.');
})->name('instructor.consultations.start')->middleware('auth');

Route::post('/instructor/consultations/{consultation}/no-answer', function (Request $request, Consultation $consultation) {
    if ((int) $consultation->instructor_id !== (int) auth()->id()) {
        abort(403);
    }

    $attempts = (int) ($consultation->call_attempts ?? 0);
    $nextStatus = $attempts >= 3 ? 'incompleted' : 'approved';
    $transitionedToIncomplete = false;

    if ((string) $consultation->status === 'in_progress' && ! $consultation->started_at) {
        $consultation->update([
            'status' => $nextStatus,
            'ended_at' => null,
            'duration_minutes' => null,
            'transcript_active' => false,
        ]);
        $transitionedToIncomplete = $nextStatus === 'incompleted';
    }

    if ($transitionedToIncomplete) {
        ConsultationNotificationService::sendIncompleteNotifications(
            $consultation,
            $attempts,
            'because there was no answer after 3 unanswered video call attempts.'
        );
    }

    $canMarkIncomplete = $attempts >= 3;

    if ($request->expectsJson()) {
        return response()->json([
            'status' => $nextStatus,
            'call_attempts' => $attempts,
            'can_mark_incomplete' => $canMarkIncomplete,
        ]);
    }

    return back()->with('success', $nextStatus === 'incompleted'
        ? 'Consultation marked as incomplete after 3 attempts.'
        : 'No answer. You can try calling again.');
})->name('instructor.consultations.no-answer')->middleware('auth');

Route::post('/instructor/consultations/{consultation}/end', function (Consultation $consultation) {
    if ((int) $consultation->instructor_id !== (int) auth()->id()) {
        abort(403);
    }

    $endedAt = now();
    $startedAt = $consultation->started_at ?: $endedAt;
    $durationMinutes = max(0, (int) $startedAt->diffInMinutes($endedAt));
    $updates = [
        'status' => 'completed',
        'ended_at' => $endedAt,
        'duration_minutes' => $durationMinutes,
        'transcript_active' => false,
    ];

    $consultation->update($updates);

    $startLabel = substr((string) $consultation->consultation_time, 0, 5);
    $endLabel = substr((string) $consultation->consultation_end_time, 0, 5);
    $timeLabel = $endLabel ? ($startLabel . ' to ' . $endLabel) : $startLabel;

    UserNotification::create([
        'user_id' => $consultation->student_id,
        'title' => 'Session Completed',
        'message' => 'Your consultation session has been marked as completed (' .
            $consultation->consultation_date . ' at ' . $timeLabel . ').',
        'type' => 'session',
        'is_read' => false,
    ]);

    notifyAdmins(
        'Session Completed',
        ($consultation->instructor?->name ?? 'Instructor') . ' marked the consultation with ' .
            ($consultation->student?->name ?? 'Student') . ' as completed.',
        'session'
    );

    return back()->with('success', 'Session completed.');
})->name('instructor.consultations.end')->middleware('auth');

Route::post('/consultations/{consultation}/answer', function (Request $request, Consultation $consultation) {
    $user = auth()->user();
    if (! $user || (int) $consultation->student_id !== (int) $user->id) {
        abort(403);
    }

    if (! in_array((string) $consultation->status, ['approved', 'in_progress'], true)) {
        return response()->json([
            'ok' => false,
            'message' => 'Consultation is not available for answering.',
        ], 422);
    }

    $updates = [
        'status' => 'in_progress',
        'ended_at' => null,
        'duration_minutes' => null,
    ];

    $updates['started_at'] = now();

    $consultation->update($updates);

    return response()->json([
        'ok' => true,
        'started_at' => optional($consultation->fresh()->started_at)?->toISOString(),
    ]);
})->name('consultations.answer')->middleware('auth');

Route::post('/consultations/{consultation}/decline-call', function (Request $request, Consultation $consultation) {
    $user = auth()->user();
    if (! $user || (int) $consultation->student_id !== (int) $user->id) {
        abort(403);
    }

    $attempts = (int) ($consultation->call_attempts ?? 0);
    $nextStatus = $attempts >= 3 ? 'incompleted' : 'approved';
    $transitionedToIncomplete = false;

    if ((string) $consultation->status === 'in_progress' && ! $consultation->started_at) {
        $consultation->update([
            'status' => $nextStatus,
            'ended_at' => null,
            'duration_minutes' => null,
            'transcript_active' => false,
        ]);
        $transitionedToIncomplete = $nextStatus === 'incompleted';
    }

    if ($transitionedToIncomplete) {
        ConsultationNotificationService::sendIncompleteNotifications(
            $consultation,
            $attempts,
            'because there was no answer after 3 unanswered video call attempts.'
        );
    }

    $canMarkIncomplete = $attempts >= 3;

    return response()->json([
        'ok' => true,
        'status' => $nextStatus,
        'call_attempts' => $attempts,
        'can_mark_incomplete' => $canMarkIncomplete,
    ]);
})->name('consultations.decline-call')->middleware('auth');

Route::post('/consultations/{consultation}/end-call', function (Request $request, Consultation $consultation) {
    $user = auth()->user();
    if (! $user) {
        abort(403);
    }

    $isParticipant = (int) $consultation->student_id === (int) $user->id
        || (int) $consultation->instructor_id === (int) $user->id;
    if (! $isParticipant) {
        abort(403);
    }

    if ((string) $consultation->status === 'completed') {
        return response()->json([
            'ok' => true,
            'already_completed' => true,
            'duration_minutes' => (int) ($consultation->duration_minutes ?? 0),
            'consultation' => [
                'id' => (int) $consultation->id,
                'status' => (string) ($consultation->status ?? 'completed'),
                'ended_at' => optional($consultation->ended_at)?->toIso8601String(),
                'duration_minutes' => $consultation->duration_minutes !== null
                    ? (int) $consultation->duration_minutes
                    : 0,
            ],
        ]);
    }

    $endedAt = now();
    $startedAt = $consultation->started_at ?: $endedAt;
    $durationMinutes = max(0, (int) $startedAt->diffInMinutes($endedAt));

    $consultation->update([
        'status' => 'completed',
        'ended_at' => $endedAt,
        'duration_minutes' => $durationMinutes,
        'transcript_active' => false,
    ]);

    $otherPartyId = (int) $user->id === (int) $consultation->student_id
        ? $consultation->instructor_id
        : $consultation->student_id;

    if ($otherPartyId) {
        UserNotification::create([
            'user_id' => $otherPartyId,
            'title' => 'Consultation Complete',
            'message' => 'Your consultation video call has been completed.',
            'type' => 'session',
            'is_read' => false,
        ]);
    }

    notifyAdmins(
        'Session Completed',
        ($consultation->instructor?->name ?? 'Instructor') . ' completed the video call session with ' .
            ($consultation->student?->name ?? 'Student') . '.',
        'session'
    );

    $consultation->refresh();

    return response()->json([
        'ok' => true,
        'duration_minutes' => $durationMinutes,
        'consultation' => [
            'id' => (int) $consultation->id,
            'status' => (string) ($consultation->status ?? 'completed'),
            'ended_at' => optional($consultation->ended_at)?->toIso8601String(),
            'duration_minutes' => $consultation->duration_minutes !== null
                ? (int) $consultation->duration_minutes
                : $durationMinutes,
        ],
    ]);
})->name('consultations.end-call')->middleware('auth');

Route::post('/instructor/consultations/{consultation}/mark-incomplete', function (Consultation $consultation) {
    if ((int) $consultation->instructor_id !== (int) auth()->id()) {
        abort(403);
    }

    if ((int) ($consultation->call_attempts ?? 0) < 3) {
        return back()->withErrors([
            'consultation_incomplete' => 'You can mark this consultation incomplete only after 3 unanswered attempts.',
        ]);
    }

    if ((string) $consultation->status === 'incompleted') {
        return back()->with('success', 'Consultation already marked as incomplete.');
    }

    $consultation->update([
        'status' => 'incompleted',
        'started_at' => null,
        'ended_at' => null,
        'duration_minutes' => null,
        'transcript_active' => false,
    ]);

    $attempts = (int) ($consultation->call_attempts ?? 0);
    ConsultationNotificationService::sendIncompleteNotifications(
        $consultation,
        $attempts,
        'because there was no answer after multiple unanswered call attempts.'
    );

    return back()->with('success', 'Consultation marked as incomplete.');
})->name('instructor.consultations.mark-incomplete')->middleware('auth');

Route::post('/instructor/consultations/{consultation}/summary', function (Request $request, Consultation $consultation) {
    if ((int) $consultation->instructor_id !== (int) auth()->id()) {
        abort(403);
    }

    $request->validate([
        'summary_text' => 'required|string|max:6000',
        'action_taken_text' => 'required|string|max:20000',
    ]);

    $wasCompleted = (string) $consultation->status === 'completed';

    $updates = [
        'summary_text' => $request->summary_text,
        'transcript_text' => $request->action_taken_text,
    ];

    // If it's a face-to-face consultation and summary is being added, mark as completed
    if (strtolower($consultation->consultation_mode) === 'face-to-face') {
        $updates['status'] = 'completed';
    }

    $consultation->update($updates);

    if (strtolower((string) $consultation->consultation_mode) === 'face-to-face' && ! $wasCompleted) {
        notifyAdmins(
            'Face-to-Face Session Completed',
            ($consultation->instructor?->name ?? 'Instructor') . ' completed the face-to-face consultation with ' .
                ($consultation->student?->name ?? 'Student') . ' and added a summary.',
            'session'
        );
    }

    if ($request->expectsJson() || $request->ajax()) {
        return response()->json([
            'ok' => true,
            'message' => 'Summary and action taken saved.',
            'summary_text' => (string) $consultation->summary_text,
            'action_taken_text' => (string) ($consultation->transcript_text ?? ''),
        ]);
    }

    return back()->with('success', 'Summary and action taken saved.');
})->name('instructor.consultations.summary')->middleware('auth');

Route::post('/instructor/consultations/{consultation}/transcript', function (Request $request, Consultation $consultation) {
    if ((int) $consultation->instructor_id !== (int) auth()->id()) {
        abort(403);
    }

    $request->validate([
        'transcript' => 'required|string|max:20000',
    ]);

    $consultation->update([
        'transcript_text' => $request->transcript,
    ]);

    return response()->json(['ok' => true]);
})->name('instructor.consultations.transcript')->middleware('auth');

Route::post('/instructor/consultations/{consultation}/transcript-toggle', function (Request $request, Consultation $consultation) {
    if ((int) $consultation->instructor_id !== (int) auth()->id()) {
        abort(403);
    }

    $request->validate([
        'active' => 'required|boolean',
    ]);

    $consultation->update([
        'transcript_active' => (bool) $request->active,
    ]);

    return response()->json(['ok' => true, 'active' => (bool) $request->active]);
})->name('instructor.consultations.transcript.toggle')->middleware('auth');

Route::get('/consultations/{consultation}/transcript-status', function (Consultation $consultation) {
    $user = auth()->user();
    if (! $user) {
        abort(403);
    }
    if ($consultation->student_id !== $user->id && $consultation->instructor_id !== $user->id) {
        abort(403);
    }

    return response()->json(['active' => (bool) $consultation->transcript_active]);
})->name('consultations.transcript.status')->middleware('auth');

Route::post('/consultations/{consultation}/transcript-append', function (Request $request, Consultation $consultation) {
    $user = auth()->user();
    if (! $user) {
        abort(403);
    }

    $request->validate([
        'role' => 'required|in:instructor,student',
        'text' => 'required|string|max:2000',
    ]);

    if ($request->role === 'instructor' && (int) $consultation->instructor_id !== (int) $user->id) {
        abort(403);
    }
    if ($request->role === 'student' && (int) $consultation->student_id !== (int) $user->id) {
        abort(403);
    }

    $timestamp = now()->format('H:i');
    $line = '[' . $timestamp . '] ' . ucfirst($request->role) . ': ' . trim((string) $request->text);
    $existing = (string) ($consultation->transcript_text ?? '');
    $consultation->update([
        'transcript_text' => trim($existing . "\n" . $line),
    ]);

    return response()->json(['ok' => true]);
})->name('consultations.transcript.append')->middleware('auth');

Route::get('/consultations/{consultation}/details', function (Consultation $consultation) {
    $user = auth()->user();
    if (! $user) {
        abort(403);
    }
    if ($consultation->student_id !== $user->id && $consultation->instructor_id !== $user->id) {
        abort(403);
    }

    $consultation->loadMissing(['student', 'instructor']);
    $formatManilaTimeLower = function (?string $time): string {
        if (! $time) {
            return '--';
        }

        $value = strlen($time) === 5 ? $time . ':00' : $time;
        $formatted = \Illuminate\Support\Carbon::createFromFormat('H:i:s', $value, 'Asia/Manila')
            ->setTimezone('Asia/Manila')
            ->format('g:i A');

        return strtolower(str_replace(' ', '', $formatted));
    };
    $formatManilaRange = function (?string $start, ?string $end) use ($formatManilaTimeLower): string {
        if (! $start && ! $end) {
            return '--';
        }

        if (! $end && $start) {
            $startValue = strlen($start) === 5 ? $start . ':00' : $start;
            $endValue = \Illuminate\Support\Carbon::createFromFormat('H:i:s', $startValue, 'Asia/Manila')
                ->copy()
                ->addHour()
                ->format('H:i:s');

            return $formatManilaTimeLower($start) . ' to ' . $formatManilaTimeLower($endValue);
        }

        return $formatManilaTimeLower($start) . ' to ' . $formatManilaTimeLower($end);
    };

    return response()->json([
        'id' => $consultation->id,
        'date' => (string) $consultation->consultation_date,
        'time' => $formatManilaRange($consultation->consultation_time, $consultation->consultation_end_time),
        'mode' => (string) $consultation->consultation_mode,
        'type' => (string) $consultation->consultation_type,
        'duration' => $consultation->duration_minutes !== null ? $consultation->duration_minutes . ' min' : '--',
        'summary' => (string) ($consultation->summary_text ?? ''),
        'transcript' => (string) ($consultation->transcript_text ?? ''),
        'instructor' => (string) ($consultation->instructor?->name ?? 'Instructor'),
        'student' => (string) ($consultation->student?->name ?? 'Student'),
        'student_id' => (string) ($consultation->student?->student_id ?? ''),
    ]);
})->name('consultations.details')->middleware('auth');

Route::get('/consultations/{consultation}/export-pdf', function (Consultation $consultation) {
    $user = auth()->user();
    if (! $user) {
        abort(403);
    }

    $isOwner = (int) $consultation->student_id === (int) $user->id
        || (int) $consultation->instructor_id === (int) $user->id;

    if ($user->user_type !== 'admin' && ! $isOwner) {
        abort(403);
    }

    $consultation->loadMissing(['student', 'instructor']);

    $formatManilaTime = function (?string $time): string {
        if (! $time) {
            return '--';
        }

        $value = strlen($time) === 5 ? $time . ':00' : $time;

        return \Illuminate\Support\Carbon::createFromFormat('H:i:s', $value, 'Asia/Manila')
            ->setTimezone('Asia/Manila')
            ->format('g:i A');
    };

    $formatManilaRange = function (?string $start, ?string $end) use ($formatManilaTime): string {
        if (! $start && ! $end) {
            return '--';
        }

        if (! $end && $start) {
            $startValue = strlen($start) === 5 ? $start . ':00' : $start;
            $endValue = \Illuminate\Support\Carbon::createFromFormat('H:i:s', $startValue, 'Asia/Manila')
                ->copy()
                ->addHour()
                ->format('H:i:s');

            return $formatManilaTime($start) . ' to ' . $formatManilaTime($endValue);
        }

        return $formatManilaTime($start) . ' to ' . $formatManilaTime($end);
    };

    $durationLabel = '--';

    try {
        if ($consultation->duration_minutes !== null && $consultation->duration_minutes !== '') {
            $durationLabel = (int) $consultation->duration_minutes . ' min';
        } elseif ($consultation->consultation_time && $consultation->consultation_end_time) {
            $durationLabel = \Illuminate\Support\Carbon::parse($consultation->consultation_end_time)
                ->diffInMinutes(\Illuminate\Support\Carbon::parse($consultation->consultation_time)) . ' min';
        } elseif ($consultation->consultation_time) {
            $durationLabel = '60 min';
        }
    } catch (\Throwable $e) {
        $durationLabel = '--';
    }

    return response()->view('consultations.export-pdf', [
        'consultation' => $consultation,
        'viewer' => $user,
        'exportedAt' => now('Asia/Manila'),
        'formattedDate' => $consultation->consultation_date
            ? \Illuminate\Support\Carbon::parse($consultation->consultation_date, 'Asia/Manila')->format('F d, Y')
            : '--',
        'formattedTime' => $formatManilaRange($consultation->consultation_time, $consultation->consultation_end_time),
        'durationLabel' => $durationLabel,
        'statusLabel' => ucfirst(str_replace('_', ' ', (string) ($consultation->status ?? 'pending'))),
        'typeLabel' => (string) ($consultation->type_label ?? $consultation->consultation_type ?? 'Consultation'),
    ]);
})->name('consultations.export-pdf')->middleware('auth');

Route::get('/admin/consultations/export-pdf', function (\Illuminate\Http\Request $request) {
    $user = auth()->user();
    if (! $user || $user->user_type !== 'admin') {
        abort(403);
    }

    $consultationTopicsByCategory = [
        'Curricular Activities' => [
            'Thesis/Project',
            'Grades',
            'Requirements not submitted',
            'Lack of quizzes/assignments',
            'Other curricular concern',
        ],
        'Behavior-Related' => [
            'Tardiness/Absences',
            'Rowdy behavior',
            'Dialogue with the party in conflict',
            'Family Problem',
        ],
        'Co-curricular activities' => [
            'Make-up activities',
            'Reschedule of graded requirement',
            'Rehearsal',
        ],
    ];

    $normalize = function ($value): string {
        return \Illuminate\Support\Str::of((string) $value)->lower()->squish()->value();
    };

    $normalizedCategoryByTopic = [];
    $normalizedCategoryKeys = [];
    $normalizedTopicKeys = [];
    foreach ($consultationTopicsByCategory as $category => $topics) {
        $normalizedCategory = $normalize($category);
        $normalizedCategoryKeys[] = $normalizedCategory;
        foreach ($topics as $topic) {
            $normalizedTopic = $normalize($topic);
            $normalizedCategoryByTopic[$normalizedTopic] = $normalizedCategory;
            $normalizedTopicKeys[] = $normalizedTopic;
        }
    }
    usort($normalizedTopicKeys, fn ($a, $b) => strlen($b) <=> strlen($a));

    $deriveCategoryAndTopic = function ($consultation) use ($normalize, $normalizedCategoryByTopic, $normalizedCategoryKeys, $normalizedTopicKeys): array {
        $rowCategory = $normalize($consultation->consultation_category ?? '');
        $rowTopic = $normalize($consultation->consultation_topic ?? '');
        $rowType = $normalize($consultation->type_label ?? $consultation->consultation_type ?? '');

        if ($rowTopic === '' && $rowType !== '') {
            if (array_key_exists($rowType, $normalizedCategoryByTopic)) {
                $rowTopic = $rowType;
            } else {
                foreach ($normalizedTopicKeys as $topic) {
                    if (str_contains($rowType, $topic)) {
                        $rowTopic = $topic;
                        break;
                    }
                }
            }
        }

        if ($rowCategory === '') {
            if ($rowTopic !== '' && array_key_exists($rowTopic, $normalizedCategoryByTopic)) {
                $rowCategory = $normalizedCategoryByTopic[$rowTopic];
            } elseif ($rowType !== '') {
                foreach ($normalizedCategoryKeys as $category) {
                    if (str_contains($rowType, $category)) {
                        $rowCategory = $category;
                        break;
                    }
                }
            }
        }

        return [$rowCategory, $rowTopic];
    };

    $getSemesterFromDate = function (?string $date): ?string {
        if (! $date) {
            return null;
        }

        try {
            $month = (int) \Illuminate\Support\Carbon::parse($date, 'Asia/Manila')->format('n');
        } catch (\Throwable $e) {
            return null;
        }

        if ($month >= 8 && $month <= 12) {
            return '1';
        }

        if ($month >= 1 && $month <= 5) {
            return '2';
        }

        return null;
    };

    $getAcademicYearFromDate = function (?string $date): string {
        if (! $date) {
            return '';
        }

        try {
            $dateObj = \Illuminate\Support\Carbon::parse($date, 'Asia/Manila');
        } catch (\Throwable $e) {
            return '';
        }

        $month = (int) $dateObj->format('n');
        $year = (int) $dateObj->format('Y');

        return $month >= 8
            ? ($year . '-' . ($year + 1))
            : (($year - 1) . '-' . $year);
    };

    $formatManilaTime = function (?string $time): string {
        if (! $time) {
            return '--';
        }

        $value = strlen($time) === 5 ? $time . ':00' : $time;

        return \Illuminate\Support\Carbon::createFromFormat('H:i:s', $value, 'Asia/Manila')
            ->setTimezone('Asia/Manila')
            ->format('g:i A');
    };

    $formatManilaRange = function (?string $start, ?string $end) use ($formatManilaTime): string {
        if (! $start && ! $end) {
            return '--';
        }

        if (! $end && $start) {
            $startValue = strlen($start) === 5 ? $start . ':00' : $start;
            $endValue = \Illuminate\Support\Carbon::createFromFormat('H:i:s', $startValue, 'Asia/Manila')
                ->copy()
                ->addHour()
                ->format('H:i:s');

            return $formatManilaTime($start) . ' to ' . $formatManilaTime($endValue);
        }

        return $formatManilaTime($start) . ' to ' . $formatManilaTime($end);
    };

    $search = $normalize($request->query('search', ''));
    $selectedCategory = $normalize($request->query('category', ''));
    $selectedTopic = $normalize($request->query('topic', ''));
    $selectedStatus = $normalize($request->query('status', ''));
    $selectedAcademicYear = $normalize($request->query('academic_year', ''));
    $selectedSemester = $normalize($request->query('semester', 'all'));
    $selectedMonth = (int) $request->query('month', 0);

    $consultations = Consultation::with(['student', 'instructor'])
        ->orderByDesc('updated_at')
        ->orderByDesc('created_at')
        ->get()
        ->filter(function ($consultation) use (
            $normalize,
            $deriveCategoryAndTopic,
            $getAcademicYearFromDate,
            $getSemesterFromDate,
            $search,
            $selectedCategory,
            $selectedTopic,
            $selectedStatus,
            $selectedAcademicYear,
            $selectedSemester,
            $selectedMonth
        ) {
            [$rowCategory, $rowTopic] = $deriveCategoryAndTopic($consultation);

            $searchHaystack = $normalize(implode(' ', [
                $consultation->student?->name ?? '',
                $consultation->student?->student_id ?? '',
                $consultation->instructor?->name ?? '',
                $consultation->consultation_date ?? '',
                $consultation->consultation_mode ?? '',
                $consultation->type_label ?? $consultation->consultation_type ?? '',
                $consultation->status ?? '',
                $consultation->summary_text ?? '',
                $consultation->transcript_text ?? '',
            ]));

            $rowDate = (string) ($consultation->consultation_date ?? '');
            $rowStatus = $normalize($consultation->status ?? '');
            $rowAcademicYear = $normalize($getAcademicYearFromDate($rowDate));
            $rowSemester = $getSemesterFromDate($rowDate);

            $rowMonth = null;
            if ($rowDate !== '') {
                try {
                    $rowMonth = (int) \Illuminate\Support\Carbon::parse($rowDate, 'Asia/Manila')->format('n');
                } catch (\Throwable $e) {
                    $rowMonth = null;
                }
            }

            return (! $search || str_contains($searchHaystack, $search))
                && (! $selectedCategory || $rowCategory === $selectedCategory)
                && (! $selectedTopic || $rowTopic === $selectedTopic)
                && (! $selectedStatus || $rowStatus === $selectedStatus)
                && (! $selectedAcademicYear || ($rowAcademicYear !== '' && str_contains($rowAcademicYear, $selectedAcademicYear)))
                && ($selectedSemester === 'all' || $rowSemester === $selectedSemester)
                && (! $selectedMonth || $rowMonth === $selectedMonth);
        })
        ->values()
        ->map(function ($consultation, $index) use ($formatManilaRange) {
            $durationLabel = '--';

            try {
                if ($consultation->duration_minutes !== null && $consultation->duration_minutes !== '') {
                    $durationLabel = (int) $consultation->duration_minutes . ' min';
                } elseif ($consultation->consultation_time && $consultation->consultation_end_time) {
                    $durationLabel = \Illuminate\Support\Carbon::parse($consultation->consultation_end_time)
                        ->diffInMinutes(\Illuminate\Support\Carbon::parse($consultation->consultation_time)) . ' min';
                } elseif ($consultation->consultation_time) {
                    $durationLabel = '60 min';
                }
            } catch (\Throwable $e) {
                $durationLabel = '--';
            }

            return [
                'code' => 'C' . str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT),
                'student' => (string) ($consultation->student?->name ?? 'Student'),
                'student_id' => (string) ($consultation->student?->student_id ?? '--'),
                'instructor' => (string) ($consultation->instructor?->name ?? 'Instructor'),
                'date' => $consultation->consultation_date
                    ? \Illuminate\Support\Carbon::parse($consultation->consultation_date, 'Asia/Manila')->format('F d, Y')
                    : '--',
                'time' => $formatManilaRange($consultation->consultation_time, $consultation->consultation_end_time),
                'duration' => $durationLabel,
                'category' => (string) ($consultation->consultation_category ?? '--'),
                'topic' => (string) ($consultation->consultation_topic ?? '--'),
                'type' => (string) ($consultation->type_label ?? $consultation->consultation_type ?? 'Consultation'),
                'mode' => (string) ($consultation->consultation_mode ?? '--'),
                'status' => ucfirst(str_replace('_', ' ', (string) ($consultation->status ?? 'pending'))),
                'summary' => (string) ($consultation->summary_text ?? ''),
                'action_taken' => (string) ($consultation->transcript_text ?? ''),
            ];
        });

    return response()->view('admin.consultations-export-pdf', [
        'consultations' => $consultations,
        'exportedAt' => now('Asia/Manila'),
        'filters' => [
            'search' => (string) $request->query('search', ''),
            'category' => (string) $request->query('category', ''),
            'topic' => (string) $request->query('topic', ''),
            'status' => (string) $request->query('status', ''),
            'academic_year' => (string) $request->query('academic_year', ''),
            'semester' => (string) $request->query('semester', 'all'),
            'month' => (string) $request->query('month', ''),
        ],
        'viewer' => $user,
    ]);
})->name('admin.consultations.export-pdf')->middleware('auth');

Route::post('/instructor/consultations/{consultation}/delete', function (Consultation $consultation) {
    if ((int) $consultation->instructor_id !== (int) auth()->id()) {
        abort(403);
    }

    $consultation->delete();

    return back()->with('success', 'Consultation deleted.');
})->name('instructor.consultations.delete')->middleware('auth');

Route::get('/notifications', function () {
    $user = auth()->user();
    if (! $user) {
        abort(403);
    }

    $notifications = UserNotification::where('user_id', $user->id)
        ->orderByDesc('created_at')
        ->paginate(10)
        ->withQueryString();

    return view('notifications.index', compact('notifications'));
})->name('notifications.index')->middleware('auth');

Route::post('/notifications/mark-all-read', function () {
    $user = auth()->user();
    if (! $user) {
        abort(403);
    }

    UserNotification::where('user_id', $user->id)
        ->where('is_read', false)
        ->update(['is_read' => true]);

    if (request()->expectsJson() || request()->ajax()) {
        $notifications = UserNotification::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'success' => true,
            'unreadNotifications' => 0,
            'notifications' => $notifications
                ->take(20)
                ->map(function ($notification) {
                    return [
                        'id' => $notification->id,
                        'title' => (string) $notification->title,
                        'message' => (string) $notification->message,
                        'is_read' => (bool) $notification->is_read,
                        'created_at' => optional($notification->created_at)?->toIso8601String(),
                        'created_at_human' => $notification->created_at?->diffForHumans(),
                    ];
                })
                ->values(),
            'latestUnreadNotification' => null,
        ]);
    }

    return back();
})->name('notifications.markAllRead')->middleware('auth');

Route::post('/notifications/{notification}/read', function (UserNotification $notification) {
    $user = auth()->user();
    if (! $user || (int) $notification->user_id !== (int) $user->id) {
        abort(403);
    }

    $notification->update(['is_read' => true]);
    return back()->with('success', 'Notification marked as read.');
})->name('notifications.read')->middleware('auth');

/**
 * API endpoint to fetch new/pending consultations for instructors
 * Used for real-time polling on the instructor dashboard
 */
Route::get('/api/instructor/consultations-summary', function () {
    $user = auth()->user();
    if (! $user || $user->user_type !== 'instructor') {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    return response()->json(buildInstructorConsultationSummaryPayload($user));
})->name('api.instructor.consultations-summary')->middleware('auth');

Route::get('/api/instructor/consultations-stream', function () {
    $user = auth()->user();
    if (! $user || $user->user_type !== 'instructor') {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    return response()->stream(function () use ($user) {
        ignore_user_abort(true);

        if (function_exists('session_write_close') && session_status() === PHP_SESSION_ACTIVE) {
            @session_write_close();
        }

        @ini_set('output_buffering', 'off');
        @ini_set('zlib.output_compression', '0');

        $sendEvent = static function (string $event, array $payload): void {
            echo "event: {$event}\n";
            echo 'data: ' . json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n\n";

            if (ob_get_level() > 0) {
                @ob_flush();
            }
            flush();
        };

        echo "retry: 2000\n\n";
        if (ob_get_level() > 0) {
            @ob_flush();
        }
        flush();

        $lastHash = null;
        $startedAt = time();

        while (! connection_aborted() && (time() - $startedAt) < 60) {
            $freshUser = User::find($user->id);

            if (! $freshUser || $freshUser->user_type !== 'instructor' || ! $freshUser->hasActiveAccount()) {
                $sendEvent('access-denied', [
                    'message' => 'Access denied.',
                    'redirect' => route('login'),
                ]);
                break;
            }

            $payload = buildInstructorConsultationSummaryPayload($freshUser);
            $hash = sha1(json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

            if ($hash !== $lastHash) {
                $sendEvent('consultations', $payload);
                $lastHash = $hash;
            } else {
                echo ": keep-alive\n\n";
                if (ob_get_level() > 0) {
                    @ob_flush();
                }
                flush();
            }

            sleep(1);
        }
    }, 200, [
        'Content-Type' => 'text/event-stream',
        'Cache-Control' => 'no-cache, no-store, must-revalidate',
        'Connection' => 'keep-alive',
        'X-Accel-Buffering' => 'no',
    ]);
})->name('api.instructor.consultations-stream')->middleware('auth');

Route::get('/api/instructor/consultations-live', function (Request $request) {
    $user = auth()->user();
    if (! $user || $user->user_type !== 'instructor') {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    if (function_exists('session_write_close') && session_status() === PHP_SESSION_ACTIVE) {
        @session_write_close();
    }

    $since = (string) $request->query('since', '');
    $startedAt = time();
    $timeoutSeconds = 25;

    do {
        $freshUser = User::find($user->id);

        if (! $freshUser || $freshUser->user_type !== 'instructor' || ! $freshUser->hasActiveAccount()) {
            return response()->json([
                'error' => 'Access denied.',
                'redirect' => route('login'),
            ], 423);
        }

        $payload = buildInstructorConsultationSummaryPayload($freshUser);
        $hash = sha1(json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        if ($since === '' || ! hash_equals($since, $hash)) {
            return response()->json([
                'changed' => true,
                'hash' => $hash,
                'payload' => $payload,
            ]);
        }

        sleep(1);
    } while ((time() - $startedAt) < $timeoutSeconds);

    return response()->json([
        'changed' => false,
        'hash' => $since,
    ]);
})->name('api.instructor.consultations-live')->middleware('auth');

// API endpoint for student to poll consultation status updates
Route::get('/api/student/consultations-summary', function () {
    $user = auth()->user();
    if (! $user || ! in_array($user->user_type, ['student', 'admin'], true)) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    $consultations = Consultation::with('instructor')
        ->whereHas('student', function ($q) use ($user) {
            $q->where('id', $user->id);
        })
        ->orderByRaw("
            CASE
                WHEN status = 'pending' THEN 0
                WHEN status = 'approved' THEN 1
                WHEN status = 'in_progress' THEN 2
                WHEN status = 'completed' THEN 3
                WHEN status = 'incompleted' THEN 4
                WHEN status = 'declined' THEN 5
                ELSE 6
            END
        ")
        ->orderByDesc('consultation_date')
        ->orderByDesc('consultation_time')
        ->get();

    $notifications = UserNotification::where('user_id', $user->id)
        ->orderByDesc('created_at')
        ->get();
    $latestUnreadNotification = $notifications->firstWhere('is_read', false);
    $activeInstructorIds = User::where('user_type', 'instructor')
        ->where(function ($query) {
            $query->whereNull('account_status')
                ->orWhere('account_status', 'active');
        })
        ->pluck('id')
        ->map(fn ($id) => (int) $id)
        ->values();

    $formatManilaTime = function (?string $time): string {
        if (! $time) {
            return '--';
        }
        $value = strlen($time) === 5 ? $time . ':00' : $time;
        return Carbon::createFromFormat('H:i:s', $value, 'Asia/Manila')
            ->setTimezone('Asia/Manila')
            ->format('g:i A');
    };

    $formatManilaRange = function (?string $start, ?string $end) use ($formatManilaTime) {
        if (! $start && ! $end) {
            return '--';
        }
        if (! $end && $start) {
            $startValue = strlen($start) === 5 ? $start . ':00' : $start;
            $endValue = Carbon::createFromFormat('H:i:s', $startValue, 'Asia/Manila')
                ->copy()
                ->addHour()
                ->format('H:i:s');
            return $formatManilaTime($start) . ' to ' . $formatManilaTime($endValue);
        }
        return $formatManilaTime($start) . ' to ' . $formatManilaTime($end);
    };

    $formatRelativeDay = function (?string $date): string {
        if (! $date) {
            return 'Unknown day';
        }
        try {
            $dateObj = \Illuminate\Support\Carbon::parse($date, 'Asia/Manila');
        } catch (\Throwable $e) {
            return 'Unknown day';
        }
        $today = \Illuminate\Support\Carbon::now('Asia/Manila')->startOfDay();
        $diffDays = $dateObj->copy()->startOfDay()->diffInDays($today, false);
        if ($diffDays === 0) {
            return 'Today';
        }
        if ($diffDays === -1) {
            return 'Tomorrow';
        }
        if ($diffDays === 1) {
            return 'Yesterday';
        }
        return $dateObj->format('M d');
    };

    $historyConsultations = $consultations
        ->filter(function ($consultation) {
            return strtolower((string) ($consultation->status ?? '')) === 'completed';
        })
        ->values()
        ->map(function ($consultation) use ($formatManilaRange) {
            return [
                'id' => (int) $consultation->id,
                'instructor' => (string) ($consultation->instructor?->name ?? 'Instructor'),
                'date' => (string) ($consultation->consultation_date ?? '--'),
                'time' => $formatManilaRange($consultation->consultation_time, $consultation->consultation_end_time),
                'type' => (string) ($consultation->type_label ?? 'Consultation'),
                'mode' => (string) ($consultation->consultation_mode ?? '--'),
                'duration' => $consultation->duration_minutes !== null
                    ? ((int) $consultation->duration_minutes . ' min')
                    : '--',
                'summary' => (string) ($consultation->summary_text ?? ''),
                'transcript' => (string) ($consultation->transcript_text ?? ''),
                'category' => (string) ($consultation->consultation_category ?? ''),
                'topic' => (string) ($consultation->consultation_topic ?? $consultation->consultation_type ?? ''),
            ];
        });

    return response()->json([
        'consultations' => $consultations->map(function ($c) use ($formatManilaRange) {
            return [
                'id' => $c->id,
                'instructor_name' => $c->instructor?->name ?? 'Instructor',
                'status' => strtolower((string) $c->status),
                'consultation_date' => $c->consultation_date,
                'consultation_time' => substr((string) $c->consultation_time, 0, 5),
                'time_range' => $formatManilaRange($c->consultation_time, $c->consultation_end_time),
                'consultation_mode' => $c->consultation_mode ?? '',
                'type_label' => $c->type_label ?? '',
                'consultation_category' => $c->consultation_category ?? '',
                'consultation_topic' => $c->consultation_topic ?? '',
                'duration_minutes' => $c->duration_minutes,
                'summary_text' => $c->summary_text ?? '',
                'transcript_text' => $c->transcript_text ?? '',
            ];
        }),
        'activeInstructorIds' => $activeInstructorIds,
        'historyConsultations' => $historyConsultations,
        'unreadNotifications' => $notifications->where('is_read', false)->count(),
        'notifications' => $notifications
            ->take(20)
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'title' => (string) $notification->title,
                    'message' => (string) $notification->message,
                    'is_read' => (bool) $notification->is_read,
                    'created_at' => optional($notification->created_at)?->toIso8601String(),
                    'created_at_human' => $notification->created_at?->diffForHumans(),
                ];
            })
            ->values(),
        'latestUnreadNotification' => $latestUnreadNotification
            ? [
                'id' => $latestUnreadNotification->id,
                'title' => (string) $latestUnreadNotification->title,
                'message' => (string) $latestUnreadNotification->message,
                'created_at' => optional($latestUnreadNotification->created_at)?->toIso8601String(),
            ]
            : null,
        'recentConsultations' => $consultations
            ->sortByDesc(function ($consultation) {
                return sprintf(
                    '%s %s',
                    (string) ($consultation->consultation_date ?? '0000-00-00'),
                    (string) ($consultation->consultation_time ?? '00:00:00')
                );
            })
            ->take(4)
            ->values()
            ->map(function ($consultation) use ($formatRelativeDay, $formatManilaRange) {
                return [
                    'title' => (string) ($consultation->type_label ?: 'Consultation Session'),
                    'status' => strtolower((string) ($consultation->status ?? 'pending')),
                    'instructor' => (string) ($consultation->instructor?->name ?? 'Instructor'),
                    'date_label' => $formatRelativeDay($consultation->consultation_date),
                    'time_label' => strtolower($formatManilaRange($consultation->consultation_time, $consultation->consultation_end_time)),
                ];
            }),
    ]);
})->name('api.student.consultations-summary')->middleware('auth');

Route::get('/api/admin/consultations-summary', function () {
    $user = auth()->user();
    if (! $user || $user->user_type !== 'admin') {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    $consultations = Consultation::with(['student', 'instructor'])
        ->orderByDesc('updated_at')
        ->orderByDesc('created_at')
        ->get();
    $students = User::where('user_type', 'student')
        ->orderBy('name')
        ->get();
    $instructors = User::where('user_type', 'instructor')
        ->orderBy('name')
        ->get();
    $onlineStudentIds = \App\Services\UserSessionService::getOnlineUserIds('student');
    $onlineInstructorIds = \App\Services\UserSessionService::getOnlineUserIds('instructor');
    $studentActiveMinutes = [];
    foreach ($students as $student) {
        $lastActiveMinutes = \App\Services\UserSessionService::getLastActiveMinutes($student->id);
        if ($lastActiveMinutes !== null) {
            $studentActiveMinutes[$student->id] = ['last_active_minutes' => $lastActiveMinutes];
        }
    }
    $instructorActiveMinutes = [];
    foreach ($instructors as $instructor) {
        $lastActiveMinutes = \App\Services\UserSessionService::getLastActiveMinutes($instructor->id);
        if ($lastActiveMinutes !== null) {
            $instructorActiveMinutes[$instructor->id] = ['last_active_minutes' => $lastActiveMinutes];
        }
    }

    $notifications = UserNotification::where('user_id', $user->id)
        ->orderByDesc('created_at')
        ->get();

    $latestUnreadNotification = $notifications->firstWhere('is_read', false);

    $formatManilaTimeMeridiem = function (?string $time): string {
        if (! $time) {
            return '--';
        }
        $value = strlen($time) === 5 ? $time . ':00' : $time;
        return \Illuminate\Support\Carbon::createFromFormat('H:i:s', $value, 'Asia/Manila')
            ->setTimezone('Asia/Manila')
            ->format('g:i a');
    };

    $formatManilaRangeDash = function (?string $start, ?string $end) use ($formatManilaTimeMeridiem): string {
        if (! $start && ! $end) {
            return '--';
        }
        if (! $end && $start) {
            $startValue = strlen($start) === 5 ? $start . ':00' : $start;
            $endValue = \Illuminate\Support\Carbon::createFromFormat('H:i:s', $startValue, 'Asia/Manila')
                ->copy()
                ->addHour()
                ->format('H:i:s');
            return $formatManilaTimeMeridiem($start) . ' to ' . $formatManilaTimeMeridiem($endValue);
        }
        return $formatManilaTimeMeridiem($start) . ' to ' . $formatManilaTimeMeridiem($end);
    };

    $formatRelativeDay = function (?string $date): string {
        if (! $date) {
            return 'Unknown day';
        }
        try {
            $dateObj = \Illuminate\Support\Carbon::parse($date, 'Asia/Manila');
        } catch (\Throwable $e) {
            return 'Unknown day';
        }
        $today = \Illuminate\Support\Carbon::now('Asia/Manila')->startOfDay();
        $diffDays = $dateObj->copy()->startOfDay()->diffInDays($today, false);
        if ($diffDays === 0) {
            return 'Today';
        }
        if ($diffDays === -1) {
            return 'Tomorrow';
        }
        if ($diffDays === 1) {
            return 'Yesterday';
        }
        return $dateObj->format('M d');
    };

    $studentRows = $students->map(function ($student) use ($consultations, $onlineStudentIds, $studentActiveMinutes) {
        $consultationCount = $consultations->where('student_id', $student->id)->count();
        return [
            'id' => $student->id,
            'name' => (string) ($student->name ?? 'Student'),
            'email' => (string) ($student->email ?? ''),
            'student_id' => (string) ($student->student_id ?? '--'),
            'joined' => $student->created_at?->format('Y-m-d') ?? '--',
            'consultations' => $consultationCount,
            'status' => $student->normalizedAccountStatus(),
            'is_online' => in_array($student->id, (array) $onlineStudentIds, true) || \App\Services\UserSessionService::isUserOnline($student->id),
            'last_active_minutes' => $studentActiveMinutes[$student->id]['last_active_minutes'] ?? \App\Services\UserSessionService::getLastActiveMinutes($student->id),
        ];
    })->values();

    $instructorRows = $instructors->map(function ($instructor) use ($consultations, $onlineInstructorIds, $instructorActiveMinutes) {
        $consultationCount = $consultations->where('instructor_id', $instructor->id)->count();
        return [
            'id' => $instructor->id,
            'name' => (string) ($instructor->name ?? 'Instructor'),
            'email' => (string) ($instructor->email ?? ''),
            'joined' => $instructor->created_at?->format('Y-m-d') ?? '--',
            'consultations' => $consultationCount,
            'status' => $instructor->normalizedAccountStatus(),
            'is_online' => in_array($instructor->id, (array) $onlineInstructorIds, true) || \App\Services\UserSessionService::isUserOnline($instructor->id),
            'last_active_minutes' => $instructorActiveMinutes[$instructor->id]['last_active_minutes'] ?? \App\Services\UserSessionService::getLastActiveMinutes($instructor->id),
        ];
    })->values();

    return response()->json([
        'stats' => [
            'total_students' => $consultations->pluck('student_id')->filter()->unique()->count(),
            'total_instructors' => $consultations->pluck('instructor_id')->filter()->unique()->count(),
            'total_consultations' => $consultations->count(),
            'completed_consultations' => $consultations->where('status', 'completed')->count(),
        ],
        'studentRows' => $studentRows,
        'instructorRows' => $instructorRows,
        'consultations' => $consultations->values()->map(function ($consultation, $index) use ($formatManilaRangeDash) {
            $startRaw = (string) ($consultation->consultation_time ?? '');
            $endRaw = (string) ($consultation->consultation_end_time ?? '');
            $durationLabel = '--';

            try {
                if ($consultation->duration_minutes !== null && $consultation->duration_minutes !== '') {
                    $durationLabel = (int) $consultation->duration_minutes . ' min';
                } elseif (trim($startRaw) !== '' && trim($endRaw) !== '') {
                    $durationMinutes = \Carbon\Carbon::parse($endRaw)->diffInMinutes(\Carbon\Carbon::parse($startRaw));
                    $durationLabel = $durationMinutes . ' min';
                } elseif (trim($startRaw) !== '') {
                    $durationLabel = '60 min';
                }
            } catch (\Throwable $e) {
                $durationLabel = '--';
            }

            return [
                'id' => $consultation->id,
                'code' => 'C' . str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT),
                'student' => (string) ($consultation->student?->name ?? 'Student'),
                'student_id' => (string) ($consultation->student?->student_id ?? '--'),
                'instructor' => (string) ($consultation->instructor?->name ?? 'Instructor'),
                'date' => (string) ($consultation->consultation_date ?? '--'),
                'time_range' => $formatManilaRangeDash($consultation->consultation_time, $consultation->consultation_end_time),
                'duration' => $durationLabel,
                'type' => (string) ($consultation->type_label ?? ($consultation->consultation_type ?? 'Consultation')),
                'category' => (string) ($consultation->consultation_category ?? ''),
                'topic' => (string) ($consultation->consultation_topic ?? ($consultation->consultation_type ?? '')),
                'mode' => (string) ($consultation->consultation_mode ?? '--'),
                'status' => strtolower((string) ($consultation->status ?? 'pending')),
                'summary' => (string) ($consultation->summary_text ?? ''),
                'action_taken' => (string) ($consultation->transcript_text ?? ''),
            ];
        }),
        'unreadNotifications' => $notifications->where('is_read', false)->count(),
        'notifications' => $notifications
            ->take(20)
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'title' => (string) $notification->title,
                    'message' => (string) $notification->message,
                    'is_read' => (bool) $notification->is_read,
                    'created_at' => optional($notification->created_at)?->toIso8601String(),
                    'created_at_human' => $notification->created_at?->diffForHumans(),
                ];
            })
            ->values(),
        'latestUnreadNotification' => $latestUnreadNotification
            ? [
                'id' => $latestUnreadNotification->id,
                'title' => (string) $latestUnreadNotification->title,
                'message' => (string) $latestUnreadNotification->message,
                    'created_at' => optional($latestUnreadNotification->created_at)?->toIso8601String(),
            ]
            : null,
        'recentConsultations' => $consultations
            ->sortByDesc(function ($consultation) {
                return $consultation->updated_at?->timestamp
                    ?? $consultation->created_at?->timestamp
                    ?? 0;
            })
            ->take(4)
            ->values()
            ->map(function ($consultation) use ($formatRelativeDay, $formatManilaRangeDash) {
                return [
                    'title' => (string) ($consultation->type_label ?: 'Consultation Session'),
                    'status' => strtolower((string) ($consultation->status ?? 'pending')),
                    'student' => (string) ($consultation->student?->name ?? 'Student'),
                    'instructor' => (string) ($consultation->instructor?->name ?? 'Instructor'),
                    'date_label' => $formatRelativeDay($consultation->consultation_date),
                    'time_label' => $formatManilaRangeDash($consultation->consultation_time, $consultation->consultation_end_time),
                ];
            }),
    ]);
})->name('api.admin.consultations-summary')->middleware('auth');

// Email Testing Routes (only available in local/testing environment)
if (app()->environment('local', 'testing')) {
    Route::prefix('email-test')->middleware('auth')->group(function () {
        Route::get('/status', [App\Http\Controllers\EmailTestController::class, 'status'])->name('email.test.status');
        Route::get('/password-reset', [App\Http\Controllers\EmailTestController::class, 'testPasswordReset'])->name('email.test.password-reset');
        Route::get('/consultation-request', [App\Http\Controllers\EmailTestController::class, 'testConsultationRequest'])->name('email.test.consultation-request');
        Route::get('/consultation-approved', [App\Http\Controllers\EmailTestController::class, 'testConsultationStatusUpdate'])->where('status', 'approved')->name('email.test.consultation-approved');
        Route::get('/consultation-declined', [App\Http\Controllers\EmailTestController::class, 'testConsultationStatusUpdate'])->where('status', 'declined')->name('email.test.consultation-declined');
        Route::get('/instructor-calling', [App\Http\Controllers\EmailTestController::class, 'testInstructorCalling'])->name('email.test.instructor-calling');
        Route::get('/student-cancellation', [App\Http\Controllers\EmailTestController::class, 'testStudentCancellation'])->name('email.test.student-cancellation');
        Route::get('/admin-action', [App\Http\Controllers\EmailTestController::class, 'testAdminAction'])->name('email.test.admin-action');
    });
}

require __DIR__.'/auth.php';

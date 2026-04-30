@extends('layouts.app')

@section('title')

@section('content')
@php
    $consultations = collect($consultations ?? []);
    $students = collect($students ?? []);
    $instructors = collect($instructors ?? []);
    $onlineStudentIds = $onlineStudentIds ?? [];
    $onlineInstructorIds = $onlineInstructorIds ?? [];
    $studentActiveMinutes = $studentActiveMinutes ?? [];
    $instructorActiveMinutes = $instructorActiveMinutes ?? [];

    $notifications = collect($notifications ?? [])
        ->map(function ($notification) {
            if (is_array($notification)) {
                return [
                    'id' => $notification['id'] ?? null,
                    'title' => $notification['title'] ?? 'Notification',
                    'message' => $notification['message'] ?? '',
                    'timestamp' => $notification['timestamp'] ?? 'Just now',
                    'read' => (bool) ($notification['read'] ?? ($notification['is_read'] ?? false)),
                ];
            }

            return [
                'id' => $notification->id ?? null,
                'title' => $notification->title ?? 'Notification',
                'message' => $notification->message ?? '',
                'timestamp' => $notification->created_at?->diffForHumans() ?? 'Just now',
                'read' => (bool) ($notification->is_read ?? false),
            ];
        })
        ->values();

    $unreadCount = $notifications->where('read', false)->count();
    $authUser = auth()->user();
    $userName = $authUser?->name ?? 'Admin';
    $rawName = trim((string) ($authUser?->name ?? ''));
    $userInitial = '';
    if ($rawName !== '') {
        $firstChar = function_exists('mb_substr') ? mb_substr($rawName, 0, 1) : substr($rawName, 0, 1);
        $userInitial = function_exists('mb_strtoupper') ? mb_strtoupper($firstChar) : strtoupper($firstChar);
    }
    if ($userInitial === '') {
        $userInitial = 'U';
    }

    $totalStudents = $consultations->pluck('student_id')->filter()->unique()->count();
    $totalInstructors = $consultations->pluck('instructor_id')->filter()->unique()->count();
    $totalConsultations = $consultations->count();
    $completedSessions = $consultations->where('status', 'completed')->count();
    $pendingConsultations = $consultations->where('status', 'pending')->count();

    $recentConsultations = $consultations
        ->sortByDesc(function ($consultation) {
            return $consultation->updated_at?->timestamp
                ?? $consultation->created_at?->timestamp
                ?? 0;
        })
        ->take(4)
        ->values();
    $formatManilaTimeMeridiem = function (?string $time): string {
        if (! $time) {
            return '--';
        }
        $value = strlen($time) === 5 ? $time . ':00' : $time;
        return \Illuminate\Support\Carbon::createFromFormat('H:i:s', $value, 'Asia/Manila')
            ->setTimezone('Asia/Manila')
            ->format('h:i A');
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
            return $formatManilaTimeMeridiem($start) . ' - ' . $formatManilaTimeMeridiem($endValue);
        }
        return $formatManilaTimeMeridiem($start) . ' - ' . $formatManilaTimeMeridiem($end);
    };
    $parseManilaDate = function (?string $date): ?\Illuminate\Support\Carbon {
        if (! $date) {
            return null;
        }
        try {
            return \Illuminate\Support\Carbon::parse($date, 'Asia/Manila');
        } catch (\Exception $e) {
            return null;
        }
    };
    $formatRelativeDay = function (?string $date) use ($parseManilaDate): string {
        $dateObj = $parseManilaDate($date);
        if (! $dateObj) {
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
    $studentSemesterFromDate = function (?string $date) use ($parseManilaDate): ?string {
        $dateObj = $parseManilaDate($date);
        if (! $dateObj) {
            return null;
        }

        $month = (int) $dateObj->format('n');

        if ($month >= 1 && $month <= 5) {
            return 'first';
        }

        if ($month >= 8 && $month <= 12) {
            return 'second';
        }

        return null;
    };
    $studentAcademicYearFromDate = function (?string $date) use ($parseManilaDate): ?string {
        $dateObj = $parseManilaDate($date);
        if (! $dateObj) {
            return null;
        }

        $year = (int) $dateObj->format('Y');
        $month = (int) $dateObj->format('n');

        if ($month >= 1 && $month <= 5) {
            return ($year - 1) . '-' . $year;
        }

        if ($month >= 8) {
            return $year . '-' . ($year + 1);
        }

        return null;
    };
    $studentRosterRows = \App\Models\StudentRegistrationRoster::query()
        ->select(['student_id', 'academic_year', 'semester'])
        ->orderByDesc('academic_year')
        ->orderBy('semester')
        ->get();

    $studentRosterPeriodsByStudent = $studentRosterRows
        ->groupBy('student_id')
        ->map(function ($rows) {
            return $rows
                ->map(function ($row) {
                    $academicYear = trim((string) ($row->academic_year ?? ''));
                    $semester = trim((string) ($row->semester ?? ''));

                    return $academicYear !== '' && $semester !== ''
                        ? $academicYear . ':' . $semester
                        : null;
                })
                ->filter()
                ->unique()
                ->values();
        });

    $studentAcademicYearOptions = $studentRosterRows
        ->pluck('academic_year')
        ->filter()
        ->unique()
        ->sortDesc()
        ->values();
    $studentRows = $students->map(function ($student) use ($consultations, $studentRosterPeriodsByStudent) {
        $studentConsultations = $consultations->where('student_id', $student->id);
        $consultationCount = $studentConsultations->count();
        $status = $student->normalizedAccountStatus();
        $yearLevelValue = \App\Models\User::normalizeYearLevel($student->year_level ?? $student->yearlevel);
        $periodKeys = collect($studentRosterPeriodsByStudent->get((string) $student->student_id, []))
            ->values();
        $academicYears = $periodKeys
            ->map(fn ($key) => explode(':', $key)[0] ?? null)
            ->filter()
            ->unique()
            ->values();
        $semesters = $periodKeys
            ->map(fn ($key) => explode(':', $key)[1] ?? null)
            ->filter()
            ->unique()
            ->values();

        return [
            'id' => $student->id,
            'name' => $student->name ?? 'Student',
            'email' => $student->email ?? '',
            'student_id' => $student->student_id ?? '--',
            'year_level' => $yearLevelValue,
            'year_level_label' => \App\Models\User::yearLevelLabel($yearLevelValue),
            'academic_years' => $academicYears->all(),
            'semesters' => $semesters->all(),
            'period_keys' => $periodKeys->all(),
            'joined' => $student->created_at?->format('Y-m-d') ?? '--',
            'consultations' => $consultationCount,
            'status' => $status,
        ];
    });

    $instructorRows = $instructors->map(function ($instructor) use ($consultations) {
        $instructorConsultations = $consultations->where('instructor_id', $instructor->id);
        $consultationCount = $instructorConsultations->count();
        $status = $instructor->normalizedAccountStatus();

        return [
            'id' => $instructor->id,
            'name' => $instructor->name ?? 'Instructor',
            'email' => $instructor->email ?? '',
            'student_id' => $instructor->student_id ?? '--',
            'joined' => $instructor->created_at?->format('Y-m-d') ?? '--',
            'consultations' => $consultationCount,
            'status' => $status,
        ];
    });

    $consultationRows = $consultations->values()->map(function ($consultation, $index) {
        $modeValue = strtolower((string) ($consultation->consultation_mode ?? ''));
        $statusValue = strtolower((string) ($consultation->status ?? ''));
        $isOnline = str_contains($modeValue, 'audio') || str_contains($modeValue, 'video') || str_contains($modeValue, 'call');
        $consultationDateValue = (string) ($consultation->consultation_date ?? '--');
        $formattedDateLong = $consultationDateValue !== '--'
            ? \Carbon\Carbon::parse($consultationDateValue)->format('F j, Y')
            : '';
        $formattedDateNoComma = $consultationDateValue !== '--'
            ? \Carbon\Carbon::parse($consultationDateValue)->format('F j Y')
            : '';
        $formattedDateShort = $consultationDateValue !== '--'
            ? \Carbon\Carbon::parse($consultationDateValue)->format('M j, Y')
            : '';
        $formattedDateIso = $consultationDateValue !== '--'
            ? \Carbon\Carbon::parse($consultationDateValue)->format('Y-m-d')
            : '';
        $formattedDateSlash = $consultationDateValue !== '--'
            ? \Carbon\Carbon::parse($consultationDateValue)->format('m/d/Y')
            : '';
        $priorityValue = trim((string) ($consultation->consultation_priority ?? ''));
        $priorityFromType = '';
        if (preg_match('/\((urgent|normal|low)\)/i', (string) ($consultation->type_label ?? ''), $priorityMatch)) {
            $priorityFromType = $priorityMatch[1];
        }
        $searchPriority = $priorityValue !== '' ? $priorityValue : $priorityFromType;

        // Build a human readable time range (e.g. "8:00 am to 9:00 am").
        $startRaw = (string) ($consultation->consultation_time ?? '');
        $endRaw = (string) ($consultation->consultation_end_time ?? '');
        $timeRange = '';
        try {
            if (trim($startRaw) !== '') {
                $start = \Carbon\Carbon::parse($startRaw)->format('g:i a');
            } else {
                $start = null;
            }

            if (trim($endRaw) !== '') {
                $end = \Carbon\Carbon::parse($endRaw)->format('g:i a');
            } elseif (!empty($start)) {
                // Fallback: assume 1 hour duration if end time missing
                $end = \Carbon\Carbon::parse($startRaw)->addHour()->format('g:i a');
            } else {
                $end = null;
            }

            if ($start && $end) {
                $timeRange = $start . ' to ' . $end;
            } elseif ($start) {
                $timeRange = $start;
            }
        } catch (\Throwable $e) {
            // If parsing fails, fall back to raw substrings
            $s = substr($startRaw, 0, 5);
            $e = substr($endRaw, 0, 5);
            $timeRange = $e ? ($s . ' to ' . $e) : $s;
        }

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
            // Keep duration fallback.
        }

        return [
            'consultation_id' => $consultation->id,
            'code' => 'C' . str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT),
            'student' => $consultation->student?->name ?? 'Student',
            'student_id' => $consultation->student?->student_id ?? '--',
            'instructor' => $consultation->instructor?->name ?? 'Instructor',
            'date' => $consultationDateValue,
            'formatted_date_long' => $formattedDateLong,
            'formatted_date_no_comma' => $formattedDateNoComma,
            'formatted_date_short' => $formattedDateShort,
            'formatted_date_iso' => $formattedDateIso,
            'formatted_date_slash' => $formattedDateSlash,
            'time_range' => $timeRange,
            'duration' => $durationLabel,
            'type' => $consultation->type_label ?? ($consultation->consultation_type ?? 'Consultation'),
            'category' => (string) ($consultation->consultation_category ?? ''),
            'topic' => (string) ($consultation->consultation_topic ?? ($consultation->consultation_type ?? '')),
            'mode' => $consultation->consultation_mode ?? '--',
            'status' => $statusValue ?: 'pending',
            'priority' => $searchPriority,
            'summary' => (string) ($consultation->summary_text ?? ''),
            'action_taken' => (string) ($consultation->transcript_text ?? ''),
        ];
    });
    $instructorScheduleMap = \App\Models\InstructorAvailability::query()
        ->whereIn('instructor_id', $instructors->pluck('id'))
        ->orderBy('semester')
        ->orderBy('academic_year')
        ->orderByRaw("FIELD(available_day, 'monday','tuesday','wednesday','thursday','friday','saturday')")
        ->orderBy('start_time')
        ->get()
        ->groupBy('instructor_id')
        ->map(function ($rows) {
            return $rows
                ->groupBy(fn ($row) => strtolower((string) $row->semester) . '|' . (string) $row->academic_year)
                ->map(fn ($items) => $items->map(fn ($slot) => [
                    'day' => strtolower((string) $slot->available_day),
                    'start_time' => substr((string) $slot->start_time, 0, 5),
                    'end_time' => substr((string) $slot->end_time, 0, 5),
                ])->values());
        });

    $statisticsRows = $consultations->values()->map(function ($consultation) {
        $priorityValue = trim((string) ($consultation->consultation_priority ?? ''));
        $priorityFromType = '';
        if (preg_match('/\((urgent|normal|low)\)/i', (string) ($consultation->type_label ?? ''), $priorityMatch)) {
            $priorityFromType = strtolower($priorityMatch[1]);
        }

        return [
            'date' => (string) ($consultation->consultation_date ?? ''),
            'type' => (string) ($consultation->type_label ?? ($consultation->consultation_type ?? 'Consultation')),
            'category' => (string) ($consultation->consultation_category ?? ''),
            'topic' => (string) ($consultation->consultation_topic ?? ($consultation->consultation_type ?? '')),
            'priority' => $priorityValue !== '' ? strtolower($priorityValue) : $priorityFromType,
            'status' => strtolower((string) ($consultation->status ?? '')),
            'mode' => (string) ($consultation->consultation_mode ?? ''),
            'student' => (string) ($consultation->student?->name ?? 'Student'),
            'instructor' => (string) ($consultation->instructor?->name ?? 'Instructor'),
        ];
    })->filter(function ($row) {
        return trim((string) ($row['date'] ?? '')) !== '';
    })->values();

    $userName = auth()->user()->name ?? 'Administrator';
    $userEmail = auth()->user()->email ?? 'admin@example.com';
@endphp

@include('admin.dashboard.partials.styles')

@include('admin.dashboard.partials.layout_header')

@include('admin.dashboard.partials.overview')

@include('admin.dashboard.partials.students')

@include('admin.dashboard.partials.instructors')

@include('admin.dashboard.partials.consultations')

@include('admin.dashboard.partials.modals')

@include('admin.dashboard.partials.scripts')

@endsection

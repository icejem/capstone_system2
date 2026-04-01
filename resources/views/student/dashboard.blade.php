@extends('layouts.app')

@section('title')

@section('content')
@php
    $consultations = $consultations ?? collect();
    $formatManilaTime = function (?string $time): string {
        if (! $time) {
            return '--:--';
        }
        $value = strlen($time) === 5 ? $time . ':00' : $time;
        return \Illuminate\Support\Carbon::createFromFormat('H:i:s', $value, 'Asia/Manila')
            ->setTimezone('Asia/Manila')
            ->format('h:i A');
    };
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
    $formatManilaTimeLowerSpaced = function (?string $time): string {
        if (! $time) {
            return '--';
        }
        $value = strlen($time) === 5 ? $time . ':00' : $time;
        return strtolower(\Illuminate\Support\Carbon::createFromFormat('H:i:s', $value, 'Asia/Manila')
            ->setTimezone('Asia/Manila')
            ->format('g:i A'));
    };
    $formatManilaRangeSpaced = function (?string $start, ?string $end) use ($formatManilaTimeLowerSpaced): string {
        if (! $start && ! $end) {
            return '--';
        }
        if (! $end && $start) {
            $startValue = strlen($start) === 5 ? $start . ':00' : $start;
            $endValue = \Illuminate\Support\Carbon::createFromFormat('H:i:s', $startValue, 'Asia/Manila')
                ->copy()
                ->addHour()
                ->format('H:i:s');
            return $formatManilaTimeLowerSpaced($start) . ' to ' . $formatManilaTimeLowerSpaced($endValue);
        }
        return $formatManilaTimeLowerSpaced($start) . ' to ' . $formatManilaTimeLowerSpaced($end);
    };
    $formatManilaRangeDash = function (?string $start, ?string $end) use ($formatManilaTime): string {
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
    $isOnlineMode = function (?string $mode): bool {
        $value = strtolower((string) $mode);
        return str_contains($value, 'audio') || str_contains($value, 'video') || str_contains($value, 'call');
    };
    $isJoinWindow = function (?string $date, ?string $time): bool {
        if (! $date || ! $time) {
            return false;
        }
        $normalizedTime = strlen($time) === 5 ? $time . ':00' : $time;
        try {
            $start = \Illuminate\Support\Carbon::createFromFormat('Y-m-d H:i:s', $date . ' ' . $normalizedTime, 'Asia/Manila');
        } catch (\Exception $e) {
            return false;
        }
        $now = \Illuminate\Support\Carbon::now('Asia/Manila');
        $windowStart = $start->copy()->subMinutes(10);
        $windowEnd = $start->copy()->addMinutes(30);
        return $now->between($windowStart, $windowEnd);
    };



    $unreadCount = $notifications->where('is_read', false)->count();
    $userName = auth()->user()->name ?? 'Student';
    $userEmail = auth()->user()->email ?? 'student@example.com';
    $authUser = auth()->user();
    $rawName = trim((string) ($authUser?->name ?? ''));
    $userInitial = '';
    if ($rawName !== '') {
        $firstChar = function_exists('mb_substr') ? mb_substr($rawName, 0, 1) : substr($rawName, 0, 1);
        $userInitial = function_exists('mb_strtoupper') ? mb_strtoupper($firstChar) : strtoupper($firstChar);
    }
    if ($userInitial === '') {
        $userInitial = 'U';
    }
    $flashSuccess = session('success');
    $onlineInstructorIds = $onlineInstructorIds ?? [];
    $instructorActiveMinutes = $instructorActiveMinutes ?? [];
    $todayManila = \Illuminate\Support\Carbon::now('Asia/Manila')->toDateString();
    $nowManila = \Illuminate\Support\Carbon::now('Asia/Manila');

    // Make flash message available globally
    $successModalDisplay = $flashSuccess ? 'flex' : 'none';
    $isUpcomingStatus = function (?string $status): bool {
        $value = strtolower((string) $status);
        return in_array($value, ['pending', 'approved', 'in_progress'], true);
    };
    $isUpcomingConsultation = function ($consultation) use ($todayManila, $nowManila, $isUpcomingStatus): bool {
        $status = strtolower((string) ($consultation->status ?? ''));
        if (! $isUpcomingStatus($status)) {
            return false;
        }

        $date = (string) ($consultation->consultation_date ?? '');
        if ($date === '') {
            return false;
        }
        if ($date > $todayManila) {
            return true;
        }
        if ($date < $todayManila) {
            return false;
        }

        if ($status === 'in_progress') {
            return true;
        }

        $timeRaw = (string) ($consultation->consultation_time ?? '');
        if ($timeRaw === '') {
            return true;
        }

        try {
            $timeValue = strlen($timeRaw) === 5 ? $timeRaw . ':00' : $timeRaw;
            $startAt = \Illuminate\Support\Carbon::createFromFormat('Y-m-d H:i:s', $date . ' ' . $timeValue, 'Asia/Manila');
            return $startAt->greaterThanOrEqualTo($nowManila);
        } catch (\Throwable $e) {
            return true;
        }
    };
    $totalConsultationsCount = $consultations->count();
    $completedSessionsCount = $consultations->filter(function ($consultation) {
        return strtolower((string) ($consultation->status ?? '')) === 'completed';
    })->count();
    $pendingRequestsCount = $consultations->filter(function ($consultation) {
        return strtolower((string) ($consultation->status ?? '')) === 'pending';
    })->count();
    $upcomingTodayCount = $consultations->filter(function ($consultation) use ($todayManila, $isUpcomingConsultation) {
        return (string) ($consultation->consultation_date ?? '') === $todayManila
            && $isUpcomingConsultation($consultation);
    })->count();
    $recentConsultations = $consultations
        ->sortByDesc(function ($consultation) {
            return sprintf(
                '%s %s',
                (string) ($consultation->consultation_date ?? '0000-00-00'),
                (string) ($consultation->consultation_time ?? '00:00:00')
            );
        })
        ->take(3)
        ->values();
    $upcomingConsultations = $consultations
        ->filter(function ($consultation) use ($isUpcomingConsultation) {
            return $isUpcomingConsultation($consultation);
        })
        ->sortBy(function ($consultation) {
            return sprintf(
                '%s %s',
                (string) ($consultation->consultation_date ?? '9999-12-31'),
                (string) ($consultation->consultation_time ?? '23:59:59')
            );
        })
        ->take(3)
        ->values();
@endphp

@include('student.dashboard.partials.styles')

@include('student.dashboard.partials.content')

@include('student.dashboard.partials.scripts')
@endsection

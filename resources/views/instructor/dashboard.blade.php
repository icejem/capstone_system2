@extends('layouts.app')

@section('title')

@section('content')
@php
    $consultations = $consultations ?? collect();
    $notifications = $notifications ?? collect();
    $feedbacks = $feedbacks ?? collect();
    $feedbackStats = $feedbackStats ?? [
        'average_rating' => 0,
        'total_feedback' => 0,
        'positive_rate' => 0,
        'this_month' => 0,
    ];
    $unreadCount = $notifications->where('is_read', false)->count();
    $userName = auth()->user()->name ?? 'Instructor';
    $userEmail = auth()->user()->email ?? '';
    $formatManilaTime = function (?string $time): string {
        if (! $time) {
            return '--:--';
        }
        $value = strlen($time) === 5 ? $time . ':00' : $time;
        return \Illuminate\Support\Carbon::createFromFormat('H:i:s', $value, 'Asia/Manila')
            ->setTimezone('Asia/Manila')
            ->format('H:i');
    };
    $formatManilaTime12 = function (?string $time): string {
        if (! $time) {
            return '--';
        }
        $value = strlen($time) === 5 ? $time . ':00' : $time;
        $formatted = \Illuminate\Support\Carbon::createFromFormat('H:i:s', $value, 'Asia/Manila')
            ->setTimezone('Asia/Manila')
            ->format('g:i A');
        return strtolower(str_replace(' ', '', $formatted));
    };
    $formatManilaRange = function (?string $start, ?string $end) use ($formatManilaTime12): string {
        if (! $start && ! $end) {
            return '--';
        }
        if (! $end && $start) {
            $startValue = strlen($start) === 5 ? $start . ':00' : $start;
            $endValue = \Illuminate\Support\Carbon::createFromFormat('H:i:s', $startValue, 'Asia/Manila')
                ->copy()
                ->addHour()
                ->format('H:i:s');
            return $formatManilaTime12($start) . ' to ' . $formatManilaTime12($endValue);
        }
        return $formatManilaTime12($start) . ' to ' . $formatManilaTime12($end);
    };
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
    $onlineStudentIds = $onlineStudentIds ?? [];
    $todayManila = \Illuminate\Support\Carbon::now('Asia/Manila')->toDateString();
    $nowManila = \Illuminate\Support\Carbon::now('Asia/Manila');
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
    $recentConsultations = $consultations
        ->sortByDesc(function ($consultation) {
            return sprintf(
                '%s %s',
                (string) ($consultation->consultation_date ?? '0000-00-00'),
                (string) ($consultation->consultation_time ?? '00:00:00')
            );
        })
        ->take(4)
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
        ->take(4)
        ->values();
@endphp
@include('instructor.dashboard.partials.styles')

@include('instructor.dashboard.partials.content')

@include('instructor.dashboard.partials.scripts')
@endsection

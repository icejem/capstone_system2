<?php

namespace App\Services;

use App\Models\Consultation;
use Illuminate\Support\Carbon;

class ConsultationOverdueService
{
    /**
     * Mark consultations as "incompleted" when their scheduled (end) time has passed
     * and the session never started.
     *
     * Returns the number of rows updated.
     */
    public static function markOverdueAsIncompleted(?Carbon $now = null): int
    {
        $now = ($now ?: now('Asia/Manila'))->copy()->setTimezone('Asia/Manila');
        $timezone = 'Asia/Manila';

        $updatedCount = 0;

        Consultation::query()
            ->with(['student', 'instructor'])
            ->whereIn('status', ['pending', 'approved', 'in_progress'])
            ->whereDate('consultation_date', '<=', $now->toDateString())
            ->orderBy('id')
            ->chunkById(200, function ($consultations) use (&$updatedCount, $now, $timezone) {
                foreach ($consultations as $consultation) {
                    $status = (string) $consultation->status;
                    $mode = mb_strtolower(trim((string) $consultation->consultation_mode));
                    $isFaceToFace = str_contains($mode, 'face');
                    $hasStarted = ! is_null($consultation->started_at);

                    if ($status === 'in_progress' && $hasStarted && ! $isFaceToFace) {
                        continue;
                    }

                    $date = (string) $consultation->consultation_date;
                    $time = (string) ($consultation->consultation_end_time ?: $consultation->consultation_time);

                    if ($date === '' || $time === '') {
                        continue;
                    }

                    $normalizedTime = strlen($time) === 5 ? $time . ':00' : $time;

                    try {
                        $deadline = Carbon::createFromFormat(
                            'Y-m-d H:i:s',
                            $date . ' ' . $normalizedTime,
                            $timezone
                        );
                    } catch (\Throwable $e) {
                        continue;
                    }

                    if (! $deadline->lt($now)) {
                        continue;
                    }

                    $updated = Consultation::query()
                        ->whereKey($consultation->id)
                        ->whereIn('status', ['pending', 'approved', 'in_progress'])
                        ->update([
                            'status' => 'incompleted',
                            'ended_at' => null,
                            'duration_minutes' => null,
                            'transcript_active' => false,
                        ]);

                    if (! $updated) {
                        continue;
                    }

                    $consultation->forceFill([
                        'status' => 'incompleted',
                        'ended_at' => null,
                        'duration_minutes' => null,
                        'transcript_active' => false,
                    ]);

                    ConsultationNotificationService::sendIncompleteNotifications(
                        $consultation,
                        (int) ($consultation->call_attempts ?? 0),
                        $isFaceToFace
                            ? 'because the scheduled face-to-face consultation time passed without the session being completed.'
                            : 'because the scheduled session time ended without the consultation starting.'
                    );

                    $updatedCount += $updated;
                }
            });

        return $updatedCount;
    }
}

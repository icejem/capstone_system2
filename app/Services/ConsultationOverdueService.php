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
        $now = $now ?: now();
        $timezone = $now->getTimezone();

        $updatedCount = 0;

        Consultation::query()
            ->with(['student', 'instructor'])
            ->whereIn('status', ['pending', 'approved'])
            ->whereNull('started_at')
            ->whereDate('consultation_date', '<=', $now->toDateString())
            ->orderBy('id')
            ->chunkById(200, function ($consultations) use (&$updatedCount, $now, $timezone) {
                foreach ($consultations as $consultation) {
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
                        ->whereIn('status', ['pending', 'approved'])
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
                        'because the scheduled session time ended without the consultation starting.'
                    );

                    $updatedCount += $updated;
                }
            });

        return $updatedCount;
    }
}

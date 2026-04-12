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
            ->whereIn('status', ['pending', 'approved'])
            ->whereNull('started_at')
            ->whereDate('consultation_date', '<=', $now->toDateString())
            ->select(['id', 'consultation_date', 'consultation_time', 'consultation_end_time'])
            ->orderBy('id')
            ->chunkById(200, function ($consultations) use (&$updatedCount, $now, $timezone) {
                $overdueIds = [];

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

                    if ($deadline->lt($now)) {
                        $overdueIds[] = $consultation->id;
                    }
                }

                if (! $overdueIds) {
                    return;
                }

                $updatedCount += Consultation::query()
                    ->whereIn('id', $overdueIds)
                    ->whereIn('status', ['pending', 'approved'])
                    ->update(['status' => 'incompleted']);
            });

        return $updatedCount;
    }
}


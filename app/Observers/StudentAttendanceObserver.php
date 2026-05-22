<?php

namespace App\Observers;

use App\Models\StudentAttendance;
use App\Models\NotificationQueue;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;

/**
 * Observes student attendance changes.
 * When a student is marked 'alpha', checks cumulative count
 * and queues a WA notification if threshold is exceeded.
 *
 * Blueprint Section: 8.2 – Alpha Threshold Observer
 */
class StudentAttendanceObserver
{
    /**
     * Handle the StudentAttendance "created" event.
     */
    public function created(StudentAttendance $attendance): void
    {
        if ($attendance->status !== 'alpha') {
            return;
        }

        $this->checkAlphaThreshold($attendance);
    }

    /**
     * Handle the StudentAttendance "updated" event.
     */
    public function updated(StudentAttendance $attendance): void
    {
        // Only trigger if status was changed TO alpha
        if ($attendance->status !== 'alpha' || !$attendance->wasChanged('status')) {
            return;
        }

        $this->checkAlphaThreshold($attendance);
    }

    /**
     * Check if student has exceeded the alpha threshold and queue notification.
     */
    protected function checkAlphaThreshold(StudentAttendance $attendance): void
    {
        $mode = Setting::where('key', 'alpha_threshold_mode')->value('value') ?? 'cumulative';
        $threshold = (int) (Setting::where('key', 'alpha_threshold_count')->value('value') ?? 5);

        $query = StudentAttendance::where('student_id', $attendance->student_id)
            ->where('status', 'alpha');

        if ($mode === 'weekly') {
            $query->whereBetween('date', [
                now()->startOfWeek(),
                now()->endOfWeek(),
            ]);
        }

        $alphaCount = $query->count();

        if ($alphaCount >= $threshold) {
            $student = $attendance->student;

            // Only queue if not already notified recently (within 24h)
            $recentlyNotified = NotificationQueue::where('student_id', $student->id)
                ->where('type', 'alpha_threshold')
                ->where('created_at', '>=', now()->subHours(24))
                ->exists();

            if ($recentlyNotified) {
                return;
            }

            $template = Setting::where('key', 'wa_message_template')->value('value') ?? '';
            $message = str_replace(
                ['{nama_santri}', '{nis}', '{kelas}', '{jumlah_alpha}'],
                [
                    $student->name ?? '-',
                    $student->nis ?? '-',
                    $student->currentClass?->name ?? '-',
                    $alphaCount,
                ],
                $template
            );

            NotificationQueue::create([
                'student_id' => $student->id,
                'type' => 'alpha_threshold',
                'phone' => $student->parent_phone,
                'message' => $message,
                'status' => 'pending',
            ]);

            Log::info("[AlphaThreshold] Student {$student->name} (#{$student->id}) exceeded threshold ({$alphaCount}/{$threshold}).");
        }
    }
}

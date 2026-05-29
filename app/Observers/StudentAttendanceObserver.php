<?php

namespace App\Observers;

use App\Models\AcademicYear;
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
     *
     * Semua Setting di-fetch dalam SATU query `whereIn` dan di-cache per request
     * via static variable — sehingga batch upsert 30 santri hanya menyentuh DB
     * 1 kali untuk settings, bukan 90 kali (3 key × 30 santri).
     */
    protected function checkAlphaThreshold(StudentAttendance $attendance): void
    {
        // Satu query bulk, cache per PHP request lifecycle
        static $settings    = null;
        static $activeYearId = null;

        if ($settings === null) {
            $settings = Setting::whereIn('key', [
                'alpha_threshold_mode',
                'alpha_threshold_count',
                'wa_message_template',
            ])->pluck('value', 'key');
        }

        // Cache AcademicYear::active() per request — JANGAN panggil per-santri
        // karena itu menyebabkan query tambahan di dalam DB transaction.
        if ($activeYearId === null) {
            $activeYear   = AcademicYear::active();
            $activeYearId = $activeYear?->id ?? 0;
        }

        $mode      = $settings->get('alpha_threshold_mode', 'cumulative');
        $threshold = (int) $settings->get('alpha_threshold_count', 5);

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
            // withTrashed() agar tidak null jika santri soft-deleted
            $student = $attendance->student()->withTrashed()->first();
            if (! $student) {
                return;
            }

            // Hanya queue jika belum ada notifikasi dalam 24 jam terakhir
            $recentlyNotified = NotificationQueue::where('student_id', $student->id)
                ->where('type', 'alpha_threshold')
                ->where('created_at', '>=', now()->subHours(24))
                ->exists();

            if ($recentlyNotified) {
                return;
            }

            // Gunakan template dari cache — tidak perlu query DB lagi
            $template = $settings->get('wa_message_template', '');

            // Gunakan currentClassForYear() — aman di dalam transaction
            $className = $activeYearId > 0
                ? ($student->currentClassForYear($activeYearId)?->name ?? '-')
                : '-';

            $message  = str_replace(
                ['{nama_santri}', '{nis}', '{kelas}', '{jumlah_alpha}'],
                [
                    $student->name   ?? '-',
                    $student->nis    ?? '-',
                    $className,
                    $alphaCount,
                ],
                $template
            );

            NotificationQueue::create([
                'student_id'   => $student->id,
                'unit_id'      => $student->unit_id,
                'type'         => 'alpha_threshold',
                'phone'        => $student->parent_phone,
                'message'      => $message,
                'status'       => 'pending',
                'triggered_at' => now(),
            ]);

            Log::info("[AlphaThreshold] Student {$student->name} (#{$student->id}) exceeded threshold ({$alphaCount}/{$threshold}).");
        }
    }
}

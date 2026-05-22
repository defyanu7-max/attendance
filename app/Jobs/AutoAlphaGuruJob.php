<?php

namespace App\Jobs;

use App\Models\TeacherAttendance;
use App\Models\Schedule;
use App\Models\SchoolCalendar;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Auto-mark teachers with scheduled lessons as 'alpha' if they haven't
 * checked in by the attendance cutoff time.
 *
 * Blueprint Section: 6.2 – Auto-Alpha Guru
 */
class AutoAlphaGuruJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        //
    }

    public function handle(): void
    {
        $today = now()->toDateString();
        $dayOfWeek = now()->dayOfWeekIso;

        // Skip holidays
        $isHoliday = SchoolCalendar::whereDate('date', $today)
            ->where('is_holiday', true)
            ->exists();

        if ($isHoliday) {
            Log::info("[AutoAlphaGuruJob] Skipped: {$today} is a holiday.");
            return;
        }

        // Get today's scheduled teachers
        $schedules = Schedule::where('day', $dayOfWeek)
            ->select('teacher_id')
            ->distinct()
            ->get();

        if ($schedules->isEmpty()) {
            Log::info("[AutoAlphaGuruJob] No schedules today.");
            return;
        }

        $marked = 0;

        DB::transaction(function () use ($schedules, $today, &$marked) {
            foreach ($schedules as $schedule) {
                // Check if teacher already has attendance for today
                $exists = TeacherAttendance::where('user_id', $schedule->teacher_id)
                    ->whereDate('date', $today)
                    ->exists();

                if (!$exists) {
                    TeacherAttendance::create([
                        'user_id' => $schedule->teacher_id,
                        'date' => $today,
                        'status' => 'alpha',
                        'notes' => 'Auto-alpha oleh sistem (belum absen hingga batas waktu)',
                    ]);
                    $marked++;
                }
            }
        });

        Log::info("[AutoAlphaGuruJob] Marked {$marked} teachers as alpha for {$today}.");
    }
}

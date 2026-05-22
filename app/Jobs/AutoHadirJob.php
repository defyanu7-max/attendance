<?php

namespace App\Jobs;

use App\Models\StudentAttendance;
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
 * Auto-mark students as 'hadir' at the start of each scheduled lesson.
 * This job runs daily via the scheduler (e.g., 07:00).
 * Teachers/admins can later correct individual records if needed.
 *
 * Blueprint Section: 6.1 – Auto-Hadir Default
 */
class AutoHadirJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        //
    }

    public function handle(): void
    {
        $today = now()->toDateString();
        $dayOfWeek = now()->dayOfWeekIso; // 1=Monday .. 7=Sunday

        // Skip if today is a holiday or weekend
        $isHoliday = SchoolCalendar::whereDate('date', $today)
            ->where('is_holiday', true)
            ->exists();

        if ($isHoliday) {
            Log::info("[AutoHadirJob] Skipped: {$today} is a holiday.");
            return;
        }

        // Get today's schedules
        $schedules = Schedule::where('day', $dayOfWeek)
            ->with('class.students')
            ->get();

        if ($schedules->isEmpty()) {
            Log::info("[AutoHadirJob] No schedules found for day {$dayOfWeek}.");
            return;
        }

        $inserted = 0;

        DB::transaction(function () use ($schedules, $today, &$inserted) {
            foreach ($schedules as $schedule) {
                $students = $schedule->class->students ?? collect();

                foreach ($students as $student) {
                    // Only create if not already exists (idempotent)
                    $exists = StudentAttendance::where('student_id', $student->id)
                        ->where('schedule_id', $schedule->id)
                        ->whereDate('date', $today)
                        ->exists();

                    if (!$exists) {
                        StudentAttendance::create([
                            'student_id' => $student->id,
                            'schedule_id' => $schedule->id,
                            'date' => $today,
                            'status' => 'hadir',
                            'academic_year_id' => $schedule->academic_year_id,
                        ]);
                        $inserted++;
                    }
                }
            }
        });

        Log::info("[AutoHadirJob] Created {$inserted} attendance records for {$today}.");
    }
}

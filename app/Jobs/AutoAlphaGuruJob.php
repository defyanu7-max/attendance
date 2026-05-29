<?php

namespace App\Jobs;

use App\Models\AcademicYear;
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

        // Skip holidays (kolom yang benar: 'type', bukan 'is_holiday')
        $isHoliday = SchoolCalendar::whereDate('date', $today)
            ->where('type', 'holiday')
            ->exists();

        if ($isHoliday) {
            Log::info("[AutoAlphaGuruJob] Skipped: {$today} is a holiday.");
            return;
        }

        $activeYear = AcademicYear::active();
        if (! $activeYear) {
            Log::info("[AutoAlphaGuruJob] Skipped: no active academic year.");
            return;
        }

        // Get today's scheduled teachers (kolom yang benar: 'day_of_week', bukan 'day')
        $schedules = Schedule::where('day_of_week', $dayOfWeek)
            ->where('academic_year_id', $activeYear->id)
            ->select('teacher_id', 'unit_id')
            ->distinct()
            ->get();

        if ($schedules->isEmpty()) {
            Log::info("[AutoAlphaGuruJob] No schedules today.");
            return;
        }

        $marked = 0;

        // Bulk-fetch existing attendance agar tidak N+1 di loop
        $existingTeacherIds = TeacherAttendance::whereDate('date', $today)
            ->whereIn('user_id', $schedules->pluck('teacher_id'))
            ->pluck('user_id')
            ->toArray();

        DB::transaction(function () use ($schedules, $today, $activeYear, $existingTeacherIds, &$marked) {
            foreach ($schedules as $schedule) {
                if (in_array($schedule->teacher_id, $existingTeacherIds)) {
                    continue;
                }

                TeacherAttendance::create([
                    'unit_id'          => $schedule->unit_id,
                    'user_id'          => $schedule->teacher_id,
                    'academic_year_id' => $activeYear->id,
                    'date'             => $today,
                    'status'           => 'alpha',
                    'is_auto'          => true,
                ]);
                $marked++;
            }
        });

        Log::info("[AutoAlphaGuruJob] Marked {$marked} teachers as alpha for {$today}.");
    }
}

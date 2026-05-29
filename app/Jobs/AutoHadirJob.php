<?php

namespace App\Jobs;

use App\Models\AcademicYear;
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

        // Skip if today is a holiday (kolom yang benar: 'type', bukan 'is_holiday')
        $isHoliday = SchoolCalendar::whereDate('date', $today)
            ->where('type', 'holiday')
            ->exists();

        if ($isHoliday) {
            Log::info("[AutoHadirJob] Skipped: {$today} is a holiday.");
            return;
        }

        $activeYear = AcademicYear::active();
        if (! $activeYear) {
            Log::info("[AutoHadirJob] Skipped: no active academic year.");
            return;
        }

        // Get today's schedules (kolom yang benar: 'day_of_week', bukan 'day')
        $schedules = Schedule::where('day_of_week', $dayOfWeek)
            ->where('academic_year_id', $activeYear->id)
            ->with(['class_' => fn ($q) => $q->with(['students' => fn ($q2) => $q2->where('students.status', 'aktif')])])
            ->get();

        if ($schedules->isEmpty()) {
            Log::info("[AutoHadirJob] No schedules found for day {$dayOfWeek}.");
            return;
        }

        $inserted = 0;

        DB::transaction(function () use ($schedules, $today, $activeYear, &$inserted) {
            foreach ($schedules as $schedule) {
                $students = $schedule->class_?->students ?? collect();
                if ($students->isEmpty()) continue;

                // Bulk-fetch existing attendance untuk schedule ini hari ini
                $existingStudentIds = StudentAttendance::where('schedule_id', $schedule->id)
                    ->whereDate('date', $today)
                    ->pluck('student_id')
                    ->toArray();

                $records = [];
                foreach ($students as $student) {
                    if (in_array($student->id, $existingStudentIds)) {
                        continue;
                    }

                    $records[] = [
                        'unit_id'          => $schedule->unit_id,
                        'student_id'       => $student->id,
                        'schedule_id'      => $schedule->id,
                        'academic_year_id' => $activeYear->id,
                        'date'             => $today,
                        'status'           => 'hadir',
                        'is_merged'        => false,
                        'created_at'       => now(),
                        'updated_at'       => now(),
                    ];
                }

                if (! empty($records)) {
                    StudentAttendance::insert($records);
                    $inserted += count($records);
                }
            }
        });

        Log::info("[AutoHadirJob] Created {$inserted} attendance records for {$today}.");
    }
}

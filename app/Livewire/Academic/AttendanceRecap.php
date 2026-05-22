<?php

namespace App\Livewire\Academic;

use App\Models\AcademicYear;
use App\Models\Classes;
use App\Models\NotificationQueue;
use App\Models\Schedule;
use App\Models\SchoolCalendar;
use App\Models\Setting;
use App\Models\StudentAttendance;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Rekap Absensi')]
class AttendanceRecap extends Component
{
    // --- Public Properties (Livewire-bound) ---

    public ?int $classId = null;
    public string $viewMode = 'by_subject'; // 'by_subject' | 'by_date'
    public string $filterMonth = '';         // '' = all, 'YYYY-MM' = specific

    // --- Lifecycle ---

    public function mount(?string $class = null): void
    {
        if ($class && $class !== 'today' && is_numeric($class)) {
            $this->classId = (int) $class;
        }
    }

    // --- Actions ---

    public function toggleMode(string $mode): void
    {
        $this->viewMode = $mode;
    }

    // --- Computed Properties ---

    #[Computed]
    public function activeYear(): ?AcademicYear
    {
        return AcademicYear::active();
    }

    #[Computed]
    public function alphaThreshold(): int
    {
        return (int) Setting::getValue('alpha_threshold_count', 5);
    }

    #[Computed]
    public function classes(): Collection
    {
        $activeYear = $this->activeYear;
        if (! $activeYear) {
            return collect();
        }

        $user = Auth::user();
        $query = Classes::where('academic_year_id', $activeYear->id)
            ->orderBy('name');

        // Walikelas can only see their homeroom class
        if ($user->role === 'walikelas') {
            $query->where('homeroom_teacher_id', $user->id);
        }

        // Guru can only see classes they teach
        if ($user->role === 'guru') {
            $teacherClassIds = Schedule::where('teacher_id', $user->id)
                ->where('academic_year_id', $activeYear->id)
                ->pluck('class_id')
                ->unique();
            $query->whereIn('id', $teacherClassIds);
        }

        return $query->get();
    }

    #[Computed]
    public function selectedClass(): ?Classes
    {
        if (! $this->classId) {
            return null;
        }

        return Classes::with(['homeroomTeacher', 'unit'])->find($this->classId);
    }

    #[Computed]
    public function availableMonths(): array
    {
        $activeYear = $this->activeYear;
        if (! $activeYear) {
            return [];
        }

        $months = [];
        $start = $activeYear->start_date->copy()->startOfMonth();
        $end = $activeYear->end_date->copy()->endOfMonth();
        $current = $start->copy();

        while ($current->lte($end)) {
            $months[] = [
                'value' => $current->format('Y-m'),
                'label' => $current->translatedFormat('F Y'),
            ];
            $current->addMonth();
        }

        return $months;
    }

    /**
     * Master computed property — builds ALL recap data.
     * R-10: All data prepared here with eager loading, NO DB queries in Blade.
     */
    #[Computed]
    public function rekapData(): array
    {
        $activeYear = $this->activeYear;
        $selectedClass = $this->selectedClass;

        if (! $activeYear || ! $selectedClass) {
            return $this->emptyRekapData();
        }

        // Authorize access
        Gate::authorize('view-class-recap', $selectedClass);

        // Determine date range based on filter
        [$dateFrom, $dateTo] = $this->resolveDateRange($activeYear);

        // --- Eager load all needed data in bulk ---

        // 1. Students in this class for the active year
        $students = $selectedClass->students()
            ->wherePivot('academic_year_id', $activeYear->id)
            ->where('students.status', 'aktif')
            ->orderBy('students.name')
            ->get();

        if ($students->isEmpty()) {
            return $this->emptyRekapData();
        }

        $studentIds = $students->pluck('id');

        // 2. Schedules for this class (needed for subject-based columns)
        $schedules = Schedule::where('class_id', $selectedClass->id)
            ->where('academic_year_id', $activeYear->id)
            ->with('subject')
            ->get();

        // 3. All attendance records in range — single query with eager loading
        $attendances = StudentAttendance::whereIn('student_id', $studentIds)
            ->where('academic_year_id', $activeYear->id)
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->with('schedule.subject')
            ->get();

        // 4. Notifications for critical students
        $notifications = NotificationQueue::whereIn('student_id', $studentIds)
            ->pluck('student_id')
            ->unique();

        // 5. Holidays in date range
        $holidays = SchoolCalendar::where('type', 'holiday')
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->pluck('date')
            ->map(fn ($d) => Carbon::parse($d)->format('Y-m-d'))
            ->toArray();

        // --- Build structured data ---

        $alphaThreshold = $this->alphaThreshold;

        if ($this->viewMode === 'by_subject') {
            return $this->buildBySubject(
                $students, $attendances, $schedules, $notifications,
                $alphaThreshold, $dateFrom, $dateTo
            );
        }

        return $this->buildByDate(
            $students, $attendances, $notifications,
            $alphaThreshold, $dateFrom, $dateTo, $holidays
        );
    }

    // --- Private Builder Methods ---

    private function resolveDateRange(AcademicYear $activeYear): array
    {
        if ($this->filterMonth !== '') {
            $month = Carbon::createFromFormat('Y-m', $this->filterMonth);
            return [
                $month->copy()->startOfMonth()->format('Y-m-d'),
                $month->copy()->endOfMonth()->format('Y-m-d'),
            ];
        }

        // Default: entire academic year range capped to today
        return [
            $activeYear->start_date->format('Y-m-d'),
            min($activeYear->end_date, now())->format('Y-m-d'),
        ];
    }

    /**
     * MODE A — Rekap per Mata Pelajaran (R-02)
     */
    private function buildBySubject(
        Collection $students,
        Collection $attendances,
        Collection $schedules,
        Collection $notifiedStudentIds,
        int $alphaThreshold,
        string $dateFrom,
        string $dateTo,
    ): array {
        // Build unique subjects from schedules
        $subjectMap = $schedules->groupBy('subject_id')
            ->map(fn (Collection $group) => [
                'id'   => $group->first()->subject_id,
                'name' => $group->first()->subject?->name ?? 'N/A',
                'code' => $group->first()->subject?->code ?? '?',
            ])
            ->values()
            ->sortBy('name')
            ->values()
            ->toArray();

        // Build columns array
        $columns = [];
        foreach ($subjectMap as $subj) {
            $columns[] = [
                'id'    => 'subj_' . $subj['id'],
                'label' => $subj['code'],
                'title' => $subj['name'],
            ];
        }

        // Index attendances by student → subject_id → status
        $attendancesByStudent = $attendances->groupBy('student_id');

        // Build student rows
        $studentRows = [];
        $columnStats = array_fill_keys(
            array_column($columns, 'id'),
            ['hadir' => 0, 'alpha' => 0, 'sakit' => 0, 'izin' => 0]
        );

        $totalAllAlpha = 0;
        $criticalStudents = [];

        foreach ($students as $student) {
            $studentAtt = $attendancesByStudent->get($student->id, collect());

            // Group by subject_id
            $bySubject = $studentAtt->groupBy(fn ($a) => $a->schedule?->subject_id);

            $statusMap = [];
            $totalAlpha = 0;
            $totalHadir = 0;
            $totalSessions = 0;

            foreach ($subjectMap as $subj) {
                $colId = 'subj_' . $subj['id'];
                $subjAtt = $bySubject->get($subj['id'], collect());

                $totalSessions += $subjAtt->count();

                $hadirCount = $subjAtt->where('status', 'hadir')->count();
                $alphaCount = $subjAtt->where('status', 'alpha')->count();
                $sakitCount = $subjAtt->where('status', 'sakit')->count();
                $izinCount  = $subjAtt->where('status', 'izin')->count();

                $totalAlpha += $alphaCount;
                $totalHadir += $hadirCount;

                // For subject mode, show the cumulative count per status
                // Determine dominant status for the badge
                $statusMap[$colId] = [
                    'hadir' => $hadirCount,
                    'alpha' => $alphaCount,
                    'sakit' => $sakitCount,
                    'izin'  => $izinCount,
                    'total' => $subjAtt->count(),
                ];

                // Column stats
                $columnStats[$colId]['hadir'] += $hadirCount;
                $columnStats[$colId]['alpha'] += $alphaCount;
                $columnStats[$colId]['sakit'] += $sakitCount;
                $columnStats[$colId]['izin']  += $izinCount;
            }

            $totalAllAlpha += $totalAlpha;
            $attendancePct = $totalSessions > 0
                ? round(($totalHadir / $totalSessions) * 100, 1)
                : 0;

            $isCritical = $totalAlpha >= $alphaThreshold;

            $row = [
                'id'             => $student->id,
                'name'           => $student->name,
                'nis'            => $student->nis,
                'status_map'     => $statusMap,
                'total_alpha'    => $totalAlpha,
                'total_hadir'    => $totalHadir,
                'total_sessions' => $totalSessions,
                'attendance_pct' => $attendancePct,
                'is_critical'    => $isCritical,
            ];

            $studentRows[] = $row;

            if ($isCritical) {
                $criticalStudents[] = (object) [
                    'name'             => $student->name,
                    'total_alpha'      => $totalAlpha,
                    'attendance_pct'   => $attendancePct,
                    'has_notification' => $notifiedStudentIds->contains($student->id),
                ];
            }
        }

        // Summary cards (R-03)
        $totalSessionsAll = collect($studentRows)->sum('total_sessions');
        $totalHadirAll = collect($studentRows)->sum('total_hadir');

        return [
            'students'     => $studentRows,
            'columns'      => $columns,
            'column_stats' => $columnStats,
            'week_groups'  => [],
            'dates'        => [],
            'summary'      => [
                'total_sessions'       => count($columns) > 0
                    ? (int) round($totalSessionsAll / max(count($studentRows), 1))
                    : 0,
                'avg_attendance'       => $totalSessionsAll > 0
                    ? round(($totalHadirAll / $totalSessionsAll) * 100, 1)
                    : 0,
                'critical_alpha_count' => count($criticalStudents),
                'total_students'       => count($studentRows),
            ],
            'critical'       => $criticalStudents,
            'total_all_alpha' => $totalAllAlpha,
        ];
    }

    /**
     * MODE B — Rekap per Tanggal / Timeline (R-02)
     */
    private function buildByDate(
        Collection $students,
        Collection $attendances,
        Collection $notifiedStudentIds,
        int $alphaThreshold,
        string $dateFrom,
        string $dateTo,
        array $holidays,
    ): array {
        // Build date range (only weekdays, exclude holidays)
        $weekendDays = Setting::getValue('default_weekend_days', [0, 6]);
        $period = CarbonPeriod::create($dateFrom, $dateTo);

        $dates = [];
        foreach ($period as $date) {
            // Include all days but mark weekends
            $dates[] = $date->copy();
        }

        // Build week groups (R-05)
        $weekGroups = $this->buildWeekGroups($dates);

        // Build columns (each date = one column)
        $columns = [];
        foreach ($dates as $date) {
            $columns[] = [
                'id'         => $date->format('Y-m-d'),
                'label'      => $date->format('d'),
                'day'        => $date->translatedFormat('D'),
                'is_weekend' => in_array($date->dayOfWeek, $weekendDays),
                'is_holiday' => in_array($date->format('Y-m-d'), $holidays),
            ];
        }

        // Index attendances: student_id → date → status
        $attMap = [];
        foreach ($attendances as $att) {
            $attMap[$att->student_id][$att->date->format('Y-m-d')][] = $att->status;
        }

        // Build student rows
        $studentRows = [];
        $columnStats = [];
        foreach ($columns as $col) {
            $columnStats[$col['id']] = ['hadir' => 0, 'alpha' => 0, 'sakit' => 0, 'izin' => 0];
        }

        $totalAllAlpha = 0;
        $criticalStudents = [];

        foreach ($students as $student) {
            $statusMap = [];
            $totalAlpha = 0;
            $totalHadir = 0;
            $totalSessions = 0;

            foreach ($columns as $col) {
                $dateKey = $col['id'];
                $dayStatuses = $attMap[$student->id][$dateKey] ?? [];

                if (empty($dayStatuses)) {
                    $statusMap[$dateKey] = null;
                } else {
                    // If multiple attendances on the same day, pick worst status
                    $totalSessions += count($dayStatuses);

                    // Count per status for this day
                    $hadirCount = count(array_filter($dayStatuses, fn ($s) => $s === 'hadir'));
                    $alphaCount = count(array_filter($dayStatuses, fn ($s) => $s === 'alpha'));
                    $sakitCount = count(array_filter($dayStatuses, fn ($s) => $s === 'sakit'));
                    $izinCount  = count(array_filter($dayStatuses, fn ($s) => $s === 'izin'));

                    $totalAlpha += $alphaCount;
                    $totalHadir += $hadirCount;

                    // Show single dominant status for badge
                    if ($alphaCount > 0) {
                        $statusMap[$dateKey] = 'alpha';
                    } elseif ($sakitCount > 0) {
                        $statusMap[$dateKey] = 'sakit';
                    } elseif ($izinCount > 0) {
                        $statusMap[$dateKey] = 'izin';
                    } else {
                        $statusMap[$dateKey] = 'hadir';
                    }

                    // Column stats
                    $columnStats[$dateKey]['hadir'] += $hadirCount;
                    $columnStats[$dateKey]['alpha'] += $alphaCount;
                    $columnStats[$dateKey]['sakit'] += $sakitCount;
                    $columnStats[$dateKey]['izin']  += $izinCount;
                }
            }

            $totalAllAlpha += $totalAlpha;
            $attendancePct = $totalSessions > 0
                ? round(($totalHadir / $totalSessions) * 100, 1)
                : 0;

            $isCritical = $totalAlpha >= $alphaThreshold;

            $studentRows[] = [
                'id'             => $student->id,
                'name'           => $student->name,
                'nis'            => $student->nis,
                'status_map'     => $statusMap,
                'total_alpha'    => $totalAlpha,
                'total_hadir'    => $totalHadir,
                'total_sessions' => $totalSessions,
                'attendance_pct' => $attendancePct,
                'is_critical'    => $isCritical,
            ];

            if ($isCritical) {
                $criticalStudents[] = (object) [
                    'name'             => $student->name,
                    'total_alpha'      => $totalAlpha,
                    'attendance_pct'   => $attendancePct,
                    'has_notification' => $notifiedStudentIds->contains($student->id),
                ];
            }
        }

        // Summary (R-03)
        $totalSessionsAll = collect($studentRows)->sum('total_sessions');
        $totalHadirAll = collect($studentRows)->sum('total_hadir');

        return [
            'students'     => $studentRows,
            'columns'      => $columns,
            'column_stats' => $columnStats,
            'week_groups'  => $weekGroups,
            'dates'        => $dates,
            'summary'      => [
                'total_sessions'       => count($dates),
                'avg_attendance'       => $totalSessionsAll > 0
                    ? round(($totalHadirAll / $totalSessionsAll) * 100, 1)
                    : 0,
                'critical_alpha_count' => count($criticalStudents),
                'total_students'       => count($studentRows),
            ],
            'critical'        => $criticalStudents,
            'total_all_alpha' => $totalAllAlpha,
        ];
    }

    /**
     * Build week groups for date mode header (R-05).
     */
    private function buildWeekGroups(array $dates): array
    {
        if (empty($dates)) {
            return [];
        }

        $groups = [];
        $currentWeek = null;
        $weekNumber = 0;

        foreach ($dates as $date) {
            $weekOfYear = $date->weekOfYear;

            if ($currentWeek !== $weekOfYear) {
                $weekNumber++;
                $currentWeek = $weekOfYear;
                $groups[] = [
                    'number' => $weekNumber,
                    'start'  => $date->format('d/m'),
                    'end'    => $date->format('d/m'),
                    'count'  => 1,
                ];
            } else {
                $groups[count($groups) - 1]['end'] = $date->format('d/m');
                $groups[count($groups) - 1]['count']++;
            }
        }

        return $groups;
    }

    private function emptyRekapData(): array
    {
        return [
            'students'        => [],
            'columns'         => [],
            'column_stats'    => [],
            'week_groups'     => [],
            'dates'           => [],
            'summary'         => [
                'total_sessions'       => 0,
                'avg_attendance'       => 0,
                'critical_alpha_count' => 0,
                'total_students'       => 0,
            ],
            'critical'        => [],
            'total_all_alpha' => 0,
        ];
    }

    // --- Render ---

    public function render()
    {
        // Auto-select first class if none selected
        $classList = $this->classes;
        if (! $this->classId && $classList->isNotEmpty()) {
            $this->classId = $classList->first()->id;
        }

        return view('livewire.academic.attendance-recap');
    }
}

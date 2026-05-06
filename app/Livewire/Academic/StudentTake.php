<?php

namespace App\Livewire\Academic;

use App\Models\AcademicYear;
use App\Models\Classes;
use App\Models\Schedule;
use App\Models\Setting;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\StudentLeave;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Input Absensi')]
class StudentTake extends Component
{
    public Schedule $schedule;
    public array $students = [];
    public bool $isPastCutoff = false;
    public string $cutoffTime = '14:00';
    public bool $isSubmitted = false;

    public function mount(Schedule $schedule): void
    {
        // Load schedule with relations
        $this->schedule = $schedule->load(['subject', 'class_', 'teacher', 'academicYear']);

        // Check gate
        Gate::authorize('input-attendance', $this->schedule);

        // Get cutoff time from settings
        $this->cutoffTime = Setting::getValue('attendance_cutoff_time', '14:00');
        $this->isPastCutoff = now()->format('H:i') > $this->cutoffTime;

        // Load students for this class
        $this->loadStudents();
    }

    private function loadStudents(): void
    {
        $activeYear = AcademicYear::active();
        if (!$activeYear) return;

        $class = $this->schedule->class_;
        $today = today();

        // Get students enrolled in this class for the active year
        $classStudents = $class->students()
            ->wherePivot('academic_year_id', $activeYear->id)
            ->where('students.status', 'aktif')
            ->orderBy('students.name')
            ->get();

        // Get today's leaves (UKS)
        $todayLeaves = StudentLeave::whereDate('date', $today)
            ->whereIn('student_id', $classStudents->pluck('id'))
            ->pluck('status', 'student_id')
            ->toArray();

        // Get existing attendance for this schedule today
        $existingAttendance = StudentAttendance::where('schedule_id', $this->schedule->id)
            ->whereDate('date', $today)
            ->pluck('status', 'student_id')
            ->toArray();

        if (!empty($existingAttendance)) {
            $this->isSubmitted = true;
        }

        $this->students = $classStudents->map(function (Student $student) use ($todayLeaves, $existingAttendance) {
            $isLocked = isset($todayLeaves[$student->id]);
            $lockReason = $todayLeaves[$student->id] ?? null;

            // Determine status: existing attendance > UKS lock > default hadir
            $status = $existingAttendance[$student->id]
                ?? ($isLocked ? $lockReason : 'hadir');

            return [
                'id' => $student->id,
                'name' => $student->name,
                'nis' => $student->nis,
                'status' => $status,
                'is_locked' => $isLocked,
                'lock_reason' => $lockReason,
                'is_merged' => false,
            ];
        })->toArray();
    }

    public function submit(): void
    {
        if ($this->isPastCutoff) {
            $this->dispatch('notify', type: 'error', message: 'Waktu input absensi telah berakhir.');
            return;
        }

        $activeYear = AcademicYear::active();
        if (!$activeYear) {
            $this->dispatch('notify', type: 'error', message: 'Tidak ada Tahun Ajaran aktif.');
            return;
        }

        $today = today();
        $userId = Auth::id();

        $records = [];
        foreach ($this->students as $student) {
            $records[] = [
                'unit_id' => $this->schedule->unit_id,
                'student_id' => $student['id'],
                'schedule_id' => $this->schedule->id,
                'academic_year_id' => $activeYear->id,
                'date' => $today->format('Y-m-d'),
                'status' => $student['status'],
                'is_merged' => $student['is_merged'],
                'merged_from_class_id' => null,
                'created_by' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Batch upsert — per blueprint Section 5.5
        StudentAttendance::upsert(
            $records,
            ['student_id', 'schedule_id', 'date'], // unique key
            ['status', 'is_merged', 'created_by', 'updated_at'] // columns to update
        );

        $this->isSubmitted = true;

        $this->dispatch('notify', type: 'success', message: 'Absensi berhasil disimpan untuk ' . count($this->students) . ' santri.');
    }

    public function render()
    {
        return view('livewire.academic.student-take');
    }
}

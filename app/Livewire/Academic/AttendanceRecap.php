<?php

namespace App\Livewire\Academic;

use App\Models\AcademicYear;
use App\Models\Classes;
use App\Models\StudentAttendance;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Rekap Absensi')]
class AttendanceRecap extends Component
{
    public ?int $classId = null;
    public string $startDate = '';
    public string $endDate = '';

    public function mount(?string $class = null): void
    {
        // Default date range: current month
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');

        // If class is 'today' or a numeric ID
        if ($class && $class !== 'today' && is_numeric($class)) {
            $this->classId = (int) $class;
        }
    }

    public function render()
    {
        $user = Auth::user();
        $activeYear = AcademicYear::active();

        // Get available classes
        $classes = collect();
        if ($activeYear) {
            $query = Classes::where('academic_year_id', $activeYear->id)
                ->orderBy('name');

            // Walikelas can only see their homeroom class
            if ($user->role === 'walikelas') {
                $query->where('homeroom_teacher_id', $user->id);
            }

            $classes = $query->get();

            // Auto-select first class if none selected
            if (!$this->classId && $classes->isNotEmpty()) {
                $this->classId = $classes->first()->id;
            }
        }

        // Get recap data
        $recap = collect();
        $selectedClass = null;

        if ($this->classId && $activeYear) {
            $selectedClass = Classes::with('homeroomTeacher')->find($this->classId);

            // Authorize
            if ($selectedClass) {
                Gate::authorize('view-class-recap', $selectedClass);

                // Get all students in this class
                $students = $selectedClass->students()
                    ->wherePivot('academic_year_id', $activeYear->id)
                    ->where('students.status', 'aktif')
                    ->orderBy('students.name')
                    ->get();

                // Get attendance data for date range
                $attendances = StudentAttendance::where('schedule_id', '!=', 0)
                    ->whereIn('student_id', $students->pluck('id'))
                    ->where('academic_year_id', $activeYear->id)
                    ->whereBetween('date', [$this->startDate, $this->endDate])
                    ->get()
                    ->groupBy('student_id');

                $recap = $students->map(function ($student) use ($attendances) {
                    $studentAttendances = $attendances->get($student->id, collect());

                    return [
                        'id' => $student->id,
                        'name' => $student->name,
                        'nis' => $student->nis,
                        'total' => $studentAttendances->count(),
                        'hadir' => $studentAttendances->where('status', 'hadir')->count(),
                        'alpha' => $studentAttendances->where('status', 'alpha')->count(),
                        'sakit' => $studentAttendances->where('status', 'sakit')->count(),
                        'izin' => $studentAttendances->where('status', 'izin')->count(),
                    ];
                });
            }
        }

        return view('livewire.academic.attendance-recap', [
            'classes' => $classes,
            'recap' => $recap,
            'selectedClass' => $selectedClass,
        ]);
    }
}

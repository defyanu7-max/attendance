<?php

namespace App\Livewire\Academic;

use App\Models\AcademicYear;
use App\Models\Schedule;
use App\Models\StudentAttendance;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Jadwal & Absen')]
class ScheduleIndex extends Component
{
    public int $selectedDay;
    public ?int $activeYearId = null;

    public function mount(): void
    {
        $this->selectedDay = (int) now()->dayOfWeek;
        $this->activeYearId = AcademicYear::active()?->id;
    }

    public function selectDay(int $day): void
    {
        $this->selectedDay = $day;
    }

    public function render()
    {
        $user = Auth::user();
        $schedules = collect();

        if ($this->activeYearId) {
            $query = Schedule::with(['subject', 'class_', 'teacher'])
                ->where('academic_year_id', $this->activeYearId)
                ->where('day_of_week', $this->selectedDay)
                ->orderBy('start_time');

            // Guru/Walikelas hanya lihat jadwal sendiri
            if (in_array($user->role, ['guru', 'walikelas'])) {
                $query->where('teacher_id', $user->id);
            }

            $schedules = $query->get();

            // Tandai apakah sudah ada absensi hari ini untuk setiap jadwal
            $today = today();
            $schedules->each(function ($schedule) use ($today) {
                $schedule->has_attendance_today = StudentAttendance::where('schedule_id', $schedule->id)
                    ->whereDate('date', $today)
                    ->exists();
            });
        }

        $days = [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
        ];

        return view('livewire.academic.schedule-index', [
            'schedules' => $schedules,
            'days' => $days,
            'activeYear' => $this->activeYearId ? AcademicYear::find($this->activeYearId) : null,
        ]);
    }
}

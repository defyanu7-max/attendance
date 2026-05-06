<?php

namespace App\Livewire\Dashboard;

use App\Models\AcademicYear;
use App\Models\Schedule;
use App\Models\Student;
use App\Models\StudentLeave;
use App\Models\NotificationQueue;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Dashboard')]
class Dashboard extends Component
{
    public function render()
    {
        $user = Auth::user();
        $today = now();
        $dayOfWeek = (int) $today->dayOfWeek; // 0=Sun..6=Sat
        $activeYear = AcademicYear::active();

        // --- Data for all roles ---
        $todaySchedules = collect();

        if ($activeYear) {
            if ($user->isAdminOrAbove()) {
                // Admin/Superadmin see all today's schedules for their unit
                $todaySchedules = Schedule::with(['subject', 'class_', 'teacher'])
                    ->where('day_of_week', $dayOfWeek)
                    ->where('academic_year_id', $activeYear->id)
                    ->when($user->unit_id, fn($q) => $q->where('unit_id', $user->unit_id))
                    ->orderBy('start_time')
                    ->get();
            } else {
                // Guru/Walikelas see their own schedules
                $todaySchedules = Schedule::with(['subject', 'class_', 'teacher'])
                    ->where('teacher_id', $user->id)
                    ->where('day_of_week', $dayOfWeek)
                    ->where('academic_year_id', $activeYear->id)
                    ->orderBy('start_time')
                    ->get();
            }
        }

        // --- Stats for admin+ ---
        $stats = [];
        if ($user->isAdminOrAbove()) {
            $stats = [
                'total_students' => Student::active()->count(),
                'today_leaves' => StudentLeave::whereDate('date', $today)->count(),
                'pending_notifications' => NotificationQueue::where('status', 'pending')->count(),
            ];
        }

        return view('livewire.dashboard.dashboard', [
            'user' => $user,
            'todaySchedules' => $todaySchedules,
            'stats' => $stats,
            'dayName' => ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'][$dayOfWeek],
            'activeYear' => $activeYear,
        ]);
    }
}

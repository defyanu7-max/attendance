<?php

namespace App\Livewire\Dashboard;

use App\Models\AcademicYear;
use App\Models\Classes;
use App\Models\NotificationQueue;
use App\Models\Schedule;
use App\Models\Setting;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\StudentLeave;
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
        $user       = Auth::user();
        $today      = now();
        $dayOfWeek  = (int) $today->dayOfWeek; // 0=Sun..6=Sat
        $activeYear = AcademicYear::active();

        // Superadmin (unit_id = null) melihat data lintas-unit
        $isSuperadmin = $user->role === 'superadmin' && is_null($user->unit_id);

        // --- Jadwal hari ini ---
        $todaySchedules = collect();

        if ($activeYear) {
            if ($user->isAdminOrAbove()) {
                $todaySchedules = Schedule::with(['subject', 'class_', 'teacher'])
                    ->where('day_of_week', $dayOfWeek)
                    ->where('academic_year_id', $activeYear->id)
                    // Superadmin: tidak filter unit → lihat semua unit
                    ->when(! $isSuperadmin, fn ($q) => $q->where('unit_id', $user->unit_id))
                    ->orderBy('start_time')
                    ->get();
            } else {
                // Guru / Walikelas: hanya jadwal miliknya
                $todaySchedules = Schedule::with(['subject', 'class_', 'teacher'])
                    ->where('teacher_id', $user->id)
                    ->where('day_of_week', $dayOfWeek)
                    ->where('academic_year_id', $activeYear->id)
                    ->orderBy('start_time')
                    ->get();
            }
        }

        // --- Stat cards untuk Admin+ ---
        $stats = [];
        if ($user->isAdminOrAbove()) {
            $alphaThreshold = (int) Setting::getValue('alpha_threshold_count', 5);

            // Query builder helper: scope ke unit jika bukan Superadmin
            $unitScope = fn ($q) => $isSuperadmin ? $q : $q->where('unit_id', $user->unit_id);

            // Total santri aktif
            $totalStudents = Student::withoutGlobalScopes()
                ->when(! $isSuperadmin, fn ($q) => $q->where('unit_id', $user->unit_id))
                ->where('status', 'aktif')
                ->count();

            // Izin/Sakit hari ini
            $todayLeaves = StudentLeave::withoutGlobalScopes()
                ->when(! $isSuperadmin, fn ($q) => $q->where('unit_id', $user->unit_id))
                ->whereDate('date', $today)
                ->count();

            // Notifikasi alpha pending
            $pendingNotifications = NotificationQueue::withoutGlobalScopes()
                ->when(! $isSuperadmin, fn ($q) => $q->where('unit_id', $user->unit_id))
                ->where('status', 'pending')
                ->count();

            // Alpha kritis hari ini (santri yang alpha >= threshold pada hari ini)
            $todayAlphaCritical = StudentAttendance::withoutGlobalScopes()
                ->when(! $isSuperadmin, fn ($q) => $q->where('unit_id', $user->unit_id))
                ->whereDate('date', $today)
                ->where('status', 'alpha')
                ->distinct('student_id')
                ->count('student_id');

            // Total kelas aktif di tahun ajaran aktif
            $activeClasses = $activeYear
                ? Classes::withoutGlobalScopes()
                    ->when(! $isSuperadmin, fn ($q) => $q->where('unit_id', $user->unit_id))
                    ->where('academic_year_id', $activeYear->id)
                    ->count()
                : 0;

            $stats = [
                'total_students'        => $totalStudents,
                'today_leaves'          => $todayLeaves,
                'pending_notifications' => $pendingNotifications,
                'today_alpha_critical'  => $todayAlphaCritical,
                'active_classes'        => $activeClasses,
            ];
        }

        return view('livewire.dashboard.dashboard', [
            'user'          => $user,
            'todaySchedules' => $todaySchedules,
            'stats'          => $stats,
            'dayName'        => ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'][$dayOfWeek],
            'activeYear'     => $activeYear,
            'isSuperadmin'   => $isSuperadmin,
        ]);
    }
}

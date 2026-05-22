<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Classes;
use App\Models\Schedule;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Observers\StudentAttendanceObserver;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // --- Observer Registration ---
        StudentAttendance::observe(StudentAttendanceObserver::class);

        // --- Gate Definitions (from Blueprint Section 12.2) ---

        Gate::define('manage-master-data', fn(User $user) =>
            in_array($user->role, ['superadmin', 'admin'])
        );

        Gate::define('manage-system', fn(User $user) =>
            $user->role === 'superadmin'
        );

        Gate::define('delete-student', function (User $user, ?Student $student = null) {
            if ($user->role === 'superadmin') return true;
            if ($user->role === 'admin') {
                return $student ? $student->unit_id === $user->unit_id : true;
            }
            return false;
        });

        Gate::define('delete-teacher', function (User $user, ?User $teacher = null) {
            if ($user->role === 'superadmin') return true;
            if ($user->role === 'admin') {
                return $teacher ? $teacher->unit_id === $user->unit_id : true;
            }
            return false;
        });

        Gate::define('view-class-recap', function (User $user, Classes $class) {
            if (in_array($user->role, ['superadmin', 'admin'])) return true;
            if ($user->role === 'walikelas') return $class->homeroom_teacher_id === $user->id;
            return false;
        });

        Gate::define('input-attendance', function (User $user, Schedule $schedule) {
            if (in_array($user->role, ['superadmin', 'admin'])) return true;
            if ($schedule->teacher_id === $user->id) return true;
            return $schedule->substitutions()
                ->where('substitute_user_id', $user->id)
                ->whereDate('date', today())
                ->exists();
        });

        Gate::define('correct-attendance', fn(User $user) =>
            in_array($user->role, ['superadmin', 'admin'])
        );

        Gate::define('manage-leaves', fn(User $user) =>
            in_array($user->role, ['superadmin', 'admin'])
        );

        Gate::define('manage-notifications', fn(User $user) =>
            in_array($user->role, ['superadmin', 'admin'])
        );

        Gate::define('view-subjects', fn(User $user) => true);
    }
}

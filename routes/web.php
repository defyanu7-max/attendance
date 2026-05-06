<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\ExcelExportController;
use App\Livewire\Auth\LoginForm;
use App\Livewire\Auth\ChangePassword;
use App\Livewire\Dashboard\Dashboard;
use App\Livewire\Academic\ScheduleIndex;
use App\Livewire\Academic\SubjectIndex;
use App\Livewire\Academic\StudentTake;
use App\Livewire\Academic\AttendanceRecap;
use App\Livewire\Academic\SubstitutionForm;
use App\Livewire\Master\StudentIndex;
use App\Livewire\Master\StudentForm;
use App\Livewire\Master\ClassIndex;
use App\Livewire\Master\ExcelImport;
use App\Livewire\Uks\LeaveForm;
use App\Livewire\Notification\AlphaNotificationIndex;
use App\Livewire\System\CalendarManager;
use App\Livewire\System\SettingsAlpha;

/*
|--------------------------------------------------------------------------
| PPNI System Routes
|--------------------------------------------------------------------------
*/

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/login', LoginForm::class)->name('login');
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::get('/', fn() => redirect()->route('dashboard'));
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/logout', LogoutController::class)->name('logout');
    Route::get('/profile/password', ChangePassword::class)->name('profile.password');

    // Academic — accessible to all authenticated users
    Route::get('/schedules', ScheduleIndex::class)->name('schedules.index');
    Route::get('/subjects', SubjectIndex::class)->name('subjects.index');
    Route::get('/attendance/{schedule}', StudentTake::class)->name('attendance.take');
    Route::get('/recap/{class?}', AttendanceRecap::class)->name('attendance.recap');

    // Master data — Admin+
    Route::middleware('can:manage-master-data')->group(function () {
        Route::get('/students', StudentIndex::class)->name('students.index');
        Route::get('/students/create', StudentForm::class)->name('students.create');
        Route::get('/students/{student}/edit', StudentForm::class)->name('students.edit');
        Route::get('/classes', ClassIndex::class)->name('classes.index');
        Route::get('/substitutions', SubstitutionForm::class)->name('substitutions.index');
        Route::get('/notifications', AlphaNotificationIndex::class)->name('notifications.index');

        // Excel Export
        Route::prefix('export')->name('export.')->group(function () {
            Route::get('/students', [ExcelExportController::class, 'students'])->name('students');
            Route::get('/teachers', [ExcelExportController::class, 'teachers'])->name('teachers');
            Route::get('/schedules', [ExcelExportController::class, 'schedules'])->name('schedules');
        });

        // Excel Import
        Route::get('/import', ExcelImport::class)->name('import.index');
    });

    // UKS — Admin+
    Route::get('/leaves', LeaveForm::class)->name('leaves.index')
        ->middleware('can:manage-leaves');

    // System — Superadmin only
    Route::middleware('can:manage-system')->prefix('system')->group(function () {
        Route::get('/calendar', CalendarManager::class)->name('system.calendar');
        Route::get('/settings', SettingsAlpha::class)->name('system.settings');
    });
});

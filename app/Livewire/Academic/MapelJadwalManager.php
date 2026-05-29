<?php

namespace App\Livewire\Academic;

use App\Models\AcademicYear;
use App\Models\Classes;
use App\Models\Schedule;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Mata Pelajaran & Jadwal')]
class MapelJadwalManager extends Component
{
    // === STATE UNIT ===
    public int $activeUnit;

    // === STATE PANEL KIRI (Subjects) ===
    public string $subjectSearch = '';
    public ?int $selectedSubjectId = null;

    // === STATE PANEL KANAN (Schedules) ===
    public string $filterDay = '';
    public string $filterClassId = '';

    // === STATE MODAL MAPEL ===
    public bool $subjectModalOpen = false;
    public ?int $editingSubjectId = null;
    public array $subjectForm = ['code' => '', 'name' => ''];

    // === STATE MODAL JADWAL ===
    public bool $scheduleModalOpen = false;
    public ?int $editingScheduleId = null;
    public array $scheduleForm = [
        'subject_id'  => '',
        'class_id'    => '',
        'teacher_id'  => '',
        'day_of_week' => '',
        'start_time'  => '',
        'end_time'    => '',
    ];

    // =========================================================================
    // LIFECYCLE
    // =========================================================================

    public function mount(): void
    {
        Gate::authorize('manage-master-data');

        $user = Auth::user();
        $this->activeUnit = $user->role === 'superadmin'
            ? 1
            : ($user->unit_id ?? 1);
    }

    // =========================================================================
    // UNIT SWITCHER
    // =========================================================================

    public function switchUnit(int $unitId): void
    {
        $user = Auth::user();

        // Admin can only access own unit
        if ($user->role !== 'superadmin' && $user->unit_id !== $unitId) {
            return;
        }

        $this->activeUnit = $unitId;
        $this->selectedSubjectId = null;
        $this->filterDay = '';
        $this->filterClassId = '';
        $this->subjectSearch = '';
    }

    // =========================================================================
    // PANEL KIRI — SUBJECT SELECTION
    // =========================================================================

    public function selectSubject(?int $subjectId): void
    {
        $this->selectedSubjectId = $subjectId;
        $this->filterDay = '';
        $this->filterClassId = '';
    }

    // =========================================================================
    // MODAL MATA PELAJARAN (CRUD)
    // =========================================================================

    public function openAddSubjectModal(): void
    {
        Gate::authorize('manage-master-data');
        $this->editingSubjectId = null;
        $this->subjectForm = ['code' => '', 'name' => ''];
        $this->resetValidation();
        $this->subjectModalOpen = true;
    }

    public function openEditSubjectModal(int $id): void
    {
        Gate::authorize('manage-master-data');

        $subject = Subject::withoutGlobalScopes()->findOrFail($id);
        $this->editingSubjectId = $subject->id;
        $this->subjectForm = [
            'code' => $subject->code,
            'name' => $subject->name,
        ];
        $this->resetValidation();
        $this->subjectModalOpen = true;
    }

    public function closeSubjectModal(): void
    {
        $this->subjectModalOpen = false;
        $this->editingSubjectId = null;
        $this->subjectForm = ['code' => '', 'name' => ''];
        $this->resetValidation();
    }

    public function saveSubject(): void
    {
        Gate::authorize('manage-master-data');

        $this->validate($this->subjectRules());

        Subject::updateOrCreate(
            ['id' => $this->editingSubjectId],
            [
                'code'    => strtoupper($this->subjectForm['code']),
                'name'    => $this->subjectForm['name'],
                'unit_id' => $this->activeUnit,
            ]
        );

        $this->closeSubjectModal();
        $this->dispatch('notify', type: 'success', message: 'Mata pelajaran berhasil disimpan.');
    }

    public function confirmDeleteSubject(int $id): void
    {
        Gate::authorize('manage-master-data');
        $subject = Subject::withoutGlobalScopes()->findOrFail($id);
        $this->dispatch('confirm-delete-subject', id: $id, name: $subject->name);
    }

    public function deleteSubject(int $id): void
    {
        Gate::authorize('manage-master-data');

        $subject = Subject::withoutGlobalScopes()->findOrFail($id);
        $subject->delete();

        if ($this->selectedSubjectId === $id) {
            $this->selectedSubjectId = null;
        }

        $this->dispatch('notify', type: 'success', message: 'Mata pelajaran berhasil dihapus.');
    }

    // =========================================================================
    // MODAL JADWAL PELAJARAN (CRUD)
    // =========================================================================

    public function openAddScheduleModal(): void
    {
        Gate::authorize('manage-master-data');

        $this->editingScheduleId = null;
        $this->scheduleForm = [
            'subject_id'  => $this->selectedSubjectId ?? '',
            'class_id'    => '',
            'teacher_id'  => '',
            'day_of_week' => '',
            'start_time'  => '',
            'end_time'    => '',
        ];
        $this->resetValidation();
        $this->scheduleModalOpen = true;
    }

    public function openEditScheduleModal(int $id): void
    {
        Gate::authorize('manage-master-data');

        $schedule = Schedule::withoutGlobalScopes()->findOrFail($id);
        $this->editingScheduleId = $schedule->id;
        $this->scheduleForm = [
            'subject_id'  => $schedule->subject_id,
            'class_id'    => $schedule->class_id,
            'teacher_id'  => $schedule->teacher_id,
            'day_of_week' => $schedule->day_of_week,
            'start_time'  => substr((string) $schedule->start_time, 0, 5),
            'end_time'    => substr((string) $schedule->end_time, 0, 5),
        ];
        $this->resetValidation();
        $this->scheduleModalOpen = true;
    }

    public function closeScheduleModal(): void
    {
        $this->scheduleModalOpen = false;
        $this->editingScheduleId = null;
        $this->scheduleForm = [
            'subject_id'  => '',
            'class_id'    => '',
            'teacher_id'  => '',
            'day_of_week' => '',
            'start_time'  => '',
            'end_time'    => '',
        ];
        $this->resetValidation();
    }

    public function saveSchedule(): void
    {
        Gate::authorize('manage-master-data');

        $this->validate($this->scheduleRules());

        $activeYear = AcademicYear::active();
        if (! $activeYear) {
            $this->dispatch('notify', type: 'error', message: 'Tidak ada tahun ajaran aktif.');
            return;
        }

        // Generate unique_code using PHP 8.5 pipe operator
        $subject = Subject::withoutGlobalScopes()->find($this->scheduleForm['subject_id']);
        $class   = Classes::withoutGlobalScopes()->find($this->scheduleForm['class_id']);

        // Guard: subject atau class dihapus di antara validasi dan simpan
        if (! $subject || ! $class) {
            $this->dispatch('notify', type: 'error', message: 'Mata pelajaran atau kelas tidak ditemukan. Silakan refresh dan coba lagi.');
            return;
        }

        $uniqueCode = $subject->code
            |> strtoupper(...)
            |> (fn(string $s) => $s . '-' . str_replace([' ', '-', '/'], '', strtoupper($class->name)))
            |> (fn(string $s) => $s . '-' . substr(str_replace('/', '', $activeYear->name), -2));

        // For edits, only update unique_code if subject/class changed
        if ($this->editingScheduleId) {
            $existing = Schedule::withoutGlobalScopes()->find($this->editingScheduleId);
            if ($existing &&
                $existing->subject_id == $this->scheduleForm['subject_id'] &&
                $existing->class_id == $this->scheduleForm['class_id']
            ) {
                $uniqueCode = $existing->unique_code;
            }
        }

        // Handle unique_code collision: append day suffix
        $baseCode = $uniqueCode;
        $dayNames = ['', 'SEN', 'SEL', 'RAB', 'KAM', 'JUM', 'SAB'];
        $daySuffix = $dayNames[(int) $this->scheduleForm['day_of_week']] ?? '';

        $existingSchedule = Schedule::withoutGlobalScopes()
            ->where('unique_code', $uniqueCode)
            ->when($this->editingScheduleId, fn ($q) => $q->where('id', '!=', $this->editingScheduleId))
            ->exists();

        if ($existingSchedule) {
            $uniqueCode = $baseCode . '-' . $daySuffix;
        }

        Schedule::updateOrCreate(
            ['id' => $this->editingScheduleId],
            [
                'unit_id'          => $this->activeUnit,
                'class_id'         => $this->scheduleForm['class_id'],
                'subject_id'       => $this->scheduleForm['subject_id'],
                'teacher_id'       => $this->scheduleForm['teacher_id'],
                'academic_year_id' => $activeYear->id,
                'day_of_week'      => (int) $this->scheduleForm['day_of_week'],
                'start_time'       => $this->scheduleForm['start_time'],
                'end_time'         => $this->scheduleForm['end_time'],
                'unique_code'      => $uniqueCode,
            ]
        );

        $this->closeScheduleModal();
        $this->dispatch('notify', type: 'success', message: 'Jadwal berhasil disimpan.');
    }

    public function confirmDeleteSchedule(int $id): void
    {
        Gate::authorize('manage-master-data');

        $schedule = Schedule::withoutGlobalScopes()
            ->with(['subject', 'class_'])
            ->findOrFail($id);

        $name = ($schedule->subject?->code ?? '?') . ' - ' . ($schedule->class_?->name ?? '?');
        $this->dispatch('confirm-delete-schedule', id: $id, name: $name);
    }

    public function deleteSchedule(int $id): void
    {
        Gate::authorize('manage-master-data');
        Schedule::withoutGlobalScopes()->findOrFail($id)->delete();
        $this->dispatch('notify', type: 'success', message: 'Jadwal berhasil dihapus.');
    }

    // =========================================================================
    // VALIDATION RULES
    // =========================================================================

    protected function subjectRules(): array
    {
        $uniqueRule = Rule::unique('subjects', 'code')
            ->where('unit_id', $this->activeUnit);

        if ($this->editingSubjectId) {
            $uniqueRule->ignore($this->editingSubjectId);
        }

        return [
            'subjectForm.code' => ['required', 'string', 'max:10', $uniqueRule],
            'subjectForm.name' => ['required', 'string', 'max:100'],
        ];
    }

    protected function scheduleRules(): array
    {
        return [
            'scheduleForm.subject_id'  => ['required', 'exists:subjects,id'],
            'scheduleForm.class_id'    => ['required', 'exists:classes,id'],
            'scheduleForm.teacher_id'  => ['required', 'exists:users,id'],
            'scheduleForm.day_of_week' => ['required', 'integer', 'between:1,6'],
            'scheduleForm.start_time'  => ['required', 'date_format:H:i'],
            'scheduleForm.end_time'    => ['required', 'date_format:H:i', 'after:scheduleForm.start_time'],
        ];
    }

    // =========================================================================
    // COMPUTED PROPERTIES — All data preparation for Blade
    // =========================================================================

    /**
     * Active academic year — di-cache oleh Livewire selama satu render cycle.
     * Semua computed property lain WAJIB menggunakan $this->activeYear
     * bukan memanggil AcademicYear::active() secara langsung.
     */
    #[Computed]
    public function activeYear(): ?AcademicYear
    {
        return AcademicYear::active();
    }

    /**
     * Count subjects per unit for tab badges.
     */
    #[Computed]
    public function countByUnit(): array
    {
        $counts = Subject::withoutGlobalScopes()
            ->selectRaw('unit_id, COUNT(*) as total')
            ->whereNull('deleted_at')
            ->groupBy('unit_id')
            ->pluck('total', 'unit_id')
            ->toArray();

        return [
            1 => ['subjects' => $counts[1] ?? 0],
            2 => ['subjects' => $counts[2] ?? 0],
        ];
    }

    /**
     * Subjects for the active unit, filtered by search.
     * Eager load schedule count for badge display.
     */
    #[Computed]
    public function subjects(): Collection
    {
        return Subject::withoutGlobalScopes()
            ->where('unit_id', $this->activeUnit)
            ->whereNull('deleted_at')
            ->when($this->subjectSearch, fn ($q) => $q->where(function ($q2) {
                $q2->where('name', 'like', "%{$this->subjectSearch}%")
                   ->orWhere('code', 'like', "%{$this->subjectSearch}%");
            }))
            ->withCount(['schedules' => fn ($q) => $q->where('academic_year_id', $this->activeYear?->id)])
            ->orderBy('code')
            ->get();
    }

    /**
     * Total schedules for the active unit (used by "Semua Jadwal" badge).
     */
    #[Computed]
    public function totalSchedulesUnit(): int
    {
        $activeYear = $this->activeYear;
        if (! $activeYear) {
            return 0;
        }

        return Schedule::withoutGlobalScopes()
            ->where('unit_id', $this->activeUnit)
            ->where('academic_year_id', $activeYear->id)
            ->whereNull('deleted_at')
            ->count();
    }

    /**
     * The currently selected subject model (for header display).
     */
    #[Computed]
    public function selectedSubject(): ?Subject
    {
        if (! $this->selectedSubjectId) {
            return null;
        }

        return Subject::withoutGlobalScopes()->find($this->selectedSubjectId);
    }

    /**
     * Schedules grouped by day_of_week for the right panel.
     * Filters: activeUnit, selectedSubjectId, filterDay, filterClassId.
     * Eager loads: subject, class_, teacher.
     */
    #[Computed]
    public function schedulesByDay(): Collection
    {
        $activeYear = $this->activeYear;
        if (! $activeYear) {
            return collect();
        }

        $query = Schedule::withoutGlobalScopes()
            ->where('unit_id', $this->activeUnit)
            ->where('academic_year_id', $activeYear->id)
            ->whereNull('deleted_at')
            ->with(['subject', 'class_', 'teacher']);

        if ($this->selectedSubjectId) {
            $query->where('subject_id', $this->selectedSubjectId);
        }

        if ($this->filterDay !== '') {
            $query->where('day_of_week', (int) $this->filterDay);
        }

        if ($this->filterClassId !== '') {
            $query->where('class_id', (int) $this->filterClassId);
        }

        return $query
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get()
            ->groupBy('day_of_week');
    }

    /**
     * Classes for the active unit & active academic year (dropdown in schedule modal + filter).
     */
    #[Computed]
    public function classesForUnit(): Collection
    {
        $activeYear = $this->activeYear;
        if (! $activeYear) {
            return collect();
        }

        return Classes::withoutGlobalScopes()
            ->where('unit_id', $this->activeUnit)
            ->where('academic_year_id', $activeYear->id)
            ->whereNull('deleted_at')
            ->orderBy('name')
            ->get();
    }

    /**
     * Teachers (guru/walikelas/admin) for the active unit.
     * IMPORTANT: User does NOT use UnitScope — filter manually.
     */
    #[Computed]
    public function teachers(): Collection
    {
        return User::where('unit_id', $this->activeUnit)
            ->whereIn('role', ['guru', 'walikelas', 'admin'])
            ->whereNull('deleted_at')
            ->orderBy('name')
            ->get();
    }

    // =========================================================================
    // RENDER
    // =========================================================================

    public function render()
    {
        return view('livewire.academic.mapel-jadwal-manager');
    }
}

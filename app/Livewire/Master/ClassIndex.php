<?php

namespace App\Livewire\Master;

use App\Models\AcademicYear;
use App\Models\Classes;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Manajemen Kelas')]
class ClassIndex extends Component
{
    public ?int $editingId = null;
    public string $name = '';
    public ?int $unit_id = null;
    public ?int $homeroom_teacher_id = null;
    public ?int $academic_year_id = null;
    public bool $showForm = false;

    public function mount(): void
    {
        Gate::authorize('manage-master-data');
        $user = Auth::user();
        $this->unit_id = $user->unit_id;
        $this->academic_year_id = AcademicYear::active()?->id;
    }

    public function openCreate(): void
    {
        $this->reset(['editingId', 'name', 'homeroom_teacher_id']);
        $user = Auth::user();
        $this->unit_id = $user->unit_id;
        $this->academic_year_id = AcademicYear::active()?->id;
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $class = Classes::findOrFail($id);
        $this->editingId = $class->id;
        $this->name = $class->name;
        $this->unit_id = $class->unit_id;
        $this->homeroom_teacher_id = $class->homeroom_teacher_id;
        $this->academic_year_id = $class->academic_year_id;
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate([
            'name' => 'required|string|max:50',
            'unit_id' => 'required|exists:units,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'homeroom_teacher_id' => 'nullable|exists:users,id',
        ]);

        Classes::updateOrCreate(
            ['id' => $this->editingId],
            [
                'name' => $this->name,
                'unit_id' => $this->unit_id,
                'homeroom_teacher_id' => $this->homeroom_teacher_id,
                'academic_year_id' => $this->academic_year_id,
            ]
        );

        $this->reset(['editingId', 'name', 'homeroom_teacher_id', 'showForm']);
        $this->dispatch('notify', type: 'success', message: 'Kelas berhasil disimpan.');
    }

    public function cancelEdit(): void
    {
        $this->reset(['editingId', 'name', 'homeroom_teacher_id', 'showForm']);
    }

    public function confirmDelete(int $id): void
    {
        $class = Classes::findOrFail($id);
        $this->dispatch('confirm-delete', id: $id, name: $class->name);
    }

    public function delete(int $id): void
    {
        Classes::findOrFail($id)->delete();
        $this->dispatch('notify', type: 'success', message: 'Kelas berhasil dihapus.');
    }

    public function render()
    {
        $user = Auth::user();
        $activeYear = AcademicYear::active();

        $classes = Classes::with(['homeroomTeacher', 'academicYear', 'unit'])
            ->withCount('students')
            ->when($activeYear, fn($q) => $q->where('academic_year_id', $activeYear->id))
            ->orderBy('name')
            ->get();

        // Teachers available for homeroom (walikelas role in this unit)
        $teachers = User::query()
            ->whereIn('role', ['walikelas', 'guru'])
            ->when($this->unit_id, fn($q) => $q->where('unit_id', $this->unit_id))
            ->orderBy('name')
            ->get();

        $units = $user->isSuperadmin() ? Unit::all() : collect();

        return view('livewire.master.class-index', [
            'classes' => $classes,
            'teachers' => $teachers,
            'units' => $units,
            'isSuperadmin' => $user->isSuperadmin(),
            'activeYear' => $activeYear,
        ]);
    }
}

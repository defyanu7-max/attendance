<?php

namespace App\Livewire\Master;

use App\Models\AcademicYear;
use App\Models\Classes;
use App\Models\Student;
use App\Models\Unit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Form Santri')]
class StudentForm extends Component
{
    public ?Student $student = null;
    public bool $isEditing = false;

    // Form fields
    public string $nis = '';
    public string $nisn = '';
    public string $name = '';
    public ?int $unit_id = null;
    public string $status = 'aktif';
    public ?int $class_id = null;

    public function mount(?Student $student = null): void
    {
        Gate::authorize('manage-master-data');

        $user = Auth::user();

        if ($student && $student->exists) {
            $this->student = $student;
            $this->isEditing = true;
            $this->nis = $student->nis;
            $this->nisn = $student->nisn ?? '';
            $this->name = $student->name;
            $this->unit_id = $student->unit_id;
            $this->status = $student->status;

            // Get current class
            $activeYear = AcademicYear::active();
            if ($activeYear) {
                $this->class_id = $student->classes()
                    ->wherePivot('academic_year_id', $activeYear->id)
                    ->first()?->id;
            }
        } else {
            // Default unit for non-superadmin
            $this->unit_id = $user->unit_id;
        }
    }

    public function save(): void
    {
        Gate::authorize('manage-master-data');

        $rules = [
            'nis' => 'required|string|max:20|unique:students,nis' . ($this->isEditing ? ',' . $this->student->id : ''),
            'nisn' => 'nullable|string|max:10',
            'name' => 'required|string|max:100',
            'unit_id' => 'required|exists:units,id',
            'status' => 'required|in:aktif,lulus,pindah,dikeluarkan',
            'class_id' => 'nullable|exists:classes,id',
        ];

        $this->validate($rules);

        $data = [
            'nis' => $this->nis,
            'nisn' => $this->nisn ?: null,
            'name' => $this->name,
            'unit_id' => $this->unit_id,
            'status' => $this->status,
        ];

        if ($this->isEditing) {
            $this->student->update($data);
            $student = $this->student;
        } else {
            $student = Student::create($data);
        }

        // Handle class enrollment
        $activeYear = AcademicYear::active();
        if ($activeYear && $this->class_id) {
            $student->classes()->syncWithPivotValues(
                [$this->class_id],
                [
                    'academic_year_id' => $activeYear->id,
                    'enrolled_at' => today()->format('Y-m-d'),
                ]
            );
        }

        $this->dispatch('notify', type: 'success', message: $this->isEditing ? 'Santri berhasil diperbarui.' : 'Santri baru berhasil ditambahkan.');
        $this->redirect(route('students.index'), navigate: true);
    }

    public function render()
    {
        $user = Auth::user();
        $activeYear = AcademicYear::active();

        // Get available classes for this unit
        $classes = collect();
        if ($this->unit_id && $activeYear) {
            $classes = Classes::where('unit_id', $this->unit_id)
                ->where('academic_year_id', $activeYear->id)
                ->orderBy('name')
                ->get();
        }

        $units = $user->isSuperadmin() ? Unit::all() : collect();

        return view('livewire.master.student-form', [
            'classes' => $classes,
            'units' => $units,
            'isSuperadmin' => $user->isSuperadmin(),
        ]);
    }
}

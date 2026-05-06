<?php

namespace App\Livewire\Master;

use App\Models\AcademicYear;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Data Santri')]
class StudentIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $search = '';
    public string $statusFilter = 'aktif';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function confirmDelete(int $id): void
    {
        Gate::authorize('delete-student');
        $student = Student::findOrFail($id);
        $this->dispatch('confirm-delete', id: $id, name: $student->name);
    }

    public function delete(int $id): void
    {
        Gate::authorize('delete-student');
        Student::findOrFail($id)->delete();
        $this->dispatch('notify', type: 'success', message: 'Santri berhasil dihapus.');
    }

    public function render()
    {
        $activeYear = AcademicYear::active();

        $students = Student::query()
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('name', 'like', "%{$this->search}%")
                        ->orWhere('nis', 'like', "%{$this->search}%")
                        ->orWhere('nisn', 'like', "%{$this->search}%");
                });
            })
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->with(['classes' => function ($q) use ($activeYear) {
                if ($activeYear) {
                    $q->wherePivot('academic_year_id', $activeYear->id);
                }
            }])
            ->orderBy('name')
            ->paginate(25);

        return view('livewire.master.student-index', [
            'students' => $students,
            'canDelete' => Gate::allows('delete-student'),
        ]);
    }
}

<?php

namespace App\Livewire\Uks;

use App\Models\Student;
use App\Models\StudentLeave;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('UKS & Izin Santri')]
class LeaveForm extends Component
{
    public string $search = '';
    public ?int $selectedStudentId = null;
    public string $leaveStatus = 'sakit';
    public string $leaveNotes = '';
    public string $dateFilter = '';

    public function mount(): void
    {
        Gate::authorize('manage-leaves');
        $this->dateFilter = today()->format('Y-m-d');
    }

    public function selectStudent(int $id): void
    {
        $this->selectedStudentId = $id;
        $this->search = '';
    }

    public function submitLeave(): void
    {
        Gate::authorize('manage-leaves');

        $this->validate([
            'selectedStudentId' => 'required|exists:students,id',
            'leaveStatus' => 'required|in:sakit,izin',
            'leaveNotes' => 'nullable|string|max:500',
        ]);

        $student = Student::findOrFail($this->selectedStudentId);

        // Check duplicate
        $exists = StudentLeave::where('student_id', $student->id)
            ->whereDate('date', today())
            ->exists();

        if ($exists) {
            $this->dispatch('notify', type: 'warning', message: $student->name . ' sudah tercatat izin/sakit hari ini.');
            return;
        }

        StudentLeave::create([
            'unit_id' => $student->unit_id,
            'student_id' => $student->id,
            'date' => today()->format('Y-m-d'),
            'status' => $this->leaveStatus,
            'notes' => $this->leaveNotes ?: null,
            'created_by' => Auth::id(),
        ]);

        $this->reset(['selectedStudentId', 'leaveNotes', 'leaveStatus']);
        $this->leaveStatus = 'sakit';
        $this->dispatch('notify', type: 'success', message: 'Izin ' . $student->name . ' berhasil dicatat.');
    }

    public function removeLeave(int $id): void
    {
        Gate::authorize('manage-leaves');
        $leave = StudentLeave::findOrFail($id);
        $name = $leave->student->name;
        $leave->delete();
        $this->dispatch('notify', type: 'success', message: 'Izin ' . $name . ' berhasil dibatalkan.');
    }

    public function render()
    {
        // Search suggestions
        $suggestions = collect();
        if (strlen($this->search) >= 2) {
            $suggestions = Student::active()
                ->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                      ->orWhere('nis', 'like', "%{$this->search}%");
                })
                ->limit(10)
                ->get(['id', 'name', 'nis']);
        }

        // Today's leaves
        $todayLeaves = StudentLeave::with('student')
            ->whereDate('date', $this->dateFilter)
            ->orderBy('created_at', 'desc')
            ->get();

        $selectedStudent = $this->selectedStudentId
            ? Student::find($this->selectedStudentId)
            : null;

        return view('livewire.uks.leave-form', [
            'suggestions' => $suggestions,
            'todayLeaves' => $todayLeaves,
            'selectedStudent' => $selectedStudent,
        ]);
    }
}

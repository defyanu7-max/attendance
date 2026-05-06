<?php

namespace App\Livewire\Academic;

use App\Models\AcademicYear;
use App\Models\Schedule;
use App\Models\ScheduleSubstitution;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Guru Badal')]
class SubstitutionForm extends Component
{
    public ?int $schedule_id = null;
    public ?int $substitute_user_id = null;
    public string $date = '';
    public string $notes = '';

    public bool $showForm = false;

    public function mount(): void
    {
        Gate::authorize('manage-master-data');
        $this->date = today()->format('Y-m-d');
    }

    public function openCreate(): void
    {
        $this->reset(['schedule_id', 'substitute_user_id', 'notes']);
        $this->date = today()->format('Y-m-d');
        $this->showForm = true;
    }

    public function save(): void
    {
        Gate::authorize('manage-master-data');

        $this->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'substitute_user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'notes' => 'nullable|string|max:500',
        ]);

        ScheduleSubstitution::updateOrCreate(
            ['schedule_id' => $this->schedule_id, 'date' => $this->date],
            [
                'substitute_user_id' => $this->substitute_user_id,
                'notes' => $this->notes ?: null,
            ]
        );

        $this->reset(['schedule_id', 'substitute_user_id', 'notes', 'showForm']);
        $this->dispatch('notify', type: 'success', message: 'Guru badal berhasil dicatat.');
    }

    public function cancelForm(): void
    {
        $this->reset(['schedule_id', 'substitute_user_id', 'notes', 'showForm']);
    }

    public function confirmDelete(int $id): void
    {
        Gate::authorize('manage-master-data');
        $sub = ScheduleSubstitution::with(['schedule.subject', 'substituteUser'])->findOrFail($id);
        $name = "Guru Badal: " . $sub->substituteUser->name . " (Mapel " . $sub->schedule->subject->name . ")";
        $this->dispatch('confirm-delete', id: $id, name: $name);
    }

    public function delete(int $id): void
    {
        Gate::authorize('manage-master-data');
        ScheduleSubstitution::findOrFail($id)->delete();
        $this->dispatch('notify', type: 'success', message: 'Guru badal berhasil dihapus.');
    }

    public function render()
    {
        $activeYear = AcademicYear::active();

        // Get today's & future substitutions
        $substitutions = ScheduleSubstitution::with([
            'schedule.subject', 'schedule.class_', 'schedule.teacher', 'substituteUser'
        ])
            ->whereDate('date', '>=', today()->subDays(7))
            ->orderBy('date', 'desc')
            ->get();

        // Schedules for today (based on day_of_week)
        $schedules = collect();
        if ($activeYear) {
            $dayOfWeek = (int) now()->dayOfWeek;
            if ($this->date) {
                $dayOfWeek = (int) \Carbon\Carbon::parse($this->date)->dayOfWeek;
            }
            $schedules = Schedule::with(['subject', 'class_', 'teacher'])
                ->where('academic_year_id', $activeYear->id)
                ->where('day_of_week', $dayOfWeek)
                ->orderBy('start_time')
                ->get();
        }

        // Available substitute teachers
        $user = Auth::user();
        $teachers = User::whereIn('role', ['guru', 'walikelas', 'admin'])
            ->when($user->unit_id, fn($q) => $q->where('unit_id', $user->unit_id))
            ->orderBy('name')
            ->get();

        return view('livewire.academic.substitution-form', [
            'substitutions' => $substitutions,
            'schedules' => $schedules,
            'teachers' => $teachers,
        ]);
    }
}

<?php

namespace App\Livewire\Academic;

use App\Models\Subject;
use App\Models\Unit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Mata Pelajaran')]
class SubjectIndex extends Component
{
    public string $search = '';

    // Form fields for create/edit
    public ?int $editingId = null;
    public string $code = '';
    public string $name = '';
    public ?int $unit_id = null;

    public bool $showForm = false;

    public function mount(): void
    {
        $user = Auth::user();
        if ($user->unit_id) {
            $this->unit_id = $user->unit_id;
        }
    }

    public function openCreate(): void
    {
        Gate::authorize('manage-master-data');
        $this->reset(['editingId', 'code', 'name']);
        $user = Auth::user();
        $this->unit_id = $user->unit_id;
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        Gate::authorize('manage-master-data');
        $subject = Subject::findOrFail($id);
        $this->editingId = $subject->id;
        $this->code = $subject->code;
        $this->name = $subject->name;
        $this->unit_id = $subject->unit_id;
        $this->showForm = true;
    }

    public function save(): void
    {
        Gate::authorize('manage-master-data');

        $this->validate([
            'code' => 'required|string|max:10',
            'name' => 'required|string|max:100',
            'unit_id' => 'required|exists:units,id',
        ]);

        Subject::updateOrCreate(
            ['id' => $this->editingId],
            [
                'code' => strtoupper($this->code),
                'name' => $this->name,
                'unit_id' => $this->unit_id,
            ]
        );

        $this->reset(['editingId', 'code', 'name', 'showForm']);
        $this->dispatch('notify', type: 'success', message: 'Mata pelajaran berhasil disimpan.');
    }

    public function cancelEdit(): void
    {
        $this->reset(['editingId', 'code', 'name', 'showForm']);
    }

    public function confirmDelete(int $id): void
    {
        Gate::authorize('manage-master-data');
        $subject = Subject::findOrFail($id);
        $this->dispatch('confirm-delete', id: $id, name: $subject->name);
    }

    public function delete(int $id): void
    {
        Gate::authorize('manage-master-data');
        Subject::findOrFail($id)->delete();
        $this->dispatch('notify', type: 'success', message: 'Mata pelajaran berhasil dihapus.');
    }

    public function render()
    {
        $subjects = Subject::query()
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%")
                ->orWhere('code', 'like', "%{$this->search}%"))
            ->with('unit')
            ->orderBy('code')
            ->get();

        $canManage = Gate::allows('manage-master-data');
        $units = $canManage ? Unit::all() : collect();

        return view('livewire.academic.subject-index', [
            'subjects' => $subjects,
            'canManage' => $canManage,
            'units' => $units,
        ]);
    }
}

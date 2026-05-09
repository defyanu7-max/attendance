<?php

namespace App\Livewire\Master;

use App\Models\User;
use App\Models\Unit;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
#[Title('Manajemen Guru')]
class TeacherIndex extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $unitId = '';
    public $perPage = 10;
    public $importFile;

    protected $paginationTheme = 'bootstrap';

    public function mount()
    {
        Gate::authorize('manage-master-data');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingUnitId()
    {
        $this->resetPage();
    }

    #[On('delete-teacher')]
    public function delete($id)
    {
        Gate::authorize('manage-system'); // Hanya Superadmin atau sesuaikan kebijakan

        $teacher = User::findOrFail($id);
        if ($teacher->role === 'guru') {
            $teacher->delete();
            $this->dispatch('notify', type: 'success', message: 'Data guru berhasil dihapus.');
        }
    }

    public function render()
    {
        $query = User::where('role', 'guru')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('username', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->unitId, function ($query) {
                $query->where('unit_id', $this->unitId);
            });

        // Untuk Superadmin tampilkan semua, admin unit tampilkan hanya unitnya
        $user = Auth::user();
        if ($user->role !== 'superadmin' && $user->unit_id) {
            $query->where('unit_id', $user->unit_id);
        }

        $teachers = $query->latest()->paginate($this->perPage);
        $units = Unit::all();

        return view('livewire.master.teacher-index', [
            'teachers' => $teachers,
            'units' => $units,
        ]);
    }
}

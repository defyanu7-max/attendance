<?php

namespace App\Livewire\Master;

use App\Models\User;
use App\Models\Unit;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class TeacherForm extends Component
{
    public ?User $teacher = null;

    public $name = '';
    public $username = '';
    public $password = '';
    public $phone = '';
    public $unit_id = '';

    public $isEdit = false;

    public function mount(?User $teacher = null)
    {
        Gate::authorize('manage-master-data');

        $user = Auth::user();
        if ($teacher && $teacher->exists) {
            $this->teacher = $teacher;
            $this->name = $teacher->name;
            $this->username = $teacher->username;
            $this->phone = $teacher->phone;
            $this->unit_id = $teacher->unit_id;
            $this->isEdit = true;
        } else {
            // Default unit for admin
            if ($user->role !== 'superadmin' && $user->unit_id) {
                $this->unit_id = $user->unit_id;
            }
        }
    }

    public function save()
    {
        Gate::authorize('manage-master-data');

        $rules = [
            'name' => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users,username' . ($this->isEdit ? ',' . $this->teacher->id : ''),
            'unit_id' => 'required|exists:units,id',
            'phone' => 'nullable|string|max:20',
        ];

        if (!$this->isEdit || $this->password) {
            $rules['password'] = 'required|min:6';
        }

        $this->validate($rules);

        $data = [
            'name' => $this->name,
            'username' => $this->username,
            'unit_id' => $this->unit_id,
            'phone' => $this->phone,
            'role' => 'guru',
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        if ($this->isEdit) {
            $this->teacher->update($data);
            session()->flash('message', 'Data guru berhasil diperbarui.');
        } else {
            User::create($data);
            $this->dispatch('notify', type: 'success', message: 'Data guru berhasil ditambahkan');
        }

        return redirect()->route('teachers.index');
    }

    #[Title('Form Guru')]
    public function render()
    {
        $units = Unit::all();
        $user = Auth::user();

        if ($user->role !== 'superadmin' && $user->unit_id) {
            $units = Unit::where('id', $user->unit_id)->get();
        }

        return view('livewire.master.teacher-form', [
            'units' => $units
        ]);
    }
}

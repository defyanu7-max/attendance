<?php

namespace App\Livewire\System;

use App\Models\AcademicYear;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Manajemen Tahun Ajaran')]
class AcademicYearManager extends Component
{
    // Form fields
    public string $formName      = '';
    public string $formStartDate = '';
    public string $formEndDate   = '';
    public bool   $formIsActive  = false;

    // State
    public bool $modalOpen = false;
    public ?int $editingId  = null;

    public function mount(): void
    {
        Gate::authorize('manage-system');
    }

    public function openCreate(): void
    {
        Gate::authorize('manage-system');

        $this->editingId     = null;
        $this->formName      = '';
        $this->formStartDate = '';
        $this->formEndDate   = '';
        $this->formIsActive  = false;
        $this->resetValidation();
        $this->modalOpen = true;
    }

    public function openEdit(int $id): void
    {
        Gate::authorize('manage-system');

        $year = AcademicYear::findOrFail($id);
        $this->editingId     = $id;
        $this->formName      = $year->name;
        $this->formStartDate = $year->start_date->format('Y-m-d');
        $this->formEndDate   = $year->end_date->format('Y-m-d');
        $this->formIsActive  = $year->is_active;
        $this->resetValidation();
        $this->modalOpen = true;
    }

    public function closeModal(): void
    {
        $this->modalOpen = false;
        $this->editingId = null;
        $this->resetValidation();
    }

    public function save(): void
    {
        Gate::authorize('manage-system');

        $this->validate([
            'formName'      => 'required|string|max:20|unique:academic_years,name,' . ($this->editingId ?? 'NULL'),
            'formStartDate' => 'required|date',
            'formEndDate'   => 'required|date|after_or_equal:formStartDate',
        ]);

        DB::transaction(function () {
            // Jika tahun baru ini diaktifkan, nonaktifkan semua yang lain
            if ($this->formIsActive) {
                AcademicYear::where('id', '!=', $this->editingId ?? 0)
                    ->update(['is_active' => false]);
            }

            AcademicYear::updateOrCreate(
                ['id' => $this->editingId],
                [
                    'name'       => trim($this->formName),
                    'start_date' => $this->formStartDate,
                    'end_date'   => $this->formEndDate,
                    'is_active'  => $this->formIsActive,
                ]
            );
        });

        $this->closeModal();
        $this->dispatch('notify', type: 'success', message: 'Tahun ajaran berhasil disimpan.');
    }

    /**
     * Aktifkan tahun ajaran dan nonaktifkan yang lain.
     * Dipanggil dari tombol "Aktifkan" di tabel.
     */
    public function activate(int $id): void
    {
        Gate::authorize('manage-system');

        DB::transaction(function () use ($id) {
            AcademicYear::query()->update(['is_active' => false]);
            AcademicYear::findOrFail($id)->update(['is_active' => true]);
        });

        $this->dispatch('notify', type: 'success', message: 'Tahun ajaran berhasil diaktifkan.');
    }

    /**
     * Hapus tahun ajaran.
     * Guard: tidak boleh menghapus tahun yang aktif.
     */
    public function delete(int $id): void
    {
        Gate::authorize('manage-system');

        $year = AcademicYear::findOrFail($id);

        if ($year->is_active) {
            $this->dispatch('notify', type: 'error', message: 'Tidak dapat menghapus tahun ajaran yang sedang aktif.');
            return;
        }

        // Guard: jika ada kelas/jadwal yang terikat
        $hasData = $year->schedules()->exists() || $year->classes()->exists();
        if ($hasData) {
            $this->dispatch('notify', type: 'warning', message: 'Tahun ajaran ini masih memiliki data jadwal atau kelas. Hapus data terkait terlebih dahulu.');
            return;
        }

        $year->delete();
        $this->dispatch('notify', type: 'success', message: 'Tahun ajaran berhasil dihapus.');
    }

    public function render()
    {
        $years = AcademicYear::orderByDesc('start_date')->get();

        return view('livewire.system.academic-year-manager', compact('years'));
    }
}

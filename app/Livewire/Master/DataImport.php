<?php

namespace App\Livewire\Master;

use App\Models\AcademicYear;
use App\Models\Classes;
use App\Models\Student;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
#[Title('Import Data Massal')]
class DataImport extends Component
{
    use WithFileUploads;

    public $file;

    #[Url]
    public string $type = 'students'; // students | teachers

    public ?int $unitId = null;

    public function mount()
    {
        Gate::authorize('manage-master-data');
    }

    public function import()
    {
        $this->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:5120',
            'type' => 'required|in:students,teachers',
        ]);

        $user = Auth::user();
        $userUnitId = $user->unit_id ?? null;
        $firstUnitId = Unit::first()?->id;
        $uId = $this->unitId ?? $userUnitId ?? $firstUnitId;

        if (!$uId) {
            $this->dispatch('notify', type: 'error', message: 'Tidak ada unit tersedia. Silakan tambah unit terlebih dahulu.');
            return;
        }

        $filePath = $this->file->getRealPath();

        if ($this->type === 'students') {
            $this->importStudents($filePath, $uId);
        } elseif ($this->type === 'teachers') {
            $this->importTeachers($filePath, $uId);
        }
    }

    private function importStudents($filePath, $uId)
    {
        $activeYear = AcademicYear::active();
        if (!$activeYear) {
            $this->dispatch('notify', type: 'error', message: 'Tidak ada Tahun Ajaran aktif.');
            return;
        }

        $classes = Classes::withoutGlobalScopes()
            ->where('unit_id', $uId)
            ->where('academic_year_id', $activeYear->id)
            ->get()
            ->mapWithKeys(fn($c) => [mb_strtolower($c->name) => $c->id])
            ->all();

        DB::beginTransaction();
        try {
            $created = 0;
            $updated = 0;
            $line = 1;

            (new FastExcel)->import($filePath, function ($row) use (&$line, &$created, &$updated, $uId, $activeYear, $classes) {
                $line++;

                if (empty($row['nis']) || empty($row['nama'])) {
                    throw new \Exception("Gagal di baris ke-{$line}: NIS dan Nama wajib diisi.");
                }

                $nis = $row['nis'];
                $student = Student::withoutGlobalScopes()->where('nis', $nis)->first();

                if ($student) {
                    $student->update([
                        'nisn' => $row['nisn'] ?? $student->nisn,
                        'name' => $row['nama'],
                        'unit_id' => $uId,
                    ]);
                    $updated++;
                } else {
                    $student = Student::withoutGlobalScopes()->create([
                        'nis' => $nis,
                        'nisn' => $row['nisn'] ?? null,
                        'name' => $row['nama'],
                        'unit_id' => $uId,
                        'status' => 'aktif'
                    ]);
                    $created++;
                }

                $namaKelas = mb_strtolower($row['nama_kelas'] ?? '');
                if ($namaKelas && isset($classes[$namaKelas])) {
                    $student->classes()->syncWithoutDetaching([
                        $classes[$namaKelas] => [
                            'academic_year_id' => $activeYear->id,
                            'enrolled_at' => now(),
                        ]
                    ]);
                }
            });

            DB::commit();
            $this->reset('file');
            $this->dispatch('notify', type: 'success', message: "Import Santri Berhasil: {$created} baru, {$updated} update.");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('notify', type: 'error', message: $e->getMessage());
        }
    }

    private function importTeachers($filePath, $uId)
    {
        DB::beginTransaction();
        try {
            $created = 0;
            $updated = 0;
            $line = 1;

            (new FastExcel)->import($filePath, function ($row) use (&$line, &$created, &$updated, $uId) {
                $line++;

                if (empty($row['nama']) || empty($row['username'])) {
                    throw new \Exception("Gagal di baris ke-{$line}: Nama dan Username wajib diisi.");
                }

                $username = mb_strtolower($row['username']);
                $user = User::withTrashed()->where('username', $username)->first();

                $data = [
                    'name' => $row['nama'],
                    'role' => 'guru',
                    'unit_id' => $uId,
                    'phone' => $row['phone'] ?? null,
                ];

                if ($user) {
                    if (!empty($row['password_default'])) {
                        $data['password'] = Hash::make($row['password_default']);
                    }
                    $user->update($data);
                    if ($user->trashed()) $user->restore();
                    $updated++;
                } else {
                    $data['username'] = $username;
                    $data['password'] = Hash::make($row['password_default'] ?: 'password');
                    User::create($data);
                    $created++;
                }
            });

            DB::commit();
            $this->reset('file');
            $this->dispatch('notify', type: 'success', message: "Import Guru Berhasil: {$created} baru, {$updated} update.");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('notify', type: 'error', message: $e->getMessage());
        }
    }

    public function render()
    {
        $user = Auth::user();
        $isSuperadmin = method_exists($user, 'isSuperadmin') && $user->isSuperadmin();
        $units = $isSuperadmin ? Unit::all() : collect();

        return view('livewire.master.data-import', [
            'units' => $units,
            'isSuperadmin' => $isSuperadmin,
        ]);
    }
}

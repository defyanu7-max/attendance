<?php

namespace App\Livewire\Master;

use App\Services\ExcelImportService;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
#[Title('Import Excel')]
class ExcelImport extends Component
{
    use WithFileUploads;

    public $file;
    public string $importType = 'students'; // students | teachers | schedules

    // Preview state
    public bool $showPreview = false;
    public array $previewData = [];
    public array $previewErrors = [];
    public int $totalRows = 0;
    public int $validRows = 0;

    // Result state
    public bool $showResult = false;
    public bool $importSuccess = false;
    public string $resultMessage = '';
    public int $createdCount = 0;
    public int $updatedCount = 0;

    public function mount(): void
    {
        Gate::authorize('manage-master-data');
    }

    public function updatedImportType(): void
    {
        $this->resetPreview();
    }

    public function updatedFile(): void
    {
        $this->resetPreview();
    }

    public function preview(): void
    {
        Gate::authorize('manage-master-data');

        $this->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:5120', // 5MB max
            'importType' => 'required|in:students,teachers,schedules',
        ], [
            'file.required' => 'File Excel wajib dipilih.',
            'file.mimes' => 'File harus berformat .xlsx atau .xls.',
            'file.max' => 'Ukuran file maksimal 5MB.',
        ]);

        $service = new ExcelImportService();
        $filePath = $this->file->getRealPath();

        try {
            $result = match ($this->importType) {
                'students'  => $service->parseStudents($filePath),
                'teachers'  => $service->parseTeachers($filePath),
                'schedules' => $service->parseSchedules($filePath),
            };

            $this->previewData = $result['data'];
            $this->previewErrors = $result['errors'];
            $this->totalRows = $result['total'];
            $this->validRows = $result['valid'];
            $this->showPreview = true;
            $this->showResult = false;
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: 'Gagal membaca file: ' . $e->getMessage());
        }
    }

    public function import(): void
    {
        Gate::authorize('manage-master-data');

        if (empty($this->previewData) || $this->validRows === 0) {
            $this->dispatch('notify', type: 'error', message: 'Tidak ada data valid untuk diimport.');
            return;
        }

        $service = new ExcelImportService();

        try {
            $result = match ($this->importType) {
                'students'  => $service->importStudents($this->previewData),
                'teachers'  => $service->importTeachers($this->previewData),
                'schedules' => $service->importSchedules($this->previewData),
            };

            if ($result['success']) {
                $this->importSuccess = true;
                $this->createdCount = $result['created'];
                $this->updatedCount = $result['updated'];
                $this->resultMessage = "Import berhasil! {$result['created']} data baru, {$result['updated']} data diperbarui.";
                $this->showResult = true;
                $this->showPreview = false;
                $this->dispatch('notify', type: 'success', message: $this->resultMessage);
            } else {
                $this->importSuccess = false;
                $this->resultMessage = 'Import gagal: ' . ($result['message'] ?? 'Unknown error');
                $this->showResult = true;
                $this->dispatch('notify', type: 'error', message: $this->resultMessage);
            }
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: 'Import gagal: ' . $e->getMessage());
        }
    }

    public function resetPreview(): void
    {
        $this->showPreview = false;
        $this->showResult = false;
        $this->previewData = [];
        $this->previewErrors = [];
        $this->totalRows = 0;
        $this->validRows = 0;
    }

    public function resetAll(): void
    {
        $this->reset(['file', 'importType', 'showPreview', 'showResult', 'previewData', 'previewErrors', 'totalRows', 'validRows', 'importSuccess', 'resultMessage', 'createdCount', 'updatedCount']);
        $this->importType = 'students';
    }

    public function render()
    {
        return view('livewire.master.excel-import');
    }
}

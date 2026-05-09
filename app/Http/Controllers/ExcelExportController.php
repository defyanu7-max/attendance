<?php

namespace App\Http\Controllers;

use App\Services\ExcelExportService;
use Illuminate\Support\Facades\Gate;

class ExcelExportController extends Controller
{
    public function __construct(
        private ExcelExportService $excelService
    ) {}

    /**
     * Export Students template/data.
     */
    public function students()
    {
        Gate::authorize('manage-master-data');
        return $this->excelService->exportStudents();
    }

    /**
     * Export Teachers template/data.
     */
    public function teachers()
    {
        Gate::authorize('manage-master-data');
        return $this->excelService->exportTeachers();
    }

    /**
     * Export Schedules template/data.
     */
    public function schedules()
    {
        Gate::authorize('manage-master-data');
        return $this->excelService->exportSchedules();
    }

    public function studentsTemplate()
    {
        Gate::authorize('manage-master-data');
        $list = collect([
            ['nis' => '123456', 'nisn' => '0012345678', 'nama' => 'Fulan bin Fulan', 'nama_kelas' => '10 A']
        ]);
        return (new \Rap2hpoutre\FastExcel\FastExcel($list))->download('template_santri.xlsx');
    }

    public function teachersTemplate()
    {
        Gate::authorize('manage-master-data');
        $list = collect([
            ['nama' => 'Ustadz Ali', 'username' => 'ali.guru', 'password_default' => 'password123', 'phone' => '08123456789']
        ]);
        return (new \Rap2hpoutre\FastExcel\FastExcel($list))->download('template_guru.xlsx');
    }
}

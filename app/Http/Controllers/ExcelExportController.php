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
}

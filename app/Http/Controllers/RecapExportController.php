<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Classes;
use App\Models\Schedule;
use App\Models\Setting;
use App\Models\StudentAttendance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Rap2hpoutre\FastExcel\FastExcel;

class RecapExportController extends Controller
{
    /**
     * Export recap data for a specific class as Excel.
     */
    public function __invoke(Request $request)
    {
        $classId = $request->query('classId');
        $month   = $request->query('month', '');

        if (! $classId) {
            abort(400, 'classId is required');
        }

        $selectedClass = Classes::with(['homeroomTeacher', 'unit'])->findOrFail($classId);

        // Authorize
        Gate::authorize('view-class-recap', $selectedClass);

        $activeYear = AcademicYear::active();
        if (! $activeYear) {
            abort(404, 'No active academic year');
        }

        // Determine date range
        if ($month !== '') {
            $monthDate = Carbon::createFromFormat('Y-m', $month);
            $dateFrom  = $monthDate->copy()->startOfMonth()->format('Y-m-d');
            $dateTo    = $monthDate->copy()->endOfMonth()->format('Y-m-d');
        } else {
            $dateFrom = $activeYear->start_date->format('Y-m-d');
            $dateTo   = min($activeYear->end_date, now())->format('Y-m-d');
        }

        // Get students
        $students = $selectedClass->students()
            ->wherePivot('academic_year_id', $activeYear->id)
            ->where('students.status', 'aktif')
            ->orderBy('students.name')
            ->get();

        if ($students->isEmpty()) {
            abort(404, 'No students in this class');
        }

        $studentIds = $students->pluck('id');

        // Get all attendances
        $attendances = StudentAttendance::whereIn('student_id', $studentIds)
            ->where('academic_year_id', $activeYear->id)
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->get()
            ->groupBy('student_id');

        $alphaThreshold = (int) Setting::getValue('alpha_threshold_count', 5);

        // Build export rows
        $exportData = $students->map(function ($student) use ($attendances, $alphaThreshold) {
            $att = $attendances->get($student->id, collect());

            $total  = $att->count();
            $hadir  = $att->where('status', 'hadir')->count();
            $alpha  = $att->where('status', 'alpha')->count();
            $sakit  = $att->where('status', 'sakit')->count();
            $izin   = $att->where('status', 'izin')->count();
            $pct    = $total > 0 ? round(($hadir / $total) * 100, 1) : 0;

            return [
                'Nama Santri'    => $student->name,
                'NIS'            => $student->nis,
                'Total Sesi'     => $total,
                'Hadir'          => $hadir,
                'Alpha'          => $alpha,
                'Sakit'          => $sakit,
                'Izin'           => $izin,
                '% Kehadiran'    => $pct . '%',
                'Status'         => $alpha >= $alphaThreshold ? 'KRITIS' : 'Normal',
            ];
        });

        $filename = 'Rekap_' . str_replace(' ', '_', $selectedClass->name);
        if ($month !== '') {
            $filename .= '_' . $month;
        }
        $filename .= '.xlsx';

        return (new FastExcel($exportData))->download($filename);
    }
}

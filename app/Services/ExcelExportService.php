<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\Classes;
use App\Models\Schedule;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExcelExportService
{
    /**
     * Convert column index (1-based) to letter (A, B, C, ..., AA, AB...).
     */
    private function colLetter(int $col): string
    {
        $letter = '';
        while ($col > 0) {
            $col--;
            $letter = chr(65 + ($col % 26)) . $letter;
            $col = intdiv($col, 26);
        }
        return $letter;
    }

    /**
     * Set cell value using column number (1-based) and row.
     */
    private function setCellByCol($sheet, int $col, int $row, mixed $value): void
    {
        $cell = $this->colLetter($col) . $row;
        $sheet->setCellValue($cell, $value);
    }

    /**
     * Apply standard header styling to a range.
     */
    private function applyHeaderStyle(Spreadsheet $spreadsheet, string $range): void
    {
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getStyle($range)->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF4D44B5'], // PPNI Primary Purple
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF303972'],
                ],
            ],
        ]);
    }

    /**
     * Apply data area styling to a range.
     */
    private function applyDataStyle(Spreadsheet $spreadsheet, string $range): void
    {
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getStyle($range)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FFD0D0D0'],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
    }

    /**
     * Apply info/instruction row styling.
     */
    private function applyInfoStyle(Spreadsheet $spreadsheet, string $range): void
    {
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getStyle($range)->applyFromArray([
            'font' => [
                'italic' => true,
                'color' => ['argb' => 'FF666666'],
                'size' => 9,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFFFF3CD'], // Light yellow
            ],
        ]);
    }

    /**
     * Add a title row to the sheet.
     */
    private function addTitleRow(Spreadsheet $spreadsheet, string $title, string $range): void
    {
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', $title);
        $sheet->mergeCells($range);
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14,
                'color' => ['argb' => 'FF4D44B5'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);
    }

    /**
     * Export Students data to Excel.
     */
    public function exportStudents(): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Santri');

        $user = Auth::user();

        // Title row
        $this->addTitleRow($spreadsheet, 'Data Santri — PPNI System', 'A1:G1');

        // Instruction row
        $sheet->setCellValue('A2', 'Instruksi: Isi data di bawah header. Kolom bertanda (*) wajib diisi. Jangan ubah header.');
        $sheet->mergeCells('A2:G2');
        $this->applyInfoStyle($spreadsheet, 'A2:G2');
        $sheet->getRowDimension(2)->setRowHeight(22);

        // Headers (row 3)
        $headers = ['No', 'NIS *', 'NISN', 'Nama Santri *', 'Kelas *', 'Status *', 'Unit *'];
        foreach ($headers as $col => $header) {
            $this->setCellByCol($sheet, $col + 1, 3, $header);
        }
        $this->applyHeaderStyle($spreadsheet, 'A3:G3');
        $sheet->getRowDimension(3)->setRowHeight(25);

        // Column widths
        $sheet->getColumnDimension('A')->setWidth(6);  // No
        $sheet->getColumnDimension('B')->setWidth(16); // NIS
        $sheet->getColumnDimension('C')->setWidth(18); // NISN
        $sheet->getColumnDimension('D')->setWidth(30); // Nama
        $sheet->getColumnDimension('E')->setWidth(15); // Kelas
        $sheet->getColumnDimension('F')->setWidth(14); // Status
        $sheet->getColumnDimension('G')->setWidth(18); // Unit

        // Fill existing data
        $students = Student::with(['unit'])
            ->when($user->unit_id, fn($q) => $q->where('unit_id', $user->unit_id))
            ->orderBy('name')
            ->get();

        $row = 4;
        foreach ($students as $i => $student) {
            $currentClass = $student->currentClass();
            $this->setCellByCol($sheet, 1, $row, $i + 1);
            $this->setCellByCol($sheet, 2, $row, $student->nis);
            $this->setCellByCol($sheet, 3, $row, $student->nisn ?? '');
            $this->setCellByCol($sheet, 4, $row, $student->name);
            $this->setCellByCol($sheet, 5, $row, $currentClass?->name ?? '');
            $this->setCellByCol($sheet, 6, $row, $student->status);
            $this->setCellByCol($sheet, 7, $row, $student->unit->name);
            $row++;
        }

        // Add empty rows for new entries
        $emptyStart = $row;
        for ($i = 0; $i < 20; $i++) {
            $this->setCellByCol($sheet, 1, $row, $students->count() + $i + 1);
            $row++;
        }

        // Apply data styling
        $lastRow = $row - 1;
        if ($lastRow >= 4) {
            $this->applyDataStyle($spreadsheet, "A4:G{$lastRow}");

            // Highlight empty rows with light blue
            if ($emptyStart <= $lastRow) {
                $sheet->getStyle("A{$emptyStart}:G{$lastRow}")->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFEBF5FF'],
                    ],
                ]);
            }
        }

        // Reference sheet for valid values
        $refSheet = $spreadsheet->createSheet();
        $refSheet->setTitle('Referensi');
        $refSheet->setCellValue('A1', 'Kelas Tersedia');
        $refSheet->setCellValue('B1', 'Unit Tersedia');
        $refSheet->setCellValue('C1', 'Status Valid');
        $refSheet->getStyle('A1:C1')->getFont()->setBold(true);

        $classes = Classes::when($user->unit_id, fn($q) => $q->where('unit_id', $user->unit_id))->orderBy('name')->get();
        foreach ($classes as $i => $class) {
            $refSheet->setCellValue('A' . ($i + 2), $class->name);
        }

        $units = Unit::orderBy('name')->get();
        foreach ($units as $i => $unit) {
            $refSheet->setCellValue('B' . ($i + 2), $unit->name);
        }

        $statuses = ['aktif', 'lulus', 'pindah', 'dikeluarkan'];
        foreach ($statuses as $i => $status) {
            $refSheet->setCellValue('C' . ($i + 2), $status);
        }

        $spreadsheet->setActiveSheetIndex(0);

        return $this->streamDownload($spreadsheet, 'Data_Santri_' . now()->format('Ymd_His') . '.xlsx');
    }

    /**
     * Export Teachers/Users data to Excel.
     */
    public function exportTeachers(): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Guru');

        $user = Auth::user();

        // Title row
        $this->addTitleRow($spreadsheet, 'Data Guru & Staff — PPNI System', 'A1:G1');

        // Instruction row
        $sheet->setCellValue('A2', 'Instruksi: Isi data di bawah header. Kolom bertanda (*) wajib diisi. Password default: "password".');
        $sheet->mergeCells('A2:G2');
        $this->applyInfoStyle($spreadsheet, 'A2:G2');
        $sheet->getRowDimension(2)->setRowHeight(22);

        // Headers (row 3)
        $headers = ['No', 'Nama Lengkap *', 'Username *', 'Role *', 'Unit *', 'No. HP', 'Password'];
        foreach ($headers as $col => $header) {
            $this->setCellByCol($sheet, $col + 1, 3, $header);
        }
        $this->applyHeaderStyle($spreadsheet, 'A3:G3');
        $sheet->getRowDimension(3)->setRowHeight(25);

        // Column widths
        $sheet->getColumnDimension('A')->setWidth(6);  // No
        $sheet->getColumnDimension('B')->setWidth(30); // Nama
        $sheet->getColumnDimension('C')->setWidth(20); // Username
        $sheet->getColumnDimension('D')->setWidth(14); // Role
        $sheet->getColumnDimension('E')->setWidth(18); // Unit
        $sheet->getColumnDimension('F')->setWidth(18); // HP
        $sheet->getColumnDimension('G')->setWidth(16); // Password

        // Fill existing data
        $teachers = User::with('unit')
            ->whereIn('role', ['admin', 'walikelas', 'guru'])
            ->when($user->unit_id, fn($q) => $q->where('unit_id', $user->unit_id))
            ->orderBy('name')
            ->get();

        $row = 4;
        foreach ($teachers as $i => $teacher) {
            $this->setCellByCol($sheet, 1, $row, $i + 1);
            $this->setCellByCol($sheet, 2, $row, $teacher->name);
            $this->setCellByCol($sheet, 3, $row, $teacher->username);
            $this->setCellByCol($sheet, 4, $row, $teacher->role);
            $this->setCellByCol($sheet, 5, $row, $teacher->unit?->name ?? '');
            $this->setCellByCol($sheet, 6, $row, $teacher->phone ?? '');
            $this->setCellByCol($sheet, 7, $row, ''); // Don't export passwords
            $row++;
        }

        // Add empty rows for new entries
        $emptyStart = $row;
        for ($i = 0; $i < 20; $i++) {
            $this->setCellByCol($sheet, 1, $row, $teachers->count() + $i + 1);
            $row++;
        }

        // Apply data styling
        $lastRow = $row - 1;
        if ($lastRow >= 4) {
            $this->applyDataStyle($spreadsheet, "A4:G{$lastRow}");

            if ($emptyStart <= $lastRow) {
                $sheet->getStyle("A{$emptyStart}:G{$lastRow}")->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFEBF5FF'],
                    ],
                ]);
            }
        }

        // Reference sheet
        $refSheet = $spreadsheet->createSheet();
        $refSheet->setTitle('Referensi');
        $refSheet->setCellValue('A1', 'Role Valid');
        $refSheet->setCellValue('B1', 'Unit Tersedia');
        $refSheet->getStyle('A1:B1')->getFont()->setBold(true);

        $roles = ['admin', 'walikelas', 'guru'];
        foreach ($roles as $i => $role) {
            $refSheet->setCellValue('A' . ($i + 2), $role);
        }

        $units = Unit::orderBy('name')->get();
        foreach ($units as $i => $unit) {
            $refSheet->setCellValue('B' . ($i + 2), $unit->name);
        }

        $spreadsheet->setActiveSheetIndex(0);

        return $this->streamDownload($spreadsheet, 'Data_Guru_' . now()->format('Ymd_His') . '.xlsx');
    }

    /**
     * Export Schedule data to Excel.
     */
    public function exportSchedules(): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Jadwal Pelajaran');

        $user = Auth::user();
        $activeYear = AcademicYear::active();

        // Title row
        $yearName = $activeYear?->name ?? '-';
        $this->addTitleRow($spreadsheet, "Jadwal Pelajaran — TA {$yearName} — PPNI System", 'A1:I1');

        // Instruction row
        $sheet->setCellValue('A2', 'Instruksi: Isi Hari (0=Minggu..6=Sabtu). Gunakan nama Kelas, Mapel, dan Guru sesuai sheet Referensi.');
        $sheet->mergeCells('A2:I2');
        $this->applyInfoStyle($spreadsheet, 'A2:I2');
        $sheet->getRowDimension(2)->setRowHeight(22);

        // Headers (row 3)
        $headers = ['No', 'Hari (0-6) *', 'Nama Hari', 'Kelas *', 'Mata Pelajaran *', 'Guru *', 'Jam Mulai *', 'Jam Selesai *', 'Unit *'];
        foreach ($headers as $col => $header) {
            $this->setCellByCol($sheet, $col + 1, 3, $header);
        }
        $this->applyHeaderStyle($spreadsheet, 'A3:I3');
        $sheet->getRowDimension(3)->setRowHeight(25);

        // Column widths
        $sheet->getColumnDimension('A')->setWidth(6);  // No
        $sheet->getColumnDimension('B')->setWidth(12); // Hari
        $sheet->getColumnDimension('C')->setWidth(12); // Nama Hari
        $sheet->getColumnDimension('D')->setWidth(15); // Kelas
        $sheet->getColumnDimension('E')->setWidth(25); // Mapel
        $sheet->getColumnDimension('F')->setWidth(25); // Guru
        $sheet->getColumnDimension('G')->setWidth(14); // Jam Mulai
        $sheet->getColumnDimension('H')->setWidth(14); // Jam Selesai
        $sheet->getColumnDimension('I')->setWidth(18); // Unit

        $dayNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        // Fill existing data
        $schedules = Schedule::with(['class_', 'subject', 'teacher', 'unit'])
            ->when($activeYear, fn($q) => $q->where('academic_year_id', $activeYear->id))
            ->when($user->unit_id, fn($q) => $q->where('unit_id', $user->unit_id))
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        $row = 4;
        foreach ($schedules as $i => $schedule) {
            $this->setCellByCol($sheet, 1, $row, $i + 1);
            $this->setCellByCol($sheet, 2, $row, $schedule->day_of_week);
            $this->setCellByCol($sheet, 3, $row, $dayNames[$schedule->day_of_week] ?? '');
            $this->setCellByCol($sheet, 4, $row, $schedule->class_?->name ?? '');
            $this->setCellByCol($sheet, 5, $row, $schedule->subject?->name ?? '');
            $this->setCellByCol($sheet, 6, $row, $schedule->teacher?->name ?? '');
            $this->setCellByCol($sheet, 7, $row, substr($schedule->start_time, 0, 5));
            $this->setCellByCol($sheet, 8, $row, substr($schedule->end_time, 0, 5));
            $this->setCellByCol($sheet, 9, $row, $schedule->unit?->name ?? '');
            $row++;
        }

        // Add empty rows
        $emptyStart = $row;
        for ($i = 0; $i < 30; $i++) {
            $this->setCellByCol($sheet, 1, $row, $schedules->count() + $i + 1);
            $row++;
        }

        // Apply data styling
        $lastRow = $row - 1;
        if ($lastRow >= 4) {
            $this->applyDataStyle($spreadsheet, "A4:I{$lastRow}");

            if ($emptyStart <= $lastRow) {
                $sheet->getStyle("A{$emptyStart}:I{$lastRow}")->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFEBF5FF'],
                    ],
                ]);
            }
        }

        // Reference sheet
        $refSheet = $spreadsheet->createSheet();
        $refSheet->setTitle('Referensi');

        $refHeaders = ['Kode Hari', 'Nama Hari', 'Kelas Tersedia', 'Mapel Tersedia', 'Guru Tersedia', 'Unit Tersedia'];
        foreach ($refHeaders as $col => $header) {
            $refSheet->setCellValue($this->colLetter($col + 1) . '1', $header);
        }
        $refSheet->getStyle('A1:F1')->getFont()->setBold(true);

        // Days
        foreach ($dayNames as $i => $day) {
            $refSheet->setCellValue('A' . ($i + 2), $i);
            $refSheet->setCellValue('B' . ($i + 2), $day);
        }

        // Classes
        $classes = Classes::when($user->unit_id, fn($q) => $q->where('unit_id', $user->unit_id))->orderBy('name')->get();
        foreach ($classes as $i => $class) {
            $refSheet->setCellValue('C' . ($i + 2), $class->name);
        }

        // Subjects
        $subjects = Subject::when($user->unit_id, fn($q) => $q->where('unit_id', $user->unit_id))->orderBy('name')->get();
        foreach ($subjects as $i => $subject) {
            $refSheet->setCellValue('D' . ($i + 2), $subject->name);
        }

        // Teachers
        $teachers = User::whereIn('role', ['admin', 'walikelas', 'guru'])
            ->when($user->unit_id, fn($q) => $q->where('unit_id', $user->unit_id))
            ->orderBy('name')
            ->get();
        foreach ($teachers as $i => $teacher) {
            $refSheet->setCellValue('E' . ($i + 2), $teacher->name);
        }

        // Units
        $units = Unit::orderBy('name')->get();
        foreach ($units as $i => $unit) {
            $refSheet->setCellValue('F' . ($i + 2), $unit->name);
        }

        // Auto-size reference columns
        foreach (range('A', 'F') as $col) {
            $refSheet->getColumnDimension($col)->setAutoSize(true);
        }

        $spreadsheet->setActiveSheetIndex(0);

        return $this->streamDownload($spreadsheet, 'Jadwal_Pelajaran_' . now()->format('Ymd_His') . '.xlsx');
    }

    /**
     * Stream the spreadsheet as a download response.
     */
    private function streamDownload(Spreadsheet $spreadsheet, string $filename): StreamedResponse
    {
        $writer = new Xlsx($spreadsheet);

        return new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'max-age=0',
        ]);
    }
}

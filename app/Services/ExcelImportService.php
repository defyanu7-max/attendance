<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\Classes;
use App\Models\Schedule;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ExcelImportService
{
    /**
     * Read and parse an Excel file, skipping title (row 1), instruction (row 2), header (row 3).
     * Data starts at row 4.
     */
    private function readSheet(string $filePath, int $sheetIndex = 0): array
    {
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getSheet($sheetIndex);
        $rows = [];

        foreach ($sheet->getRowIterator(4) as $row) { // Start from row 4
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
            $cells = [];
            foreach ($cellIterator as $cell) {
                $cells[] = trim((string) ($cell->getValue() ?? ''));
            }
            // Skip completely empty rows
            $nonEmpty = array_filter($cells, fn($v) => $v !== '');
            if (count($nonEmpty) > 1) { // at least 2 non-empty cells (No + 1 data)
                $rows[] = $cells;
            }
        }

        return $rows;
    }

    /**
     * Parse student rows and return validated data + errors.
     * Excel columns: No, NIS, NISN, Nama, Kelas, Status, Unit
     */
    public function parseStudents(string $filePath): array
    {
        $rows = $this->readSheet($filePath);
        $parsed = [];
        $errors = [];

        $unitMap = Unit::pluck('id', 'name')->mapWithKeys(fn($id, $name) => [mb_strtolower($name) => $id])->all();
        $classMap = Classes::withoutGlobalScopes()->pluck('id', 'name')->mapWithKeys(fn($id, $name) => [mb_strtolower($name) => $id])->all();
        $validStatuses = ['aktif', 'lulus', 'pindah', 'dikeluarkan'];

        foreach ($rows as $i => $row) {
            $rowNum = $i + 4; // Excel row number
            $nis    = $row[1] ?? '';
            $nisn   = $row[2] ?? '';
            $name   = $row[3] ?? '';
            $kelas  = $row[4] ?? '';
            $status = mb_strtolower($row[5] ?? 'aktif');
            $unit   = $row[6] ?? '';

            $rowErrors = [];

            if (empty($nis)) $rowErrors[] = "NIS wajib diisi";
            if (empty($name)) $rowErrors[] = "Nama wajib diisi";
            if (!in_array($status, $validStatuses)) $rowErrors[] = "Status '{$status}' tidak valid";

            $unitId = $unitMap[mb_strtolower($unit)] ?? null;
            if (empty($unit)) $rowErrors[] = "Unit wajib diisi";
            elseif (!$unitId) $rowErrors[] = "Unit '{$unit}' tidak ditemukan";

            $classId = $classMap[mb_strtolower($kelas)] ?? null;
            if (!empty($kelas) && !$classId) $rowErrors[] = "Kelas '{$kelas}' tidak ditemukan";

            if (!empty($rowErrors)) {
                $errors[] = ['row' => $rowNum, 'data' => $name ?: $nis, 'errors' => $rowErrors];
            }

            $parsed[] = [
                'row'      => $rowNum,
                'nis'      => $nis,
                'nisn'     => $nisn ?: null,
                'name'     => $name,
                'kelas'    => $kelas,
                'class_id' => $classId,
                'status'   => $status,
                'unit'     => $unit,
                'unit_id'  => $unitId,
                'valid'    => empty($rowErrors),
            ];
        }

        return ['data' => $parsed, 'errors' => $errors, 'total' => count($parsed), 'valid' => count(array_filter($parsed, fn($r) => $r['valid']))];
    }

    /**
     * Import validated students into the database.
     */
    public function importStudents(array $validRows): array
    {
        $created = 0;
        $updated = 0;
        $activeYear = AcademicYear::active();

        DB::beginTransaction();
        try {
            foreach ($validRows as $row) {
                if (!$row['valid']) continue;

                $student = Student::withoutGlobalScopes()->where('nis', $row['nis'])->first();

                if ($student) {
                    $student->update([
                        'nisn'    => $row['nisn'],
                        'name'    => $row['name'],
                        'unit_id' => $row['unit_id'],
                        'status'  => $row['status'],
                    ]);
                    $updated++;
                } else {
                    $student = Student::withoutGlobalScopes()->create([
                        'nis'     => $row['nis'],
                        'nisn'    => $row['nisn'],
                        'name'    => $row['name'],
                        'unit_id' => $row['unit_id'],
                        'status'  => $row['status'],
                    ]);
                    $created++;
                }

                // Attach class if specified and academic year is active
                if ($row['class_id'] && $activeYear) {
                    $student->classes()->syncWithoutDetaching([
                        $row['class_id'] => [
                            'academic_year_id' => $activeYear->id,
                            'enrolled_at'      => now(),
                        ],
                    ]);
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }

        return ['success' => true, 'created' => $created, 'updated' => $updated];
    }

    /**
     * Parse teacher rows and return validated data + errors.
     * Excel columns: No, Nama, Username, Role, Unit, No HP, Password
     */
    public function parseTeachers(string $filePath): array
    {
        $rows = $this->readSheet($filePath);
        $parsed = [];
        $errors = [];

        $unitMap = Unit::pluck('id', 'name')->mapWithKeys(fn($id, $name) => [mb_strtolower($name) => $id])->all();
        $validRoles = ['admin', 'walikelas', 'guru'];

        foreach ($rows as $i => $row) {
            $rowNum   = $i + 4;
            $name     = $row[1] ?? '';
            $username = mb_strtolower($row[2] ?? '');
            $role     = mb_strtolower($row[3] ?? '');
            $unit     = $row[4] ?? '';
            $phone    = $row[5] ?? '';
            $password = $row[6] ?? '';

            $rowErrors = [];

            if (empty($name)) $rowErrors[] = "Nama wajib diisi";
            if (empty($username)) $rowErrors[] = "Username wajib diisi";
            if (!in_array($role, $validRoles)) $rowErrors[] = "Role '{$role}' tidak valid (admin/walikelas/guru)";

            $unitId = $unitMap[mb_strtolower($unit)] ?? null;
            if (empty($unit)) $rowErrors[] = "Unit wajib diisi";
            elseif (!$unitId) $rowErrors[] = "Unit '{$unit}' tidak ditemukan";

            if (!empty($rowErrors)) {
                $errors[] = ['row' => $rowNum, 'data' => $name ?: $username, 'errors' => $rowErrors];
            }

            $parsed[] = [
                'row'      => $rowNum,
                'name'     => $name,
                'username' => $username,
                'role'     => $role,
                'unit'     => $unit,
                'unit_id'  => $unitId,
                'phone'    => $phone ?: null,
                'password' => $password,
                'valid'    => empty($rowErrors),
            ];
        }

        return ['data' => $parsed, 'errors' => $errors, 'total' => count($parsed), 'valid' => count(array_filter($parsed, fn($r) => $r['valid']))];
    }

    /**
     * Import validated teachers into the database.
     */
    public function importTeachers(array $validRows): array
    {
        $created = 0;
        $updated = 0;

        DB::beginTransaction();
        try {
            foreach ($validRows as $row) {
                if (!$row['valid']) continue;

                $existing = User::withTrashed()->where('username', $row['username'])->first();

                $data = [
                    'name'    => $row['name'],
                    'role'    => $row['role'],
                    'unit_id' => $row['unit_id'],
                    'phone'   => $row['phone'],
                ];

                if ($existing) {
                    // Only update password if explicitly provided
                    if (!empty($row['password'])) {
                        $data['password'] = $row['password']; // will be hashed by cast
                    }
                    $existing->update($data);
                    if ($existing->trashed()) $existing->restore();
                    $updated++;
                } else {
                    $data['username'] = $row['username'];
                    $data['password'] = !empty($row['password']) ? $row['password'] : 'password';
                    User::create($data);
                    $created++;
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }

        return ['success' => true, 'created' => $created, 'updated' => $updated];
    }

    /**
     * Parse schedule rows and return validated data + errors.
     * Excel columns: No, Hari(0-6), NamaHari, Kelas, Mapel, Guru, JamMulai, JamSelesai, Unit
     */
    public function parseSchedules(string $filePath): array
    {
        $rows = $this->readSheet($filePath);
        $parsed = [];
        $errors = [];

        $unitMap    = Unit::pluck('id', 'name')->mapWithKeys(fn($id, $name) => [mb_strtolower($name) => $id])->all();
        $classMap   = Classes::withoutGlobalScopes()->get()->mapWithKeys(fn($c) => [mb_strtolower($c->name) . '|' . $c->unit_id => $c->id])->all();
        $subjectMap = Subject::withoutGlobalScopes()->get()->mapWithKeys(fn($s) => [mb_strtolower($s->name) . '|' . $s->unit_id => $s->id])->all();
        $teacherMap = User::whereIn('role', ['admin', 'walikelas', 'guru'])->get()->mapWithKeys(fn($u) => [mb_strtolower($u->name) . '|' . ($u->unit_id ?? 0) => $u->id])->all();
        // Also create a fallback teacher map without unit
        $teacherMapFallback = User::whereIn('role', ['admin', 'walikelas', 'guru'])->pluck('id', 'name')->mapWithKeys(fn($id, $name) => [mb_strtolower($name) => $id])->all();

        $activeYear = AcademicYear::active();

        foreach ($rows as $i => $row) {
            $rowNum    = $i + 4;
            $dayOfWeek = $row[1] ?? '';
            $kelas     = $row[3] ?? '';
            $mapel     = $row[4] ?? '';
            $guru      = $row[5] ?? '';
            $jamMulai  = $row[6] ?? '';
            $jamSelesai= $row[7] ?? '';
            $unit      = $row[8] ?? '';

            $rowErrors = [];

            if ($dayOfWeek === '' || !is_numeric($dayOfWeek) || (int)$dayOfWeek < 0 || (int)$dayOfWeek > 6) {
                $rowErrors[] = "Hari harus angka 0-6";
            }
            if (empty($kelas)) $rowErrors[] = "Kelas wajib diisi";
            if (empty($mapel)) $rowErrors[] = "Mata Pelajaran wajib diisi";
            if (empty($guru)) $rowErrors[] = "Guru wajib diisi";
            if (empty($jamMulai)) $rowErrors[] = "Jam Mulai wajib diisi";
            if (empty($jamSelesai)) $rowErrors[] = "Jam Selesai wajib diisi";

            $unitId = $unitMap[mb_strtolower($unit)] ?? null;
            if (empty($unit)) $rowErrors[] = "Unit wajib diisi";
            elseif (!$unitId) $rowErrors[] = "Unit '{$unit}' tidak ditemukan";

            $classId = null;
            if ($unitId) {
                $classId = $classMap[mb_strtolower($kelas) . '|' . $unitId] ?? null;
            }
            if (!empty($kelas) && !$classId) $rowErrors[] = "Kelas '{$kelas}' tidak ditemukan di unit ini";

            $subjectId = null;
            if ($unitId) {
                $subjectId = $subjectMap[mb_strtolower($mapel) . '|' . $unitId] ?? null;
            }
            if (!empty($mapel) && !$subjectId) $rowErrors[] = "Mapel '{$mapel}' tidak ditemukan di unit ini";

            $teacherId = null;
            if ($unitId) {
                $teacherId = $teacherMap[mb_strtolower($guru) . '|' . $unitId] ?? $teacherMapFallback[mb_strtolower($guru)] ?? null;
            } else {
                $teacherId = $teacherMapFallback[mb_strtolower($guru)] ?? null;
            }
            if (!empty($guru) && !$teacherId) $rowErrors[] = "Guru '{$guru}' tidak ditemukan";

            // Normalize time format
            $startTime = $this->normalizeTime($jamMulai);
            $endTime   = $this->normalizeTime($jamSelesai);

            if (!empty($rowErrors)) {
                $errors[] = ['row' => $rowNum, 'data' => "{$kelas} - {$mapel}", 'errors' => $rowErrors];
            }

            $parsed[] = [
                'row'          => $rowNum,
                'day_of_week'  => (int) $dayOfWeek,
                'kelas'        => $kelas,
                'class_id'     => $classId,
                'mapel'        => $mapel,
                'subject_id'   => $subjectId,
                'guru'         => $guru,
                'teacher_id'   => $teacherId,
                'start_time'   => $startTime,
                'end_time'     => $endTime,
                'unit'         => $unit,
                'unit_id'      => $unitId,
                'valid'        => empty($rowErrors),
            ];
        }

        return ['data' => $parsed, 'errors' => $errors, 'total' => count($parsed), 'valid' => count(array_filter($parsed, fn($r) => $r['valid']))];
    }

    /**
     * Import validated schedules into the database.
     */
    public function importSchedules(array $validRows): array
    {
        $created = 0;
        $updated = 0;
        $activeYear = AcademicYear::active();

        if (!$activeYear) {
            return ['success' => false, 'message' => 'Tidak ada Tahun Ajaran aktif.'];
        }

        DB::beginTransaction();
        try {
            foreach ($validRows as $row) {
                if (!$row['valid']) continue;

                $schedule = Schedule::withoutGlobalScopes()
                    ->where('class_id', $row['class_id'])
                    ->where('subject_id', $row['subject_id'])
                    ->where('day_of_week', $row['day_of_week'])
                    ->where('start_time', $row['start_time'])
                    ->where('academic_year_id', $activeYear->id)
                    ->first();

                $data = [
                    'unit_id'          => $row['unit_id'],
                    'class_id'         => $row['class_id'],
                    'subject_id'       => $row['subject_id'],
                    'teacher_id'       => $row['teacher_id'],
                    'academic_year_id' => $activeYear->id,
                    'day_of_week'      => $row['day_of_week'],
                    'start_time'       => $row['start_time'],
                    'end_time'         => $row['end_time'],
                ];

                if ($schedule) {
                    $schedule->update($data);
                    $updated++;
                } else {
                    Schedule::withoutGlobalScopes()->create($data);
                    $created++;
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }

        return ['success' => true, 'created' => $created, 'updated' => $updated];
    }

    /**
     * Normalize time string to H:i:s format.
     */
    private function normalizeTime(string $time): string
    {
        $time = trim($time);
        if (empty($time)) return '00:00:00';

        // Handle HH:MM or H:MM
        if (preg_match('/^(\d{1,2}):(\d{2})$/', $time, $m)) {
            return sprintf('%02d:%02d:00', $m[1], $m[2]);
        }
        // Handle HH:MM:SS
        if (preg_match('/^(\d{1,2}):(\d{2}):(\d{2})$/', $time, $m)) {
            return sprintf('%02d:%02d:%02d', $m[1], $m[2], $m[3]);
        }
        // Handle decimal fraction (Excel time format)
        if (is_numeric($time) && (float)$time < 1) {
            $totalSeconds = round((float)$time * 86400);
            $hours = floor($totalSeconds / 3600);
            $minutes = floor(($totalSeconds % 3600) / 60);
            return sprintf('%02d:%02d:00', $hours, $minutes);
        }

        return $time;
    }
}

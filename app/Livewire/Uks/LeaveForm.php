<?php

namespace App\Livewire\Uks;

use App\Models\SchoolCalendar;
use App\Models\Student;
use App\Models\StudentLeave;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('UKS & Izin Santri')]
class LeaveForm extends Component
{
    public string $search          = '';
    public ?int $selectedStudentId  = null;
    public string $leaveStatus      = 'sakit';
    public string $leaveNotes       = '';
    public string $dateFilter       = '';

    // Rentang tanggal (multi-hari)
    public string $startDate = '';
    public string $endDate   = '';

    public function mount(): void
    {
        Gate::authorize('manage-leaves');
        $today            = today()->format('Y-m-d');
        $this->dateFilter = $today;
        $this->startDate  = $today;
        $this->endDate    = $today;
    }

    public function selectStudent(int $id): void
    {
        $this->selectedStudentId = $id;
        $this->search            = '';
    }

    public function submitLeave(): void
    {
        Gate::authorize('manage-leaves');

        $this->validate([
            'selectedStudentId' => 'required|exists:students,id',
            'leaveStatus'       => 'required|in:sakit,izin',
            'leaveNotes'        => 'nullable|string|max:500',
            'startDate'         => 'required|date|date_format:Y-m-d',
            'endDate'           => 'required|date|date_format:Y-m-d|after_or_equal:startDate',
        ]);

        // Batas maksimum rentang izin: 14 hari
        $start = \Carbon\Carbon::parse($this->startDate);
        $end   = \Carbon\Carbon::parse($this->endDate);
        if ($start->diffInDays($end) > 13) {
            $this->addError('endDate', 'Rentang izin maksimal 14 hari.');
            return;
        }

        $student = Student::findOrFail($this->selectedStudentId);

        // Ambil hari libur dalam rentang sekali (bulk, bukan per hari)
        $holidayDates = SchoolCalendar::whereDate('date', '>=', $this->startDate)
            ->whereDate('date', '<=', $this->endDate)
            ->where('type', 'holiday')
            ->where(function ($q) use ($student) {
                $q->whereNull('unit_id')->orWhere('unit_id', $student->unit_id);
            })
            ->pluck('date')
            ->map(fn ($d) => \Carbon\Carbon::parse($d)->format('Y-m-d'))
            ->toArray();

        // Ambil tanggal yang sudah ada izin dalam rentang (hindari duplikat)
        $existingDates = StudentLeave::where('student_id', $student->id)
            ->whereDate('date', '>=', $this->startDate)
            ->whereDate('date', '<=', $this->endDate)
            ->pluck('date')
            ->map(fn ($d) => \Carbon\Carbon::parse($d)->format('Y-m-d'))
            ->toArray();

        // Iterasi setiap hari dalam rentang
        $period  = CarbonPeriod::create($this->startDate, $this->endDate);
        $records = [];
        $skipped = 0;

        foreach ($period as $date) {
            $dateStr = $date->format('Y-m-d');

            // Skip weekend (0=Minggu, 6=Sabtu)
            if (in_array($date->dayOfWeek, [0, 6])) {
                $skipped++;
                continue;
            }

            // Skip hari libur
            if (in_array($dateStr, $holidayDates)) {
                $skipped++;
                continue;
            }

            // Skip yang sudah ada
            if (in_array($dateStr, $existingDates)) {
                $skipped++;
                continue;
            }

            $records[] = [
                'unit_id'    => $student->unit_id,
                'student_id' => $student->id,
                'date'       => $dateStr,
                'status'     => $this->leaveStatus,
                'notes'      => $this->leaveNotes ?: null,
                'created_by' => Auth::id(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (empty($records)) {
            $this->dispatch('notify', type: 'warning', message: 'Tidak ada hari valid untuk dicatat (sudah ada, libur, atau weekend).');
            return;
        }

        // Bulk insert semua hari sekaligus
        StudentLeave::insert($records);

        $insertedCount = count($records);
        $message       = "Izin {$student->name} berhasil dicatat untuk {$insertedCount} hari";
        if ($skipped > 0) {
            $message .= " ({$skipped} hari dilewati: weekend/libur/duplikat)";
        }
        $message .= '.';

        $this->reset(['selectedStudentId', 'leaveNotes', 'leaveStatus', 'search']);
        $this->leaveStatus = 'sakit';
        $today = today()->format('Y-m-d');
        $this->startDate  = $today;
        $this->endDate    = $today;

        $this->dispatch('notify', type: 'success', message: $message);
    }

    public function removeLeave(int $id): void
    {
        Gate::authorize('manage-leaves');

        // Eager-load 'student' sekaligus agar tidak ada lazy load query kedua
        $leave = StudentLeave::with('student')->findOrFail($id);
        $name  = $leave->student->name;
        $leave->delete();

        $this->dispatch('notify', type: 'success', message: 'Izin ' . $name . ' berhasil dibatalkan.');
    }

    public function render()
    {
        // Search suggestions
        $suggestions = collect();
        if (strlen($this->search) >= 2) {
            $suggestions = Student::active()
                ->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                      ->orWhere('nis', 'like', "%{$this->search}%");
                })
                ->limit(10)
                ->get(['id', 'name', 'nis']);
        }

        // Daftar izin sesuai filter tanggal
        $todayLeaves = StudentLeave::with('student')
            ->whereDate('date', $this->dateFilter)
            ->orderBy('created_at', 'desc')
            ->get();

        $selectedStudent = $this->selectedStudentId
            ? Student::find($this->selectedStudentId)
            : null;

        return view('livewire.uks.leave-form', [
            'suggestions'     => $suggestions,
            'todayLeaves'     => $todayLeaves,
            'selectedStudent' => $selectedStudent,
        ]);
    }
}

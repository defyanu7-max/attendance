@section('title', 'Mata Pelajaran & Jadwal')
@section('page-title', 'Mata Pelajaran & Jadwal')

@php
    $user = auth()->user();
    $dayNames = ['', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
@endphp

<div class="container-fluid">

    {{-- Breadcrumb --}}
    <div class="row page-titles">
        <div class="col-sm-6">
            <h4 class="breadcrumb-title">Mata Pelajaran & Jadwal</h4>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Mata Pelajaran & Jadwal</li>
            </ol>
        </div>
    </div>

    {{-- ================================================================ --}}
    {{-- LAYER 1 — UNIT SWITCHER (Tab Bootstrap)                         --}}
    {{-- ================================================================ --}}
    <div class="row">
        <div class="col-12">
            <div class="card mb-0" style="border-bottom-left-radius:0; border-bottom-right-radius:0;">
                <div class="card-body py-0 px-0">
                    <ul class="nav nav-tabs mb-0 px-3 pt-2" role="tablist" id="unit-tabs">
                        @if($user->role === 'superadmin' || $user->unit_id === 1)
                        <li class="nav-item">
                            <button
                                wire:click="switchUnit(1)"
                                class="nav-link {{ $activeUnit === 1 ? 'active fw-semibold' : '' }}"
                                type="button"
                                id="tab-unit-1"
                            >
                                <i class="bi bi-building me-1"></i>MTs
                                <span class="badge bg-secondary ms-1 fs-11">{{ $this->countByUnit[1]['subjects'] }} Mapel</span>
                            </button>
                        </li>
                        @endif

                        @if($user->role === 'superadmin' || $user->unit_id === 2)
                        <li class="nav-item">
                            <button
                                wire:click="switchUnit(2)"
                                class="nav-link {{ $activeUnit === 2 ? 'active fw-semibold' : '' }}"
                                type="button"
                                id="tab-unit-2"
                            >
                                <i class="bi bi-building me-1"></i>MA
                                <span class="badge bg-secondary ms-1 fs-11">{{ $this->countByUnit[2]['subjects'] }} Mapel</span>
                            </button>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- ================================================================ --}}
    {{-- LAYER 2 — SPLIT PANEL (35% kiri | 65% kanan)                    --}}
    {{-- ================================================================ --}}
    <div class="row g-0">

        {{-- ============================================================ --}}
        {{-- PANEL KIRI — DAFTAR MATA PELAJARAN (35%)                     --}}
        {{-- ============================================================ --}}
        <div class="col-lg-4">
            <div class="card h-100 border-0 shadow-sm" style="border-top-left-radius:0; border-top-right-radius:0;">
                <div class="card-header py-2 d-flex align-items-center justify-content-between">
                    <span class="fw-semibold fs-14">Mata Pelajaran</span>
                    @can('manage-master-data')
                    <button wire:click="openAddSubjectModal()" class="btn btn-primary btn-xs" id="btn-add-subject" title="Tambah Mapel">
                        <i class="bi bi-plus-lg"></i>
                    </button>
                    @endcan
                </div>

                {{-- Search mapel --}}
                <div class="px-3 pt-2">
                    <input
                        wire:model.live.debounce.250ms="subjectSearch"
                        type="text"
                        class="form-control form-control-sm"
                        placeholder="Cari mata pelajaran..."
                        id="subject-search"
                    >
                </div>

                {{-- List mapel --}}
                <div class="card-body p-0 mt-2" style="overflow-y: auto; max-height: 60vh;">
                    <ul class="list-group list-group-flush">

                        {{-- Item "Semua Jadwal" --}}
                        <li
                            wire:click="selectSubject(null)"
                            class="list-group-item list-group-item-action d-flex justify-content-between align-items-center px-3 py-2
                                   {{ $selectedSubjectId === null ? 'active' : '' }}"
                            style="cursor:pointer"
                            id="subject-all"
                        >
                            <div>
                                <i class="bi bi-grid-3x3-gap me-2 opacity-50"></i>
                                <span class="fs-13 fw-semibold">Semua Jadwal</span>
                            </div>
                            <span class="badge {{ $selectedSubjectId === null ? 'bg-white text-primary' : 'bg-primary' }} rounded-pill fs-11">
                                {{ $this->totalSchedulesUnit }}
                            </span>
                        </li>

                        @forelse($this->subjects as $subject)
                        <li
                            wire:click="selectSubject({{ $subject->id }})"
                            class="list-group-item list-group-item-action d-flex justify-content-between align-items-center px-3 py-2
                                   {{ $selectedSubjectId === $subject->id ? 'active' : '' }}"
                            style="cursor:pointer"
                            id="subject-item-{{ $subject->id }}"
                        >
                            <div class="d-flex flex-column">
                                <span class="fs-13 fw-semibold">{{ $subject->name }}</span>
                                <span class="fs-11 {{ $selectedSubjectId === $subject->id ? 'text-white opacity-75' : 'text-muted' }}">
                                    Kode: {{ $subject->code }}
                                </span>
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                <span class="badge {{ $selectedSubjectId === $subject->id ? 'bg-white text-primary' : 'bg-primary' }} rounded-pill fs-11">
                                    {{ $subject->schedules_count }} Jadwal
                                </span>
                                @can('manage-master-data')
                                <button
                                    wire:click.stop="openEditSubjectModal({{ $subject->id }})"
                                    class="btn btn-xs {{ $selectedSubjectId === $subject->id ? 'btn-light' : 'btn-outline-secondary' }}"
                                    title="Edit Mapel"
                                >
                                    <i class="bi bi-pencil" style="font-size:10px"></i>
                                </button>
                                @endcan
                            </div>
                        </li>
                        @empty
                        <li class="list-group-item text-center text-muted py-4 fs-13">
                            <i class="bi bi-inbox d-block fs-24 mb-1 opacity-25"></i>
                            Belum ada mata pelajaran
                        </li>
                        @endforelse

                    </ul>
                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- PANEL KANAN — JADWAL PELAJARAN (65%)                         --}}
        {{-- ============================================================ --}}
        <div class="col-lg-8">
            <div class="card h-100 border-0 shadow-sm" style="border-top-left-radius:0; border-top-right-radius:0; border-left: 1px solid #eee;">

                {{-- Header panel kanan (dinamis) --}}
                <div class="card-header py-2 d-flex align-items-center justify-content-between flex-wrap gap-2">

                    {{-- Judul kontekstual --}}
                    <div>
                        @if($selectedSubjectId && $this->selectedSubject)
                            <span class="fw-semibold fs-14">
                                Jadwal: {{ $this->selectedSubject->name }}
                            </span>
                            <span class="badge bg-secondary ms-1 fs-11">{{ $this->selectedSubject->code }}</span>
                        @else
                            <span class="fw-semibold fs-14">Semua Jadwal — {{ $activeUnit === 1 ? 'MTs' : 'MA' }}</span>
                        @endif
                    </div>

                    {{-- Filter hari + Filter kelas + Tombol tambah jadwal --}}
                    <div class="d-flex gap-2 align-items-center flex-wrap">

                        {{-- Filter hari --}}
                        <select wire:model.live="filterDay" class="form-select form-select-sm" style="width:auto" id="filter-day">
                            <option value="">Semua Hari</option>
                            <option value="1">Senin</option>
                            <option value="2">Selasa</option>
                            <option value="3">Rabu</option>
                            <option value="4">Kamis</option>
                            <option value="5">Jumat</option>
                            <option value="6">Sabtu</option>
                        </select>

                        {{-- Filter kelas --}}
                        <select wire:model.live="filterClassId" class="form-select form-select-sm" style="width:auto" id="filter-class">
                            <option value="">Semua Kelas</option>
                            @foreach($this->classesForUnit as $cls)
                                <option value="{{ $cls->id }}">{{ $cls->name }}</option>
                            @endforeach
                        </select>

                        @can('manage-master-data')
                        <button wire:click="openAddScheduleModal()" class="btn btn-primary btn-sm" id="btn-add-schedule">
                            <i class="bi bi-plus-lg me-1"></i>Tambah Jadwal
                        </button>
                        @endcan
                    </div>
                </div>

                {{-- Tabel jadwal dikelompokkan per hari --}}
                <div class="card-body p-0" style="overflow-y: auto; max-height: 65vh;">

                    @forelse($this->schedulesByDay as $dayNumber => $daySchedules)
                    <div class="mb-1">

                        {{-- Sub-header hari --}}
                        <div class="px-3 py-1 d-flex align-items-center gap-2"
                             style="background: var(--rgba-primary-1, rgba(77,68,181,0.06)); border-left: 3px solid var(--primary, #4d44b5);">
                            <span class="fw-semibold fs-13" style="color: var(--primary, #4d44b5)">
                                {{ $dayNames[$dayNumber] ?? 'Hari ?' }}
                            </span>
                            <span class="badge bg-primary rounded-pill fs-11">{{ $daySchedules->count() }} jadwal</span>
                        </div>

                        {{-- Baris jadwal dalam hari ini --}}
                        <table class="table table-sm table-hover mb-0">
                            <tbody>
                                @foreach($daySchedules as $schedule)
                                <tr>
                                    {{-- Waktu --}}
                                    <td style="width: 100px">
                                        <span class="fs-12 fw-semibold text-body">
                                            {{ substr($schedule->start_time, 0, 5) }}–{{ substr($schedule->end_time, 0, 5) }}
                                        </span>
                                    </td>

                                    {{-- Mapel badge + Kelas --}}
                                    <td>
                                        @if(!$selectedSubjectId)
                                        <span class="badge bg-light text-dark border fs-11 me-1">
                                            {{ $schedule->subject?->code ?? '?' }}
                                        </span>
                                        @endif
                                        <span class="fs-13 fw-medium">{{ $schedule->class_?->name ?? '-' }}</span>
                                    </td>

                                    {{-- Guru --}}
                                    <td>
                                        <span class="fs-12 text-muted">
                                            <i class="bi bi-person me-1"></i>{{ $schedule->teacher?->name ?? '-' }}
                                        </span>
                                    </td>

                                    {{-- Kode unik --}}
                                    <td style="width: 120px">
                                        <span class="badge bg-light text-muted border fs-11">
                                            {{ $schedule->unique_code ?? '-' }}
                                        </span>
                                    </td>

                                    {{-- Aksi --}}
                                    @can('manage-master-data')
                                    <td style="width: 80px" class="text-end pe-3">
                                        <button
                                            wire:click="openEditScheduleModal({{ $schedule->id }})"
                                            class="btn btn-xs btn-outline-primary me-1"
                                            title="Edit Jadwal"
                                        >
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button
                                            wire:click="confirmDeleteSchedule({{ $schedule->id }})"
                                            class="btn btn-xs btn-outline-danger"
                                            title="Hapus Jadwal"
                                        >
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                    @endcan
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @empty
                    <div class="text-center text-muted py-5 fs-13">
                        <i class="bi bi-calendar-x d-block fs-32 mb-2 opacity-25"></i>
                        Belum ada jadwal
                        @if($selectedSubjectId)
                            untuk mata pelajaran ini
                        @endif
                    </div>
                    @endforelse

                </div>
            </div>
        </div>

    </div> {{-- end .row split panel --}}

    {{-- ================================================================ --}}
    {{-- MODAL FORM MATA PELAJARAN                                       --}}
    {{-- ================================================================ --}}
    @if($subjectModalOpen)
    <div class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,0.5)" id="subject-modal">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header py-2">
                    <h5 class="modal-title fs-15">{{ $editingSubjectId ? 'Edit' : 'Tambah' }} Mata Pelajaran</h5>
                    <button wire:click="closeSubjectModal" type="button" class="btn-close btn-sm"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold fs-13">Kode Mapel <span class="text-danger">*</span></label>
                        <input wire:model="subjectForm.code" type="text"
                               class="form-control form-control-sm @error('subjectForm.code') is-invalid @enderror"
                               placeholder="Contoh: MTK, BIND, ALQH"
                               maxlength="10" style="text-transform:uppercase"
                               id="input-subject-code">
                        @error('subjectForm.code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold fs-13">Nama Mata Pelajaran <span class="text-danger">*</span></label>
                        <input wire:model="subjectForm.name" type="text"
                               class="form-control form-control-sm @error('subjectForm.name') is-invalid @enderror"
                               placeholder="Contoh: Matematika"
                               id="input-subject-name">
                        @error('subjectForm.name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="alert alert-info py-2 fs-12 mb-0">
                        <i class="bi bi-info-circle me-1"></i>
                        Mata pelajaran ini akan didaftarkan ke unit <strong>{{ $activeUnit === 1 ? 'MTs' : 'MA' }}</strong>.
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button wire:click="closeSubjectModal" class="btn btn-sm btn-light">Batal</button>
                    <button wire:click="saveSubject" class="btn btn-sm btn-primary"
                            wire:loading.attr="disabled" id="btn-save-subject">
                        <span wire:loading.remove wire:target="saveSubject">Simpan</span>
                        <span wire:loading wire:target="saveSubject">
                            <span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ================================================================ --}}
    {{-- MODAL FORM JADWAL PELAJARAN                                     --}}
    {{-- ================================================================ --}}
    @if($scheduleModalOpen)
    <div class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,0.5)" id="schedule-modal">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 480px">
            <div class="modal-content">
                <div class="modal-header py-2">
                    <h5 class="modal-title fs-15">{{ $editingScheduleId ? 'Edit' : 'Tambah' }} Jadwal</h5>
                    <button wire:click="closeScheduleModal" type="button" class="btn-close btn-sm"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">

                        {{-- Mata Pelajaran --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold fs-13">Mata Pelajaran <span class="text-danger">*</span></label>
                            <select wire:model.live="scheduleForm.subject_id"
                                    class="form-select form-select-sm @error('scheduleForm.subject_id') is-invalid @enderror"
                                    id="input-schedule-subject">
                                <option value="">— Pilih Mapel —</option>
                                @foreach($this->subjects as $subject)
                                    <option value="{{ $subject->id }}">{{ $subject->code }} — {{ $subject->name }}</option>
                                @endforeach
                            </select>
                            @error('scheduleForm.subject_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Kelas --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold fs-13">Kelas <span class="text-danger">*</span></label>
                            <select wire:model="scheduleForm.class_id"
                                    class="form-select form-select-sm @error('scheduleForm.class_id') is-invalid @enderror"
                                    id="input-schedule-class">
                                <option value="">— Pilih Kelas —</option>
                                @foreach($this->classesForUnit as $cls)
                                    <option value="{{ $cls->id }}">{{ $cls->name }}</option>
                                @endforeach
                            </select>
                            @error('scheduleForm.class_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Guru Pengampu --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold fs-13">Guru Pengampu <span class="text-danger">*</span></label>
                            <select wire:model="scheduleForm.teacher_id"
                                    class="form-select form-select-sm @error('scheduleForm.teacher_id') is-invalid @enderror"
                                    id="input-schedule-teacher">
                                <option value="">— Pilih Guru —</option>
                                @foreach($this->teachers as $teacher)
                                    <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                                @endforeach
                            </select>
                            @error('scheduleForm.teacher_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Hari --}}
                        <div class="col-6">
                            <label class="form-label fw-semibold fs-13">Hari <span class="text-danger">*</span></label>
                            <select wire:model="scheduleForm.day_of_week"
                                    class="form-select form-select-sm @error('scheduleForm.day_of_week') is-invalid @enderror"
                                    id="input-schedule-day">
                                <option value="">— Hari —</option>
                                <option value="1">Senin</option>
                                <option value="2">Selasa</option>
                                <option value="3">Rabu</option>
                                <option value="4">Kamis</option>
                                <option value="5">Jumat</option>
                                <option value="6">Sabtu</option>
                            </select>
                            @error('scheduleForm.day_of_week')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Jam Mulai --}}
                        <div class="col-3">
                            <label class="form-label fw-semibold fs-13">Mulai <span class="text-danger">*</span></label>
                            <input wire:model="scheduleForm.start_time" type="time"
                                   class="form-control form-control-sm @error('scheduleForm.start_time') is-invalid @enderror"
                                   id="input-schedule-start">
                            @error('scheduleForm.start_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Jam Selesai --}}
                        <div class="col-3">
                            <label class="form-label fw-semibold fs-13">Selesai <span class="text-danger">*</span></label>
                            <input wire:model="scheduleForm.end_time" type="time"
                                   class="form-control form-control-sm @error('scheduleForm.end_time') is-invalid @enderror"
                                   id="input-schedule-end">
                            @error('scheduleForm.end_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button wire:click="closeScheduleModal" class="btn btn-sm btn-light">Batal</button>
                    <button wire:click="saveSchedule" class="btn btn-sm btn-primary"
                            wire:loading.attr="disabled" id="btn-save-schedule">
                        <span wire:loading.remove wire:target="saveSchedule">Simpan Jadwal</span>
                        <span wire:loading wire:target="saveSchedule">
                            <span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>

{{-- SweetAlert2 already loaded globally in app.blade.php — do NOT load again here --}}
@push('scripts')
<script>
document.addEventListener('livewire:initialized', () => {

    Livewire.on('confirm-delete-schedule', ({ id, name }) => {
        Swal.fire({
            title: 'Hapus jadwal ini?',
            text: `${name} akan dihapus dari sistem.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: 'var(--primary, #4d44b5)',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                @this.deleteSchedule(id);
            }
        });
    });

    Livewire.on('confirm-delete-subject', ({ id, name }) => {
        Swal.fire({
            title: 'Hapus mata pelajaran ini?',
            text: `${name} dan seluruh jadwal terkait akan dihapus.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: 'var(--primary, #4d44b5)',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                @this.deleteSubject(id);
            }
        });
    });

});
</script>
@endpush

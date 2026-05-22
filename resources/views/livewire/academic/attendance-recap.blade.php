@section('title', 'Rekap Absensi Kelas')
@section('page-title', 'Rekap Absensi')

@php
    $rekap         = $this->rekapData;
    $students      = $rekap['students'];
    $columns       = $rekap['columns'];
    $columnStats   = $rekap['column_stats'];
    $weekGroups    = $rekap['week_groups'];
    $summary       = $rekap['summary'];
    $critical      = $rekap['critical'];
    $totalAllAlpha = $rekap['total_all_alpha'];
    $threshold     = $this->alphaThreshold;
    $activeYear    = $this->activeYear;
    $class         = $this->selectedClass;
    $criticalStudents = collect($critical);
@endphp

<div class="container-fluid">

    {{-- Breadcrumb --}}
    <div class="row page-titles">
        <div class="col-sm-6">
            <h4 class="breadcrumb-title">Rekap Absensi Kelas</h4>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('schedules.index') }}">Absensi</a></li>
                <li class="breadcrumb-item active">Rekap Absensi</li>
            </ol>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- LEVEL 1 — Header Konteks (R-01)                           --}}
    {{-- ========================================================= --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body py-3">
                    <div class="row align-items-end g-3">
                        {{-- Kelas --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Kelas</label>
                            <select wire:model.live="classId" class="form-select" id="recap-class-select">
                                <option value="">-- Pilih Kelas --</option>
                                @foreach($this->classes as $cls)
                                    <option value="{{ $cls->id }}">{{ $cls->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Filter Bulan --}}
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Bulan</label>
                            <select wire:model.live="filterMonth" class="form-select" id="recap-month-filter">
                                <option value="">Semua Bulan</option>
                                @foreach($this->availableMonths as $m)
                                    <option value="{{ $m['value'] }}">{{ $m['label'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Toggle Mode + Export (R-02, R-08) --}}
                        <div class="col-md-5">
                            <div class="d-flex align-items-end justify-content-end gap-2 flex-wrap">
                                {{-- Toggle Mode --}}
                                <div class="btn-group btn-group-sm" role="group" aria-label="View mode toggle">
                                    <button
                                        wire:click="toggleMode('by_subject')"
                                        type="button"
                                        class="btn {{ $viewMode === 'by_subject' ? 'btn-primary' : 'btn-outline-primary' }}"
                                        id="toggle-by-subject"
                                    >
                                        <i class="bi bi-book me-1"></i>Per Mapel
                                    </button>
                                    <button
                                        wire:click="toggleMode('by_date')"
                                        type="button"
                                        class="btn {{ $viewMode === 'by_date' ? 'btn-primary' : 'btn-outline-primary' }}"
                                        id="toggle-by-date"
                                    >
                                        <i class="bi bi-calendar3 me-1"></i>Per Tanggal
                                    </button>
                                </div>

                                {{-- Export (hanya untuk walikelas/admin/superadmin) --}}
                                @if($class)
                                    @can('view-class-recap', $class)
                                    <a href="{{ route('recap.export', ['classId' => $class->id, 'month' => $filterMonth]) }}"
                                       class="btn btn-sm btn-outline-success" id="recap-export-btn">
                                        <i class="bi bi-download me-1"></i>Export
                                    </a>
                                    @endcan
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Info kelas terpilih --}}
                    @if($class)
                    <div class="mt-2">
                        <p class="fs-12 text-muted mb-0">
                            {{ $class->name }} · {{ $activeYear?->name ?? '-' }}
                            <span class="ms-2 badge bg-secondary">{{ $class->unit?->name ?? '-' }}</span>
                            @if($class->homeroomTeacher)
                                <span class="ms-2">
                                    <i class="bi bi-person me-1"></i>Walikelas: {{ $class->homeroomTeacher->name }}
                                </span>
                            @endif
                        </p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if(empty($students))
        {{-- Empty state --}}
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-clipboard-data fs-1 text-muted"></i>
                        <p class="text-muted mt-2">Pilih kelas untuk melihat rekap absensi.</p>
                    </div>
                </div>
            </div>
        </div>
    @else

    {{-- ========================================================= --}}
    {{-- LEVEL 2 — Summary Cards (R-03, WAJIB 4 kartu)             --}}
    {{-- ========================================================= --}}
    <div class="row mb-4 g-3">

        {{-- Card 1: Total Pertemuan --}}
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center py-3">
                    <div class="fs-28 fw-bold text-primary">{{ $summary['total_sessions'] }}</div>
                    <div class="fs-12 text-muted mt-1">
                        {{ $viewMode === 'by_date' ? 'Total Hari' : 'Total Pertemuan' }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 2: Rata-rata Kehadiran --}}
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center py-3">
                    <div class="fs-28 fw-bold text-success">{{ $summary['avg_attendance'] }}%</div>
                    <div class="fs-12 text-muted mt-1">Rata-rata Kehadiran</div>
                </div>
            </div>
        </div>

        {{-- Card 3: Santri Alpha Kritis --}}
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100 {{ $summary['critical_alpha_count'] > 0 ? 'border-danger border' : '' }}">
                <div class="card-body text-center py-3">
                    <div class="fs-28 fw-bold text-danger">{{ $summary['critical_alpha_count'] }}</div>
                    <div class="fs-12 text-muted mt-1">Santri Alpha Kritis</div>
                </div>
            </div>
        </div>

        {{-- Card 4: Total Santri --}}
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center py-3">
                    <div class="fs-28 fw-bold" style="color: var(--title)">{{ $summary['total_students'] }}</div>
                    <div class="fs-12 text-muted mt-1">Total Santri</div>
                </div>
            </div>
        </div>

    </div>

    {{-- ========================================================= --}}
    {{-- LEVEL 3 — Tabel Rekap Utama (R-04, R-05, R-09)            --}}
    {{-- ========================================================= --}}
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header flex-wrap gap-2 d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="card-title mb-0">
                            <i class="bi bi-bar-chart-line me-2"></i>Rekap Absensi
                        </h5>
                        <p class="fs-12 text-muted mb-0">
                            Mode: {{ $viewMode === 'by_subject' ? 'Per Mata Pelajaran' : 'Per Tanggal (Timeline)' }}
                        </p>
                    </div>
                    <span class="badge bg-primary">{{ count($students) }} Santri</span>
                </div>

                <div class="card-body p-0">

                    {{-- R-09: Scroll container --}}
                    <div class="table-responsive recap-scroll-container" style="max-height: 70vh; overflow-y: auto;">
                        <table class="table table-bordered table-hover table-sm align-middle mb-0"
                               style="border-collapse: separate; border-spacing: 0;">

                            {{-- Colgroup (R-09) --}}
                            <colgroup>
                                <col style="width: 40px">
                                <col style="min-width: 160px; max-width: 200px">
                                @foreach($columns as $col)
                                    <col style="min-width: {{ $viewMode === 'by_date' ? '36px' : '60px' }}">
                                @endforeach
                                <col style="min-width: 70px">
                                <col style="min-width: 80px">
                            </colgroup>

                            {{-- ===== THEAD ===== --}}
                            <thead class="sticky-top" style="top: 0; z-index: 2;">

                                @if($viewMode === 'by_date' && !empty($weekGroups))
                                {{-- Mode B: Two-row header (R-05) --}}
                                <tr class="table-light">
                                    <th rowspan="2" class="align-middle text-center">#</th>
                                    <th rowspan="2" class="align-middle" style="min-width:160px">Nama Santri</th>
                                    @foreach($weekGroups as $week)
                                    <th
                                        colspan="{{ $week['count'] }}"
                                        class="text-center border-start fs-12 text-muted fw-semibold py-2"
                                    >
                                        Minggu {{ $week['number'] }}
                                        <span class="d-block fw-normal" style="font-size:10px">
                                            {{ $week['start'] }} – {{ $week['end'] }}
                                        </span>
                                    </th>
                                    @endforeach
                                    <th rowspan="2" class="text-center align-middle border-start" style="min-width:70px">Alpha</th>
                                    <th rowspan="2" class="text-center align-middle" style="min-width:80px">% Hadir</th>
                                </tr>
                                <tr class="table-light">
                                    @foreach($columns as $col)
                                    <th class="text-center px-1 {{ $col['is_weekend'] || $col['is_holiday'] ? 'bg-light' : '' }}"
                                        style="min-width:36px; font-size:11px;">
                                        <div class="fw-semibold">{{ $col['label'] }}</div>
                                        <div class="text-muted" style="font-size:9px">{{ $col['day'] }}</div>
                                    </th>
                                    @endforeach
                                </tr>

                                @else
                                {{-- Mode A: Single-row header --}}
                                <tr class="table-light">
                                    <th class="text-center">#</th>
                                    <th style="min-width:160px">Nama Santri</th>
                                    @foreach($columns as $col)
                                    <th class="text-center" title="{{ $col['title'] ?? $col['label'] }}">
                                        {{ $col['label'] }}
                                    </th>
                                    @endforeach
                                    <th class="text-center border-start" style="min-width:70px">Alpha</th>
                                    <th class="text-center" style="min-width:80px">% Hadir</th>
                                </tr>
                                @endif

                            </thead>

                            {{-- ===== TBODY ===== --}}
                            <tbody>
                                @foreach($students as $idx => $student)
                                <tr class="{{ $student['is_critical'] ? 'table-danger' : '' }}">

                                    {{-- No --}}
                                    <td class="text-center">{{ $idx + 1 }}</td>

                                    {{-- Nama Santri — sticky kiri (R-09) --}}
                                    <td class="fw-medium sticky-col"
                                        style="position: sticky; left: 0; background: {{ $student['is_critical'] ? '#fff5f5' : 'white' }}; z-index: 1;">
                                        <div>{{ $student['name'] }}</div>
                                        <div class="fs-11 text-muted">{{ $student['nis'] }}</div>
                                    </td>

                                    {{-- Status cells (R-04) --}}
                                    @foreach($columns as $col)
                                        @if($viewMode === 'by_subject')
                                            {{-- Mode A: Show cumulative counts --}}
                                            @php
                                                $cellData = $student['status_map'][$col['id']] ?? null;
                                            @endphp
                                            <td class="text-center px-1">
                                                @if($cellData && $cellData['total'] > 0)
                                                    <div class="d-flex flex-column gap-0" style="font-size:10px; line-height:1.4">
                                                        @if($cellData['hadir'] > 0)
                                                            <span class="badge badge-hadir rounded-1" style="min-width:28px; font-size:11px;">
                                                                H:{{ $cellData['hadir'] }}
                                                            </span>
                                                        @endif
                                                        @if($cellData['alpha'] > 0)
                                                            <span class="badge badge-alpha rounded-1 mt-1" style="min-width:28px; font-size:11px;">
                                                                A:{{ $cellData['alpha'] }}
                                                            </span>
                                                        @endif
                                                        @if($cellData['sakit'] > 0)
                                                            <span class="badge badge-sakit rounded-1 mt-1" style="min-width:28px; font-size:11px;">
                                                                S:{{ $cellData['sakit'] }}
                                                            </span>
                                                        @endif
                                                        @if($cellData['izin'] > 0)
                                                            <span class="badge badge-izin rounded-1 mt-1" style="min-width:28px; font-size:11px;">
                                                                I:{{ $cellData['izin'] }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="badge bg-light text-muted border rounded-1" style="min-width:28px; font-size:11px;">—</span>
                                                @endif
                                            </td>
                                        @else
                                            {{-- Mode B: Single badge per cell --}}
                                            @php
                                                $status = $student['status_map'][$col['id']] ?? null;
                                                $cellConfig = match ($status) {
                                                    'hadir' => ['class' => 'badge-hadir',   'label' => 'H'],
                                                    'alpha' => ['class' => 'badge-alpha',   'label' => 'A'],
                                                    'sakit' => ['class' => 'badge-sakit',   'label' => 'S'],
                                                    'izin'  => ['class' => 'badge-izin',    'label' => 'I'],
                                                    default => ['class' => 'bg-light text-muted border', 'label' => '—'],
                                                };
                                            @endphp
                                            <td class="text-center px-1">
                                                <span class="badge {{ $cellConfig['class'] }} rounded-1"
                                                      style="min-width:28px; font-size:11px;">
                                                    {{ $cellConfig['label'] }}
                                                </span>
                                            </td>
                                        @endif
                                    @endforeach

                                    {{-- Total Alpha — highlight if critical (R-04) --}}
                                    <td class="text-center fw-bold border-start {{ $student['total_alpha'] >= $threshold ? 'text-danger' : 'text-body' }}">
                                        {{ $student['total_alpha'] }}
                                        @if($student['total_alpha'] >= $threshold)
                                            <i class="bi bi-exclamation-circle-fill text-danger ms-1" title="Melampaui threshold"></i>
                                        @endif
                                    </td>

                                    {{-- % Hadir — progress bar mini (R-04) --}}
                                    @php $pct = $student['attendance_pct']; @endphp
                                    <td class="text-center" style="min-width: 80px;">
                                        <div class="d-flex flex-column align-items-center gap-1">
                                            <span class="fs-12 fw-semibold {{ $pct >= 75 ? 'text-success' : ($pct >= 50 ? 'text-warning' : 'text-danger') }}">
                                                {{ $pct }}%
                                            </span>
                                            <div class="progress w-100" style="height: 4px;">
                                                <div
                                                    class="progress-bar {{ $pct >= 75 ? 'bg-success' : ($pct >= 50 ? 'bg-warning' : 'bg-danger') }}"
                                                    style="width: {{ $pct }}%"
                                                ></div>
                                            </div>
                                        </div>
                                    </td>

                                </tr>
                                @endforeach
                            </tbody>

                            {{-- ===== TFOOT — Statistik per kolom (R-06) ===== --}}
                            <tfoot class="table-light">
                                <tr class="fw-semibold fs-12">
                                    <td colspan="2" class="text-end pe-3 text-muted sticky-col"
                                        style="position: sticky; left: 0; background: #f8f9fa; z-index: 1;">
                                        Jumlah per kolom ↓
                                    </td>
                                    @foreach($columns as $col)
                                    <td class="text-center">
                                        @php $stat = $columnStats[$col['id']] ?? ['hadir'=>0,'alpha'=>0,'sakit'=>0,'izin'=>0]; @endphp
                                        <div class="d-flex flex-column gap-0" style="font-size:10px; line-height:1.4">
                                            <span class="text-success" title="Hadir">{{ $stat['hadir'] }}</span>
                                            <span class="text-danger"  title="Alpha">{{ $stat['alpha'] }}</span>
                                            <span class="text-warning" title="Sakit">{{ $stat['sakit'] }}</span>
                                            <span class="text-info"    title="Izin">{{ $stat['izin'] }}</span>
                                        </div>
                                    </td>
                                    @endforeach
                                    <td class="text-center text-danger fw-bold">{{ $totalAllAlpha }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>

                        </table>
                    </div>

                    {{-- ===== Legenda Status (R-11) ===== --}}
                    <div class="px-3 pb-3">
                        <div class="d-flex align-items-center gap-3 mt-2 flex-wrap fs-12 text-muted">
                            <span class="fw-semibold">Keterangan:</span>
                            <span><span class="badge badge-hadir me-1">H</span>Hadir</span>
                            <span><span class="badge badge-alpha me-1">A</span>Alpha</span>
                            <span><span class="badge badge-sakit me-1">S</span>Sakit</span>
                            <span><span class="badge badge-izin me-1">I</span>Izin</span>
                            <span><span class="badge bg-light text-muted border me-1">—</span>Tidak Ada Jadwal / Libur</span>
                            <span class="ms-2">
                                <i class="bi bi-square-fill text-danger opacity-25 me-1"></i>
                                Baris merah = melampaui threshold alpha (≥ {{ $threshold }}x)
                            </span>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- LEVEL 5 — Panel Peringatan Alpha (R-07, WAJIB di bawah)   --}}
    {{-- ========================================================= --}}
    @if($criticalStudents->isNotEmpty())
    <div class="row">
        <div class="col-lg-12">
            <div class="card border-danger mt-0">
                <div class="card-header bg-danger text-white py-2">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <strong>Santri Melampaui Threshold Alpha (≥ {{ $threshold }}x)</strong>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0" id="critical-alpha-table">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Santri</th>
                                <th class="text-center">Total Alpha</th>
                                <th class="text-center">% Kehadiran</th>
                                <th>Status Notifikasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($criticalStudents as $s)
                            <tr>
                                <td class="fw-semibold">{{ $s->name }}</td>
                                <td class="text-center">
                                    <span class="badge badge-alpha fs-13">{{ $s->total_alpha }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="text-danger fw-bold">{{ $s->attendance_pct }}%</span>
                                </td>
                                <td>
                                    @if($s->has_notification)
                                        <span class="badge bg-warning text-dark">
                                            <i class="bi bi-whatsapp me-1"></i>Notifikasi Terkirim
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">Belum Ada Notifikasi</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif

    @endif {{-- end empty check --}}
</div>

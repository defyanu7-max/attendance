@section('title', 'Input Absensi')
@section('page-title', 'Input Absensi')

<div class="container-fluid">
    {{-- Page Header --}}
    <div class="row page-titles">
        <div class="col">
            <h4 class="breadcrumb-title">Input Absensi</h4>
            <p class="text-muted mb-0">
                <strong>{{ $schedule->subject->name }}</strong> —
                {{ $schedule->class_->name }}
                @if($schedule->unique_code)
                    <span class="badge bg-secondary ms-1">{{ $schedule->unique_code }}</span>
                @endif
                <span class="badge bg-primary ms-1">{{ $schedule->time_range }}</span>
            </p>
        </div>
    </div>

    {{-- Holiday Blocker --}}
    @if($isHoliday)
    <div class="alert alert-danger d-flex align-items-start" role="alert">
        <i class="bi bi-calendar-x-fill me-3 fs-3 flex-shrink-0"></i>
        <div>
            <h5 class="mb-1 fw-bold">Hari Ini adalah Hari Libur</h5>
            <p class="mb-2">{{ $holidayDescription }} — Input absensi tidak dapat dilakukan.</p>
            <a href="{{ route('schedules.index') }}" class="btn btn-danger btn-sm py-2 px-3">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Jadwal
            </a>
        </div>
    </div>
    @endif

    {{-- Cutoff Warning --}}
    @if($isPastCutoff && !$isHoliday)
    <div class="alert alert-warning d-flex align-items-center" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2 fs-4 flex-shrink-0"></i>
        <div>Waktu input absensi telah berakhir (pukul <strong>{{ $cutoffTime }}</strong> WIB). Data ditampilkan sebagai referensi saja.</div>
    </div>
    @endif

    {{-- Success Banner --}}
    @if($isSubmitted && !$isHoliday)
    <div class="alert alert-success d-flex align-items-center" role="alert">
        <i class="bi bi-check-circle-fill me-2 fs-4 flex-shrink-0"></i>
        <div>Absensi sudah tercatat. Anda masih bisa mengubah status hingga pukul <strong>{{ $cutoffTime }}</strong> WIB.</div>
    </div>
    @endif

    {{-- Attendance Table --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h4 class="card-title mb-0">
                        <i class="bi bi-list-check me-2"></i> Daftar Hadir
                    </h4>
                    <span class="badge bg-primary fs-13 px-3 py-2">{{ count($students) }} Santri</span>
                </div>
                <div class="card-body p-0 p-md-3">
                    @if(empty($students))
                        <div class="text-center py-5">
                            <i class="bi bi-people fs-1 text-muted"></i>
                            <p class="text-muted mt-2">Tidak ada santri yang terdaftar di kelas ini.</p>
                        </div>
                    @else
                        {{--
                            Mobile-first: pada layar kecil tabel menjadi scrollable horizontal.
                            Kolom NIS disembunyikan di mobile (d-none d-md-table-cell).
                            Tombol status diperbesar untuk touch target yang nyaman.
                        --}}
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="40" class="text-center">#</th>
                                        <th>Nama Santri</th>
                                        {{-- Sembunyikan NIS di mobile, tampil di md+ --}}
                                        <th class="d-none d-md-table-cell" width="90">NIS</th>
                                        <th>Status Kehadiran</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($students as $i => $student)
                                    <tr class="{{ $student['is_locked'] ? 'table-light' : '' }}"
                                        wire:key="student-{{ $student['id'] }}">
                                        <td class="text-center text-muted">{{ $i + 1 }}</td>
                                        <td>
                                            <span class="fw-semibold">{{ $student['name'] }}</span>
                                            @if($student['is_merged'])
                                                <span class="badge bg-info text-dark ms-1 d-none d-sm-inline">Gabung</span>
                                            @endif
                                            {{-- NIS tampil di bawah nama di mobile --}}
                                            <small class="d-block d-md-none text-muted">{{ $student['nis'] }}</small>
                                        </td>
                                        <td class="d-none d-md-table-cell">
                                            <code class="fs-12">{{ $student['nis'] }}</code>
                                        </td>
                                        <td>
                                            @if($student['is_locked'])
                                                {{-- UKS lock: badge lebih besar --}}
                                                <span class="badge bg-{{ $student['lock_reason'] === 'sakit' ? 'warning' : 'info' }} text-dark px-3 py-2 fs-13">
                                                    <i class="bi bi-lock-fill me-1"></i>
                                                    {{ ucfirst($student['lock_reason']) }} (UKS)
                                                </span>
                                            @else
                                                {{--
                                                    Touch-friendly radio buttons.
                                                    Mobile: 2×2 grid wrap via flex-wrap.
                                                    Desktop: single row.
                                                --}}
                                                <div class="btn-group flex-wrap" role="group"
                                                     aria-label="Status kehadiran {{ $student['name'] }}">
                                                    @foreach ([
                                                        'hadir' => ['success', 'bi-check-circle'],
                                                        'alpha' => ['danger',  'bi-x-circle'],
                                                        'sakit' => ['warning', 'bi-thermometer-half'],
                                                        'izin'  => ['info',    'bi-card-text'],
                                                    ] as $status => [$color, $icon])
                                                    <input
                                                        type="radio"
                                                        class="btn-check"
                                                        id="st-{{ $student['id'] }}-{{ $status }}"
                                                        wire:model="students.{{ $i }}.status"
                                                        value="{{ $status }}"
                                                        @disabled($isPastCutoff || $isHoliday)
                                                        aria-label="{{ ucfirst($status) }}"
                                                    >
                                                    <label
                                                        class="btn btn-outline-{{ $color }} btn-sm py-2 px-2"
                                                        for="st-{{ $student['id'] }}-{{ $status }}"
                                                        style="min-width: 62px;"
                                                    >
                                                        <i class="bi {{ $icon }} d-block d-sm-inline"></i>
                                                        <span class="d-none d-sm-inline ms-1">{{ ucfirst($status) }}</span>
                                                        <span class="d-inline d-sm-none" style="font-size:10px">{{ ucfirst($status) }}</span>
                                                    </label>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                @if(!$isPastCutoff && !$isHoliday && !empty($students))
                <div class="card-footer">
                    {{-- Mobile: tombol full-width agar mudah ditekan --}}
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-stretch align-items-sm-center gap-2">
                        <a href="{{ route('schedules.index') }}" class="btn btn-outline-secondary py-2">
                            <i class="bi bi-arrow-left me-1"></i> Kembali
                        </a>
                        <button
                            wire:click="submit"
                            wire:loading.attr="disabled"
                            wire:target="submit"
                            class="btn btn-primary py-3 px-4 fw-semibold"
                            type="button"
                            id="btn-simpan-absensi"
                        >
                            <span wire:loading.remove wire:target="submit">
                                <i class="bi bi-save me-1"></i> Simpan Absensi ({{ count($students) }} santri)
                            </span>
                            <span wire:loading wire:target="submit">
                                <span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...
                            </span>
                        </button>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>


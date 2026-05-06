@section('title', 'Input Absensi')
@section('page-title', 'Input Absensi')

<div class="container-fluid">
    {{-- Page Header --}}
    <div class="row page-titles">
        <div class="col">
            <h4 class="breadcrumb-title">Input Absensi</h4>
            <p class="text-muted mb-0">
                {{ $schedule->subject->name }} — {{ $schedule->class_->name }}
                @if($schedule->unique_code)
                    <span class="badge bg-secondary ms-1">{{ $schedule->unique_code }}</span>
                @endif
                <span class="badge bg-primary ms-1">{{ $schedule->time_range }}</span>
            </p>
        </div>
    </div>

    {{-- Cutoff Warning --}}
    @if($isPastCutoff)
    <div class="alert alert-warning d-flex align-items-center" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2 fs-4"></i>
        <div>Waktu input absensi telah berakhir (pukul {{ $cutoffTime }} WIB). Data ditampilkan sebagai referensi saja.</div>
    </div>
    @endif

    {{-- Success Banner --}}
    @if($isSubmitted)
    <div class="alert alert-success d-flex align-items-center" role="alert">
        <i class="bi bi-check-circle-fill me-2 fs-4"></i>
        <div>Absensi sudah tercatat. Anda masih bisa mengubah status hingga pukul {{ $cutoffTime }} WIB.</div>
    </div>
    @endif

    {{-- Attendance Table --}}
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="bi bi-list-check me-2"></i> Daftar Hadir
                    </h4>
                    <span class="badge bg-primary">{{ count($students) }} Santri</span>
                </div>
                <div class="card-body">
                    @if(empty($students))
                        <div class="text-center py-5">
                            <i class="bi bi-people fs-1 text-muted"></i>
                            <p class="text-muted mt-2">Tidak ada santri yang terdaftar di kelas ini.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="40">#</th>
                                        <th>Nama Santri</th>
                                        <th width="80">NIS</th>
                                        <th width="300">Status Kehadiran</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($students as $i => $student)
                                    <tr class="{{ $student['is_locked'] ? 'row-locked' : '' }}" wire:key="student-{{ $student['id'] }}">
                                        <td>{{ $i + 1 }}</td>
                                        <td>
                                            {{ $student['name'] }}
                                            @if($student['is_merged'])
                                                <span class="badge bg-info text-dark ms-1" title="Kelas gabungan">Gabung</span>
                                            @endif
                                        </td>
                                        <td><code class="fs-12">{{ $student['nis'] }}</code></td>
                                        <td>
                                            @if($student['is_locked'])
                                                <span class="badge badge-{{ $student['lock_reason'] === 'sakit' ? 'sakit' : 'izin' }}">
                                                    <i class="bi bi-lock-fill me-1"></i>
                                                    {{ ucfirst($student['lock_reason']) }} (UKS)
                                                </span>
                                            @else
                                                <div class="btn-group btn-group-sm" role="group">
                                                    @foreach (['hadir', 'alpha', 'sakit', 'izin'] as $status)
                                                    <input
                                                        type="radio"
                                                        class="btn-check"
                                                        id="status-{{ $student['id'] }}-{{ $status }}"
                                                        wire:model="students.{{ $i }}.status"
                                                        value="{{ $status }}"
                                                        @disabled($isPastCutoff)
                                                    >
                                                    <label
                                                        class="btn btn-outline-{{ ['hadir'=>'success','alpha'=>'danger','sakit'=>'warning','izin'=>'info'][$status] }}"
                                                        for="status-{{ $student['id'] }}-{{ $status }}"
                                                    >{{ ucfirst($status) }}</label>
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
                @if(!$isPastCutoff && !empty($students))
                <div class="card-footer d-flex justify-content-between align-items-center">
                    <a href="{{ route('schedules.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Kembali
                    </a>
                    <button
                        wire:click="submit"
                        wire:loading.attr="disabled"
                        wire:loading.class="disabled"
                        class="btn btn-primary px-4"
                        type="button"
                    >
                        <span wire:loading.remove>
                            <i class="bi bi-save me-1"></i> Simpan Absensi
                        </span>
                        <span wire:loading>
                            <span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...
                        </span>
                    </button>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@section('title', 'Jadwal & Absen')
@section('page-title', 'Jadwal & Absen')

<div class="container-fluid">
    {{-- Page Header --}}
    <div class="row page-titles">
        <div class="col-sm-6">
            <h4 class="breadcrumb-title">Jadwal & Absen</h4>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Jadwal & Absen</li>
            </ol>
        </div>
        <div class="col-sm-6 text-end">
            @can('manage-master-data')
                <a href="{{ route('export.schedules') }}" class="btn btn-success me-2">
                    <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
                </a>
            @endcan
            @if($activeYear)
                <span class="badge bg-primary fs-14 px-3 py-2">
                    <i class="bi bi-mortarboard me-1"></i> TA {{ $activeYear->name }}
                </span>
            @endif
        </div>
    </div>

    {{-- Day Selector --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body py-3">
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($days as $dayNum => $dayName)
                            <button
                                wire:click="selectDay({{ $dayNum }})"
                                class="btn {{ $selectedDay === $dayNum ? 'btn-primary' : 'btn-outline-primary' }} px-4"
                            >
                                {{ $dayName }}
                                @if($dayNum === (int) now()->dayOfWeek)
                                    <span class="badge bg-white text-primary ms-1">Hari Ini</span>
                                @endif
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Schedule Table --}}
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="bi bi-calendar-week me-2"></i>
                        Jadwal Hari {{ $days[$selectedDay] ?? '' }}
                    </h4>
                    <span class="badge bg-primary">{{ $schedules->count() }} Jadwal</span>
                </div>
                <div class="card-body">
                    @if(!$activeYear)
                        <div class="text-center py-5">
                            <i class="bi bi-exclamation-triangle fs-1 text-warning"></i>
                            <p class="text-muted mt-2">Belum ada Tahun Ajaran Aktif. Hubungi Superadmin.</p>
                        </div>
                    @elseif($schedules->isEmpty())
                        <div class="text-center py-5">
                            <i class="bi bi-calendar-x fs-1 text-muted"></i>
                            <p class="text-muted mt-2">Tidak ada jadwal untuk hari ini.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="40">#</th>
                                        <th>Mata Pelajaran</th>
                                        <th>Kelas</th>
                                        <th>Guru</th>
                                        <th>Waktu</th>
                                        <th width="130">Status</th>
                                        <th width="120">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($schedules as $schedule)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <span class="fw-semibold">{{ $schedule->subject->name }}</span>
                                            @if($schedule->unique_code)
                                                <br><code class="fs-12">{{ $schedule->unique_code }}</code>
                                            @endif
                                        </td>
                                        <td>{{ $schedule->class_->name }}</td>
                                        <td>{{ $schedule->teacher->name }}</td>
                                        <td>
                                            <span class="badge bg-secondary">
                                                {{ $schedule->time_range }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($schedule->has_attendance_today ?? false)
                                                <span class="badge bg-success">
                                                    <i class="bi bi-check-circle me-1"></i> Sudah Absen
                                                </span>
                                            @else
                                                <span class="badge bg-warning text-dark">
                                                    <i class="bi bi-clock me-1"></i> Belum
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('attendance.take', $schedule) }}"
                                               class="btn btn-primary btn-xs">
                                                <i class="bi bi-check2-square me-1"></i> Absen
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

<div class="container-fluid">
    {{-- Welcome Banner --}}
    <div class="row">
        <div class="col-12">
            <div class="card overflow-hidden">
                <div class="card-body pb-0 pt-4">
                    <div class="row">
                        <div class="col-xl-8 col-lg-7">
                            <h4 class="fw-bold text-primary">
                                Selamat Datang, {{ $user->name }}! 👋
                            </h4>
                            <p class="text-muted mb-2">
                                <i class="bi bi-calendar3 me-1"></i>
                                {{ $dayName }}, {{ now()->translatedFormat('d F Y') }}
                                @if($activeYear)
                                    — <span class="badge bg-primary">TA {{ $activeYear->name }}</span>
                                @else
                                    — <span class="badge bg-danger">Belum ada Tahun Ajaran Aktif</span>
                                @endif
                            </p>
                            <p class="text-muted">
                                Role: <span class="badge bg-secondary">{{ ucfirst($user->role) }}</span>
                                @if($user->unit)
                                    | Unit: <span class="badge bg-info text-dark">{{ $user->unit->name }}</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Stat Cards (Admin+) --}}
    @if($user->isAdminOrAbove() && !empty($stats))
    <div class="row">
        <div class="col-xl-4 col-sm-6">
            <div class="card">
                <div class="card-body d-flex align-items-center">
                    <div class="me-auto">
                        <h2 class="fs-32 fw-bold mb-0 text-primary">{{ $stats['total_students'] }}</h2>
                        <span class="fs-14 text-muted">Total Santri Aktif</span>
                    </div>
                    <div class="icon-box bg-primary-light rounded-circle p-3">
                        <i class="bi bi-people fs-24 text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-sm-6">
            <div class="card">
                <div class="card-body d-flex align-items-center">
                    <div class="me-auto">
                        <h2 class="fs-32 fw-bold mb-0 text-warning">{{ $stats['today_leaves'] }}</h2>
                        <span class="fs-14 text-muted">Izin/Sakit Hari Ini</span>
                    </div>
                    <div class="icon-box bg-warning-light rounded-circle p-3">
                        <i class="bi bi-heart-pulse fs-24 text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-sm-6">
            <div class="card">
                <div class="card-body d-flex align-items-center">
                    <div class="me-auto">
                        <h2 class="fs-32 fw-bold mb-0 text-danger">{{ $stats['pending_notifications'] }}</h2>
                        <span class="fs-14 text-muted">Notifikasi Alpha Pending</span>
                    </div>
                    <div class="icon-box bg-danger-light rounded-circle p-3">
                        <i class="bi bi-bell-exclamation fs-24 text-danger"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Today's Schedule --}}
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="bi bi-calendar-day me-2"></i>
                        Jadwal Hari Ini — {{ $dayName }}
                    </h4>
                    <span class="badge bg-primary">{{ $todaySchedules->count() }} Jadwal</span>
                </div>
                <div class="card-body">
                    @if($todaySchedules->isEmpty())
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
                                        <th width="120">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($todaySchedules as $schedule)
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

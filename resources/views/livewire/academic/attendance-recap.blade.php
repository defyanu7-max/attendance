@section('title', 'Rekap Absensi Kelas')
@section('page-title', 'Rekap Absensi')

<div class="container-fluid">
    <div class="row page-titles">
        <div class="col-sm-6">
            <h4 class="breadcrumb-title">Rekap Absensi Kelas</h4>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Rekap Absensi</li>
            </ol>
        </div>
    </div>

    {{-- Filter --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body py-3">
                    <div class="row align-items-end g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Kelas</label>
                            <select wire:model.live="classId" class="form-select">
                                <option value="">-- Pilih Kelas --</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Dari Tanggal</label>
                            <input type="date" wire:model.live="startDate" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Sampai Tanggal</label>
                            <input type="date" wire:model.live="endDate" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Recap Table --}}
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="bi bi-bar-chart-line me-2"></i>
                        Rekap
                        @if($selectedClass)
                            — {{ $selectedClass->name }}
                            @if($selectedClass->homeroomTeacher)
                                <span class="text-muted fs-14 fw-normal ms-2">
                                    Walikelas: {{ $selectedClass->homeroomTeacher->name }}
                                </span>
                            @endif
                        @endif
                    </h4>
                    <span class="badge bg-primary">{{ $recap->count() }} Santri</span>
                </div>
                <div class="card-body">
                    @if($recap->isEmpty())
                        <div class="text-center py-5">
                            <i class="bi bi-clipboard-data fs-1 text-muted"></i>
                            <p class="text-muted mt-2">Pilih kelas untuk melihat rekap absensi.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="40">#</th>
                                        <th>Nama</th>
                                        <th>NIS</th>
                                        <th class="text-center" width="80">Total</th>
                                        <th class="text-center" width="80">
                                            <span class="badge badge-hadir">Hadir</span>
                                        </th>
                                        <th class="text-center" width="80">
                                            <span class="badge badge-alpha">Alpha</span>
                                        </th>
                                        <th class="text-center" width="80">
                                            <span class="badge badge-sakit">Sakit</span>
                                        </th>
                                        <th class="text-center" width="80">
                                            <span class="badge badge-izin">Izin</span>
                                        </th>
                                        <th class="text-center" width="80">%</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recap as $row)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td class="fw-semibold">{{ $row['name'] }}</td>
                                        <td><code class="fs-12">{{ $row['nis'] }}</code></td>
                                        <td class="text-center">{{ $row['total'] }}</td>
                                        <td class="text-center text-success fw-bold">{{ $row['hadir'] }}</td>
                                        <td class="text-center text-danger fw-bold">{{ $row['alpha'] }}</td>
                                        <td class="text-center text-warning fw-bold">{{ $row['sakit'] }}</td>
                                        <td class="text-center text-info fw-bold">{{ $row['izin'] }}</td>
                                        <td class="text-center">
                                            @if($row['total'] > 0)
                                                {{ round(($row['hadir'] / $row['total']) * 100) }}%
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    @php
                                        $totalAll = $recap->sum('total');
                                        $hadirAll = $recap->sum('hadir');
                                        $alphaAll = $recap->sum('alpha');
                                        $sakitAll = $recap->sum('sakit');
                                        $izinAll = $recap->sum('izin');
                                    @endphp
                                    <tr class="fw-bold">
                                        <td colspan="3">Total</td>
                                        <td class="text-center">{{ $totalAll }}</td>
                                        <td class="text-center text-success">{{ $hadirAll }}</td>
                                        <td class="text-center text-danger">{{ $alphaAll }}</td>
                                        <td class="text-center text-warning">{{ $sakitAll }}</td>
                                        <td class="text-center text-info">{{ $izinAll }}</td>
                                        <td class="text-center">
                                            {{ $totalAll > 0 ? round(($hadirAll / $totalAll) * 100) . '%' : '-' }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

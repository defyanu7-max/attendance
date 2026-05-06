@section('title', 'Import Excel')
@section('page-title', 'Import Excel')

<div class="container-fluid">
    <div class="row page-titles">
        <div class="col-sm-6">
            <h4 class="breadcrumb-title">Import Excel</h4>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Import Excel</li>
            </ol>
        </div>
        <div class="col-sm-6 text-end">
            <a href="{{ route('export.' . $importType) }}" class="btn btn-success">
                <i class="bi bi-download me-1"></i> Download Template
            </a>
        </div>
    </div>

    {{-- Upload Form --}}
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="bi bi-file-earmark-arrow-up me-2"></i> Upload File Excel
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">Jenis Data <span class="text-danger">*</span></label>
                            <select wire:model.live="importType" class="form-select">
                                <option value="students">Data Santri</option>
                                <option value="teachers">Data Guru</option>
                                <option value="schedules">Jadwal Pelajaran</option>
                            </select>
                        </div>
                        <div class="col-md-5 mb-3">
                            <label class="form-label fw-semibold">File Excel <span class="text-danger">*</span></label>
                            <input type="file" wire:model="file"
                                   class="form-control @error('file') is-invalid @enderror"
                                   accept=".xlsx,.xls">
                            @error('file') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <div wire:loading wire:target="file" class="text-primary mt-1">
                                <span class="spinner-border spinner-border-sm me-1"></span> Mengupload...
                            </div>
                        </div>
                        <div class="col-md-3 mb-3 d-flex align-items-end">
                            <button wire:click="preview" class="btn btn-primary w-100"
                                    wire:loading.attr="disabled" wire:target="preview,file"
                                    @if(!$file) disabled @endif>
                                <span wire:loading.remove wire:target="preview">
                                    <i class="bi bi-eye me-1"></i> Preview
                                </span>
                                <span wire:loading wire:target="preview">
                                    <span class="spinner-border spinner-border-sm me-1"></span> Memproses...
                                </span>
                            </button>
                        </div>
                    </div>

                    {{-- Info box --}}
                    <div class="alert alert-info border-0 d-flex align-items-start mb-0">
                        <i class="bi bi-info-circle fs-5 me-2 mt-1"></i>
                        <div>
                            <strong>Petunjuk:</strong>
                            <ul class="mb-0 ps-3 mt-1">
                                <li>Gunakan file Excel yang didownload dari tombol <strong>Export Excel</strong> atau <strong>Download Template</strong>.</li>
                                <li>Data dimulai dari baris ke-4 (baris 1-3 = judul, instruksi, header).</li>
                                <li>Data yang sudah ada akan <strong>diperbarui</strong> berdasarkan NIS (santri) / Username (guru).</li>
                                <li>Cek sheet <strong>Referensi</strong> di file Excel untuk daftar nilai yang valid.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Stats --}}
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="bi bi-bar-chart me-2"></i> Ringkasan
                    </h4>
                </div>
                <div class="card-body">
                    @if($showPreview)
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Total Baris</span>
                            <span class="badge bg-primary fs-14 px-3">{{ $totalRows }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Data Valid</span>
                            <span class="badge bg-success fs-14 px-3">{{ $validRows }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Data Error</span>
                            <span class="badge bg-{{ count($previewErrors) > 0 ? 'danger' : 'secondary' }} fs-14 px-3">{{ count($previewErrors) }}</span>
                        </div>
                        <hr>
                        @if($validRows > 0)
                            <button wire:click="import" class="btn btn-primary w-100"
                                    wire:loading.attr="disabled" wire:target="import"
                                    wire:confirm="Yakin import {{ $validRows }} data valid?">
                                <span wire:loading.remove wire:target="import">
                                    <i class="bi bi-cloud-arrow-up me-1"></i> Import {{ $validRows }} Data
                                </span>
                                <span wire:loading wire:target="import">
                                    <span class="spinner-border spinner-border-sm me-1"></span> Mengimport...
                                </span>
                            </button>
                        @else
                            <div class="alert alert-warning mb-0">
                                <i class="bi bi-exclamation-triangle me-1"></i> Tidak ada data valid untuk diimport.
                            </div>
                        @endif
                    @elseif($showResult)
                        @if($importSuccess)
                            <div class="text-center py-3">
                                <i class="bi bi-check-circle fs-1 text-success"></i>
                                <h5 class="mt-2 text-success">Import Berhasil!</h5>
                                <div class="d-flex justify-content-between mt-3">
                                    <span class="text-muted">Data Baru</span>
                                    <span class="badge bg-success fs-14 px-3">{{ $createdCount }}</span>
                                </div>
                                <div class="d-flex justify-content-between mt-2">
                                    <span class="text-muted">Diperbarui</span>
                                    <span class="badge bg-info fs-14 px-3">{{ $updatedCount }}</span>
                                </div>
                                <button wire:click="resetAll" class="btn btn-outline-primary w-100 mt-3">
                                    <i class="bi bi-arrow-repeat me-1"></i> Import Lagi
                                </button>
                            </div>
                        @else
                            <div class="text-center py-3">
                                <i class="bi bi-x-circle fs-1 text-danger"></i>
                                <h5 class="mt-2 text-danger">Import Gagal</h5>
                                <p class="text-muted">{{ $resultMessage }}</p>
                                <button wire:click="resetAll" class="btn btn-outline-primary w-100 mt-2">
                                    <i class="bi bi-arrow-repeat me-1"></i> Coba Lagi
                                </button>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-cloud-arrow-up fs-1 text-muted opacity-50"></i>
                            <p class="text-muted mt-2">Upload file Excel lalu klik <strong>Preview</strong> untuk melihat data.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Preview Table --}}
    @if($showPreview)
    <div class="row">
        {{-- Errors --}}
        @if(count($previewErrors) > 0)
        <div class="col-lg-12">
            <div class="card border-danger">
                <div class="card-header bg-danger-subtle">
                    <h4 class="card-title mb-0 text-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i> {{ count($previewErrors) }} Baris Bermasalah (Tidak Akan Diimport)
                    </h4>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead class="table-danger">
                                <tr>
                                    <th width="60">Baris</th>
                                    <th>Data</th>
                                    <th>Error</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($previewErrors as $error)
                                <tr>
                                    <td><code>{{ $error['row'] }}</code></td>
                                    <td class="fw-semibold">{{ $error['data'] }}</td>
                                    <td>
                                        @foreach($error['errors'] as $msg)
                                            <span class="badge bg-danger me-1 mb-1">{{ $msg }}</span>
                                        @endforeach
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

        {{-- Data Preview --}}
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="bi bi-table me-2"></i> Preview Data ({{ $validRows }} valid dari {{ $totalRows }} baris)
                    </h4>
                    <button wire:click="resetPreview" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-x-lg me-1"></i> Tutup Preview
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                        <table class="table table-hover table-sm mb-0">
                            @if($importType === 'students')
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th width="30">#</th>
                                        <th width="40">OK</th>
                                        <th>NIS</th>
                                        <th>NISN</th>
                                        <th>Nama</th>
                                        <th>Kelas</th>
                                        <th>Status</th>
                                        <th>Unit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($previewData as $row)
                                    <tr class="{{ $row['valid'] ? '' : 'table-danger' }}">
                                        <td>{{ $row['row'] }}</td>
                                        <td>
                                            @if($row['valid'])
                                                <i class="bi bi-check-circle-fill text-success"></i>
                                            @else
                                                <i class="bi bi-x-circle-fill text-danger"></i>
                                            @endif
                                        </td>
                                        <td><code>{{ $row['nis'] }}</code></td>
                                        <td class="text-muted">{{ $row['nisn'] ?? '-' }}</td>
                                        <td class="fw-semibold">{{ $row['name'] }}</td>
                                        <td>{{ $row['kelas'] }}</td>
                                        <td><span class="badge bg-{{ $row['status'] === 'aktif' ? 'success' : 'secondary' }}">{{ $row['status'] }}</span></td>
                                        <td>{{ $row['unit'] }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            @elseif($importType === 'teachers')
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th width="30">#</th>
                                        <th width="40">OK</th>
                                        <th>Nama</th>
                                        <th>Username</th>
                                        <th>Role</th>
                                        <th>Unit</th>
                                        <th>No. HP</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($previewData as $row)
                                    <tr class="{{ $row['valid'] ? '' : 'table-danger' }}">
                                        <td>{{ $row['row'] }}</td>
                                        <td>
                                            @if($row['valid'])
                                                <i class="bi bi-check-circle-fill text-success"></i>
                                            @else
                                                <i class="bi bi-x-circle-fill text-danger"></i>
                                            @endif
                                        </td>
                                        <td class="fw-semibold">{{ $row['name'] }}</td>
                                        <td><code>{{ $row['username'] }}</code></td>
                                        <td><span class="badge bg-primary">{{ $row['role'] }}</span></td>
                                        <td>{{ $row['unit'] }}</td>
                                        <td class="text-muted">{{ $row['phone'] ?? '-' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            @elseif($importType === 'schedules')
                                @php $dayNames = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu']; @endphp
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th width="30">#</th>
                                        <th width="40">OK</th>
                                        <th>Hari</th>
                                        <th>Kelas</th>
                                        <th>Mapel</th>
                                        <th>Guru</th>
                                        <th>Waktu</th>
                                        <th>Unit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($previewData as $row)
                                    <tr class="{{ $row['valid'] ? '' : 'table-danger' }}">
                                        <td>{{ $row['row'] }}</td>
                                        <td>
                                            @if($row['valid'])
                                                <i class="bi bi-check-circle-fill text-success"></i>
                                            @else
                                                <i class="bi bi-x-circle-fill text-danger"></i>
                                            @endif
                                        </td>
                                        <td>{{ $dayNames[$row['day_of_week']] ?? $row['day_of_week'] }}</td>
                                        <td>{{ $row['kelas'] }}</td>
                                        <td class="fw-semibold">{{ $row['mapel'] }}</td>
                                        <td>{{ $row['guru'] }}</td>
                                        <td>
                                            <span class="badge bg-secondary">{{ substr($row['start_time'], 0, 5) }} - {{ substr($row['end_time'], 0, 5) }}</span>
                                        </td>
                                        <td>{{ $row['unit'] }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

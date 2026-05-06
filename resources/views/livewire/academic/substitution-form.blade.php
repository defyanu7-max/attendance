@section('title', 'Guru Badal')
@section('page-title', 'Guru Badal')

<div class="container-fluid">
    <div class="row page-titles">
        <div class="col-sm-6">
            <h4 class="breadcrumb-title">Guru Badal</h4>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Guru Badal</li>
            </ol>
        </div>
        <div class="col-sm-6 text-end">
            <button wire:click="openCreate" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Tambah Badal
            </button>
        </div>
    </div>

    {{-- Input Form --}}
    @if($showForm)
    <div class="row">
        <div class="col-lg-6">
            <div class="card border-primary">
                <div class="card-header bg-primary">
                    <h4 class="card-title text-white mb-0">Atur Guru Badal</h4>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tanggal <span class="text-danger">*</span></label>
                        <input type="date" wire:model.live="date"
                               class="form-control @error('date') is-invalid @enderror">
                        @error('date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Jadwal <span class="text-danger">*</span></label>
                        <select wire:model="schedule_id" class="form-select @error('schedule_id') is-invalid @enderror">
                            <option value="">-- Pilih Jadwal --</option>
                            @foreach($schedules as $schedule)
                                <option value="{{ $schedule->id }}">
                                    {{ $schedule->subject->name }} — {{ $schedule->class_->name }}
                                    ({{ $schedule->time_range }}) — {{ $schedule->teacher->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('schedule_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Guru Pengganti <span class="text-danger">*</span></label>
                        <select wire:model="substitute_user_id"
                                class="form-select @error('substitute_user_id') is-invalid @enderror">
                            <option value="">-- Pilih Guru Pengganti --</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}">{{ $teacher->name }} ({{ ucfirst($teacher->role) }})</option>
                            @endforeach
                        </select>
                        @error('substitute_user_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Catatan</label>
                        <textarea wire:model="notes" class="form-control" rows="2"
                                  placeholder="Alasan penggantian (opsional)"></textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button wire:click="save" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> Simpan
                        </button>
                        <button wire:click="cancelForm" class="btn btn-outline-secondary">Batal</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Substitution History --}}
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="bi bi-arrow-repeat me-2"></i> Riwayat Guru Badal (7 Hari Terakhir)
                    </h4>
                </div>
                <div class="card-body">
                    @if($substitutions->isEmpty())
                        <div class="text-center py-5">
                            <i class="bi bi-check-circle fs-1 text-success"></i>
                            <p class="text-muted mt-2">Tidak ada penggantian guru terdaftar.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="40">#</th>
                                        <th>Tanggal</th>
                                        <th>Jadwal</th>
                                        <th>Guru Asli</th>
                                        <th>Guru Pengganti</th>
                                        <th>Catatan</th>
                                        <th width="70">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($substitutions as $sub)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ \Carbon\Carbon::parse($sub->date)->format('d/m/Y') }}</td>
                                        <td>
                                            <strong>{{ $sub->schedule->subject->name }}</strong>
                                            — {{ $sub->schedule->class_->name }}
                                        </td>
                                        <td>{{ $sub->schedule->teacher->name }}</td>
                                        <td class="fw-semibold text-primary">{{ $sub->substituteUser->name }}</td>
                                        <td class="text-muted fs-12">{{ $sub->notes ?? '-' }}</td>
                                        <td>
                                            <button wire:click="confirmDelete({{ $sub->id }})"
                                                    class="btn btn-danger btn-xs sharp">
                                                <i class="bi bi-trash"></i>
                                            </button>
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

@script
<script>
    Livewire.on('confirm-delete', (event) => {
        let id = event[0].id;
        let name = event[0].name;

        Swal.fire({
            title: 'Hapus guru badal?',
            text: `${name} akan dihapus dari sistem.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: 'var(--primary)',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                $wire.delete(id);
            }
        });
    });
</script>
@endscript

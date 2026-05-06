@section('title', 'Kalender Libur')
@section('page-title', 'Kalender Libur')

<div class="container-fluid">
    <div class="row page-titles">
        <div class="col-sm-6">
            <h4 class="breadcrumb-title">Kalender Libur & Acara</h4>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item">Sistem</li>
                <li class="breadcrumb-item active">Kalender</li>
            </ol>
        </div>
        <div class="col-sm-6 text-end">
            <button wire:click="openCreate" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Tambah Event
            </button>
        </div>
    </div>

    {{-- Inline Form --}}
    @if($showForm)
    <div class="row">
        <div class="col-lg-6">
            <div class="card border-primary">
                <div class="card-header bg-primary">
                    <h4 class="card-title text-white mb-0">
                        {{ $editingId ? 'Edit' : 'Tambah' }} Event Kalender
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" wire:model="date"
                                   class="form-control @error('date') is-invalid @enderror">
                            @error('date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Jenis <span class="text-danger">*</span></label>
                            <select wire:model="type" class="form-select">
                                <option value="holiday">Hari Libur</option>
                                <option value="special_event">Acara Khusus</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Deskripsi</label>
                        <input type="text" wire:model="description"
                               class="form-control @error('description') is-invalid @enderror"
                               placeholder="Maulid Nabi, Akhir Semester, dll." maxlength="150">
                        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="d-flex gap-2">
                        <button wire:click="save" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> Simpan
                        </button>
                        <button wire:click="cancelEdit" class="btn btn-outline-secondary">Batal</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Events List --}}
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="bi bi-calendar-event me-2"></i> Daftar Event
                    </h4>
                    <input type="month" wire:model.live="monthFilter" class="form-control form-control-sm" style="width: 180px;">
                </div>
                <div class="card-body">
                    @if($events->isEmpty())
                        <div class="text-center py-5">
                            <i class="bi bi-calendar-x fs-1 text-muted"></i>
                            <p class="text-muted mt-2">Tidak ada event pada bulan ini.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="40">#</th>
                                        <th>Tanggal</th>
                                        <th>Hari</th>
                                        <th>Jenis</th>
                                        <th>Deskripsi</th>
                                        <th width="120">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($events as $event)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td class="fw-semibold">{{ $event->date->format('d/m/Y') }}</td>
                                        <td>{{ $event->date->translatedFormat('l') }}</td>
                                        <td>
                                            @if($event->type === 'holiday')
                                                <span class="badge bg-danger">Libur</span>
                                            @else
                                                <span class="badge bg-info text-dark">Acara</span>
                                            @endif
                                        </td>
                                        <td>{{ $event->description ?? '-' }}</td>
                                        <td>
                                            <button wire:click="edit({{ $event->id }})"
                                                    class="btn btn-primary btn-xs sharp me-1">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button wire:click="confirmDelete({{ $event->id }})"
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
            title: 'Hapus event ini?',
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

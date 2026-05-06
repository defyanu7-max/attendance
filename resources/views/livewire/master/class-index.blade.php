@section('title', 'Manajemen Kelas')
@section('page-title', 'Manajemen Kelas')

<div class="container-fluid">
    <div class="row page-titles">
        <div class="col-sm-6">
            <h4 class="breadcrumb-title">Manajemen Kelas</h4>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Kelas</li>
            </ol>
        </div>
        <div class="col-sm-6 text-end">
            <button wire:click="openCreate" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Tambah Kelas
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
                        {{ $editingId ? 'Edit' : 'Tambah' }} Kelas
                    </h4>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Kelas <span class="text-danger">*</span></label>
                        <input type="text" wire:model="name"
                               class="form-control @error('name') is-invalid @enderror"
                               placeholder="10A / 11 IPA 1">
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    @if($isSuperadmin)
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Unit <span class="text-danger">*</span></label>
                        <select wire:model.live="unit_id" class="form-select @error('unit_id') is-invalid @enderror">
                            <option value="">-- Pilih Unit --</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                            @endforeach
                        </select>
                        @error('unit_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Walikelas</label>
                        <select wire:model="homeroom_teacher_id"
                                class="form-select @error('homeroom_teacher_id') is-invalid @enderror">
                            <option value="">-- Pilih Walikelas --</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}">{{ $teacher->name }} ({{ ucfirst($teacher->role) }})</option>
                            @endforeach
                        </select>
                        @error('homeroom_teacher_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
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

    {{-- Class Table --}}
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="bi bi-building me-2"></i> Daftar Kelas
                        @if($activeYear)
                            <span class="badge bg-primary ms-2">TA {{ $activeYear->name }}</span>
                        @endif
                    </h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="40">#</th>
                                    <th>Nama Kelas</th>
                                    <th>Unit</th>
                                    <th>Walikelas</th>
                                    <th>Jumlah Santri</th>
                                    <th width="120">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($classes as $class)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="fw-semibold">{{ $class->name }}</td>
                                    <td><span class="badge bg-primary">{{ $class->unit->name }}</span></td>
                                    <td>{{ $class->homeroomTeacher?->name ?? '-' }}</td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $class->students_count }} santri</span>
                                    </td>
                                    <td>
                                        <button wire:click="edit({{ $class->id }})"
                                                class="btn btn-primary btn-xs sharp me-1">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button wire:click="confirmDelete({{ $class->id }})"
                                                class="btn btn-danger btn-xs sharp">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        Belum ada kelas terdaftar.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
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
            title: 'Hapus kelas ini?',
            text: `Kelas "${name}" akan dihapus dari sistem.`,
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

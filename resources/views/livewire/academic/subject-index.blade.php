@section('title', 'Mata Pelajaran')
@section('page-title', 'Mata Pelajaran')

<div class="container-fluid">
    <div class="row page-titles">
        <div class="col-sm-6">
            <h4 class="breadcrumb-title">Mata Pelajaran</h4>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Mata Pelajaran</li>
            </ol>
        </div>
        <div class="col-sm-6 text-end">
            @if($canManage)
                <button wire:click="openCreate" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-1"></i> Tambah Mapel
                </button>
            @endif
        </div>
    </div>

    {{-- Inline Form --}}
    @if($showForm)
    <div class="row">
        <div class="col-lg-6">
            <div class="card border-primary">
                <div class="card-header bg-primary">
                    <h4 class="card-title text-white mb-0">
                        {{ $editingId ? 'Edit' : 'Tambah' }} Mata Pelajaran
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">Kode <span class="text-danger">*</span></label>
                            <input type="text" wire:model="code"
                                   class="form-control @error('code') is-invalid @enderror"
                                   placeholder="MTK" maxlength="10">
                            @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-8 mb-3">
                            <label class="form-label fw-semibold">Nama Mapel <span class="text-danger">*</span></label>
                            <input type="text" wire:model="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   placeholder="Matematika">
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    @if(auth()->user()->isSuperadmin())
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Unit <span class="text-danger">*</span></label>
                        <select wire:model="unit_id" class="form-select @error('unit_id') is-invalid @enderror">
                            <option value="">-- Pilih Unit --</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                            @endforeach
                        </select>
                        @error('unit_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    @endif
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

    {{-- Subject Table --}}
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="bi bi-book me-2"></i> Daftar Mata Pelajaran
                    </h4>
                    <div style="width: 250px;">
                        <input type="text" wire:model.live.debounce.300ms="search"
                               class="form-control form-control-sm"
                               placeholder="Cari mapel...">
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="40">#</th>
                                    <th width="100">Kode</th>
                                    <th>Nama Mata Pelajaran</th>
                                    <th>Unit</th>
                                    @if($canManage)
                                        <th width="120">Aksi</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($subjects as $subject)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><code>{{ $subject->code }}</code></td>
                                    <td>{{ $subject->name }}</td>
                                    <td><span class="badge bg-primary">{{ $subject->unit->name }}</span></td>
                                    @if($canManage)
                                    <td>
                                        <button wire:click="edit({{ $subject->id }})"
                                                class="btn btn-primary btn-xs sharp me-1">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button wire:click="confirmDelete({{ $subject->id }})"
                                                class="btn btn-danger btn-xs sharp">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                    @endif
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="{{ $canManage ? 5 : 4 }}" class="text-center text-muted py-4">
                                        Belum ada mata pelajaran.
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
            title: 'Hapus mata pelajaran?',
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

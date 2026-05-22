<div class="container-fluid">
    <div class="row page-titles">
        <div class="col-sm-6">
            <h4 class="breadcrumb-title">Data Master</h4>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Manajemen Guru</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Daftar Guru</h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('export.teachers.template') }}" class="btn btn-info btn-sm text-white">
                            <i class="bi bi-download me-1"></i> Unduh Template
                        </a>
                        <a href="{{ route('import.index', ['type' => 'teachers']) }}" class="btn btn-success btn-sm">
                            <i class="bi bi-file-earmark-excel me-1"></i> Import Excel
                        </a>
                        <a href="{{ route('teachers.create') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus-lg me-1"></i> Tambah Guru
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if (session()->has('message'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('message') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <input type="text" class="form-control" placeholder="Cari nama atau NIP..." wire:model.live.debounce.300ms="search">
                        </div>
                        @if(auth()->user()->role === 'superadmin')
                        <div class="col-md-3">
                            <select class="form-select" wire:model.live="unitId">
                                <option value="">Semua Unit</option>
                                @foreach($units as $unit)
                                <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        <div class="col-md-2">
                            <select class="form-select" wire:model.live="perPage">
                                <option value="10">10 per halaman</option>
                                <option value="25">25 per halaman</option>
                                <option value="50">50 per halaman</option>
                                <option value="100">100 per halaman</option>
                            </select>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover table-bordered mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="50">#</th>
                                    <th>Nama Guru</th>
                                    <th>NIP / Username</th>
                                    <th>Unit</th>
                                    <th>No. HP</th>
                                    <th width="150" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($teachers as $index => $teacher)
                                <tr>
                                    <td>{{ $teachers->firstItem() + $index }}</td>
                                    <td>{{ $teacher->name }}</td>
                                    <td><code>{{ $teacher->username }}</code></td>
                                    <td><span class="badge bg-primary">{{ $teacher->unit?->name ?? '-' }}</span></td>
                                    <td>{{ $teacher->phone ?? '-' }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('teachers.edit', $teacher->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        @if(Gate::allows('delete-teacher', $teacher))
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDelete('{{ $teacher->id }}')" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">Tidak ada data guru ditemukan.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $teachers->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function confirmDelete(id) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data guru ini akan dihapus secara soft-delete!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                Livewire.dispatch('delete-teacher', {
                    id: id
                });
            }
        });
    }

    document.addEventListener('livewire:initialized', () => {
        Livewire.on('notify', ({ type, message }) => {
            toastr[type](message);
        });
    });
</script>
@endpush
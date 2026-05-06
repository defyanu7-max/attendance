@section('title', 'Data Santri')
@section('page-title', 'Data Santri')

<div class="container-fluid">
    <div class="row page-titles">
        <div class="col-sm-6">
            <h4 class="breadcrumb-title">Data Santri</h4>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Santri</li>
            </ol>
        </div>
        <div class="col-sm-6 text-end">
            <a href="{{ route('export.students') }}" class="btn btn-success me-2">
                <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
            </a>
            <a href="{{ route('students.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Tambah Santri
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-md-4">
                            <h4 class="card-title mb-0">
                                <i class="bi bi-people me-2"></i> Daftar Santri
                            </h4>
                        </div>
                        <div class="col-md-4">
                            <select wire:model.live="statusFilter" class="form-select form-select-sm">
                                <option value="aktif">Aktif</option>
                                <option value="lulus">Lulus</option>
                                <option value="pindah">Pindah</option>
                                <option value="dikeluarkan">Dikeluarkan</option>
                                <option value="">Semua Status</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="text" wire:model.live.debounce.300ms="search"
                                   class="form-control form-control-sm"
                                   placeholder="Cari nama / NIS / NISN...">
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="40">#</th>
                                    <th>Nama</th>
                                    <th>NIS</th>
                                    <th>NISN</th>
                                    <th>Kelas</th>
                                    <th>Status</th>
                                    <th width="120">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($students as $student)
                                <tr>
                                    <td>{{ $students->firstItem() + $loop->index }}</td>
                                    <td class="fw-semibold">{{ $student->name }}</td>
                                    <td><code class="fs-12">{{ $student->nis }}</code></td>
                                    <td class="text-muted fs-12">{{ $student->nisn ?? '-' }}</td>
                                    <td>{{ $student->classes->first()?->name ?? '-' }}</td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'aktif' => 'success',
                                                'lulus' => 'info',
                                                'pindah' => 'warning',
                                                'dikeluarkan' => 'danger',
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $statusColors[$student->status] ?? 'secondary' }}">
                                            {{ ucfirst($student->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('students.edit', $student) }}"
                                           class="btn btn-primary btn-xs sharp me-1">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        @if($canDelete)
                                        <button wire:click="confirmDelete({{ $student->id }})"
                                                class="btn btn-danger btn-xs sharp">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        Tidak ada data santri ditemukan.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-3">
                        {{ $students->links() }}
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
            title: 'Hapus santri ini?',
            text: `${name} akan dihapus (soft delete) dari sistem.`,
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

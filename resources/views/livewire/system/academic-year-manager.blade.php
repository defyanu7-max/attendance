@section('title', 'Tahun Ajaran')
@section('page-title', 'Manajemen Tahun Ajaran')

<div class="container-fluid">

    {{-- Breadcrumb --}}
    <div class="row page-titles">
        <div class="col-sm-6">
            <h4 class="breadcrumb-title">Tahun Ajaran</h4>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Tahun Ajaran</li>
            </ol>
        </div>
        <div class="col-sm-6 text-end">
            @can('manage-system')
            <button wire:click="openCreate" class="btn btn-primary" id="btn-create-year">
                <i class="bi bi-plus-lg me-1"></i> Tambah Tahun Ajaran
            </button>
            @endcan
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 col-xl-6">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h4 class="card-title mb-0">
                        <i class="bi bi-calendar-range me-2"></i> Daftar Tahun Ajaran
                    </h4>
                    <span class="badge bg-secondary">{{ $years->count() }} tahun</span>
                </div>
                <div class="card-body p-0">
                    @if($years->isEmpty())
                        <div class="text-center py-5">
                            <i class="bi bi-calendar-x fs-1 text-muted opacity-25"></i>
                            <p class="text-muted mt-2">Belum ada tahun ajaran. Buat yang pertama!</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tahun Ajaran</th>
                                        <th class="d-none d-md-table-cell">Mulai</th>
                                        <th class="d-none d-md-table-cell">Selesai</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-end pe-3">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($years as $year)
                                    <tr wire:key="year-{{ $year->id }}"
                                        class="{{ $year->is_active ? 'table-success' : '' }}">
                                        <td>
                                            <span class="fw-semibold">{{ $year->name }}</span>
                                            {{-- Tanggal tampil di bawah nama di mobile --}}
                                            <small class="d-block d-md-none text-muted">
                                                {{ $year->start_date->format('d/m/Y') }} –
                                                {{ $year->end_date->format('d/m/Y') }}
                                            </small>
                                        </td>
                                        <td class="d-none d-md-table-cell text-muted">
                                            {{ $year->start_date->format('d M Y') }}
                                        </td>
                                        <td class="d-none d-md-table-cell text-muted">
                                            {{ $year->end_date->format('d M Y') }}
                                        </td>
                                        <td class="text-center">
                                            @if($year->is_active)
                                                <span class="badge bg-success px-3 py-2">
                                                    <i class="bi bi-check-circle me-1"></i> Aktif
                                                </span>
                                            @else
                                                <span class="badge bg-light text-muted border px-3 py-2">
                                                    Tidak Aktif
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-3">
                                            <div class="d-flex justify-content-end gap-1">
                                                @can('manage-system')
                                                {{-- Tombol Aktifkan (hanya jika tidak aktif) --}}
                                                @unless($year->is_active)
                                                <button
                                                    wire:click="activate({{ $year->id }})"
                                                    wire:loading.attr="disabled"
                                                    wire:target="activate({{ $year->id }})"
                                                    class="btn btn-outline-success btn-xs py-1 px-2"
                                                    title="Jadikan Aktif"
                                                    id="btn-activate-{{ $year->id }}"
                                                >
                                                    <span wire:loading.remove wire:target="activate({{ $year->id }})">
                                                        <i class="bi bi-toggle-off"></i>
                                                    </span>
                                                    <span wire:loading wire:target="activate({{ $year->id }})">
                                                        <span class="spinner-border spinner-border-sm"></span>
                                                    </span>
                                                </button>
                                                @endunless

                                                {{-- Tombol Edit --}}
                                                <button
                                                    wire:click="openEdit({{ $year->id }})"
                                                    class="btn btn-xs btn-outline-primary py-1 px-2"
                                                    title="Edit"
                                                    id="btn-edit-year-{{ $year->id }}"
                                                >
                                                    <i class="bi bi-pencil"></i>
                                                </button>

                                                {{-- Tombol Hapus (non-aktif saja) --}}
                                                @unless($year->is_active)
                                                <button
                                                    wire:click="delete({{ $year->id }})"
                                                    wire:confirm="Hapus tahun ajaran '{{ $year->name }}'? Tindakan ini tidak dapat dibatalkan."
                                                    class="btn btn-xs btn-outline-danger py-1 px-2"
                                                    title="Hapus"
                                                    id="btn-delete-year-{{ $year->id }}"
                                                >
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                                @endunless
                                                @endcan
                                            </div>
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

        {{-- Info panel --}}
        <div class="col-lg-4 col-xl-6">
            <div class="card border-0 bg-light">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">
                        <i class="bi bi-info-circle text-primary me-2"></i>Panduan Tahun Ajaran
                    </h6>
                    <ul class="mb-0 text-muted fs-13" style="line-height: 2">
                        <li>Hanya <strong>satu</strong> tahun ajaran yang bisa aktif sekaligus.</li>
                        <li>Mengaktifkan tahun baru otomatis menonaktifkan yang sebelumnya.</li>
                        <li>Tahun aktif tidak bisa dihapus.</li>
                        <li>Format nama: <code>2025/2026</code> atau <code>2025-2026 Ganjil</code>.</li>
                        <li>Jadwal dan kelas akan terikat ke tahun ajaran yang dipilih.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- ================================================================ --}}
    {{-- MODAL FORM                                                       --}}
    {{-- ================================================================ --}}
    @if($modalOpen)
    <div class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,0.5)" id="modal-academic-year"
         role="dialog" aria-modal="true" aria-labelledby="modal-title-year">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 440px">
            <div class="modal-content">
                <div class="modal-header py-3">
                    <h5 class="modal-title fs-15" id="modal-title-year">
                        <i class="bi bi-calendar-plus me-2 text-primary"></i>
                        {{ $editingId ? 'Edit' : 'Tambah' }} Tahun Ajaran
                    </h5>
                    <button wire:click="closeModal" type="button" class="btn-close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Nama Tahun Ajaran <span class="text-danger">*</span>
                        </label>
                        <input
                            wire:model="formName"
                            type="text"
                            class="form-control @error('formName') is-invalid @enderror"
                            placeholder="Contoh: 2025/2026"
                            maxlength="20"
                            id="input-year-name"
                            autofocus
                        >
                        @error('formName')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">
                                Tanggal Mulai <span class="text-danger">*</span>
                            </label>
                            <input
                                wire:model="formStartDate"
                                type="date"
                                class="form-control @error('formStartDate') is-invalid @enderror"
                                id="input-year-start"
                            >
                            @error('formStartDate')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">
                                Tanggal Selesai <span class="text-danger">*</span>
                            </label>
                            <input
                                wire:model="formEndDate"
                                type="date"
                                class="form-control @error('formEndDate') is-invalid @enderror"
                                id="input-year-end"
                                min="{{ $formStartDate }}"
                            >
                            @error('formEndDate')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-3">
                        <div class="form-check form-switch">
                            <input
                                wire:model="formIsActive"
                                class="form-check-input"
                                type="checkbox"
                                role="switch"
                                id="input-year-active"
                            >
                            <label class="form-check-label fw-semibold" for="input-year-active">
                                Jadikan Tahun Ajaran Aktif
                            </label>
                        </div>
                        @if($formIsActive)
                        <div class="alert alert-warning py-2 mt-2 fs-12 mb-0">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            Tahun ajaran lain yang sedang aktif akan otomatis dinonaktifkan.
                        </div>
                        @endif
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button wire:click="closeModal" class="btn btn-light">Batal</button>
                    <button
                        wire:click="save"
                        wire:loading.attr="disabled"
                        wire:target="save"
                        class="btn btn-primary"
                        id="btn-save-year"
                    >
                        <span wire:loading.remove wire:target="save">
                            <i class="bi bi-save me-1"></i> Simpan
                        </span>
                        <span wire:loading wire:target="save">
                            <span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>

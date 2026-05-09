<div class="container-fluid">
    <div class="row page-titles">
        <div class="col-sm-6">
            <h4 class="breadcrumb-title">Data Master</h4>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Import Data</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 col-md-10 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="card-title text-white mb-0"><i class="bi bi-file-earmark-excel me-2"></i> Import Data Massal</h4>
                </div>
                <div class="card-body">
                    <form wire:submit="import">
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label class="form-label fw-bold">Jenis Data <span class="text-danger">*</span></label>
                                <select class="form-select @error('type') is-invalid @enderror" wire:model.live="type">
                                    <option value="students">Data Santri</option>
                                    <option value="teachers">Data Guru</option>
                                </select>
                                @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            @if(auth()->user()->role === 'superadmin')
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Pilih Unit (Opsional)</label>
                                <select class="form-select" wire:model="unitId">
                                    <option value="">-- Sesuai Akun (Default) --</option>
                                    @foreach($units as $unit)
                                        <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">File Excel (.xlsx, .xls) <span class="text-danger">*</span></label>
                            <input type="file" class="form-control @error('file') is-invalid @enderror" wire:model="file" accept=".xlsx,.xls">
                            <div wire:loading wire:target="file" class="text-primary mt-1" style="font-size: 13px;">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Mengunggah file, mohon tunggu...
                            </div>
                            @error('file') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="alert alert-info mb-4">
                            <i class="bi bi-info-circle me-1"></i> Pastikan format file sesuai dengan template.
                            <br>
                            @if($type === 'students')
                                <a href="{{ route('export.students.template') }}" class="btn btn-sm btn-info text-white mt-2">
                                    <i class="bi bi-download"></i> Unduh Template Santri
                                </a>
                            @elseif($type === 'teachers')
                                <a href="{{ route('export.teachers.template') }}" class="btn btn-sm btn-info text-white mt-2">
                                    <i class="bi bi-download"></i> Unduh Template Guru
                                </a>
                            @endif
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-success px-4" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="import">
                                    <i class="bi bi-upload me-1"></i> Import Sekarang
                                </span>
                                <span wire:loading wire:target="import">
                                    <span class="spinner-border spinner-border-sm me-1"></span> Sedang Memproses...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('notify', ({ type, message }) => {
            toastr[type](message);
        });
    });
</script>
@endpush

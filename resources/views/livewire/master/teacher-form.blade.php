<div class="container-fluid">
    <div class="row page-titles">
        <div class="col-sm-6">
            <h4 class="breadcrumb-title">Data Master</h4>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('teachers.index') }}">Manajemen Guru</a></li>
                <li class="breadcrumb-item active">{{ $isEdit ? 'Edit' : 'Tambah' }} Guru</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ $isEdit ? 'Edit Data Guru' : 'Tambah Guru Baru' }}</h4>
                </div>
                <div class="card-body">
                    <form wire:submit="save">
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" wire:model="name" placeholder="Contoh: Ustadz Fulan">
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">NIP / Username <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('username') is-invalid @enderror" wire:model="username" placeholder="Masukkan NIP atau username">
                                @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Unit <span class="text-danger">*</span></label>
                                <select class="form-select @error('unit_id') is-invalid @enderror" wire:model="unit_id" @if(auth()->user()->role !== 'superadmin') disabled @endif>
                                    <option value="">-- Pilih Unit --</option>
                                    @foreach($units as $unit)
                                        <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                    @endforeach
                                </select>
                                @error('unit_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Password {{ $isEdit ? '(Opsional)' : '<span class="text-danger">*</span>' }}</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" wire:model="password" placeholder="{{ $isEdit ? 'Kosongkan jika tidak ingin mengubah' : 'Minimal 6 karakter' }}">
                                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">No. HP / WhatsApp (Opsional)</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" wire:model="phone" placeholder="Contoh: 08123456789">
                                @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('teachers.index') }}" class="btn btn-light">Batal</a>
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                <span wire:loading.remove><i class="bi bi-save me-1"></i> Simpan</span>
                                <span wire:loading><span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@section('title', 'Login')

<div class="col-md-6 col-lg-5">
    <div class="authincation-content">
        <div class="row no-gutters">
            <div class="col-xl-12">
                <div class="auth-form">
                    <div class="text-center mb-4">
                        <h3 class="login-brand-title">PPNI System</h3>
                        <p class="text-muted">Sistem Absensi KBM — Pondok Pesantren</p>
                    </div>

                    {{--
                        PENTING: Gunakan wire:submit (tanpa .prevent).
                        Livewire 3 sudah otomatis handle preventDefault.
                        wire:submit.prevent menyebabkan konflik yang membuat
                        loading spinner tidak pernah berhenti.
                    --}}
                    <form wire:submit="login">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-person me-1"></i> Username / NIP
                            </label>
                            <input
                                type="text"
                                wire:model="username"
                                class="form-control form-control-lg @error('username') is-invalid @enderror"
                                placeholder="Masukkan username atau NIP"
                                autocomplete="username"
                                autofocus
                            >
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-key me-1"></i> Password
                            </label>
                            <input
                                type="password"
                                wire:model="password"
                                class="form-control form-control-lg @error('password') is-invalid @enderror"
                                placeholder="Masukkan password"
                                autocomplete="current-password"
                            >
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="text-center mt-4">
                            <button
                                type="submit"
                                class="btn btn-primary btn-block w-100 py-3 fs-16"
                                wire:loading.attr="disabled"
                                wire:target="login"
                                id="btn-login"
                            >
                                <span wire:loading.remove wire:target="login">
                                    <i class="bi bi-box-arrow-in-right me-1"></i> Masuk
                                </span>
                                <span wire:loading wire:target="login">
                                    <span class="spinner-border spinner-border-sm me-1"></span>
                                    Memproses...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

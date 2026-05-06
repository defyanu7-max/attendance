@section('title', 'Pengaturan Sistem')
@section('page-title', 'Pengaturan Sistem')

<div class="container-fluid">
    <div class="row page-titles">
        <div class="col-sm-6">
            <h4 class="breadcrumb-title">Pengaturan Sistem</h4>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item">Sistem</li>
                <li class="breadcrumb-item active">Pengaturan</li>
            </ol>
        </div>
    </div>

    <div class="row">
        {{-- Settings Form --}}
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="bi bi-gear me-2"></i> Pengaturan Alpha & Notifikasi
                    </h4>
                </div>
                <div class="card-body">
                    <form wire:submit="save">
                        {{-- Alpha Threshold Mode --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Mode Hitung Alpha</label>
                            <select wire:model="alpha_threshold_mode" class="form-select">
                                <option value="cumulative">Kumulatif (seluruh semester)</option>
                                <option value="weekly">Per Minggu</option>
                            </select>
                            <div class="form-text">Menentukan cara menghitung jumlah alpha untuk trigger notifikasi.</div>
                        </div>

                        {{-- Alpha Threshold Count --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Batas Alpha Maksimal</label>
                            <input type="number" wire:model="alpha_threshold_count"
                                   class="form-control @error('alpha_threshold_count') is-invalid @enderror"
                                   min="1" max="99">
                            @error('alpha_threshold_count') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <div class="form-text">Notifikasi otomatis dibuat saat santri mencapai jumlah ini.</div>
                        </div>

                        {{-- Attendance Cutoff Time --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Jam Batas Input Absensi</label>
                            <input type="time" wire:model="attendance_cutoff_time"
                                   class="form-control @error('attendance_cutoff_time') is-invalid @enderror">
                            @error('attendance_cutoff_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <div class="form-text">Guru tidak bisa input absensi setelah jam ini. Default: 14:00 WIB.</div>
                        </div>

                        {{-- WA Notification Toggle --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Notifikasi WhatsApp</label>
                            <select wire:model="wa_notification_enabled" class="form-select">
                                <option value="true">Aktif</option>
                                <option value="false">Non-Aktif</option>
                            </select>
                            <div class="form-text">Toggle seluruh sistem notifikasi alpha WA.</div>
                        </div>

                        {{-- Default Weekend Days --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold d-block">Hari Libur Mingguan (Weekend)</label>
                            <div class="d-flex flex-wrap gap-3 mt-2">
                                @php
                                    $days = [
                                        0 => 'Minggu', 1 => 'Senin', 2 => 'Selasa', 
                                        3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu'
                                    ];
                                @endphp
                                @foreach($days as $val => $label)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                               value="{{ $val }}" 
                                               id="day_{{ $val }}"
                                               wire:model="default_weekend_days">
                                        <label class="form-check-label" for="day_{{ $val }}">
                                            {{ $label }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            <div class="form-text">Hari terpilih akan dianggap libur oleh sistem absensi otomatis.</div>
                        </div>

                        {{-- WA Message Template --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Template Pesan WA</label>
                            <textarea wire:model="wa_message_template"
                                      class="form-control @error('wa_message_template') is-invalid @enderror"
                                      rows="5"></textarea>
                            @error('wa_message_template') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <div class="form-text">
                                Variabel tersedia: <code>{nama_santri}</code>, <code>{nis}</code>,
                                <code>{kelas}</code>, <code>{jumlah_alpha}</code>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary px-4"
                                wire:loading.attr="disabled">
                            <span wire:loading.remove>
                                <i class="bi bi-save me-1"></i> Simpan Pengaturan
                            </span>
                            <span wire:loading>
                                <span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...
                            </span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Change Log --}}
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="bi bi-clock-history me-2"></i> Riwayat Perubahan
                    </h4>
                </div>
                <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                    @if($recentLogs->isEmpty())
                        <p class="text-muted text-center py-3">Belum ada riwayat perubahan.</p>
                    @else
                        @foreach($recentLogs as $log)
                        <div class="border-bottom pb-2 mb-2">
                            <div class="d-flex justify-content-between">
                                <code class="fs-12">{{ $log->key }}</code>
                                <span class="fs-12 text-muted">{{ $log->created_at?->format('d/m H:i') }}</span>
                            </div>
                            <div class="fs-12">
                                <span class="text-danger">{{ \Illuminate\Support\Str::limit($log->old_value, 30) }}</span>
                                → <span class="text-success">{{ \Illuminate\Support\Str::limit($log->new_value, 30) }}</span>
                            </div>
                            <div class="fs-12 text-muted">oleh {{ $log->changedByUser?->name ?? 'System' }}</div>
                        </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

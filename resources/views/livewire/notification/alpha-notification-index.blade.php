@section('title', 'Notifikasi Alpha')
@section('page-title', 'Notifikasi Alpha')

<div class="container-fluid">
    <div class="row page-titles">
        <div class="col-sm-6">
            <h4 class="breadcrumb-title">Notifikasi Alpha</h4>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Notifikasi Alpha</li>
            </ol>
        </div>
        <div class="col-sm-6 text-end">
            @if($pendingCount > 0)
                <span class="badge bg-danger fs-14 px-3 py-2">
                    <i class="bi bi-bell-fill me-1"></i> {{ $pendingCount }} Pending
                </span>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="bi bi-bell me-2"></i> Daftar Notifikasi
                    </h4>
                    <div class="d-flex align-items-center gap-2">
                        @if($statusFilter === 'pending' && $pendingCount > 0)
                        <button wire:click="sendAllPending"
                                wire:loading.attr="disabled"
                                wire:confirm="Dispatch job pengiriman semua {{ $pendingCount }} notifikasi pending ke queue?"
                                class="btn btn-success btn-sm">
                            <span wire:loading.remove wire:target="sendAllPending">
                                <i class="bi bi-send-fill me-1"></i> Kirim Semua ({{ $pendingCount }})
                            </span>
                            <span wire:loading wire:target="sendAllPending">
                                <span class="spinner-border spinner-border-sm me-1"></span> Mengirim...
                            </span>
                        </button>
                        @endif
                        <select wire:model.live="statusFilter" class="form-select form-select-sm" style="width: 150px;">
                            <option value="pending">Pending</option>
                            <option value="sent">Terkirim</option>
                            <option value="dismissed">Diabaikan</option>
                            <option value="">Semua</option>
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    @if($notifications->isEmpty())
                        <div class="text-center py-5">
                            <i class="bi bi-check-circle fs-1 text-success"></i>
                            <p class="text-muted mt-2">Tidak ada notifikasi alpha{{ $statusFilter === 'pending' ? ' yang perlu ditindaklanjuti' : '' }}.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="40">#</th>
                                        <th>Santri</th>
                                        <th width="80">Alpha</th>
                                        <th>Pesan WA</th>
                                        <th>Waktu</th>
                                        <th>Status</th>
                                        <th width="180">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($notifications as $notif)
                                    <tr>
                                        <td>{{ $notifications->firstItem() + $loop->index }}</td>
                                        <td>
                                            <strong>{{ $notif->student->name }}</strong>
                                            <br><code class="fs-12">{{ $notif->student->nis }}</code>
                                        </td>
                                        <td>
                                            <span class="badge badge-alpha fs-14">{{ $notif->alpha_count }}×</span>
                                        </td>
                                        <td>
                                            <span class="text-muted fs-12">{{ \Illuminate\Support\Str::limit($notif->message, 80) }}</span>
                                        </td>
                                        <td class="text-muted fs-12">
                                            {{ $notif->triggered_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td>
                                            @if($notif->status === 'pending')
                                                <span class="badge bg-warning text-dark">Pending</span>
                                            @elseif($notif->status === 'sent')
                                                <span class="badge bg-success">Terkirim</span>
                                                @if($notif->sentByUser)
                                                    <br><span class="fs-12 text-muted">oleh {{ $notif->sentByUser->name }}</span>
                                                @endif
                                            @else
                                                <span class="badge bg-secondary">Diabaikan</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($notif->status === 'pending')
                                                <button wire:click="copyMessage({{ $notif->id }})"
                                                        class="btn btn-info btn-xs me-1"
                                                        title="Salin pesan WA">
                                                    <i class="bi bi-clipboard"></i>
                                                </button>
                                                <button wire:click="sendNow({{ $notif->id }})"
                                                        wire:loading.attr="disabled"
                                                        wire:target="sendNow({{ $notif->id }})"
                                                        class="btn btn-success btn-xs me-1"
                                                        title="Kirim Sekarang">
                                                    <span wire:loading.remove wire:target="sendNow({{ $notif->id }})">
                                                        <i class="bi bi-send"></i>
                                                    </span>
                                                    <span wire:loading wire:target="sendNow({{ $notif->id }})">
                                                        <span class="spinner-border spinner-border-sm"></span>
                                                    </span>
                                                </button>
                                                <button wire:click="markAsSent({{ $notif->id }})"
                                                        class="btn btn-outline-success btn-xs me-1"
                                                        title="Tandai terkirim (manual)">
                                                    <i class="bi bi-check2"></i>
                                                </button>
                                                <button wire:click="dismiss({{ $notif->id }})"
                                                        class="btn btn-secondary btn-xs"
                                                        title="Abaikan">
                                                    <i class="bi bi-x-lg"></i>
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            {{ $notifications->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@script
<script>
    Livewire.on('copy-to-clipboard', (event) => {
        let message = event[0].message;
        navigator.clipboard.writeText(message).then(() => {
            toastr.success('Pesan WA berhasil disalin ke clipboard!');
        }).catch(() => {
            // Fallback for older browsers
            const textarea = document.createElement('textarea');
            textarea.value = message;
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);
            toastr.success('Pesan WA berhasil disalin ke clipboard!');
        });
    });
</script>
@endscript

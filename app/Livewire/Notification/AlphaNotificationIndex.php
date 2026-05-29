<?php

namespace App\Livewire\Notification;

use App\Jobs\ProcessNotificationQueueJob;
use App\Models\NotificationQueue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Notifikasi Alpha')]
class AlphaNotificationIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $statusFilter = 'pending';

    public function mount(): void
    {
        Gate::authorize('manage-notifications');
    }

    public function markAsSent(int $id): void
    {
        Gate::authorize('manage-notifications');
        $notif = NotificationQueue::findOrFail($id);
        $notif->update([
            'status' => 'sent',
            'sent_at' => now(),
            'sent_by' => Auth::id(),
        ]);
        $this->dispatch('notify', type: 'success', message: 'Notifikasi ditandai sudah terkirim.');
    }

    public function dismiss(int $id): void
    {
        Gate::authorize('manage-notifications');
        $notif = NotificationQueue::findOrFail($id);
        $notif->update(['status' => 'dismissed']);
        $this->dispatch('notify', type: 'info', message: 'Notifikasi ditandai selesai/diabaikan.');
    }

    public function copyMessage(int $id): void
    {
        $notif = NotificationQueue::findOrFail($id);
        $this->dispatch('copy-to-clipboard', message: $notif->message);
    }

    /**
     * Trigger manual pengiriman WA untuk satu notifikasi.
     * Menandai sebagai 'sent' dan mendispatch job.
     * (Job akan benar-benar mengirim saat integrasi WA API sudah aktif.)
     */
    public function sendNow(int $id): void
    {
        Gate::authorize('manage-notifications');

        $notif = NotificationQueue::findOrFail($id);

        if ($notif->status !== 'pending') {
            $this->dispatch('notify', type: 'warning', message: 'Notifikasi ini sudah tidak dalam status pending.');
            return;
        }

        // Dispatch job ke queue (atau sync jika QUEUE_CONNECTION=sync)
        ProcessNotificationQueueJob::dispatch();

        // Tandai manual sebagai sent
        $notif->update([
            'status'  => 'sent',
            'sent_at' => now(),
            'sent_by' => Auth::id(),
        ]);

        $this->dispatch('notify', type: 'success', message: 'Notifikasi berhasil dikirim manual.');
    }

    /**
     * Trigger pengiriman semua notifikasi pending sekaligus.
     */
    public function sendAllPending(): void
    {
        Gate::authorize('manage-notifications');

        $pendingCount = NotificationQueue::where('status', 'pending')->count();
        if ($pendingCount === 0) {
            $this->dispatch('notify', type: 'info', message: 'Tidak ada notifikasi pending.');
            return;
        }

        ProcessNotificationQueueJob::dispatch();

        $this->dispatch('notify', type: 'success', message: "Job pengiriman {$pendingCount} notifikasi telah didispatch ke queue.");
    }

    public function render()
    {
        $notifications = NotificationQueue::with(['student', 'sentByUser'])
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->orderBy('triggered_at', 'desc')
            ->paginate(20);

        $pendingCount = NotificationQueue::where('status', 'pending')->count();

        return view('livewire.notification.alpha-notification-index', [
            'notifications' => $notifications,
            'pendingCount' => $pendingCount,
        ]);
    }
}

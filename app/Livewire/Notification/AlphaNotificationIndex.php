<?php

namespace App\Livewire\Notification;

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

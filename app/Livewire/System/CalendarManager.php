<?php

namespace App\Livewire\System;

use App\Models\SchoolCalendar;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Kalender Libur')]
class CalendarManager extends Component
{
    public string $date = '';
    public string $type = 'holiday';
    public string $description = '';
    public ?int $unit_id = null;
    public ?int $editingId = null;
    public bool $showForm = false;

    public string $monthFilter = '';

    public function mount(): void
    {
        Gate::authorize('manage-system');
        $this->monthFilter = now()->format('Y-m');
    }

    public function openCreate(): void
    {
        $this->reset(['editingId', 'date', 'type', 'description', 'unit_id']);
        $this->type = 'holiday';
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $event = SchoolCalendar::findOrFail($id);
        $this->editingId = $event->id;
        $this->date = $event->date->format('Y-m-d');
        $this->type = $event->type;
        $this->description = $event->description ?? '';
        $this->unit_id = $event->unit_id;
        $this->showForm = true;
    }

    public function save(): void
    {
        Gate::authorize('manage-system');

        $this->validate([
            'date' => 'required|date',
            'type' => 'required|in:holiday,special_event',
            'description' => 'nullable|string|max:150',
        ]);

        SchoolCalendar::updateOrCreate(
            ['id' => $this->editingId],
            [
                'date' => $this->date,
                'type' => $this->type,
                'description' => $this->description ?: null,
                'unit_id' => $this->unit_id,
            ]
        );

        $this->reset(['editingId', 'date', 'type', 'description', 'unit_id', 'showForm']);
        $this->dispatch('notify', type: 'success', message: 'Kalender berhasil disimpan.');
    }

    public function cancelEdit(): void
    {
        $this->reset(['editingId', 'date', 'type', 'description', 'unit_id', 'showForm']);
    }

    public function confirmDelete(int $id): void
    {
        Gate::authorize('manage-system');
        $event = SchoolCalendar::findOrFail($id);
        $dateStr = $event->date->format('d/m/Y');
        $this->dispatch('confirm-delete', id: $id, name: "Event pada tanggal $dateStr");
    }

    public function delete(int $id): void
    {
        Gate::authorize('manage-system');
        SchoolCalendar::findOrFail($id)->delete();
        $this->dispatch('notify', type: 'success', message: 'Event berhasil dihapus.');
    }

    public function render()
    {
        $events = SchoolCalendar::query()
            ->when($this->monthFilter, function ($q) {
                $q->whereYear('date', substr($this->monthFilter, 0, 4))
                  ->whereMonth('date', substr($this->monthFilter, 5, 2));
            })
            ->orderBy('date')
            ->get();

        return view('livewire.system.calendar-manager', [
            'events' => $events,
        ]);
    }
}

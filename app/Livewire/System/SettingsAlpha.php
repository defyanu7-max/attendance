<?php

namespace App\Livewire\System;

use App\Models\Setting;
use App\Models\SettingsLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Pengaturan Sistem')]
class SettingsAlpha extends Component
{
    // Settings fields
    public string $alpha_threshold_mode = 'cumulative';
    public int $alpha_threshold_count = 5;
    public string $attendance_cutoff_time = '14:00';
    public string $wa_notification_enabled = 'true';
    public string $wa_message_template = '';
    public array $default_weekend_days = [0, 6];

    public function mount(): void
    {
        Gate::authorize('manage-system');

        // Load settings from DB
        $this->alpha_threshold_mode = Setting::getValue('alpha_threshold_mode', 'cumulative');
        $this->alpha_threshold_count = (int) Setting::getValue('alpha_threshold_count', '5');
        $this->attendance_cutoff_time = Setting::getValue('attendance_cutoff_time', '14:00');
        $this->wa_notification_enabled = Setting::getValue('wa_notification_enabled', 'true');
        $this->wa_message_template = Setting::getValue('wa_message_template', '');
        
        $weekendDays = Setting::getValue('default_weekend_days', [0, 6]);
        $this->default_weekend_days = is_array($weekendDays) ? $weekendDays : json_decode($weekendDays, true) ?? [0, 6];
    }

    public function save(): void
    {
        Gate::authorize('manage-system');

        $this->validate([
            'alpha_threshold_mode' => 'required|in:cumulative,weekly',
            'alpha_threshold_count' => 'required|integer|min:1|max:99',
            'attendance_cutoff_time' => 'required|date_format:H:i',
            'wa_notification_enabled' => 'required|in:true,false',
            'wa_message_template' => 'required|string',
        ]);

        $this->default_weekend_days = array_map('intval', $this->default_weekend_days);

        $settings = [
            'alpha_threshold_mode' => $this->alpha_threshold_mode,
            'alpha_threshold_count' => (string) $this->alpha_threshold_count,
            'attendance_cutoff_time' => $this->attendance_cutoff_time,
            'wa_notification_enabled' => $this->wa_notification_enabled,
            'wa_message_template' => $this->wa_message_template,
            'default_weekend_days' => $this->default_weekend_days,
        ];

        foreach ($settings as $key => $newValue) {
            $setting = Setting::where('key', $key)->first();
            $oldValue = $setting?->value; // raw string from DB
            $type = $setting?->type ?? 'string';

            // Convert raw DB string to PHP value for comparison
            $oldValueConverted = $oldValue;
            if ($type === 'json') {
                $oldValueConverted = json_decode($oldValue, true);
            } elseif ($type === 'integer') {
                $oldValueConverted = (int) $oldValue;
            } elseif ($type === 'boolean') {
                $oldValueConverted = filter_var($oldValue, FILTER_VALIDATE_BOOLEAN);
            }

            // Compare PHP values (using relaxed comparison for basic types, strict for arrays after normalization)
            $changed = false;
            if (is_array($newValue)) {
                sort($oldValueConverted);
                sort($newValue);
                $changed = ($oldValueConverted !== $newValue);
            } else {
                $changed = ($oldValueConverted != $newValue);
            }

            if ($changed) {
                // Update using helper (handles encoding)
                Setting::setValue($key, $newValue);

                // Log change with string representations
                SettingsLog::create([
                    'key' => $key,
                    'old_value' => is_array($oldValueConverted) ? json_encode($oldValueConverted) : (string) $oldValueConverted,
                    'new_value' => is_array($newValue) ? json_encode($newValue) : (string) $newValue,
                    'changed_by' => Auth::id() ?? 1, // Fallback to 1 if auth is weird
                ]);
            }
        }

        $this->dispatch('notify', type: 'success', message: 'Pengaturan berhasil disimpan.');
    }

    public function render()
    {
        $recentLogs = SettingsLog::with('changedByUser')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return view('livewire.system.settings-alpha', [
            'recentLogs' => $recentLogs,
        ]);
    }
}

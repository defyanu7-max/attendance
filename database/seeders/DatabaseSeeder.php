<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Setting;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // --- Units ---
        $mts = Unit::create(['name' => 'MTs']);
        $ma = Unit::create(['name' => 'MA']);

        // --- Academic Year ---
        AcademicYear::create([
            'name' => '2025/2026',
            'start_date' => '2025-07-14',
            'end_date' => '2026-06-20',
            'is_active' => true,
        ]);

        // --- Users ---
        $this->call(UserSeeder::class);

        // --- Settings ---
        $settings = [
            [
                'key' => 'alpha_threshold_mode',
                'value' => 'cumulative',
                'type' => 'string',
                'description' => 'Mode hitung alpha: cumulative atau weekly',
            ],
            [
                'key' => 'alpha_threshold_count',
                'value' => '5',
                'type' => 'integer',
                'description' => 'Jumlah alpha maksimal sebelum notifikasi',
            ],
            [
                'key' => 'attendance_cutoff_time',
                'value' => '14:00',
                'type' => 'string',
                'description' => 'Jam batas input absensi KBM (format HH:MM)',
            ],
            [
                'key' => 'wa_notification_enabled',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Toggle seluruh sistem notifikasi WA',
            ],
            [
                'key' => 'wa_message_template',
                'value' => 'Assalamu\'alaikum. Dengan hormat, kami informasikan bahwa santri bernama {nama_santri} (NIS: {nis}) dari kelas {kelas} telah tercatat alpha sebanyak {jumlah_alpha} kali. Mohon perhatian dan tindak lanjut. Jazakumullahu khairan.',
                'type' => 'string',
                'description' => 'Template pesan WA dengan variabel',
            ],
            [
                'key' => 'default_weekend_days',
                'value' => '[0,6]',
                'type' => 'json',
                'description' => 'Hari weekend default: 0=Minggu, 6=Sabtu',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }
    }
}

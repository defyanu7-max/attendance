<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Classes;
use App\Models\Subject;
use App\Models\Unit;
use App\Models\User;
use App\Models\Schedule;
use App\Models\Student;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DummyScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $activeYear = AcademicYear::active();
        if (! $activeYear) {
            $this->command->warn('No active AcademicYear found. Create one before seeding schedules.');
            return;
        }

        $units = Unit::all();
        if ($units->isEmpty()) {
            $this->command->warn('No units found. Seed units first (DatabaseSeeder creates MTs & MA).');
            return;
        }

        $subjectsTemplate = [
            ['code' => 'MATH', 'name' => 'Matematika'],
            ['code' => 'BIND', 'name' => 'Bahasa Indonesia'],
            ['code' => 'PAI',  'name' => 'Pendidikan Agama'],
        ];

        foreach ($units as $unit) {
            $this->command->info("Seeding unit: {$unit->name}");

            // ensure at least one class for the active year
            $class = Classes::updateOrCreate(
                [
                    'name' => '10A',
                    'unit_id' => $unit->id,
                    'academic_year_id' => $activeYear->id,
                ],
                [
                    'homeroom_teacher_id' => null,
                ]
            );

            // ensure subjects for this unit
            $subjects = [];
            foreach ($subjectsTemplate as $s) {
                $subject = Subject::updateOrCreate(
                    ['unit_id' => $unit->id, 'name' => $s['name']],
                    ['code' => $s['code'], 'name' => $s['name'], 'unit_id' => $unit->id]
                );
                $subjects[] = $subject;
            }

            // find or create a teacher for this unit
            $teacher = User::where('unit_id', $unit->id)
                ->whereIn('role', ['guru', 'walikelas'])
                ->first();

            if (! $teacher) {
                $username = 'dummy.guru.' . $unit->id;
                $teacher = User::updateOrCreate([
                    'username' => $username,
                ], [
                    'name' => "Dummy Guru {$unit->name}",
                    'password' => Hash::make('password'),
                    'role' => 'guru',
                    'unit_id' => $unit->id,
                    'phone' => null,
                ]);
            }

            // create some dummy students for the class (if missing)
            for ($i = 1; $i <= 5; $i++) {
                $nis = 'DUMMY-' . $unit->id . '-' . str_pad($i, 3, '0', STR_PAD_LEFT);
                $student = Student::updateOrCreate(
                    ['nis' => $nis],
                    [
                        'nisn' => null,
                        'name' => "Siswa Dummy {$i} {$unit->name}",
                        'unit_id' => $unit->id,
                        'status' => 'aktif',
                    ]
                );

                // attach to class for the active academic year
                $student->classes()->syncWithoutDetaching([
                    $class->id => [
                        'academic_year_id' => $activeYear->id,
                        'enrolled_at' => now(),
                    ]
                ]);
            }

            // create schedules: assign each subject to a different day/time
            $days = [1, 2, 3]; // Mon, Tue, Wed
            $times = [
                ['08:00:00', '09:30:00'],
                ['09:45:00', '11:15:00'],
                ['13:00:00', '14:30:00'],
            ];

            foreach ($subjects as $idx => $subject) {
                $day = $days[$idx % count($days)];
                $start = $times[$idx % count($times)][0];
                $end = $times[$idx % count($times)][1];

                Schedule::updateOrCreate(
                    [
                        'unit_id' => $unit->id,
                        'class_id' => $class->id,
                        'subject_id' => $subject->id,
                        'day_of_week' => $day,
                        'start_time' => $start,
                    ],
                    [
                        'end_time' => $end,
                        'teacher_id' => $teacher->id,
                        'academic_year_id' => $activeYear->id,
                        'unique_code' => 'DUMMY-' . $unit->id . '-' . $class->id . '-' . $subject->id . '-' . $day,
                    ]
                );
            }
        }

        $this->command->info('Dummy schedules seeded.');
    }
}

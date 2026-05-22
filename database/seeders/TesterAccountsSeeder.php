<?php

namespace Database\Seeders;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TesterAccountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // --- Superadmin tester (global) ---
        User::updateOrCreate([
            'username' => 'tester.superadmin',
        ], [
            'name' => 'Tester Superadmin',
            'password' => Hash::make('password'),
            'role' => 'superadmin',
            'unit_id' => null,
            'phone' => '080000000000',
        ]);

        // --- Per-unit tester accounts: admin, walikelas, guru ---
        $units = Unit::all();

        foreach ($units as $unit) {
            $slug = Str::slug($unit->name, '.');

            User::updateOrCreate([
                'username' => "tester.admin.{$slug}",
            ], [
                'name' => "Tester Admin {$unit->name}",
                'password' => Hash::make('password'),
                'role' => 'admin',
                'unit_id' => $unit->id,
                'phone' => '080000000' . str_pad($unit->id, 3, '0', STR_PAD_LEFT),
            ]);

            User::updateOrCreate([
                'username' => "tester.wk.{$slug}",
            ], [
                'name' => "Tester Walikelas {$unit->name}",
                'password' => Hash::make('password'),
                'role' => 'walikelas',
                'unit_id' => $unit->id,
                'phone' => '080000001' . str_pad($unit->id, 3, '0', STR_PAD_LEFT),
            ]);

            User::updateOrCreate([
                'username' => "tester.guru.{$slug}",
            ], [
                'name' => "Tester Guru {$unit->name}",
                'password' => Hash::make('password'),
                'role' => 'guru',
                'unit_id' => $unit->id,
                'phone' => '080000002' . str_pad($unit->id, 3, '0', STR_PAD_LEFT),
            ]);
        }
    }
}

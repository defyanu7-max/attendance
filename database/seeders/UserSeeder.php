<?php

namespace Database\Seeders;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mts = Unit::where('name', 'MTs')->first();
        $ma = Unit::where('name', 'MA')->first();

        // --- Superadmin (Tanpa Unit) ---
        User::updateOrCreate(
            ['username' => 'superadmin'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'role' => 'superadmin',
                'unit_id' => null,
                'phone' => '081234567890',
            ]
        );

        // --- MTs Users ---
        User::updateOrCreate(
            ['username' => 'admin.mts'],
            [
                'name' => 'Admin MTs',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'unit_id' => $mts->id,
                'phone' => '081234567891',
            ]
        );

        User::updateOrCreate(
            ['username' => 'ahmad.wk'],
            [
                'name' => 'Ustadz Ahmad (Walikelas MTs)',
                'password' => Hash::make('password'),
                'role' => 'walikelas',
                'unit_id' => $mts->id,
                'phone' => '081234567892',
            ]
        );

        User::updateOrCreate(
            ['username' => 'budi.guru'],
            [
                'name' => 'Ustadz Budi (Guru MTs)',
                'password' => Hash::make('password'),
                'role' => 'guru',
                'unit_id' => $mts->id,
                'phone' => '081234567893',
            ]
        );

        // --- MA Users ---
        User::updateOrCreate(
            ['username' => 'admin.ma'],
            [
                'name' => 'Admin MA',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'unit_id' => $ma->id,
                'phone' => '081234567894',
            ]
        );

        User::updateOrCreate(
            ['username' => 'chandra.wk'],
            [
                'name' => 'Ustadz Chandra (Walikelas MA)',
                'password' => Hash::make('password'),
                'role' => 'walikelas',
                'unit_id' => $ma->id,
                'phone' => '081234567895',
            ]
        );

        User::updateOrCreate(
            ['username' => 'dani.guru'],
            [
                'name' => 'Ustadz Dani (Guru MA)',
                'password' => Hash::make('password'),
                'role' => 'guru',
                'unit_id' => $ma->id,
                'phone' => '081234567896',
            ]
        );
    }
}

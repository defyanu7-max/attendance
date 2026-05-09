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
        User::create([
            'name' => 'Super Admin',
            'username' => 'superadmin',
            'password' => Hash::make('password'),
            'role' => 'superadmin',
            'unit_id' => null,
            'phone' => '081234567890',
        ]);

        // --- MTs Users ---
        User::create([
            'name' => 'Admin MTs',
            'username' => 'admin.mts',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'unit_id' => $mts->id,
            'phone' => '081234567891',
        ]);

        User::create([
            'name' => 'Ustadz Ahmad (Walikelas MTs)',
            'username' => 'ahmad.wk',
            'password' => Hash::make('password'),
            'role' => 'walikelas',
            'unit_id' => $mts->id,
            'phone' => '081234567892',
        ]);

        User::create([
            'name' => 'Ustadz Budi (Guru MTs)',
            'username' => 'budi.guru',
            'password' => Hash::make('password'),
            'role' => 'guru',
            'unit_id' => $mts->id,
            'phone' => '081234567893',
        ]);

        // --- MA Users ---
        User::create([
            'name' => 'Admin MA',
            'username' => 'admin.ma',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'unit_id' => $ma->id,
            'phone' => '081234567894',
        ]);

        User::create([
            'name' => 'Ustadz Chandra (Walikelas MA)',
            'username' => 'chandra.wk',
            'password' => Hash::make('password'),
            'role' => 'walikelas',
            'unit_id' => $ma->id,
            'phone' => '081234567895',
        ]);

        User::create([
            'name' => 'Ustadz Dani (Guru MA)',
            'username' => 'dani.guru',
            'password' => Hash::make('password'),
            'role' => 'guru',
            'unit_id' => $ma->id,
            'phone' => '081234567896',
        ]);
    }
}

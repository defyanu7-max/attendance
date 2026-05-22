<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use App\Models\User;
use App\Models\Unit;
use App\Livewire\Master\TeacherForm;
use Illuminate\Support\Facades\Hash;

class TeacherFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed basic data (units, users)
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
    }

    public function test_superadmin_can_create_teacher_with_unit_selection(): void
    {
        $super = User::where('role', 'superadmin')->first();
        $unit = Unit::first();

        $this->actingAs($super);

        Livewire::test(TeacherForm::class)
            ->set('name', 'Test Guru')
            ->set('username', 'nip123')
            ->set('password', 'secret123')
            ->set('phone', '08123456789')
            ->set('unit_id', $unit->id)
            ->call('save')
            ->assertRedirect(route('teachers.index'));

        $this->assertDatabaseHas('users', [
            'username' => 'nip123',
            'role' => 'guru',
            'unit_id' => $unit->id,
        ]);

        $user = User::where('username', 'nip123')->first();
        $this->assertTrue(Hash::check('secret123', $user->password));
    }

    public function test_admin_can_create_teacher_and_unit_is_assigned_to_admin_unit(): void
    {
        $admin = User::where('role', 'admin')->first();

        $this->actingAs($admin);

        Livewire::test(TeacherForm::class)
            ->set('name', 'Admin Created Guru')
            ->set('username', 'nip_admin')
            ->set('password', 'secretpwd')
            ->set('phone', '08120001111')
            ->call('save')
            ->assertRedirect(route('teachers.index'));

        $user = User::where('username', 'nip_admin')->first();
        $this->assertNotNull($user);
        $this->assertEquals($admin->unit_id, $user->unit_id);
        $this->assertEquals('guru', $user->role);
    }

    public function test_username_must_be_unique_when_creating_teacher(): void
    {
        $super = User::where('role', 'superadmin')->first();
        $unit = Unit::first();

        $this->actingAs($super);

        // First create
        Livewire::test(TeacherForm::class)
            ->set('name', 'First')
            ->set('username', 'dupuser')
            ->set('password', 'pw12345')
            ->set('unit_id', $unit->id)
            ->call('save')
            ->assertRedirect(route('teachers.index'));

        // Attempt duplicate
        Livewire::test(TeacherForm::class)
            ->set('name', 'Second')
            ->set('username', 'dupuser')
            ->set('password', 'pw56789')
            ->set('unit_id', $unit->id)
            ->call('save')
            ->assertHasErrors(['username' => 'unique']);
    }

    public function test_superadmin_can_delete_any_teacher(): void
    {
        $super = User::where('role', 'superadmin')->first();
        $teacher = User::where('role', 'guru')->first();

        $this->actingAs($super);

        Livewire::test(\App\Livewire\Master\TeacherIndex::class)
            ->dispatch('delete-teacher', id: $teacher->id);

        $this->assertSoftDeleted('users', ['id' => $teacher->id]);
    }

    public function test_admin_can_delete_teacher_in_same_unit(): void
    {
        $admin = User::where('role', 'admin')->first();
        $teacher = User::factory()->create([
            'role' => 'guru',
            'unit_id' => $admin->unit_id,
        ]);

        $this->actingAs($admin);

        Livewire::test(\App\Livewire\Master\TeacherIndex::class)
            ->dispatch('delete-teacher', id: $teacher->id);

        $this->assertSoftDeleted('users', ['id' => $teacher->id]);
    }
}

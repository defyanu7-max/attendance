<?php

namespace Database\Factories;

use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'username' => fake()->unique()->userName(),
            'password' => static::$password ??= Hash::make('password'),
            'role' => 'guru',
            'unit_id' => Unit::first()?->id ?? Unit::factory()->create()->id,
            'phone' => fake()->optional()->phoneNumber(),
        ];
    }

    /**
     * Indicate that the user is a superadmin.
     */
    public function superadmin(): static
    {
        return $this->state(fn(array $attributes) => [
            'role' => 'superadmin',
            'unit_id' => null,
        ]);
    }

    /**
     * Indicate that the user is an admin.
     */
    public function admin(): static
    {
        return $this->state(fn(array $attributes) => [
            'role' => 'admin',
        ]);
    }

    /**
     * Indicate that the user is a walikelas.
     */
    public function walikelas(): static
    {
        return $this->state(fn(array $attributes) => [
            'role' => 'walikelas',
        ]);
    }

    /**
     * Indicate that the user is a guru.
     */
    public function guru(): static
    {
        return $this->state(fn(array $attributes) => [
            'role' => 'guru',
        ]);
    }

    /**
     * Assign user to a specific unit.
     */
    public function forUnit(Unit $unit): static
    {
        return $this->state(fn(array $attributes) => [
            'unit_id' => $unit->id,
        ]);
    }
}

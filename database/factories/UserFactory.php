<?php

namespace Database\Factories;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     * A default 'student' role is created if it does not already exist,
     * ensuring the NOT NULL constraint on users.role_id is always satisfied
     * in both production seeders and in-memory test databases (RefreshDatabase).
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Ensure at least the student role exists (idempotent, safe for tests)
        $studentRole = Role::firstOrCreate(['name' => 'student']);

        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role_id' => $studentRole->id,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Set the user's role to admin.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role_id' => Role::firstOrCreate(['name' => 'admin'])->id,
        ]);
    }

    /**
     * Set the user's role to professor.
     */
    public function professor(): static
    {
        return $this->state(fn (array $attributes) => [
            'role_id' => Role::firstOrCreate(['name' => 'professor'])->id,
        ]);
    }

    /**
     * Set the user's role to student.
     */
    public function student(): static
    {
        return $this->state(fn (array $attributes) => [
            'role_id' => Role::firstOrCreate(['name' => 'student'])->id,
        ]);
    }
}

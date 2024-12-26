<?php

namespace Database\Factories;

use App\Helper\ImageFake;
use App\Models\Position;
use Database\Seeders\PositionSeeder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        return [
            'name' => fake()->firstName(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->regexify('^[\+]{1}380([0-9]{9})$'),
            'position_id' => Position::all()->random()->id,
            'photo' => ImageFake::createImage('users'),

        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return $this
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}

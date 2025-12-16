<?php

namespace Database\Factories;

use App\Models\SocialIdentity;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SocialIdentityFactory extends Factory
{
    protected $model = SocialIdentity::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'provider_name' => fake()->randomElement(['github', 'google']),
            'provider_id' => fake()->unique()->numerify('##########'),
            'provider_token' => fake()->sha256(),
            'avatar_url' => fake()->imageUrl(),
        ];
    }

    public function github(): self
    {
        return $this->state(fn (array $attributes) => [
            'provider_name' => 'github',
        ]);
    }

    public function google(): self
    {
        return $this->state(fn (array $attributes) => [
            'provider_name' => 'google',
        ]);
    }
}

<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserUsage>
 */
class UserUsageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),

            'type' => fake()
                ->randomElement([
                    'message_sent',
                    'ai_response',
                    'file_upload',
                ]),

            'tokens' => fake()
                ->numberBetween(0, 5000),

            'messages' => fake()
                ->numberBetween(0, 10),

            'bytes' => fake()
                ->numberBetween(0, 1024 * 1024),

            'cost' => fake()
                ->randomFloat(4, 0, 10),

            'metadata' => [
                'chat_id' => fake()
                    ->numberBetween(1, 100),
                'model' => 'gemini-2.0-flash-lite',
            ],

            'created_at' => now(),
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'order_number' => 'ORD-' . Str::upper(Str::random(12)),
            'ordered_at' => now(),
            'total' => 0,
            'status' => 'pending',
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
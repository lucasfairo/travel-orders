<?php

namespace Database\Factories;

use App\Models\TravelOrder;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TravelOrderFactory extends Factory
{
    protected $model = TravelOrder::class;

    /**
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'requester_name' => $this->faker->name,
            'destination' => $this->faker->city,
            'departure_date' => $this->faker->dateTimeBetween('now', '+1 week'),
            'return_date' => $this->faker->dateTimeBetween('+1 week', '+2 weeks'),
            'status' => $this->faker->randomElement(['requested', 'approved', 'cancelled']),
        ];
    }
}

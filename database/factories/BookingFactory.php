<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $checkIn = $this->faker->dateTimeBetween('now', '+3 months');
        $checkOut = $this->faker->dateTimeBetween($checkIn, '+1 week');
        $nights = $checkIn->diff($checkOut)->days;
        $pricePerNight = 120.00; // Match the current pricing

        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'depart' => $checkIn->format('Y-m-d'),
            'leave' => $checkOut->format('Y-m-d'),
            'venue' => $this->faker->randomElement(['The Light House', 'Saras']),
            'nights' => $nights,
            'total_price' => $nights * $pricePerNight,
            'status' => $this->faker->randomElement(['pending', 'confirmed', 'cancelled']),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\Email;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmailFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Email::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'to'=>$this->faker->email,
            'from'=>$this->faker->email,
            'subject'=>$this->faker->sentence,
            'status'=>rand(0,2)
        ];
    }
}

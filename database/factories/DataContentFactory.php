<?php

namespace Database\Factories;

use App\Models\DataContent;
use Illuminate\Database\Eloquent\Factories\Factory;

class DataContentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = DataContent::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->sentence(4, true),
            'content' => $this->faker->text(400),
        ];
    }
}

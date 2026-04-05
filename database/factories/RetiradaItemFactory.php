<?php

namespace Database\Factories;

use App\Models\Produto;
use App\Models\Retirada;
use App\Models\RetiradaItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RetiradaItem>
 */
class RetiradaItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = RetiradaItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'retirada_id' => Retirada::factory(),
            'produto_id' => Produto::factory(),
            'quantidade' => $this->faker->numberBetween(1, 5),
        ];
    }
}

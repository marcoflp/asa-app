<?php

namespace Database\Factories;

use App\Models\Produto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Produto>
 */
class ProdutoFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Produto::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nome' => $this->faker->words(3, true),
            'categoria' => $this->faker->randomElement(['Alimentos', 'Roupas', 'Higiene', 'Limpeza']),
            'unidade' => $this->faker->randomElement(['kg', 'unidade', 'litro', 'pacote']),
            'estoque' => $this->faker->numberBetween(10, 100),
            'descricao' => $this->faker->sentence(),
            'ativo' => true,
        ];
    }
}

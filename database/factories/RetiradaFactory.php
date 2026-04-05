<?php

namespace Database\Factories;

use App\Models\Beneficiario;
use App\Models\Retirada;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Retirada>
 */
class RetiradaFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Retirada::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'beneficiario_id' => Beneficiario::factory(),
            'data' => now(),
            'observacoes' => $this->faker->sentence(),
        ];
    }
}

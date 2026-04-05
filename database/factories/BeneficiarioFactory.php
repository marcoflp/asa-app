<?php

namespace Database\Factories;

use App\Models\Beneficiario;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Beneficiario>
 */
class BeneficiarioFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Beneficiario::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nome' => $this->faker->name(),
            'telefone' => $this->faker->phoneNumber(),
            'rua' => $this->faker->streetName(),
            'numero' => $this->faker->buildingNumber(),
            'bairro' => $this->faker->word(),
            'cidade' => 'Passo Fundo',
            'cep' => $this->faker->postcode(),
            'rg' => $this->faker->numerify('##########'),
            'cpf' => $this->faker->numerify('###########'),
            'num_pessoas_familia' => $this->faker->numberBetween(1, 10),
            'filhos' => [],
            'inscrito_programa_governo' => $this->faker->boolean(),
            'programa_governo' => null,
            'recebe_estudo_biblico' => $this->faker->boolean(),
            'instrutor_biblico' => null,
            'observacoes' => $this->faker->sentence(),
        ];
    }
}

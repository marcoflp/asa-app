<?php

use App\Livewire\Retiradas\Form as RetiradaForm;
use App\Models\Beneficiario;
use App\Models\Produto;
use App\Models\User;
use Livewire\Livewire;

function beneficiario(array $dados = []): Beneficiario
{
    return Beneficiario::create(array_merge(['nome' => 'Fulano de Tal', 'num_pessoas_familia' => 1], $dados));
}

function produto(array $dados = []): Produto
{
    return Produto::create(array_merge(['nome' => 'Arroz', 'categoria' => 'Alimento', 'unidade' => 'kg', 'estoque' => 20, 'ativo' => true], $dados));
}

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

test('busca de beneficiário ignora acentos e maiúsculas', function () {
    beneficiario(['nome' => 'João Conceição', 'bairro' => 'Centro']);
    beneficiario(['nome' => 'Maria Souza', 'bairro' => 'Boqueirão']);

    Livewire::test(RetiradaForm::class)
        ->set('searchBeneficiario', 'joao conceicao')
        ->assertSee('João Conceição')
        ->assertDontSee('Maria Souza');
});

test('busca de beneficiário encontra pelo CPF ou telefone', function () {
    beneficiario(['nome' => 'Ana Lima', 'cpf' => '123.456.789-00', 'telefone' => '(54) 99111-2222']);
    beneficiario(['nome' => 'Pedro Alves', 'cpf' => '987.654.321-00', 'telefone' => '(54) 98888-7777']);

    Livewire::test(RetiradaForm::class)
        ->set('searchBeneficiario', '456789')
        ->assertSee('Ana Lima')
        ->assertDontSee('Pedro Alves')
        ->set('searchBeneficiario', '99111')
        ->assertSee('Ana Lima')
        ->assertDontSee('Pedro Alves');
});

test('selecionar beneficiário guarda o id e permite trocar', function () {
    $beneficiario = beneficiario(['nome' => 'Ana Lima']);

    Livewire::test(RetiradaForm::class)
        ->call('selecionarBeneficiario', $beneficiario->id)
        ->assertSet('beneficiario_id', $beneficiario->id)
        ->assertSee('Trocar')
        ->call('trocarBeneficiario')
        ->assertSet('beneficiario_id', 0);
});

test('busca de produto por categoria não mostra produtos inativos', function () {
    produto(['nome' => 'Feijão', 'categoria' => 'Alimento', 'ativo' => true]);
    produto(['nome' => 'Lentilha', 'categoria' => 'Alimento', 'ativo' => false]);

    Livewire::test(RetiradaForm::class)
        ->set('searchProduto', 'alimento')
        ->assertSee('Feijão')
        ->assertDontSee('Lentilha');
});

test('quantidade digitada nunca fica menor que 1', function () {
    $produto = produto();

    Livewire::test(RetiradaForm::class)
        ->call('adicionarProduto', $produto->id)
        ->set('items.0.quantidade', 0)
        ->assertSet('items.0.quantidade', 1)
        ->set('items.0.quantidade', 5)
        ->assertSet('items.0.quantidade', 5);
});

test('não permite retirada com data no futuro', function () {
    $beneficiario = beneficiario();
    $produto = produto();

    Livewire::test(RetiradaForm::class)
        ->call('selecionarBeneficiario', $beneficiario->id)
        ->set('data', now()->addDay()->toDateString())
        ->call('adicionarProduto', $produto->id)
        ->call('save')
        ->assertHasErrors(['data']);
});

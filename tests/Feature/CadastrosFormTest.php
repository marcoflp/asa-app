<?php

use App\Livewire\Beneficiarios\Form as BeneficiarioForm;
use App\Livewire\Produtos\Form as ProdutoForm;
use App\Models\Beneficiario;
use App\Models\Produto;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

// ---------- Beneficiário ----------

test('recusa CPF inválido com mensagem clara', function () {
    Livewire::test(BeneficiarioForm::class)
        ->set('cpf', '123.456.789-00')
        ->assertHasErrors(['cpf'])
        ->assertSee('CPF inválido');
});

test('aceita CPF válido', function () {
    Livewire::test(BeneficiarioForm::class)
        ->set('cpf', '529.982.247-25')
        ->assertHasNoErrors(['cpf']);
});

test('CPF repetido mostra mensagem amigável em vez de erro do banco', function () {
    Beneficiario::create(['nome' => 'Ana Lima', 'num_pessoas_familia' => 2, 'cpf' => '529.982.247-25']);

    Livewire::test(BeneficiarioForm::class)
        ->set('nome', 'Outra Pessoa')
        ->set('cpf', '529.982.247-25')
        ->call('save')
        ->assertHasErrors(['cpf' => 'unique']);
});

test('editar cadastro antigo com CPF inválido não trava se o CPF não mudar', function () {
    $antigo = Beneficiario::create(['nome' => 'Cadastro Antigo', 'num_pessoas_familia' => 1, 'cpf' => '111.111.111-11']);

    Livewire::test(BeneficiarioForm::class, ['beneficiario' => $antigo])
        ->set('observacoes', 'Atualizado')
        ->call('save')
        ->assertHasNoErrors();

    expect($antigo->fresh()->observacoes)->toBe('Atualizado');
});

test('avisa quando já existe beneficiário com o mesmo nome, ignorando acentos', function () {
    Beneficiario::create(['nome' => 'José da Conceição', 'num_pessoas_familia' => 1, 'bairro' => 'CENTRO']);

    Livewire::test(BeneficiarioForm::class)
        ->set('nome', 'jose da conceicao')
        ->assertSee('Já existe cadastro parecido')
        ->assertSee('José da Conceição');
});

test('telefone incompleto é recusado', function () {
    Livewire::test(BeneficiarioForm::class)
        ->set('telefone', '(54) 9999')
        ->assertHasErrors(['telefone']);
});

test('bairro digitado sem acento usa a grafia da lista', function () {
    Livewire::test(BeneficiarioForm::class)
        ->set('nome', 'Maria Teste')
        ->set('bairro', 'boqueirao')
        ->call('save')
        ->assertHasNoErrors();

    expect(Beneficiario::where('nome', 'Maria Teste')->value('bairro'))->toBe('BOQUEIRÃO');
});

test('idade de filho fora de 0 a 17 é recusada', function () {
    Livewire::test(BeneficiarioForm::class)
        ->set('filho_idade', 25)
        ->call('addFilho')
        ->assertHasErrors(['filho_idade'])
        ->assertSet('filhos', []);
});

// ---------- Produto ----------

test('produto com estoque negativo pode ser editado e salvo', function () {
    $fralda = Produto::create(['nome' => 'Fralda', 'categoria' => 'Higiene', 'unidade' => 'pacote', 'estoque' => -16, 'ativo' => true]);

    Livewire::test(ProdutoForm::class, ['produto' => $fralda])
        ->set('descricao', 'Tamanhos variados')
        ->call('save')
        ->assertHasNoErrors();

    expect($fralda->fresh()->descricao)->toBe('Tamanhos variados');
});

test('desligar controle de estoque grava estoque vazio', function () {
    $arroz = Produto::create(['nome' => 'Arroz', 'categoria' => 'Alimento', 'unidade' => 'kg', 'estoque' => 5, 'ativo' => true]);

    Livewire::test(ProdutoForm::class, ['produto' => $arroz])
        ->assertSet('controlarEstoque', true)
        ->set('controlarEstoque', false)
        ->call('save')
        ->assertHasNoErrors();

    expect($arroz->fresh()->estoque)->toBeNull();
});

test('categoria digitada sem acento reaproveita a existente', function () {
    Produto::create(['nome' => 'Calça', 'categoria' => 'Vestuário', 'unidade' => 'peça', 'ativo' => true]);

    Livewire::test(ProdutoForm::class)
        ->set('nome', 'Camiseta')
        ->set('categoria', 'vestuario')
        ->set('unidade', 'PECA')
        ->call('save')
        ->assertHasNoErrors();

    $camiseta = Produto::where('nome', 'Camiseta')->first();
    expect($camiseta->categoria)->toBe('Vestuário')
        ->and($camiseta->unidade)->toBe('peça');
});

test('avisa quando já existe produto com o mesmo nome', function () {
    Produto::create(['nome' => 'Feijão', 'categoria' => 'Alimento', 'unidade' => 'kg', 'ativo' => true]);

    Livewire::test(ProdutoForm::class)
        ->set('nome', 'feijao')
        ->assertSee('Já existe um produto com esse nome');
});

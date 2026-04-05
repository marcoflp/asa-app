<?php

use App\Models\User;
use App\Models\Beneficiario;
use App\Models\Produto;
use App\Models\Retirada;
use App\Livewire\Beneficiarios\Form as BeneficiarioForm;
use App\Livewire\Produtos\Form as ProdutoForm;
use App\Livewire\Retiradas\Form as RetiradaForm;
use App\Livewire\Dashboard;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('system smoke test: full user journey', function () {
    // 1. Visit Dashboard
    $this->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Dashboard');

    // 2. Create a Beneficiary
    Livewire::test(BeneficiarioForm::class)
        ->set('nome', 'João Silva')
        ->set('cpf', '123.456.789-00')
        ->set('telefone', '(54) 99999-8888')
        ->set('num_pessoas_familia', 4)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('beneficiarios.index'));

    $this->assertDatabaseHas('beneficiarios', ['nome' => 'João Silva']);
    $beneficiario = Beneficiario::where('nome', 'João Silva')->first();

    // 3. Create a Product
    Livewire::test(ProdutoForm::class)
        ->set('nome', 'Cesta Básica Padrão')
        ->set('categoria', 'Alimentos')
        ->set('unidade', 'unidade')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('produtos.index'));

    $this->assertDatabaseHas('produtos', ['nome' => 'Cesta Básica Padrão']);
    $produto = Produto::where('nome', 'Cesta Básica Padrão')->first();

    // 4. Perform a Withdrawal
    Livewire::test(RetiradaForm::class)
        ->set('beneficiario_id', $beneficiario->id)
        ->set('data', now()->toDateString())
        ->set('item_produto_id', $produto->id)
        ->set('item_quantidade', 1)
        ->call('addItem')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('retiradas.index'));

    $this->assertDatabaseHas('retiradas', ['beneficiario_id' => $beneficiario->id]);
    $retirada = Retirada::where('beneficiario_id', $beneficiario->id)->first();
    $this->assertDatabaseHas('retirada_items', [
        'retirada_id' => $retirada->id,
        'produto_id' => $produto->id,
        'quantidade' => 1
    ]);

    // 5. Verify Dashboard Metrics
    Livewire::test(Dashboard::class)
        ->assertViewHas('totalBeneficiariosGeral', 1)
        ->assertViewHas('totalProdutosGeral', 1)
        ->assertSee('João Silva');
});

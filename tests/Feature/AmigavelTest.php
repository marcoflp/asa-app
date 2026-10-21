<?php

use App\Livewire\Beneficiarios\Form as BeneficiarioForm;
use App\Livewire\Produtos\Form as ProdutoForm;
use App\Livewire\Produtos\Index as ProdutoIndex;
use App\Livewire\Retiradas\Form as RetiradaForm;
use App\Livewire\Retiradas\Index as RetiradaIndex;
use App\Models\Beneficiario;
use App\Models\Produto;
use App\Models\Retirada;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

function novaFamilia(string $nome, array $extra = []): Beneficiario
{
    return Beneficiario::create(array_merge(['nome' => $nome, 'num_pessoas_familia' => 2, 'cidade' => 'Passo Fundo'], $extra));
}

function novoProduto(string $nome, ?int $estoque = 20, array $extra = []): Produto
{
    return Produto::create(array_merge(['nome' => $nome, 'categoria' => 'Alimento', 'unidade' => 'kg', 'estoque' => $estoque, 'ativo' => true], $extra));
}

// ---------- Cadastro parecido ----------

test('reconhece nomes parecidos', function (string $a, string $b, bool $esperado) {
    expect(BeneficiarioForm::nomesParecidos($a, $b))->toBe($esperado);
})->with([
    'igual sem acento' => ['José da Conceição', 'jose da conceicao', true],
    'erro de digitação' => ['Maria Aparecida Souza', 'Maria Aparecida Sousa', true],
    'sem nome do meio' => ['Maria de Fátima Souza', 'Maria Souza', true],
    'pessoas diferentes' => ['Maria Souza', 'Ana Souza', false],
]);

test('cadastro novo parecido abre a janela e não salva até confirmar', function () {
    novaFamilia('Maria de Fátima Souza');

    $tela = Livewire::test(BeneficiarioForm::class)
        ->set('nome', 'Maria Souza')
        ->call('save')
        ->assertDispatched('modal-show', name: 'cadastro-parecido')
        ->assertNoRedirect();

    expect(Beneficiario::count())->toBe(1);

    $tela->call('continuarCadastro')->assertRedirect(route('beneficiarios.index'));

    expect(Beneficiario::where('nome', 'Maria Souza')->exists())->toBeTrue();
});

test('produto novo com nome repetido abre a janela e pode reativar o existente', function () {
    $antigo = novoProduto('Leite em pó', 0, ['ativo' => false]);

    Livewire::test(ProdutoForm::class)
        ->set('nome', 'leite em po')
        ->set('categoria', 'Alimento')
        ->set('unidade', 'kg')
        ->call('save')
        ->assertDispatched('modal-show', name: 'produto-parecido')
        ->call('reativarProduto', $antigo->id)
        ->assertRedirect(route('produtos.index'));

    expect($antigo->fresh()->ativo)->toBeTrue()
        ->and(Produto::count())->toBe(1);
});

// ---------- Exclusão ----------

test('produto já usado em retirada não é excluído, mas pode ser desativado', function () {
    $familia = novaFamilia('Ana Lima');
    $arroz = novoProduto('Arroz');
    $retirada = Retirada::create(['beneficiario_id' => $familia->id, 'data' => today()]);
    $retirada->items()->create(['produto_id' => $arroz->id, 'quantidade' => 1]);

    Livewire::test(ProdutoIndex::class)
        ->call('confirmDelete', $arroz->id)
        ->assertSet('excluindoUsos', 1)
        ->call('delete');

    expect(Produto::find($arroz->id))->not->toBeNull();

    Livewire::test(ProdutoIndex::class)
        ->call('confirmDelete', $arroz->id)
        ->call('desativar');

    expect($arroz->fresh()->ativo)->toBeFalse();
});

// ---------- Estoque ----------

test('editar uma retirada não desconta o estoque duas vezes', function () {
    $familia = novaFamilia('Ana Lima');
    $arroz = novoProduto('Arroz', 20);

    Livewire::test(RetiradaForm::class)
        ->call('selecionarBeneficiario', $familia->id)
        ->call('adicionarProduto', $arroz->id)
        ->set('items.0.quantidade', 3)
        ->call('save');

    expect($arroz->fresh()->estoque)->toBe(17);

    $retirada = Retirada::first();
    Livewire::test(RetiradaForm::class, ['retirada' => $retirada])
        ->set('observacoes', 'corrigido')
        ->call('save')
        ->assertHasNoErrors();

    expect($arroz->fresh()->estoque)->toBe(17);
});

test('excluir uma retirada devolve os itens ao estoque', function () {
    $familia = novaFamilia('Ana Lima');
    $arroz = novoProduto('Arroz', 20);
    $retirada = Retirada::create(['beneficiario_id' => $familia->id, 'data' => today()]);
    $retirada->items()->create(['produto_id' => $arroz->id, 'quantidade' => 4]);

    expect($arroz->fresh()->estoque)->toBe(16);

    Livewire::test(RetiradaIndex::class)
        ->call('confirmDelete', $retirada->id)
        ->call('delete');

    expect($arroz->fresh()->estoque)->toBe(20)
        ->and(Retirada::count())->toBe(0);
});

// ---------- Retirada recente ----------

test('retirada para família que retirou há poucos dias pede confirmação', function () {
    $familia = novaFamilia('Ana Lima');
    $arroz = novoProduto('Arroz');
    Retirada::create(['beneficiario_id' => $familia->id, 'data' => today()->subDays(2)]);

    $tela = Livewire::test(RetiradaForm::class)
        ->call('selecionarBeneficiario', $familia->id)
        ->call('adicionarProduto', $arroz->id)
        ->call('save')
        ->assertDispatched('modal-show', name: 'retirada-recente')
        ->assertNoRedirect();

    expect(Retirada::count())->toBe(1);

    $tela->call('confirmarRetiradaRecente')->assertRedirect(route('retiradas.index'));

    expect(Retirada::count())->toBe(2);
});

// ---------- Página inicial ----------

test('página inicial mostra só os produtos marcados como urgentes', function () {
    novoProduto('Fralda', null, ['urgente' => true]);
    novoProduto('Feijão', 0);
    novoProduto('Leite', null, ['urgente' => true, 'ativo' => false]);

    $this->get('/')
        ->assertSee('Fralda')
        ->assertDontSee('Feijão')
        ->assertDontSee('Leite');
});

test('marcar e desmarcar urgente pela lista de produtos', function () {
    $fralda = novoProduto('Fralda', null);

    Livewire::test(ProdutoIndex::class)->call('toggleUrgente', $fralda->id);
    expect($fralda->fresh()->urgente)->toBeTrue();

    Livewire::test(ProdutoIndex::class)->call('toggleUrgente', $fralda->id);
    expect($fralda->fresh()->urgente)->toBeFalse();
});

<?php

use App\Models\Beneficiario;
use App\Models\Produto;
use App\Models\Retirada;
use App\Models\User;
use App\Painel\Intervalo;
use Carbon\CarbonImmutable;

function familiaDoPainel(string $nome, array $atributos = []): Beneficiario
{
    return Beneficiario::create(['nome' => $nome, 'num_pessoas_familia' => 3, 'cidade' => 'Passo Fundo', ...$atributos]);
}

function produtoDoPainel(string $nome, array $atributos = []): Produto
{
    return Produto::create(['nome' => $nome, 'categoria' => 'Alimento', 'unidade' => 'kg', 'estoque' => null, 'ativo' => true, ...$atributos]);
}

function retiradaDoPainel(Beneficiario $familia, CarbonImmutable $data, array $itens = []): Retirada
{
    $retirada = Retirada::create(['beneficiario_id' => $familia->id, 'data' => $data->toDateString()]);

    foreach ($itens as [$produto, $quantidade]) {
        $retirada->items()->create(['produto_id' => $produto->id, 'quantidade' => $quantidade]);
    }

    return $retirada;
}

beforeEach(function () {
    $this->actingAs(User::factory()->create(['name' => 'Marco Dias']));
});

test('a página inicial monta o painel com os endereços da API', function () {
    $this->get(route('dashboard'))
        ->assertOk()
        ->assertSee('<asa-painel', false)
        ->assertSee(e(json_encode(route('api.painel.impacto'))), false);
});

test('a API do painel exige login', function () {
    auth()->logout();

    $this->getJson(route('api.painel.atencao'))->assertUnauthorized();
    $this->getJson(route('api.painel.impacto'))->assertUnauthorized();
});

test('os pontos de atenção trazem estoque acabando, famílias sumidas, cadastros sem documento e urgentes', function () {
    $sumida = familiaDoPainel('Família Sumida', ['foto_documento' => 'frente.jpg']);
    retiradaDoPainel($sumida, CarbonImmutable::today()->subDays(90));
    $recente = familiaDoPainel('Família Recente');
    retiradaDoPainel($recente, CarbonImmutable::today());
    produtoDoPainel('Óleo', ['estoque' => 3, 'unidade' => 'litro']);
    produtoDoPainel('Arroz', ['estoque' => 50]);
    produtoDoPainel('Fralda', ['urgente' => true]);

    $this->getJson(route('api.painel.atencao'))
        ->assertOk()
        ->assertJsonPath('atendidasHoje.0.familia', 'Família Recente')
        ->assertJsonPath('estoqueAcabando.0.nome', 'Óleo')
        ->assertJsonCount(1, 'estoqueAcabando')
        ->assertJsonPath('familiasSemVir.0.nome', 'Família Sumida')
        ->assertJsonPath('familiasSemVir.0.diasSemVir', 90)
        ->assertJsonPath('cadastrosSemDocumento', 1)
        ->assertJsonPath('produtosUrgentes', ['Fralda']);
});

test('o impacto compara com o período anterior de mesmo tamanho', function () {
    $familia = familiaDoPainel('Ana Lima');
    $arroz = produtoDoPainel('Arroz');
    retiradaDoPainel($familia, CarbonImmutable::today()->subDays(10), [[$arroz, 1]]);
    retiradaDoPainel($familia, CarbonImmutable::today()->subDays(2), [[$arroz, 2]]);
    retiradaDoPainel($familia, CarbonImmutable::today(), [[$arroz, 3]]);

    $this->getJson(route('api.painel.impacto', ['periodo' => '7-dias']))
        ->assertOk()
        ->assertJsonPath('intervalo.agrupamento', 'dia')
        ->assertJsonPath('indicadores.retiradas', ['valor' => 2, 'variacao' => 100])
        ->assertJsonPath('indicadores.itens', ['valor' => 5, 'variacao' => 400])
        ->assertJsonPath('indicadores.familias.valor', 1)
        ->assertJsonPath('indicadores.pessoas.valor', 3)
        ->assertJsonCount(7, 'retiradasAoLongoDoTempo');
});

test('períodos maiores que duas semanas são agrupados por semana', function () {
    $this->getJson(route('api.painel.impacto', ['periodo' => '3-meses']))
        ->assertOk()
        ->assertJsonPath('intervalo.agrupamento', 'semana');
});

test('a categoria filtra só o ranking de produtos', function () {
    $familia = familiaDoPainel('Ana Lima');
    $arroz = produtoDoPainel('Arroz');
    $sabonete = produtoDoPainel('Sabonete', ['categoria' => 'Higiene', 'unidade' => 'unidade']);
    retiradaDoPainel($familia, CarbonImmutable::today(), [[$arroz, 4], [$sabonete, 1]]);

    $this->getJson(route('api.painel.impacto', ['periodo' => 'hoje', 'categoria' => 'Higiene']))
        ->assertOk()
        ->assertJsonPath('produtosMaisRetirados', [['nome' => 'Sabonete', 'categoria' => 'Higiene', 'unidade' => 'unidade', 'total' => 1]])
        ->assertJsonCount(2, 'itensPorCategoria');
});

test('famílias por bairro agrupam o que passa dos seis maiores', function () {
    $produto = produtoDoPainel('Arroz');
    foreach (['Centro', 'Centro', 'Vera Cruz', 'Boqueirão', 'Petrópolis', 'São José', 'Cruzeiro', 'Annes', ''] as $indice => $bairro) {
        retiradaDoPainel(familiaDoPainel("Família {$indice}", ['bairro' => $bairro]), CarbonImmutable::today(), [[$produto, 1]]);
    }

    $bairros = $this->getJson(route('api.painel.impacto', ['periodo' => 'hoje']))->assertOk()->json('familiasPorBairro');

    expect($bairros)->toHaveCount(7)
        ->and($bairros[0])->toBe(['nome' => 'Centro', 'total' => 2, 'agrupado' => false])
        ->and(end($bairros))->toBe(['nome' => 'Outros bairros', 'total' => 2, 'agrupado' => true]);
});

test('período personalizado exige datas válidas', function () {
    $this->getJson(route('api.painel.impacto', ['periodo' => 'personalizado']))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['inicio', 'fim']);

    $this->getJson(route('api.painel.impacto', [
        'periodo' => 'personalizado',
        'inicio' => CarbonImmutable::today()->toDateString(),
        'fim' => CarbonImmutable::today()->subDay()->toDateString(),
    ]))->assertJsonValidationErrors(['fim']);

    $this->getJson(route('api.painel.impacto', [
        'periodo' => 'personalizado',
        'inicio' => CarbonImmutable::today()->subDays(3)->toDateString(),
        'fim' => CarbonImmutable::today()->toDateString(),
    ]))->assertOk()->assertJsonCount(4, 'retiradasAoLongoDoTempo');
});

test('o relatório em PDF é baixado com o período no nome', function () {
    $this->get(route('api.painel.relatorio', ['periodo' => 'hoje']))
        ->assertOk()
        ->assertDownload('relatorio-asa-'.CarbonImmutable::today()->toDateString().'-a-'.CarbonImmutable::today()->toDateString().'.pdf');
});

test('o intervalo divide o período em semanas sem passar do fim', function () {
    $intervalo = Intervalo::entre(CarbonImmutable::parse('2026-10-01'), CarbonImmutable::parse('2026-10-20'));

    $fatias = collect($intervalo->fatias())->map(fn (Intervalo $fatia) => [$fatia->inicio->toDateString(), $fatia->fim->toDateString()]);

    expect($intervalo->dias())->toBe(20)
        ->and($fatias->all())->toBe([
            ['2026-10-01', '2026-10-07'],
            ['2026-10-08', '2026-10-14'],
            ['2026-10-15', '2026-10-20'],
        ])
        ->and($intervalo->anterior()->inicio->toDateString())->toBe('2026-09-11');
});

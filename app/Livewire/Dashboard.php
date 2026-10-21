<?php

namespace App\Livewire;

use App\Models\Beneficiario;
use App\Models\Produto;
use App\Models\Retirada;
use App\Models\RetiradaItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Livewire\Component;

class Dashboard extends Component
{
    public string $periodo = 'mensal';

    public ?string $dataInicio = null;

    public ?string $dataFim = null;

    /** Categoria clicada no gráfico: filtra o ranking de produtos. */
    public string $categoria = '';

    /** Famílias que não aparecem há mais de X dias entram em "Precisa de atenção". */
    const DIAS_SEM_VIR = 60;

    public function updatedPeriodo(): void
    {
        $this->categoria = '';
    }

    public function filtrarCategoria(string $categoria): void
    {
        $this->categoria = $this->categoria === $categoria ? '' : $categoria;
    }

    public function getPeriodoDates(): array
    {
        if ($this->periodo === 'personalizado' && $this->dataInicio && $this->dataFim) {
            return [
                Carbon::parse($this->dataInicio)->startOfDay(),
                Carbon::parse($this->dataFim)->endOfDay(),
            ];
        }

        return match ($this->periodo) {
            'hoje' => [now()->startOfDay(), now()->endOfDay()],
            'semanal' => [now()->subDays(6)->startOfDay(), now()->endOfDay()],
            'mensal' => [now()->subDays(29)->startOfDay(), now()->endOfDay()],
            'trimestral' => [now()->subMonths(3)->addDay()->startOfDay(), now()->endOfDay()],
            'semestral' => [now()->subMonths(6)->addDay()->startOfDay(), now()->endOfDay()],
            default => [now()->subDays(29)->startOfDay(), now()->endOfDay()],
        };
    }

    /** Números de um período: usados pelos cartões, pela comparação e pelo PDF. */
    private function resumo(CarbonInterface $inicio, CarbonInterface $fim): array
    {
        $retiradas = Retirada::whereBetween('data', [$inicio, $fim]);
        $familias = (clone $retiradas)->distinct()->pluck('beneficiario_id');

        return [
            'retiradas' => (clone $retiradas)->count(),
            'familias' => $familias->count(),
            'pessoas' => (int) Beneficiario::whereIn('id', $familias)->sum('num_pessoas_familia'),
            'itens' => (int) RetiradaItem::whereHas('retirada', fn ($q) => $q->whereBetween('data', [$inicio, $fim]))->sum('quantidade'),
        ];
    }

    /** "3 litros", "1 kg", "8 unidades": unidade no plural quando precisa. */
    public static function unidade(int|float $qtd, ?string $unidade): string
    {
        $unidade = (string) $unidade;
        if (abs($qtd) == 1 || $unidade === '' || in_array($unidade, ['kg', 'g', 'ml', 'l'])) {
            return $unidade;
        }

        return str_ends_with($unidade, 'r') ? $unidade.'es' : (str_ends_with($unidade, 's') ? $unidade : $unidade.'s');
    }

    /** Variação em % contra o período anterior de mesmo tamanho (null quando não dá para comparar). */
    private static function variacao(int $atual, int $anterior): ?int
    {
        if ($anterior === 0) {
            return null;
        }

        return (int) round((($atual - $anterior) / $anterior) * 100);
    }

    /** Retiradas agrupadas por dia (até 31 dias) ou por semana, com os dias sem retirada em zero. */
    private function serieTemporal(CarbonInterface $inicio, CarbonInterface $fim): array
    {
        $porDia = Retirada::selectRaw('date(data) as dia, count(*) as total')
            ->whereBetween('data', [$inicio, $fim])
            ->groupBy('dia')
            ->pluck('total', 'dia');

        $dias = (int) $inicio->copy()->startOfDay()->diffInDays($fim->copy()->startOfDay()) + 1;
        // Atendimento é semanal (sábados): acima de 2 semanas agrupa por semana para não ficar cheio de dias vazios
        $semanal = $dias > 14;
        $serie = [];

        $cursor = $inicio->copy()->startOfDay();
        while ($cursor->lte($fim)) {
            $ate = $semanal ? $cursor->copy()->addDays(6)->min($fim->copy()->startOfDay()) : $cursor->copy();
            $total = 0;
            for ($d = $cursor->copy(); $d->lte($ate); $d = $d->addDay()) {
                $total += (int) ($porDia[$d->toDateString()] ?? 0);
            }
            $serie[] = [
                'rotulo' => $cursor->format('d/m'),
                'dica' => $semanal ? 'Semana de '.$cursor->format('d/m').' a '.$ate->format('d/m') : $cursor->translatedFormat('l, d/m'),
                'total' => $total,
                'sabado' => ! $semanal && $cursor->isSaturday(),
            ];
            $cursor = $ate->copy()->addDay();
        }

        return ['pontos' => $serie, 'semanal' => $semanal];
    }

    public function getDashboardData($inicio, $fim): array
    {
        $atual = $this->resumo($inicio, $fim);
        $duracao = (int) $inicio->copy()->startOfDay()->diffInDays($fim->copy()->startOfDay()) + 1;
        $antes = $this->resumo($inicio->copy()->subDays($duracao), $inicio->copy()->subDay()->endOfDay());

        $itensPorCategoria = RetiradaItem::selectRaw('produtos.categoria, sum(retirada_items.quantidade) as total')
            ->join('produtos', 'retirada_items.produto_id', '=', 'produtos.id')
            ->whereHas('retirada', fn ($q) => $q->whereBetween('data', [$inicio, $fim]))
            ->groupBy('produtos.categoria')
            ->orderByDesc('total')
            ->get();

        $topProdutos = RetiradaItem::selectRaw('produto_id, sum(quantidade) as total')
            ->whereHas('retirada', fn ($q) => $q->whereBetween('data', [$inicio, $fim]))
            ->when($this->categoria, fn ($q) => $q->whereHas('produto', fn ($p) => $p->where('categoria', $this->categoria)))
            ->with('produto:id,nome,categoria,unidade')
            ->groupBy('produto_id')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        // Famílias atendidas por bairro: 6 maiores + "Outros"
        $porBairro = Beneficiario::whereIn('id', Retirada::whereBetween('data', [$inicio, $fim])->select('beneficiario_id'))
            ->get(['bairro'])
            ->groupBy(fn ($b) => trim((string) $b->bairro) !== '' ? mb_convert_case(trim($b->bairro), MB_CASE_TITLE, 'UTF-8') : 'Não informado')
            ->map->count()
            ->sortDesc();
        $bairros = $porBairro->take(6);
        if ($porBairro->count() > 6) {
            $bairros['Outros bairros'] = $porBairro->slice(6)->sum();
        }

        return [
            // usados pelo PDF
            'totalRetiradas' => $atual['retiradas'],
            'totalBeneficiarios' => $atual['familias'],
            'totalItens' => $atual['itens'],
            'topProdutos' => $topProdutos,
            'itensPorCategoria' => $itensPorCategoria,
            'inicio' => $inicio,
            'fim' => $fim,
            'periodo' => $this->periodo,
            // painel
            'resumo' => $atual,
            'variacoes' => collect($atual)->map(fn ($v, $k) => self::variacao($v, $antes[$k]))->all(),
            'serie' => $this->serieTemporal($inicio, $fim),
            'bairros' => $bairros,
        ];
    }

    /** O que precisa de ação agora, independente do período escolhido. */
    private function atencao(): array
    {
        $ultimas = Retirada::selectRaw('beneficiario_id, max(data) as ultima')->groupBy('beneficiario_id')->pluck('ultima', 'beneficiario_id');
        $limite = today()->subDays(self::DIAS_SEM_VIR);

        $sumidas = Beneficiario::whereIn('id', $ultimas->filter(fn ($d) => Carbon::parse($d)->lt($limite))->keys())
            ->get(['id', 'nome', 'bairro', 'telefone'])
            ->map(function ($b) use ($ultimas) {
                $b->dias = (int) abs(Carbon::parse($ultimas[$b->id])->startOfDay()->diffInDays(today()));

                return $b;
            })
            ->sortByDesc('dias')
            ->values();

        // Cadastros sem nenhuma das 4 fotos de documento
        $semDocumentos = Beneficiario::query();
        foreach (['foto_documento', 'foto_documento_verso', 'foto_documento_consentimento', 'foto_documento_comprovante_residencia'] as $campo) {
            $semDocumentos->where(fn ($q) => $q->whereNull($campo)->orWhere($campo, ''));
        }

        return [
            'hoje' => Retirada::with('beneficiario:id,nome')->withCount('items')->whereDate('data', today())->latest('id')->get(),
            'estoqueBaixo' => Produto::ativo()->whereNotNull('estoque')->where('estoque', '<', 10)->orderBy('estoque')->get(['id', 'nome', 'estoque', 'unidade']),
            'urgentes' => Produto::ativo()->where('urgente', true)->orderBy('nome')->pluck('nome'),
            'sumidas' => $sumidas,
            'semDocumentos' => $semDocumentos->count(),
        ];
    }

    public function gerarRelatorio()
    {
        [$inicio, $fim] = $this->getPeriodoDates();
        $data = $this->getDashboardData($inicio, $fim);
        $data['titulo'] = 'Relatório de Atividades - '.ucfirst($this->periodo);

        $pdf = Pdf::loadView('reports.dashboard', $data);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'relatorio-dashboard-'.$this->periodo.'.pdf');
    }

    public function render()
    {
        [$inicio, $fim] = $this->getPeriodoDates();

        return view('livewire.dashboard', $this->getDashboardData($inicio, $fim) + $this->atencao())
            ->layout('layouts.app', ['title' => 'Início']);
    }
}

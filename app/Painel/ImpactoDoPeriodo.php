<?php

namespace App\Painel;

use App\Models\Beneficiario;
use App\Models\Retirada;
use App\Models\RetiradaItem;
use Illuminate\Support\Collection;

final class ImpactoDoPeriodo
{
    private const BAIRROS_EM_DESTAQUE = 6;

    private const PRODUTOS_NO_RANKING = 8;

    public function gerar(Intervalo $intervalo, ?string $categoria = null): array
    {
        $atual = $this->indicadores($intervalo);
        $anterior = $this->indicadores($intervalo->anterior());

        return [
            'intervalo' => [
                'inicio' => $intervalo->inicio->toDateString(),
                'fim' => $intervalo->fim->toDateString(),
                'agrupamento' => $intervalo->agrupaPorSemana() ? 'semana' : 'dia',
            ],
            'indicadores' => collect($atual)
                ->map(fn (int $valor, string $nome) => [
                    'valor' => $valor,
                    'variacao' => $this->variacaoPercentual($valor, $anterior[$nome]),
                ])
                ->all(),
            'retiradasAoLongoDoTempo' => $this->retiradasAoLongoDoTempo($intervalo),
            'itensPorCategoria' => $this->itensPorCategoria($intervalo),
            'produtosMaisRetirados' => $this->produtosMaisRetirados($intervalo, $categoria),
            'familiasPorBairro' => $this->familiasPorBairro($intervalo),
        ];
    }

    private function indicadores(Intervalo $intervalo): array
    {
        $familias = $this->retiradasNo($intervalo)->distinct()->pluck('beneficiario_id');

        return [
            'familias' => $familias->count(),
            'pessoas' => (int) Beneficiario::whereIn('id', $familias)->sum('num_pessoas_familia'),
            'retiradas' => $this->retiradasNo($intervalo)->count(),
            'itens' => (int) $this->itensNo($intervalo)->sum('quantidade'),
        ];
    }

    private function variacaoPercentual(int $atual, int $anterior): ?int
    {
        return $anterior === 0 ? null : (int) round(($atual - $anterior) / $anterior * 100);
    }

    private function retiradasAoLongoDoTempo(Intervalo $intervalo): array
    {
        $porDia = $this->retiradasNo($intervalo)
            ->selectRaw('date(data) as dia, count(*) as total')
            ->groupBy('dia')
            ->pluck('total', 'dia');

        return collect($intervalo->fatias())
            ->map(fn (Intervalo $fatia) => [
                'inicio' => $fatia->inicio->toDateString(),
                'fim' => $fatia->fim->toDateString(),
                'total' => collect($fatia->inicio->daysUntil($fatia->fim))
                    ->sum(fn ($dia) => (int) ($porDia[$dia->toDateString()] ?? 0)),
            ])
            ->all();
    }

    private function itensPorCategoria(Intervalo $intervalo): array
    {
        return $this->itensNo($intervalo)
            ->join('produtos', 'retirada_items.produto_id', '=', 'produtos.id')
            ->selectRaw('produtos.categoria as nome, sum(retirada_items.quantidade) as total')
            ->groupBy('produtos.categoria')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($linha) => ['nome' => $linha->nome, 'total' => (int) $linha->total])
            ->all();
    }

    private function produtosMaisRetirados(Intervalo $intervalo, ?string $categoria): array
    {
        return $this->itensNo($intervalo)
            ->join('produtos', 'retirada_items.produto_id', '=', 'produtos.id')
            ->when($categoria, fn ($consulta) => $consulta->where('produtos.categoria', $categoria))
            ->selectRaw('produtos.nome, produtos.categoria, produtos.unidade, sum(retirada_items.quantidade) as total')
            ->groupBy('produtos.id', 'produtos.nome', 'produtos.categoria', 'produtos.unidade')
            ->orderByDesc('total')
            ->limit(self::PRODUTOS_NO_RANKING)
            ->get()
            ->map(fn ($linha) => [
                'nome' => $linha->nome,
                'categoria' => $linha->categoria,
                'unidade' => $linha->unidade,
                'total' => (int) $linha->total,
            ])
            ->all();
    }

    private function familiasPorBairro(Intervalo $intervalo): array
    {
        $porBairro = Beneficiario::whereIn('id', $this->retiradasNo($intervalo)->select('beneficiario_id'))
            ->pluck('bairro')
            ->map(fn (?string $bairro) => $this->nomeDoBairro($bairro))
            ->countBy()
            ->sortDesc();

        return $porBairro
            ->take(self::BAIRROS_EM_DESTAQUE)
            ->map(fn (int $total, string $nome) => ['nome' => $nome, 'total' => $total, 'agrupado' => $nome === 'Não informado'])
            ->values()
            ->when(
                $porBairro->count() > self::BAIRROS_EM_DESTAQUE,
                fn (Collection $lista) => $lista->push([
                    'nome' => 'Outros bairros',
                    'total' => $porBairro->slice(self::BAIRROS_EM_DESTAQUE)->sum(),
                    'agrupado' => true,
                ]),
            )
            ->all();
    }

    private function nomeDoBairro(?string $bairro): string
    {
        $bairro = trim((string) $bairro);

        return $bairro === '' ? 'Não informado' : mb_convert_case($bairro, MB_CASE_TITLE, 'UTF-8');
    }

    private function retiradasNo(Intervalo $intervalo)
    {
        return Retirada::whereBetween('data', [$intervalo->inicio, $intervalo->fim]);
    }

    private function itensNo(Intervalo $intervalo)
    {
        return RetiradaItem::whereHas('retirada', fn ($consulta) => $consulta->whereBetween('data', [$intervalo->inicio, $intervalo->fim]));
    }
}

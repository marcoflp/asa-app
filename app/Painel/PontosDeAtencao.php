<?php

namespace App\Painel;

use App\Models\Beneficiario;
use App\Models\Produto;
use App\Models\Retirada;
use Carbon\CarbonImmutable;

final class PontosDeAtencao
{
    public const DIAS_SEM_VIR = 60;

    private const ESTOQUE_BAIXO = 10;

    private const CAMPOS_DE_DOCUMENTO = [
        'foto_documento',
        'foto_documento_verso',
        'foto_documento_consentimento',
        'foto_documento_comprovante_residencia',
    ];

    public function gerar(): array
    {
        return [
            'atendidasHoje' => $this->atendidasHoje(),
            'estoqueAcabando' => $this->estoqueAcabando(),
            'familiasSemVir' => $this->familiasSemVir(),
            'diasSemVir' => self::DIAS_SEM_VIR,
            'cadastrosSemDocumento' => $this->cadastrosSemDocumento(),
            'produtosUrgentes' => Produto::ativo()->where('urgente', true)->orderBy('nome')->pluck('nome')->all(),
        ];
    }

    private function atendidasHoje(): array
    {
        return Retirada::with('beneficiario:id,nome')
            ->withCount('items')
            ->whereDate('data', CarbonImmutable::today())
            ->latest('id')
            ->get()
            ->map(fn (Retirada $retirada) => [
                'id' => $retirada->id,
                'familia' => $retirada->beneficiario?->nome,
                'itens' => $retirada->items_count,
                'url' => route('retiradas.edit', $retirada),
            ])
            ->all();
    }

    private function estoqueAcabando(): array
    {
        return Produto::ativo()
            ->whereNotNull('estoque')
            ->where('estoque', '<', self::ESTOQUE_BAIXO)
            ->orderBy('estoque')
            ->get(['id', 'nome', 'estoque', 'unidade'])
            ->map(fn (Produto $produto) => [
                'id' => $produto->id,
                'nome' => $produto->nome,
                'estoque' => (int) $produto->estoque,
                'unidade' => $produto->unidade,
                'url' => route('produtos.edit', $produto),
            ])
            ->all();
    }

    private function familiasSemVir(): array
    {
        $limite = CarbonImmutable::today()->subDays(self::DIAS_SEM_VIR);

        $ultimaRetirada = Retirada::selectRaw('beneficiario_id, max(data) as ultima')
            ->groupBy('beneficiario_id')
            ->pluck('ultima', 'beneficiario_id')
            ->map(fn (string $data) => CarbonImmutable::parse($data))
            ->filter(fn (CarbonImmutable $data) => $data->lt($limite));

        return Beneficiario::whereIn('id', $ultimaRetirada->keys())
            ->get(['id', 'nome'])
            ->map(fn (Beneficiario $familia) => [
                'id' => $familia->id,
                'nome' => $familia->nome,
                'diasSemVir' => (int) $ultimaRetirada[$familia->id]->diffInDays(CarbonImmutable::today()),
                'url' => route('beneficiarios.edit', $familia),
            ])
            ->sortByDesc('diasSemVir')
            ->values()
            ->all();
    }

    private function cadastrosSemDocumento(): int
    {
        return collect(self::CAMPOS_DE_DOCUMENTO)
            ->reduce(
                fn ($consulta, string $campo) => $consulta->where(fn ($vazio) => $vazio->whereNull($campo)->orWhere($campo, '')),
                Beneficiario::query(),
            )
            ->count();
    }
}

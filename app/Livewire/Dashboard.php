<?php

namespace App\Livewire;

use App\Models\Beneficiario;
use App\Models\Produto;
use App\Models\Retirada;
use App\Models\RetiradaItem;
use Illuminate\Support\Carbon;
use Livewire\Component;

class Dashboard extends Component
{
    public string $periodo = 'mensal'; // diario | semanal | mensal

    public function render()
    {
        [$inicio, $fim] = match ($this->periodo) {
            'diario'  => [now()->startOfDay(), now()->endOfDay()],
            'semanal' => [now()->startOfWeek(), now()->endOfWeek()],
            default   => [now()->startOfMonth(), now()->endOfMonth()],
        };

        $retiradas = Retirada::with('items')
            ->whereBetween('data', [$inicio, $fim]);

        $totalRetiradas = (clone $retiradas)->count();
        $totalBeneficiarios = (clone $retiradas)->distinct('beneficiario_id')->count('beneficiario_id');
        $totalItens = RetiradaItem::whereHas('retirada',
            fn($q) => $q->whereBetween('data', [$inicio, $fim])
        )->sum('quantidade');

        // Top 5 produtos mais retirados no período
        $topProdutos = RetiradaItem::selectRaw('produto_id, sum(quantidade) as total')
            ->whereHas('retirada', fn($q) => $q->whereBetween('data', [$inicio, $fim]))
            ->with('produto:id,nome,categoria')
            ->groupBy('produto_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // Retiradas por dia (para gráfico de barras simples)
        $retiradasPorDia = Retirada::selectRaw('date(data) as dia, count(*) as total')
            ->whereBetween('data', [$inicio, $fim])
            ->groupBy('dia')
            ->orderBy('dia')
            ->pluck('total', 'dia');

        // Últimas retiradas
        $ultimasRetiradas = Retirada::with('beneficiario')
            ->whereBetween('data', [$inicio, $fim])
            ->orderByDesc('data')
            ->limit(8)
            ->get();

        // Totais gerais (sempre)
        $totalBeneficiariosGeral = Beneficiario::count();
        $totalProdutosGeral = Produto::ativo()->count();

        return view('livewire.dashboard', compact(
            'totalRetiradas', 'totalBeneficiarios', 'totalItens',
            'topProdutos', 'retiradasPorDia', 'ultimasRetiradas',
            'totalBeneficiariosGeral', 'totalProdutosGeral', 'inicio', 'fim'
        ))->layout('layouts.app', ['title' => 'Dashboard']);
    }
}

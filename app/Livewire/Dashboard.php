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
    public string $periodo = 'mensal';

    public function getPeriodoDates()
    {
        return match ($this->periodo) {
            'hoje'       => [now()->startOfDay(), now()->endOfDay()],
            'semanal'    => [now()->subDays(7)->startOfDay(), now()->endOfDay()],
            'mensal'     => [now()->subDays(30)->startOfDay(), now()->endOfDay()],
            'trimestral' => [now()->subMonths(3)->startOfDay(), now()->endOfDay()],
            'semestral'  => [now()->subMonths(6)->startOfDay(), now()->endOfDay()],
            default      => [now()->startOfMonth(), now()->endOfMonth()],
        };
    }

    public function getDashboardData($inicio, $fim)
    {
        $retiradas = Retirada::with('items')
            ->whereBetween('data', [$inicio, $fim]);

        $totalRetiradas = (clone $retiradas)->count();
        $totalBeneficiarios = (clone $retiradas)->distinct('beneficiario_id')->count('beneficiario_id');
        $totalItens = RetiradaItem::whereHas('retirada',
            fn($q) => $q->whereBetween('data', [$inicio, $fim])
        )->sum('quantidade');

        $topProdutos = RetiradaItem::selectRaw('produto_id, sum(quantidade) as total')
            ->whereHas('retirada', fn($q) => $q->whereBetween('data', [$inicio, $fim]))
            ->with('produto:id,nome,categoria,unidade')
            ->groupBy('produto_id')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $retiradasPorDia = Retirada::selectRaw('date(data) as dia, count(*) as total')
            ->whereBetween('data', [$inicio, $fim])
            ->groupBy('dia')
            ->orderBy('dia')
            ->pluck('total', 'dia');

        $ultimasRetiradas = Retirada::with('beneficiario')
            ->whereBetween('data', [$inicio, $fim])
            ->orderByDesc('data')
            ->limit(8)
            ->get();

        $totalBeneficiariosGeral = Beneficiario::count();
        $totalProdutosGeral = Produto::ativo()->count();

        // Itens por categoria
        $itensPorCategoria = RetiradaItem::selectRaw('produtos.categoria, sum(retirada_items.quantidade) as total')
            ->join('produtos', 'retirada_items.produto_id', '=', 'produtos.id')
            ->whereHas('retirada', fn($q) => $q->whereBetween('data', [$inicio, $fim]))
            ->groupBy('produtos.categoria')
            ->orderByDesc('total')
            ->get();

        return compact(
            'totalRetiradas', 'totalBeneficiarios', 'totalItens',
            'topProdutos', 'retiradasPorDia', 'ultimasRetiradas',
            'totalBeneficiariosGeral', 'totalProdutosGeral', 'inicio', 'fim',
            'itensPorCategoria'
        ) + ['periodo' => $this->periodo];
    }

    public function gerarRelatorio()
    {
        [$inicio, $fim] = $this->getPeriodoDates();
        $data = $this->getDashboardData($inicio, $fim);
        $data['titulo'] = "Relatório de Atividades - " . ucfirst($this->periodo);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.dashboard', $data);
        
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'relatorio-dashboard-' . $this->periodo . '.pdf');
    }

    public function render()
    {
        [$inicio, $fim] = $this->getPeriodoDates();
        $data = $this->getDashboardData($inicio, $fim);

        return view('livewire.dashboard', $data)
            ->layout('layouts.app', ['title' => 'Dashboard']);
    }
}

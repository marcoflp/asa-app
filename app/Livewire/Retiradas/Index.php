<?php

namespace App\Livewire\Retiradas;

use App\Models\Beneficiario;
use App\Models\Produto;
use App\Models\Retirada;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $beneficiarioId = '';
    public string $dataInicio = '';
    public string $dataFim = '';
    public string $periodoPredefinido = ''; // '', 'hoje', '7dias', 'mes_atual', 'ano_atual'
    public string $produtoId = '';
    public string $ordenacao = 'recente'; // recente, antigo, beneficiario_asc
    public bool $mostrarFiltros = false;

    public ?int $deletingId = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'beneficiarioId' => ['except' => ''],
        'dataInicio' => ['except' => ''],
        'dataFim' => ['except' => ''],
        'periodoPredefinido' => ['except' => ''],
        'produtoId' => ['except' => ''],
        'ordenacao' => ['except' => 'recente'],
    ];

    public function updated($propertyName): void
    {
        if (in_array($propertyName, ['search', 'beneficiarioId', 'dataInicio', 'dataFim', 'produtoId', 'ordenacao'])) {
            $this->resetPage();
        }
    }

    public function setPeriodo(string $periodo): void
    {
        $this->periodoPredefinido = $periodo;
        $hoje = Carbon::today();

        match ($periodo) {
            'hoje' => [
                $this->dataInicio = $hoje->toDateString(),
                $this->dataFim = $hoje->toDateString(),
            ],
            '7dias' => [
                $this->dataInicio = $hoje->copy()->subDays(6)->toDateString(),
                $this->dataFim = $hoje->toDateString(),
            ],
            'mes_atual' => [
                $this->dataInicio = $hoje->copy()->startOfMonth()->toDateString(),
                $this->dataFim = $hoje->copy()->endOfMonth()->toDateString(),
            ],
            'ano_atual' => [
                $this->dataInicio = $hoje->copy()->startOfYear()->toDateString(),
                $this->dataFim = $hoje->copy()->endOfYear()->toDateString(),
            ],
            default => [
                $this->dataInicio = '',
                $this->dataFim = '',
            ],
        };

        $this->resetPage();
    }

    public function toggleFiltros(): void
    {
        $this->mostrarFiltros = !$this->mostrarFiltros;
    }

    public function limparFiltros(): void
    {
        $this->reset(['search', 'beneficiarioId', 'dataInicio', 'dataFim', 'periodoPredefinido', 'produtoId', 'ordenacao']);
        $this->resetPage();
    }

    public function getFiltrosAtivosCountProperty(): int
    {
        $count = 0;
        if (!empty($this->beneficiarioId)) $count++;
        if (!empty($this->dataInicio) || !empty($this->dataFim)) $count++;
        if (!empty($this->produtoId)) $count++;
        if ($this->ordenacao !== 'recente') $count++;
        return $count;
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId = $id;
    }

    public function delete(): void
    {
        if ($this->deletingId) {
            Retirada::findOrFail($this->deletingId)->delete();
            $this->deletingId = null;
            session()->flash('success', 'Retirada removida com sucesso.');
        }
    }

    public function render()
    {
        $beneficiariosDisponiveis = Beneficiario::orderBy('nome')->get(['id', 'nome', 'cpf']);
        $produtosDisponiveis = Produto::orderBy('nome')->get(['id', 'nome', 'unidade']);

        $query = Retirada::with(['beneficiario', 'items.produto'])
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->whereHas('beneficiario', function ($b) {
                        $b->where('nome', 'like', "%{$this->search}%")
                          ->orWhere('cpf', 'like', "%{$this->search}%")
                          ->orWhere('bairro', 'like', "%{$this->search}%");
                    })->orWhere('observacoes', 'like', "%{$this->search}%");
                });
            })
            ->when($this->beneficiarioId, fn($q) => $q->where('retiradas.beneficiario_id', $this->beneficiarioId))
            ->when($this->dataInicio, fn($q) => $q->whereDate('retiradas.data', '>=', $this->dataInicio))
            ->when($this->dataFim, fn($q) => $q->whereDate('retiradas.data', '<=', $this->dataFim))
            ->when($this->produtoId, function ($q) {
                $q->whereHas('items', fn($item) => $item->where('produto_id', $this->produtoId));
            });

        // Ordenação
        match ($this->ordenacao) {
            'antigo' => $query->orderBy('retiradas.data')->orderBy('retiradas.id'),
            'beneficiario_asc' => $query->join('beneficiarios', 'retiradas.beneficiario_id', '=', 'beneficiarios.id')
                                       ->orderBy('beneficiarios.nome')
                                       ->select('retiradas.*'),
            default => $query->orderByDesc('retiradas.data')->orderByDesc('retiradas.id'),
        };

        $retiradas = $query->paginate(15);

        return view('livewire.retiradas.index', [
            'retiradas' => $retiradas,
            'beneficiariosDisponiveis' => $beneficiariosDisponiveis,
            'produtosDisponiveis' => $produtosDisponiveis,
        ])->layout('layouts.app', ['title' => 'Retiradas']);
    }
}

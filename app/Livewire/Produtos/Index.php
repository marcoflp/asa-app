<?php

namespace App\Livewire\Produtos;

use App\Models\Produto;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $categoria = '';
    public string $statusEstoque = ''; // todos, em_estoque, baixo_estoque, sem_estoque
    public string $statusAtivo = ''; // todos, ativo, inativo
    public string $ordenacao = 'categoria_nome'; // categoria_nome, nome_asc, nome_desc, menor_estoque, maior_estoque
    public bool $mostrarFiltros = false;

    public ?int $deletingId = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'categoria' => ['except' => ''],
        'statusEstoque' => ['except' => ''],
        'statusAtivo' => ['except' => ''],
        'ordenacao' => ['except' => 'categoria_nome'],
    ];

    public function updated($propertyName): void
    {
        if (in_array($propertyName, ['search', 'categoria', 'statusEstoque', 'statusAtivo', 'ordenacao'])) {
            $this->resetPage();
        }
    }

    public function toggleFiltros(): void
    {
        $this->mostrarFiltros = !$this->mostrarFiltros;
    }

    public function limparFiltros(): void
    {
        $this->reset(['search', 'categoria', 'statusEstoque', 'statusAtivo', 'ordenacao']);
        $this->resetPage();
    }

    public function getFiltrosAtivosCountProperty(): int
    {
        $count = 0;
        if (!empty($this->categoria)) $count++;
        if (!empty($this->statusEstoque)) $count++;
        if (!empty($this->statusAtivo)) $count++;
        if ($this->ordenacao !== 'categoria_nome') $count++;
        return $count;
    }

    public function toggleAtivo(int $id): void
    {
        $produto = Produto::findOrFail($id);
        $produto->update(['ativo' => !$produto->ativo]);
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId = $id;
    }

    public function delete(): void
    {
        if ($this->deletingId) {
            Produto::findOrFail($this->deletingId)->delete();
            $this->deletingId = null;
            session()->flash('success', 'Produto removido com sucesso.');
        }
    }

    public function render()
    {
        $categorias = Produto::distinct()->orderBy('categoria')->pluck('categoria');

        $query = Produto::query()
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('nome', 'like', "%{$this->search}%")
                        ->orWhere('descricao', 'like', "%{$this->search}%");
                });
            })
            ->when($this->categoria, fn($q) => $q->where('categoria', $this->categoria))
            ->when($this->statusAtivo === 'ativo', fn($q) => $q->where('ativo', true))
            ->when($this->statusAtivo === 'inativo', fn($q) => $q->where('ativo', false))
            ->when($this->statusEstoque === 'em_estoque', fn($q) => $q->where('estoque', '>', 0))
            ->when($this->statusEstoque === 'baixo_estoque', fn($q) => $q->where('estoque', '>', 0)->where('estoque', '<=', 10))
            ->when($this->statusEstoque === 'sem_estoque', fn($q) => $q->where(fn($sub) => $sub->where('estoque', '<=', 0)->orWhereNull('estoque')));

        // Ordenação
        match ($this->ordenacao) {
            'nome_asc' => $query->orderBy('nome'),
            'nome_desc' => $query->orderByDesc('nome'),
            'menor_estoque' => $query->orderBy('estoque')->orderBy('nome'),
            'maior_estoque' => $query->orderByDesc('estoque')->orderBy('nome'),
            default => $query->orderBy('categoria')->orderBy('nome'),
        };

        $produtos = $query->paginate(20);

        return view('livewire.produtos.index', [
            'produtos' => $produtos,
            'categorias' => $categorias,
        ])->layout('layouts.app', ['title' => 'Produtos']);
    }
}

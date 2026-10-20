<?php

namespace App\Livewire\Produtos;

use App\Models\Produto;
use App\Models\RetiradaItem;
use Flux\Flux;
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
        $this->mostrarFiltros = ! $this->mostrarFiltros;
    }

    public function limparFiltros(): void
    {
        $this->reset(['search', 'categoria', 'statusEstoque', 'statusAtivo', 'ordenacao']);
        $this->resetPage();
    }

    public function getFiltrosAtivosCountProperty(): int
    {
        $count = 0;
        if (! empty($this->categoria)) {
            $count++;
        }
        if (! empty($this->statusEstoque)) {
            $count++;
        }
        if (! empty($this->statusAtivo)) {
            $count++;
        }
        if ($this->ordenacao !== 'categoria_nome') {
            $count++;
        }

        return $count;
    }

    public function toggleAtivo(int $id): void
    {
        $produto = Produto::findOrFail($id);
        $produto->update(['ativo' => ! $produto->ativo]);
    }

    /** Liga/desliga "urgente": aparece em "O que estamos precisando" na página inicial. */
    public function toggleUrgente(int $id): void
    {
        $produto = Produto::findOrFail($id);
        $produto->update(['urgente' => ! $produto->urgente]);

        session()->now('success', $produto->urgente
            ? "{$produto->nome} agora aparece como urgente na página inicial."
            : "{$produto->nome} saiu da lista de urgentes da página inicial.");
    }

    /** Dados mostrados na janela de exclusão. */
    public string $excluindoNome = '';

    public int $excluindoUsos = 0;

    public function confirmDelete(int $id): void
    {
        $p = Produto::findOrFail($id);
        $this->deletingId = $p->id;
        $this->excluindoNome = $p->nome;
        $this->excluindoUsos = RetiradaItem::where('produto_id', $p->id)->count();

        Flux::modal('confirm-delete')->show();
    }

    public function delete(): void
    {
        if (! $this->deletingId) {
            return;
        }

        $produto = Produto::findOrFail($this->deletingId);

        // Produto que já saiu em retiradas não pode ser apagado (perderia o histórico); a janela oferece desativar
        if (RetiradaItem::where('produto_id', $produto->id)->exists()) {
            return;
        }

        $produto->delete();
        $this->deletingId = null;
        session()->now('success', "O produto {$produto->nome} foi excluído.");
    }

    public function desativar(): void
    {
        if ($this->deletingId) {
            $produto = Produto::findOrFail($this->deletingId);
            $produto->update(['ativo' => false]);
            $this->deletingId = null;
            session()->now('success', "O produto {$produto->nome} foi desativado. Ele não aparece mais nas retiradas, mas o histórico continua guardado.");
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
            ->when($this->categoria, fn ($q) => $q->where('categoria', $this->categoria))
            ->when($this->statusAtivo === 'ativo', fn ($q) => $q->where('ativo', true))
            ->when($this->statusAtivo === 'inativo', fn ($q) => $q->where('ativo', false))
            ->when($this->statusEstoque === 'em_estoque', fn ($q) => $q->where('estoque', '>', 0))
            ->when($this->statusEstoque === 'baixo_estoque', fn ($q) => $q->where('estoque', '>', 0)->where('estoque', '<=', 10))
            ->when($this->statusEstoque === 'sem_estoque', fn ($q) => $q->where(fn ($sub) => $sub->where('estoque', '<=', 0)->orWhereNull('estoque')));

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

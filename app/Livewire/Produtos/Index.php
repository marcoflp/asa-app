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
    public ?int $deletingId = null;

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingCategoria(): void { $this->resetPage(); }

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

        $produtos = Produto::query()
            ->when($this->search, fn($q) => $q->where('nome', 'like', "%{$this->search}%"))
            ->when($this->categoria, fn($q) => $q->where('categoria', $this->categoria))
            ->orderBy('categoria')
            ->orderBy('nome')
            ->paginate(20);

        return view('livewire.produtos.index', compact('produtos', 'categorias'))
            ->layout('layouts.app', ['title' => 'Produtos']);
    }
}

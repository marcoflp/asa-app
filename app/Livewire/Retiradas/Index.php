<?php

namespace App\Livewire\Retiradas;

use App\Models\Retirada;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $dataInicio = '';
    public string $dataFim = '';
    public ?int $deletingId = null;

    public function updatingSearch(): void { $this->resetPage(); }

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
        $retiradas = Retirada::with(['beneficiario', 'items.produto'])
            ->when($this->search, fn($q) => $q->whereHas('beneficiario',
                fn($b) => $b->where('nome', 'like', "%{$this->search}%")
            ))
            ->when($this->dataInicio, fn($q) => $q->whereDate('data', '>=', $this->dataInicio))
            ->when($this->dataFim, fn($q) => $q->whereDate('data', '<=', $this->dataFim))
            ->orderByDesc('data')
            ->paginate(15);

        return view('livewire.retiradas.index', compact('retiradas'))
            ->layout('layouts.app', ['title' => 'Retiradas']);
    }
}

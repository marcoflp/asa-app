<?php

namespace App\Livewire\Beneficiarios;

use App\Models\Beneficiario;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public ?int $deletingId = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId = $id;
    }

    public function delete(): void
    {
        if ($this->deletingId) {
            Beneficiario::findOrFail($this->deletingId)->delete();
            $this->deletingId = null;
            session()->flash('success', 'Beneficiário removido com sucesso.');
        }
    }

    public function render()
    {
        $beneficiarios = Beneficiario::query()
            ->when($this->search, fn($q) => $q->where('nome', 'like', "%{$this->search}%")
                ->orWhere('cpf', 'like', "%{$this->search}%")
                ->orWhere('bairro', 'like', "%{$this->search}%"))
            ->orderBy('nome')
            ->paginate(15);

        return view('livewire.beneficiarios.index', compact('beneficiarios'))
            ->layout('layouts.app', ['title' => 'Beneficiários']);
    }
}

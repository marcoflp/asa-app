<?php

namespace App\Livewire\Retiradas;

use App\Models\Beneficiario;
use App\Models\Produto;
use App\Models\Retirada;
use Livewire\Component;

class Form extends Component
{
    public ?Retirada $retirada = null;

    public int $beneficiario_id = 0;
    public string $data = '';
    public string $observacoes = '';
    public array $items = []; // [{produto_id, quantidade}]

    // Temporários para adicionar item
    public int $item_produto_id = 0;
    public int $item_quantidade = 1;

    public function mount(?Retirada $retirada = null): void
    {
        $this->data = now()->toDateString();

        if ($retirada && $retirada->exists) {
            $this->retirada = $retirada;
            $this->beneficiario_id = $retirada->beneficiario_id;
            $this->data = $retirada->data->toDateString();
            $this->observacoes = $retirada->observacoes ?? '';
            $this->items = $retirada->items->map(fn($i) => [
                'produto_id' => $i->produto_id,
                'quantidade' => $i->quantidade,
                'produto_nome' => $i->produto->nome,
            ])->toArray();
        }
    }

    public function addItem(): void
    {
        if (!$this->item_produto_id || $this->item_quantidade < 1) return;

        // Evita duplicata — soma quantidade se produto já estiver na lista
        foreach ($this->items as &$item) {
            if ($item['produto_id'] === $this->item_produto_id) {
                $item['quantidade'] += $this->item_quantidade;
                $this->item_produto_id = 0;
                $this->item_quantidade = 1;
                return;
            }
        }

        $produto = Produto::find($this->item_produto_id);
        $this->items[] = [
            'produto_id' => $this->item_produto_id,
            'quantidade' => $this->item_quantidade,
            'produto_nome' => $produto->nome,
        ];

        $this->item_produto_id = 0;
        $this->item_quantidade = 1;
    }

    public function removeItem(int $index): void
    {
        array_splice($this->items, $index, 1);
        $this->items = array_values($this->items);
    }

    public function save(): void
    {
        $this->validate([
            'beneficiario_id' => 'required|exists:beneficiarios,id',
            'data' => 'required|date',
            'items' => 'required|array|min:1',
            'observacoes' => 'nullable|string',
        ], [
            'items.min' => 'Adicione ao menos um item à retirada.',
        ]);

        $dados = [
            'beneficiario_id' => $this->beneficiario_id,
            'data' => $this->data,
            'observacoes' => $this->observacoes ?: null,
        ];

        if ($this->retirada && $this->retirada->exists) {
            $this->retirada->update($dados);
            $this->retirada->items()->delete();
            $retirada = $this->retirada;
        } else {
            $retirada = Retirada::create($dados);
        }

        foreach ($this->items as $item) {
            $retirada->items()->create([
                'produto_id' => $item['produto_id'],
                'quantidade' => $item['quantidade'],
            ]);
        }

        session()->flash('success', 'Retirada salva com sucesso.');
        $this->redirect(route('retiradas.index'), navigate: true);
    }

    public function render()
    {
        $title = $this->retirada?->exists ? 'Editar Retirada' : 'Nova Retirada';

        return view('livewire.retiradas.form', [
            'beneficiarios' => Beneficiario::orderBy('nome')->get(['id', 'nome']),
            'produtos' => Produto::ativo()->orderBy('categoria')->orderBy('nome')->get(['id', 'nome', 'categoria', 'unidade']),
        ])->layout('layouts.app', ['title' => $title]);
    }
}

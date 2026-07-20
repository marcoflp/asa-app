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
    public array $items = [];

    public string $searchBeneficiario = '';
    public string $searchProduto = '';

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
                'produto_unidade' => $i->produto->unidade ?? 'un',
            ])->toArray();
        }
    }

    public function adicionarProduto(int $produtoId): void
    {
        foreach ($this->items as &$item) {
            if ($item['produto_id'] === $produtoId) {
                $item['quantidade']++;
                $this->searchProduto = ''; // Limpar busca após adicionar
                return;
            }
        }

        $produto = Produto::find($produtoId);
        if ($produto) {
            $this->items[] = [
                'produto_id' => $produto->id,
                'quantidade' => 1,
                'produto_nome' => $produto->nome,
                'produto_unidade' => $produto->unidade ?? 'un',
            ];
        }
        
        $this->searchProduto = '';
    }

    public function incrementarItem(int $index): void
    {
        if (isset($this->items[$index])) {
            $this->items[$index]['quantidade']++;
        }
    }

    public function decrementarItem(int $index): void
    {
        if (isset($this->items[$index])) {
            if ($this->items[$index]['quantidade'] > 1) {
                $this->items[$index]['quantidade']--;
            } else {
                $this->removeItem($index);
            }
        }
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

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($dados) {
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
            });

            session()->flash('success', 'Retirada salva com sucesso.');
            $this->redirect(route('retiradas.index'), navigate: true);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Erro ao salvar retirada: " . $e->getMessage());
            $this->addError('geral', 'Ocorreu um erro ao salvar a retirada. Verifique o estoque e tente novamente.');
        }
    }

    public function render()
    {
        $title = $this->retirada?->exists ? 'Editar Retirada' : 'Nova Retirada';

        $beneficiarios = Beneficiario::query()
            ->when($this->searchBeneficiario, fn($q) => $q->where('nome', 'like', '%' . $this->searchBeneficiario . '%'))
            ->orderBy('nome')
            ->get(['id', 'nome']);

        $produtos = Produto::ativo()
            ->when($this->searchProduto, fn($q) => $q->where('nome', 'like', '%' . $this->searchProduto . '%')->orWhere('categoria', 'like', '%' . $this->searchProduto . '%'))
            ->orderBy('categoria')
            ->orderBy('nome')
            ->get(['id', 'nome', 'categoria', 'unidade', 'estoque']);

        return view('livewire.retiradas.form', [
            'beneficiarios' => $beneficiarios,
            'produtos' => $produtos,
        ])->layout('layouts.app', ['title' => $title]);
    }
}

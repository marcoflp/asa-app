<?php

namespace App\Livewire\Retiradas;

use App\Models\Beneficiario;
use App\Models\Produto;
use App\Models\Retirada;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
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
            $this->items = $retirada->items->map(fn ($i) => [
                'produto_id' => $i->produto_id,
                'quantidade' => $i->quantidade,
                'produto_nome' => $i->produto->nome,
                'produto_unidade' => $i->produto->unidade ?? 'un',
            ])->toArray();
        }
    }

    public function selecionarBeneficiario(int $id): void
    {
        if (Beneficiario::whereKey($id)->exists()) {
            $this->beneficiario_id = $id;
            $this->searchBeneficiario = '';
            $this->resetErrorBag('beneficiario_id');
        }
    }

    public function trocarBeneficiario(): void
    {
        $this->beneficiario_id = 0;
    }

    public function updatedItems($value, $key): void
    {
        // Quantidade digitada à mão: nunca menor que 1
        if (str_ends_with((string) $key, '.quantidade')) {
            $index = (int) explode('.', $key)[0];
            $this->items[$index]['quantidade'] = max(1, (int) $value);
        }
    }

    /** Normaliza texto para busca: minúsculo e sem acentos. */
    private static function normalizar(?string $texto): string
    {
        return Str::lower(Str::ascii((string) $texto));
    }

    /**
     * Filtra registros por todas as palavras digitadas, sem diferenciar acentos.
     * Números digitados também são procurados nos campos numéricos (CPF, telefone).
     */
    private static function filtrar(Collection $registros, string $busca, array $camposTexto, array $camposNumero = []): Collection
    {
        $termos = array_filter(explode(' ', self::normalizar($busca)));
        $digitos = preg_replace('/\D/', '', $busca);

        if (! $termos) {
            return $registros;
        }

        return $registros->filter(function ($r) use ($termos, $digitos, $camposTexto, $camposNumero) {
            if (strlen($digitos) >= 3) {
                foreach ($camposNumero as $campo) {
                    if (str_contains(preg_replace('/\D/', '', (string) $r->{$campo}), $digitos)) {
                        return true;
                    }
                }
            }

            $texto = self::normalizar(implode(' ', array_map(fn ($c) => $r->{$c}, $camposTexto)));

            foreach ($termos as $termo) {
                if (! str_contains($texto, $termo)) {
                    return false;
                }
            }

            return true;
        })->sortBy(function ($r) use ($termos, $camposTexto) {
            // Quem começa com o que foi digitado aparece primeiro
            return str_starts_with(self::normalizar($r->{$camposTexto[0]}), $termos[array_key_first($termos)]) ? 0 : 1;
        })->values();
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
            'data' => 'required|date|before_or_equal:today',
            'items' => 'required|array|min:1',
            'observacoes' => 'nullable|string',
        ], [
            'items.min' => 'Adicione ao menos um item à retirada.',
            'items.required' => 'Adicione ao menos um item à retirada.',
            'beneficiario_id.exists' => 'Escolha um beneficiário da lista.',
            'beneficiario_id.required' => 'Escolha um beneficiário da lista.',
            'data.before_or_equal' => 'A data não pode ser no futuro.',
        ]);

        $dados = [
            'beneficiario_id' => $this->beneficiario_id,
            'data' => $this->data,
            'observacoes' => $this->observacoes ?: null,
        ];

        try {
            DB::transaction(function () use ($dados) {
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
            Log::error('Erro ao salvar retirada: '.$e->getMessage());
            $this->addError('geral', 'Ocorreu um erro ao salvar a retirada. Verifique o estoque e tente novamente.');
        }
    }

    public function render()
    {
        $title = $this->retirada?->exists ? 'Editar Retirada' : 'Nova Retirada';

        $beneficiarioSelecionado = $this->beneficiario_id
            ? Beneficiario::withMax('retiradas', 'data')->find($this->beneficiario_id, ['id', 'nome', 'bairro'])
            : null;

        $beneficiarios = collect();
        if (! $beneficiarioSelecionado) {
            $todos = Beneficiario::withMax('retiradas', 'data')
                ->orderBy('nome')
                ->get(['id', 'nome', 'bairro', 'cpf', 'telefone']);

            $beneficiarios = trim($this->searchBeneficiario) === ''
                // Sem busca: quem foi atendido mais recentemente primeiro
                ? $todos->sortByDesc('retiradas_max_data')->take(6)->values()
                : self::filtrar($todos, $this->searchBeneficiario, ['nome', 'bairro'], ['cpf', 'telefone'])->take(8);
        }

        $produtos = collect();
        if (trim($this->searchProduto) !== '') {
            $produtos = self::filtrar(
                Produto::ativo()->orderBy('nome')->get(['id', 'nome', 'categoria', 'unidade', 'estoque']),
                $this->searchProduto,
                ['nome', 'categoria'],
            )->take(10);
        }

        return view('livewire.retiradas.form', [
            'beneficiarios' => $beneficiarios,
            'beneficiarioSelecionado' => $beneficiarioSelecionado,
            'produtos' => $produtos,
        ])->layout('layouts.app', ['title' => $title]);
    }
}

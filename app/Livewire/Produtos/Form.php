<?php

namespace App\Livewire\Produtos;

use App\Models\Produto;
use Flux\Flux;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Form extends Component
{
    public ?Produto $produto = null;

    public string $nome = '';

    public string $categoria = '';

    public string $unidade = '';

    public string $descricao = '';

    public bool $controlarEstoque = false;

    public ?int $estoque = null;

    public bool $ativo = true;

    const UNIDADES = ['unidade', 'kg', 'litro', 'frasco', 'pacote', 'lata', 'par', 'peça', 'rolo', 'caixa'];

    public function mount(?Produto $produto = null): void
    {
        if ($produto && $produto->exists) {
            $this->produto = $produto;
            $this->nome = $produto->nome;
            $this->categoria = $produto->categoria ?? '';
            $this->unidade = $produto->unidade ?? '';
            $this->descricao = $produto->descricao ?? '';
            $this->estoque = $produto->estoque;
            $this->controlarEstoque = $produto->estoque !== null;
            $this->ativo = (bool) $produto->ativo;
        }
    }

    private static function normalizar(?string $texto): string
    {
        return Str::of((string) $texto)->ascii()->lower()->squish()->toString();
    }

    /** Usa a grafia já existente quando o texto bate com uma opção (sem diferenciar acentos). */
    private static function canonico(string $valor, iterable $opcoes): string
    {
        $valor = trim($valor);
        $alvo = self::normalizar($valor);

        foreach ($opcoes as $o) {
            if (self::normalizar($o) === $alvo) {
                return $o;
            }
        }

        return $valor;
    }

    #[Computed]
    public function categorias(): array
    {
        return Produto::whereNotNull('categoria')->distinct()->orderBy('categoria')->pluck('categoria')->filter()->values()->all();
    }

    #[Computed]
    public function unidades(): array
    {
        $doBanco = Produto::whereNotNull('unidade')->distinct()->pluck('unidade')->filter()->all();

        return collect(self::UNIDADES)->merge($doBanco)->unique(fn ($u) => self::normalizar($u))->values()->all();
    }

    /** Produtos com o mesmo nome, para evitar cadastro duplicado. */
    #[Computed]
    public function parecidos(): Collection
    {
        $nome = self::normalizar($this->nome);
        if (strlen($nome) < 3) {
            return collect();
        }

        return Produto::query()
            ->when($this->produto?->id, fn ($q, $id) => $q->whereKeyNot($id))
            ->get(['id', 'nome', 'categoria', 'unidade', 'estoque', 'ativo'])
            ->filter(fn ($p) => self::normalizar($p->nome) === $nome
                || (strlen($nome) >= 6 && levenshtein(self::normalizar($p->nome), $nome) <= 1))
            ->values();
    }

    /** O voluntário já confirmou na janela que é outro produto. */
    public bool $confirmouProdutoDiferente = false;

    public function continuarCadastro(): void
    {
        $this->confirmouProdutoDiferente = true;
        Flux::modal('produto-parecido')->close();
        $this->save();
    }

    /** Na janela de produto parecido: em vez de criar outro, reativa o que já existe. */
    public function reativarProduto(int $id): void
    {
        $existente = Produto::findOrFail($id);
        $existente->update(['ativo' => true]);

        session()->flash('success', "O produto \"{$existente->nome}\" foi reativado e já aparece nas retiradas.");
        $this->redirect(route('produtos.index'), navigate: true);
    }

    public function updatedControlarEstoque(bool $ligado): void
    {
        if ($ligado && $this->estoque === null) {
            $this->estoque = 0;
        }
    }

    public function updated(string $campo): void
    {
        if ($campo === 'nome') {
            $this->validateOnly('nome');
            $this->confirmouProdutoDiferente = false;
        }
    }

    protected function rules(): array
    {
        return [
            'nome' => 'required|string|min:2|max:255',
            'categoria' => 'required|string|max:100',
            'unidade' => 'required|string|max:50',
            'descricao' => 'nullable|string',
            // Pode ficar negativo quando saiu mais do que foi registrado; o voluntário corrige com a contagem real
            'estoque' => $this->controlarEstoque ? 'required|integer|min:-100000|max:100000' : 'nullable',
        ];
    }

    protected function messages(): array
    {
        return [
            'nome.required' => 'Informe o nome do produto.',
            'categoria.required' => 'Escolha ou digite uma categoria.',
            'unidade.required' => 'Escolha ou digite a unidade.',
            'estoque.required' => 'Informe quantos tem em estoque (pode ser 0).',
            'estoque.integer' => 'O estoque precisa ser um número inteiro.',
        ];
    }

    public function save(): void
    {
        $this->categoria = self::canonico($this->categoria, $this->categorias);
        $this->unidade = self::canonico($this->unidade, $this->unidades);

        try {
            $this->validate();
        } catch (ValidationException $e) {
            $this->dispatch('formulario-invalido');
            throw $e;
        }

        if (! $this->produto?->exists && ! $this->confirmouProdutoDiferente && $this->parecidos->isNotEmpty()) {
            Flux::modal('produto-parecido')->show();

            return;
        }

        $data = [
            'nome' => trim($this->nome),
            'categoria' => $this->categoria,
            'unidade' => $this->unidade,
            'descricao' => $this->descricao ?: null,
            'estoque' => $this->controlarEstoque ? (int) $this->estoque : null,
            'ativo' => $this->ativo,
        ];

        try {
            if ($this->produto && $this->produto->exists) {
                $this->produto->update($data);
            } else {
                Produto::create($data);
            }

            session()->flash('success', 'Produto salvo com sucesso.');
            $this->redirect(route('produtos.index'), navigate: true);
        } catch (\Exception $e) {
            Log::error('Erro ao salvar produto: '.$e->getMessage());
            $this->addError('geral', 'Não foi possível salvar agora. Tente de novo em instantes.');
        }
    }

    public function render()
    {
        $title = $this->produto?->exists ? 'Editar Produto' : 'Novo Produto';

        return view('livewire.produtos.form')
            ->layout('layouts.app', ['title' => $title]);
    }
}

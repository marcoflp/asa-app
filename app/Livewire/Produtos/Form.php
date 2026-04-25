<?php

namespace App\Livewire\Produtos;

use App\Models\Produto;
use Livewire\Component;

class Form extends Component
{
    public ?Produto $produto = null;

    public string $nome = '';
    public string $categoria = '';
    public string $novaCategoria = '';
    public string $unidade = '';
    public string $novaUnidade = '';
    public string $descricao = '';
    public ?int $estoque = null;
    public bool $ativo = true;

    const UNIDADES = ['unidade', 'kg', 'litro', 'frasco', 'pacote', 'lata', 'par', 'peça', 'rolo', 'caixa'];

    public function mount(?Produto $produto = null): void
    {
        if ($produto && $produto->exists) {
            $this->produto = $produto;
            $this->nome = $produto->nome;
            $this->categoria = $produto->categoria;
            $this->unidade = in_array($produto->unidade, self::UNIDADES) ? $produto->unidade : 'outro';
            $this->novaUnidade = in_array($produto->unidade, self::UNIDADES) ? '' : $produto->unidade;
            $this->descricao = $produto->descricao ?? '';
            $this->estoque = $produto->estoque;
            $this->ativo = $produto->ativo;
        }
    }

    public function save(): void
    {
        $unidadeFinal = $this->unidade === 'outro' ? $this->novaUnidade : $this->unidade;
        $categoriaFinal = $this->categoria === 'outra' ? $this->novaCategoria : $this->categoria;

        $this->validate([
            'nome' => 'required|string|max:255',
            'categoria' => 'required|string',
            'unidade' => 'required|string',
            'descricao' => 'nullable|string',
            'estoque' => 'nullable|integer|min:0',
        ]);

        $this->validateOnly('nome', ['nome' => 'required']);

        if (!$categoriaFinal) {
            $this->addError('categoria', 'Informe a categoria.');
            return;
        }

        if (!$unidadeFinal) {
            $this->addError('unidade', 'Informe a unidade.');
            return;
        }

        $data = [
            'nome' => $this->nome,
            'categoria' => $categoriaFinal,
            'unidade' => $unidadeFinal,
            'descricao' => $this->descricao ?: null,
            'estoque' => $this->estoque,
            'ativo' => $this->ativo,
        ];

        if ($this->produto && $this->produto->exists) {
            $this->produto->update($data);
        } else {
            Produto::create($data);
        }

        session()->flash('success', 'Produto salvo com sucesso.');
        $this->redirect(route('produtos.index'), navigate: true);
    }

    public function render()
    {
        $categorias = Produto::distinct()->orderBy('categoria')->pluck('categoria');
        $title = $this->produto?->exists ? 'Editar Produto' : 'Novo Produto';

        return view('livewire.produtos.form', compact('categorias'))
            ->layout('layouts.app', ['title' => $title]);
    }
}

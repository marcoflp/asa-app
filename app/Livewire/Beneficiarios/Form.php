<?php

namespace App\Livewire\Beneficiarios;

use App\Models\Beneficiario;
use Livewire\Component;
use Livewire\WithFileUploads;

class Form extends Component
{
    use WithFileUploads;

    public ?Beneficiario $beneficiario = null;

    public string $nome = '';
    public string $telefone = '';
    public string $rua = '';
    public string $numero = '';
    public string $bairro = '';
    public string $cidade = 'Passo Fundo';
    public string $cep = '';
    public string $rg = '';
    public string $cpf = '';
    public int $num_pessoas_familia = 1;
    public array $filhos = [];
    public bool $inscrito_programa_governo = false;
    public string $programa_governo = '';
    public bool $recebe_estudo_biblico = false;
    public string $instrutor_biblico = '';
    public string $observacoes = '';
    public $foto_documento;
    public ?string $foto_documento_path = null;

    // Campos temporários para adicionar filho
    public int $filho_idade = 0;

    public function mount(?Beneficiario $beneficiario = null): void
    {
        if ($beneficiario && $beneficiario->exists) {
            $this->beneficiario = $beneficiario;
            $this->fill($beneficiario->only([
                'nome', 'telefone', 'rua', 'numero', 'bairro', 'cidade', 'cep',
                'rg', 'cpf', 'num_pessoas_familia',
                'inscrito_programa_governo', 'programa_governo',
                'recebe_estudo_biblico', 'instrutor_biblico', 'observacoes',
            ]));
            $this->filhos = $beneficiario->filhos ?? [];
            $this->foto_documento_path = $beneficiario->foto_documento;
        }
    }

    public function addFilho(): void
    {
        $this->filhos[] = ['idade' => $this->filho_idade];
        $this->filho_idade = 0;
    }

    public function removeFilho(int $index): void
    {
        array_splice($this->filhos, $index, 1);
        $this->filhos = array_values($this->filhos);
    }

    public function save(): void
    {
        $data = $this->validate([
            'nome' => 'required|string|max:255',
            'telefone' => 'nullable|string|max:20',
            'rua' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:20',
            'bairro' => 'nullable|string|max:100',
            'cidade' => 'required|string|max:100',
            'cep' => 'nullable|string|max:9',
            'rg' => 'nullable|string|max:20',
            'cpf' => 'nullable|string|max:14',
            'foto_documento' => 'nullable|image|max:10240',
            'num_pessoas_familia' => 'required|integer|min:1',
            'filhos' => 'nullable|array',
            'inscrito_programa_governo' => 'boolean',
            'programa_governo' => 'nullable|string|max:255',
            'recebe_estudo_biblico' => 'boolean',
            'instrutor_biblico' => 'nullable|string|max:255',
            'observacoes' => 'nullable|string',
        ]);

        try {
            if ($this->foto_documento) {
                $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
                $image = $manager->read($this->foto_documento->getRealPath());
                $image->scale(width: 1000);
                $filename = 'documentos/' . $this->foto_documento->hashName();
                \Illuminate\Support\Facades\Storage::disk('public')->put($filename, (string) $image->toJpeg(70));
                $data['foto_documento'] = $filename;
            }

            if ($this->beneficiario && $this->beneficiario->exists) {
                $this->beneficiario->update($data);
                session()->flash('success', 'Beneficiário atualizado com sucesso.');
            } else {
                Beneficiario::create($data);
                session()->flash('success', 'Beneficiário cadastrado com sucesso.');
            }

            $this->redirect(route('beneficiarios.index'), navigate: true);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Erro ao salvar beneficiário: " . $e->getMessage());
            $this->addError('geral', 'Erro ao salvar os dados. ' . $e->getMessage());
        }
    }

    public function render()
    {
        $title = $this->beneficiario?->exists ? 'Editar Beneficiário' : 'Novo Beneficiário';

        return view('livewire.beneficiarios.form')
            ->layout('layouts.app', ['title' => $title]);
    }
}

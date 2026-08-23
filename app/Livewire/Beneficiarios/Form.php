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
    public string $bairroSelecionado = '';
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
    public $foto_documento_verso;
    public ?string $foto_documento_verso_path = null;
    public $foto_documento_consentimento;
    public ?string $foto_documento_consentimento_path = null;
    public $foto_documento_comprovante_residencia;
    public ?string $foto_documento_comprovante_residencia_path = null;

    // Bairros e distritos oficiais de Passo Fundo em MAIÚSCULO e sem repetições
    public array $bairrosPadrao = [
        'ANNES',
        'BELA VISTA',
        'BOM JESUS',
        'BOM RECREIO',
        'BOQUEIRÃO',
        'CAPÃO BONITO',
        'CENTRO',
        'COHAB SECCHI',
        'CRUZ ALTA',
        'CRUZEIRO',
        'INTEGRAÇÃO',
        'JOSÉ ALEXANDRE ZÁCHIA (ZÁCHIA)',
        'LOTEAMENTO POPULAR',
        'LUCAS ARAÚJO',
        'NENÊ GRAEFF',
        'OPERÁRIA',
        'PASSO DO MIRANDA',
        'PETRÓPOLIS',
        'PLANALTINA',
        'PULADOR',
        'RODRIGUES',
        'ROSELÂNDIA',
        'SANTA MARIA',
        'SANTA MARTA',
        'SANTO ANTÃO',
        'SÃO CRISTÓVÃO',
        'SÃO JOÃO DA BELA VISTA',
        'SÃO JOSÉ',
        'SÃO LUIZ GONZAGA',
        'SÃO PEDRINHO',
        'SÃO ROQUE',
        'SÃO VALENTIM',
        'SEDE INDEPENDÊNCIA',
        'VALINHOS',
        'VERA CRUZ',
        'VILA CRUZEIRO',
        'VILA LUIZA',
        'VILA MATTOS',
        'VILA NOVA',
        'VILA RODRIGUES',
        'VILA ROSSO',
        'VILA VICTOR ISSLER',
    ];

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
            $this->foto_documento_verso_path = $beneficiario->foto_documento_verso;
            $this->foto_documento_consentimento_path = $beneficiario->foto_documento_consentimento;
            $this->foto_documento_comprovante_residencia_path = $beneficiario->foto_documento_comprovante_residencia;
        }

        // Configuração do bairro selecionado
        $listaBairros = $this->bairrosDisponiveis;
        if (!empty($this->bairro)) {
            $bairroFormatado = mb_strtoupper(trim($this->bairro), 'UTF-8');
            $bairroExistente = collect($listaBairros)->first(function ($b) use ($bairroFormatado) {
                return $b === $bairroFormatado || str_contains($b, $bairroFormatado) || str_contains($bairroFormatado, $b);
            });

            if ($bairroExistente) {
                $this->bairro = $bairroExistente;
                $this->bairroSelecionado = $bairroExistente;
            } else {
                $this->bairroSelecionado = 'outro';
            }
        }
    }

    public function updatedBairroSelecionado(string $val): void
    {
        if ($val !== 'outro' && !empty($val)) {
            $this->bairro = $val;
        } elseif ($val === '') {
            $this->bairro = '';
        }
    }

    public function getBairrosDisponiveisProperty(): array
    {
        $bairrosDb = Beneficiario::whereNotNull('bairro')
            ->where('bairro', '!=', '')
            ->distinct()
            ->pluck('bairro')
            ->map(fn($b) => mb_strtoupper(trim($b), 'UTF-8'))
            ->filter()
            ->toArray();

        $todos = array_unique(array_merge($this->bairrosPadrao, $bairrosDb));
        sort($todos, SORT_NATURAL | SORT_FLAG_CASE);
        return array_values($todos);
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
        // Se selecionou um bairro na lista, garante que ele está no campo $bairro em maiúsculo
        if ($this->bairroSelecionado !== 'outro' && !empty($this->bairroSelecionado)) {
            $this->bairro = $this->bairroSelecionado;
        } elseif (!empty($this->bairro)) {
            $this->bairro = mb_strtoupper(trim($this->bairro), 'UTF-8');
        }

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
            'foto_documento' => 'nullable|file|mimes:jpg,jpeg,png,gif,heic,heif|max:10240',
            'foto_documento_verso' => 'nullable|file|mimes:jpg,jpeg,png,gif,heic,heif|max:10240',
            'foto_documento_consentimento' => 'nullable|file|mimes:jpg,jpeg,png,gif,heic,heif|max:10240',
            'foto_documento_comprovante_residencia' => 'nullable|file|mimes:jpg,jpeg,png,gif,heic,heif|max:10240',
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
                $data['foto_documento'] = $this->storePhoto($this->foto_documento);
            } else {
                unset($data['foto_documento']);
            }

            if ($this->foto_documento_verso) {
                $data['foto_documento_verso'] = $this->storePhoto($this->foto_documento_verso);
            } else {
                unset($data['foto_documento_verso']);
            }

            if ($this->foto_documento_consentimento) {
                $data['foto_documento_consentimento'] = $this->storePhoto($this->foto_documento_consentimento);
            } else {
                unset($data['foto_documento_consentimento']);
            }

            if ($this->foto_documento_comprovante_residencia) {
                $data['foto_documento_comprovante_residencia'] = $this->storePhoto($this->foto_documento_comprovante_residencia);
            } else {
                unset($data['foto_documento_comprovante_residencia']);
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

    private function storePhoto($file): string
    {
        $hashName = pathinfo($file->hashName(), PATHINFO_FILENAME) . '.jpg';
        $filename = 'documentos/' . $hashName;

        $filePath = $file->getRealPath();
        $isHeicConverted = false;

        // Se for HEIC, converte para JPEG antes de processar
        if (class_exists('\Maestroerror\HeicToJpg') && \Maestroerror\HeicToJpg::isHeic($filePath)) {
            $tempJpegPath = tempnam(sys_get_temp_dir(), 'heic') . '.jpg';
            \Maestroerror\HeicToJpg::convert($filePath)->saveAs($tempJpegPath);
            $filePath = $tempJpegPath;
            $isHeicConverted = true;
        }

        try {
            if (class_exists('\Intervention\Image\Facades\Image')) {
                $image = \Intervention\Image\Facades\Image::make($filePath);
                
                $image->resize(1000, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });

                $encoded = (string) $image->encode('jpg', 70);
            } else {
                // Fallback para Intervention Image v4
                $manager = \Intervention\Image\ImageManager::usingDriver(\Intervention\Image\Drivers\Gd\Driver::class);
                $image = $manager->decodePath($filePath);
                $image->scale(width: 1000);
                $encoded = (string) $image->encodeUsingFormat(\Intervention\Image\Format::JPEG, 70);
            }

            \Illuminate\Support\Facades\Storage::disk('public')->put($filename, $encoded);
        } finally {
            if ($isHeicConverted && file_exists($filePath)) {
                unlink($filePath);
            }
        }
        
        return $filename;
    }

    public function render()
    {
        $title = $this->beneficiario?->exists ? 'Editar Beneficiário' : 'Novo Beneficiário';

        return view('livewire.beneficiarios.form')
            ->layout('layouts.app', ['title' => $title]);
    }
}

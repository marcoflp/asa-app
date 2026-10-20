<?php

namespace App\Livewire\Beneficiarios;

use App\Models\Beneficiario;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Facades\Image;
use Intervention\Image\Format;
use Intervention\Image\ImageManager;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maestroerror\HeicToJpg;

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
    public ?int $filho_idade = null;

    public function mount(?Beneficiario $beneficiario = null): void
    {
        if ($beneficiario && $beneficiario->exists) {
            $this->beneficiario = $beneficiario;
            $dados = $beneficiario->only([
                'nome', 'telefone', 'rua', 'numero', 'bairro', 'cidade', 'cep',
                'rg', 'cpf', 'num_pessoas_familia',
                'inscrito_programa_governo', 'programa_governo',
                'recebe_estudo_biblico', 'instrutor_biblico', 'observacoes',
            ]);
            // Campos vazios no banco (null) viram texto vazio no formulário
            $this->fill(array_map(fn ($v) => $v ?? '', $dados));
            $this->num_pessoas_familia = (int) ($beneficiario->num_pessoas_familia ?: 1);
            $this->cidade = $this->cidade ?: 'Passo Fundo';
            $this->inscrito_programa_governo = (bool) $beneficiario->inscrito_programa_governo;
            $this->recebe_estudo_biblico = (bool) $beneficiario->recebe_estudo_biblico;
            $this->filhos = $beneficiario->filhos ?? [];
            $this->foto_documento_path = $beneficiario->foto_documento;
            $this->foto_documento_verso_path = $beneficiario->foto_documento_verso;
            $this->foto_documento_consentimento_path = $beneficiario->foto_documento_consentimento;
            $this->foto_documento_comprovante_residencia_path = $beneficiario->foto_documento_comprovante_residencia;
        }

        // Usa a grafia oficial do bairro quando ele estiver na lista
        $this->bairro = $this->bairroCanonico($this->bairro);
    }

    /** Minúsculo e sem acentos, para comparar textos. */
    private static function normalizar(?string $texto): string
    {
        return Str::of((string) $texto)->ascii()->lower()->squish()->toString();
    }

    private static function digitos(?string $texto): string
    {
        return preg_replace('/\D/', '', (string) $texto);
    }

    /** Valida os dígitos verificadores do CPF. */
    public static function cpfValido(string $cpf): bool
    {
        $cpf = self::digitos($cpf);
        if (strlen($cpf) !== 11 || preg_match('/^(\d)\1{10}$/', $cpf)) {
            return false;
        }
        for ($t = 9; $t < 11; $t++) {
            $soma = 0;
            for ($i = 0; $i < $t; $i++) {
                $soma += (int) $cpf[$i] * (($t + 1) - $i);
            }
            if ((int) $cpf[$t] !== ((10 * $soma) % 11) % 10) {
                return false;
            }
        }

        return true;
    }

    /** Devolve o bairro com a grafia da lista (sem diferenciar acentos) ou em maiúsculo se for novo. */
    private function bairroCanonico(?string $bairro): string
    {
        $bairro = trim((string) $bairro);
        if ($bairro === '') {
            return '';
        }

        $alvo = self::normalizar($bairro);
        $existente = collect($this->bairrosDisponiveis)->first(fn ($b) => self::normalizar($b) === $alvo);

        return $existente ?? mb_strtoupper($bairro, 'UTF-8');
    }

    protected function rules(): array
    {
        $id = $this->beneficiario?->id;
        $cpfOriginal = $this->beneficiario?->cpf;

        return [
            'nome' => 'required|string|min:3|max:255',
            'telefone' => ['nullable', 'string', 'max:20', function ($attr, $valor, $falha) {
                if ($valor && ! in_array(strlen(self::digitos($valor)), [10, 11])) {
                    $falha('Telefone incompleto. Use DDD + número, ex: (54) 99999-9999.');
                }
            }],
            'rua' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:20',
            'bairro' => 'nullable|string|max:100',
            'cidade' => 'required|string|max:100',
            'cep' => 'nullable|string|max:9',
            'rg' => 'nullable|string|max:20',
            'cpf' => ['nullable', 'string', 'max:14',
                Rule::unique('beneficiarios', 'cpf')->ignore($id),
                function ($attr, $valor, $falha) use ($cpfOriginal) {
                    // Cadastros antigos com CPF inválido não ficam travados se o CPF não foi alterado
                    if ($valor && $valor !== $cpfOriginal && ! self::cpfValido($valor)) {
                        $falha('CPF inválido. Confira os números.');
                    }
                }],
            'foto_documento' => 'nullable|file|mimes:jpg,jpeg,png,gif,heic,heif|max:10240',
            'foto_documento_verso' => 'nullable|file|mimes:jpg,jpeg,png,gif,heic,heif|max:10240',
            'foto_documento_consentimento' => 'nullable|file|mimes:jpg,jpeg,png,gif,heic,heif|max:10240',
            'foto_documento_comprovante_residencia' => 'nullable|file|mimes:jpg,jpeg,png,gif,heic,heif|max:10240',
            'num_pessoas_familia' => 'required|integer|min:1|max:30',
            'filhos' => 'nullable|array',
            'inscrito_programa_governo' => 'boolean',
            'programa_governo' => 'nullable|string|max:255',
            'recebe_estudo_biblico' => 'boolean',
            'instrutor_biblico' => 'nullable|string|max:255',
            'observacoes' => 'nullable|string',
        ];
    }

    protected function messages(): array
    {
        return [
            'nome.required' => 'Informe o nome completo.',
            'nome.min' => 'Informe o nome completo.',
            'cpf.unique' => 'Já existe um beneficiário com este CPF.',
            'num_pessoas_familia.min' => 'A família tem pelo menos 1 pessoa.',
            'foto_documento.mimes' => 'Envie uma foto (JPG, PNG ou HEIC).',
            'foto_documento.max' => 'A foto é grande demais (máximo 10 MB).',
            'foto_documento_verso.mimes' => 'Envie uma foto (JPG, PNG ou HEIC).',
            'foto_documento_verso.max' => 'A foto é grande demais (máximo 10 MB).',
            'foto_documento_consentimento.mimes' => 'Envie uma foto (JPG, PNG ou HEIC).',
            'foto_documento_consentimento.max' => 'A foto é grande demais (máximo 10 MB).',
            'foto_documento_comprovante_residencia.mimes' => 'Envie uma foto (JPG, PNG ou HEIC).',
            'foto_documento_comprovante_residencia.max' => 'A foto é grande demais (máximo 10 MB).',
        ];
    }

    /** Endereço encontrado pelo CEP (mensagem de retorno para o voluntário). */
    public string $cepStatus = '';

    /** Ao completar o CEP, preenche rua, bairro e cidade (sem apagar o que já foi digitado). */
    public function updatedCep(string $valor): void
    {
        $cep = self::digitos($valor);
        $this->cepStatus = '';

        if (strlen($cep) !== 8) {
            return;
        }

        try {
            $r = Http::timeout(4)->get("https://viacep.com.br/ws/{$cep}/json/")->json();
        } catch (\Throwable $e) {
            $this->cepStatus = 'nao-consultado';

            return;
        }

        if (! is_array($r) || ! empty($r['erro'])) {
            $this->cepStatus = 'nao-encontrado';

            return;
        }

        if ($this->rua === '' && ! empty($r['logradouro'])) {
            $this->rua = $r['logradouro'];
        }
        if ($this->bairro === '' && ! empty($r['bairro'])) {
            $this->bairro = $this->bairroCanonico($r['bairro']);
        }
        if (! empty($r['localidade'])) {
            $this->cidade = $r['localidade'];
        }
        $this->cepStatus = 'ok';
    }

    /** Valida o campo assim que o voluntário sai dele. */
    public function updated(string $campo): void
    {
        if (in_array($campo, ['nome', 'cpf', 'telefone', 'num_pessoas_familia'])) {
            $this->validateOnly($campo);
        }
        if (str_starts_with($campo, 'foto_documento')) {
            $this->validateOnly($campo);
        }
    }

    /** Cadastros parecidos (mesmo CPF ou mesmo nome) para evitar duplicidade. */
    #[Computed]
    public function parecidos(): Collection
    {
        $nome = self::normalizar($this->nome);
        $cpf = self::digitos($this->cpf);

        if (strlen($nome) < 5 && strlen($cpf) < 11) {
            return collect();
        }

        return Beneficiario::query()
            ->when($this->beneficiario?->id, fn ($q, $id) => $q->whereKeyNot($id))
            ->get(['id', 'nome', 'bairro', 'cpf'])
            ->filter(fn ($b) => (strlen($cpf) === 11 && self::digitos($b->cpf) === $cpf)
                || (strlen($nome) >= 5 && self::normalizar($b->nome) === $nome))
            ->take(3)
            ->values();
    }

    public function getBairrosDisponiveisProperty(): array
    {
        $bairrosDb = Beneficiario::whereNotNull('bairro')
            ->where('bairro', '!=', '')
            ->distinct()
            ->pluck('bairro')
            ->map(fn ($b) => mb_strtoupper(trim($b), 'UTF-8'))
            ->filter()
            ->toArray();

        $todos = array_unique(array_merge($this->bairrosPadrao, $bairrosDb));
        sort($todos, SORT_NATURAL | SORT_FLAG_CASE);

        return array_values($todos);
    }

    public function addFilho(): void
    {
        $this->validate(['filho_idade' => 'required|integer|min:0|max:17'], [
            'filho_idade.*' => 'Informe uma idade entre 0 e 17 anos.',
        ]);

        $this->filhos[] = ['idade' => (int) $this->filho_idade];
        $this->filho_idade = null;
    }

    public function removeFilho(int $index): void
    {
        array_splice($this->filhos, $index, 1);
        $this->filhos = array_values($this->filhos);
    }

    public function save(): void
    {
        $this->bairro = $this->bairroCanonico($this->bairro);

        try {
            $data = $this->validate();
        } catch (ValidationException $e) {
            // Leva o voluntário até o primeiro campo com erro
            $this->dispatch('formulario-invalido');
            throw $e;
        }

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
            Log::error('Erro ao salvar beneficiário: '.$e->getMessage());
            $this->addError('geral', 'Não foi possível salvar agora. Tente de novo em instantes; se continuar, avise o responsável pelo sistema.');
        }
    }

    private function storePhoto($file): string
    {
        $hashName = pathinfo($file->hashName(), PATHINFO_FILENAME).'.jpg';
        $filename = 'documentos/'.$hashName;

        $filePath = $file->getRealPath();
        $isHeicConverted = false;

        // Se for HEIC, converte para JPEG antes de processar
        if (class_exists('\Maestroerror\HeicToJpg') && HeicToJpg::isHeic($filePath)) {
            $tempJpegPath = tempnam(sys_get_temp_dir(), 'heic').'.jpg';
            HeicToJpg::convert($filePath)->saveAs($tempJpegPath);
            $filePath = $tempJpegPath;
            $isHeicConverted = true;
        }

        try {
            if (class_exists('\Intervention\Image\Facades\Image')) {
                $image = Image::make($filePath);

                $image->resize(1000, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });

                $encoded = (string) $image->encode('jpg', 70);
            } else {
                // Fallback para Intervention Image v4
                $manager = ImageManager::usingDriver(Driver::class);
                $image = $manager->decodePath($filePath);
                $image->scale(width: 1000);
                $encoded = (string) $image->encodeUsingFormat(Format::JPEG, 70);
            }

            Storage::disk('public')->put($filename, $encoded);
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

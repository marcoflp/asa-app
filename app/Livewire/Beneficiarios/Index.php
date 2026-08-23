<?php

namespace App\Livewire\Beneficiarios;

use App\Models\Beneficiario;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $bairro = '';
    public string $programaGoverno = '';
    public string $estudoBiblico = '';
    public string $documentos = '';
    public string $ordenacao = 'nome_asc';
    public bool $mostrarFiltros = false;

    public ?int $deletingId = null;
    public ?Beneficiario $selectedBeneficiario = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'bairro' => ['except' => ''],
        'programaGoverno' => ['except' => ''],
        'estudoBiblico' => ['except' => ''],
        'documentos' => ['except' => ''],
        'ordenacao' => ['except' => 'nome_asc'],
    ];

    public function updated($propertyName): void
    {
        if (in_array($propertyName, ['search', 'bairro', 'programaGoverno', 'estudoBiblico', 'documentos', 'ordenacao'])) {
            $this->resetPage();
        }
    }

    public function toggleFiltros(): void
    {
        $this->mostrarFiltros = !$this->mostrarFiltros;
    }

    public function limparFiltros(): void
    {
        $this->reset(['search', 'bairro', 'programaGoverno', 'estudoBiblico', 'documentos', 'ordenacao']);
        $this->resetPage();
    }

    public function getFiltrosAtivosCountProperty(): int
    {
        $count = 0;
        if (!empty($this->bairro)) $count++;
        if (!empty($this->programaGoverno)) $count++;
        if (!empty($this->estudoBiblico)) $count++;
        if (!empty($this->documentos)) $count++;
        if ($this->ordenacao !== 'nome_asc') $count++;
        return $count;
    }

    public function show(int $id): void
    {
        $this->selectedBeneficiario = Beneficiario::with(['retiradas' => fn($q) => $q->orderByDesc('data')])->findOrFail($id);
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

    public function render()
    {
        $bairrosDb = Beneficiario::whereNotNull('bairro')
            ->where('bairro', '!=', '')
            ->distinct()
            ->pluck('bairro')
            ->map(fn($b) => mb_strtoupper(trim($b), 'UTF-8'))
            ->filter()
            ->toArray();

        $bairrosDisponiveis = array_unique(array_merge($this->bairrosPadrao, $bairrosDb));
        sort($bairrosDisponiveis, SORT_NATURAL | SORT_FLAG_CASE);

        $query = Beneficiario::query()
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('nome', 'like', "%{$this->search}%")
                        ->orWhere('cpf', 'like', "%{$this->search}%")
                        ->orWhere('rg', 'like', "%{$this->search}%")
                        ->orWhere('telefone', 'like', "%{$this->search}%")
                        ->orWhere('bairro', 'like', "%{$this->search}%");
                });
            })
            ->when($this->bairro, fn($q) => $q->where('bairro', 'like', "%{$this->bairro}%"))
            ->when($this->programaGoverno === 'sim', fn($q) => $q->where('inscrito_programa_governo', true))
            ->when($this->programaGoverno === 'nao', fn($q) => $q->where(fn($sub) => $sub->where('inscrito_programa_governo', false)->orWhereNull('inscrito_programa_governo')))
            ->when($this->estudoBiblico === 'sim', fn($q) => $q->where('recebe_estudo_biblico', true))
            ->when($this->estudoBiblico === 'nao', fn($q) => $q->where(fn($sub) => $sub->where('recebe_estudo_biblico', false)->orWhereNull('recebe_estudo_biblico')))
            ->when($this->documentos === 'completos', function ($q) {
                $q->whereNotNull('foto_documento')->where('foto_documento', '!=', '')
                  ->whereNotNull('foto_documento_verso')->where('foto_documento_verso', '!=', '')
                  ->whereNotNull('foto_documento_comprovante_residencia')->where('foto_documento_comprovante_residencia', '!=', '')
                  ->whereNotNull('foto_documento_consentimento')->where('foto_documento_consentimento', '!=', '');
            })
            ->when($this->documentos === 'incompletos', function ($q) {
                $q->where(function ($sub) {
                    $sub->whereNull('foto_documento')->orWhere('foto_documento', '=', '')
                        ->orWhereNull('foto_documento_verso')->orWhere('foto_documento_verso', '=', '')
                        ->orWhereNull('foto_documento_comprovante_residencia')->orWhere('foto_documento_comprovante_residencia', '=', '')
                        ->orWhereNull('foto_documento_consentimento')->orWhere('foto_documento_consentimento', '=', '');
                });
            })
            ->when($this->documentos === 'sem_documentos', function ($q) {
                $q->where(fn($sub) => $sub->whereNull('foto_documento')->orWhere('foto_documento', '=', ''))
                  ->where(fn($sub) => $sub->whereNull('foto_documento_verso')->orWhere('foto_documento_verso', '=', ''))
                  ->where(fn($sub) => $sub->whereNull('foto_documento_comprovante_residencia')->orWhere('foto_documento_comprovante_residencia', '=', ''))
                  ->where(fn($sub) => $sub->whereNull('foto_documento_consentimento')->orWhere('foto_documento_consentimento', '=', ''));
            });

        // Ordenação
        match ($this->ordenacao) {
            'nome_desc' => $query->orderByDesc('nome'),
            'recentes' => $query->orderByDesc('created_at'),
            'antigos' => $query->orderBy('created_at'),
            default => $query->orderBy('nome'),
        };

        $beneficiarios = $query->paginate(15);

        return view('livewire.beneficiarios.index', [
            'beneficiarios' => $beneficiarios,
            'bairrosDisponiveis' => $bairrosDisponiveis,
        ])->layout('layouts.app', ['title' => 'Beneficiários']);
    }
}

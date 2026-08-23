<div class="flex h-full w-full flex-1 flex-col gap-4">

    @if (session('success'))
        <flux:callout variant="success" icon="check-circle">{{ session('success') }}</flux:callout>
    @endif

    <div class="flex items-center justify-between gap-4">
        <flux:heading size="xl" class="text-zinc-900 dark:text-zinc-50 font-bold">Beneficiários</flux:heading>
        <flux:button id="tour-btn-novo-beneficiario" href="{{ route('beneficiarios.create') }}" variant="primary" icon="plus" wire:navigate class="hidden sm:inline-flex font-bold shadow-xs">
            Novo Beneficiário
        </flux:button>
    </div>

    {{-- BARRA DE BUSCA & FILTROS --}}
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5">
        <div class="flex-1">
            <flux:input id="tour-search-beneficiario" wire:model.live.debounce.300ms="search" placeholder="Buscar por nome, CPF, RG, telefone ou bairro..." icon="magnifying-glass" class="w-full" />
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <button 
                type="button" 
                wire:click="toggleFiltros"
                class="flex-1 sm:flex-initial flex items-center justify-center gap-2 px-3.5 py-2 rounded-xl border text-xs sm:text-sm font-semibold transition-all cursor-pointer {{ $mostrarFiltros || $this->filtrosAtivosCount > 0 ? 'bg-emerald-50 dark:bg-emerald-950/60 border-emerald-500/50 text-emerald-800 dark:text-emerald-300 font-bold' : 'bg-white dark:bg-zinc-900 border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-800' }}"
            >
                <flux:icon.funnel class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                <span>Filtros</span>
                @if ($this->filtrosAtivosCount > 0)
                    <span class="inline-flex items-center justify-center w-5 h-5 text-[11px] font-bold bg-emerald-700 text-white rounded-full">
                        {{ $this->filtrosAtivosCount }}
                    </span>
                @endif
            </button>
            @if ($this->filtrosAtivosCount > 0 || !empty($search))
                <button 
                    type="button" 
                    wire:click="limparFiltros"
                    class="p-2 text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 rounded-xl hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors cursor-pointer text-xs font-semibold"
                    title="Limpar todos os filtros"
                >
                    Limpar
                </button>
            @endif
        </div>
    </div>

    {{-- PAINEL EXPANSÍVEL DE FILTROS --}}
    @if ($mostrarFiltros)
        <div 
            x-data 
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            class="p-4 sm:p-5 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-sm space-y-4"
        >
            <div class="flex items-center justify-between border-b border-zinc-100 dark:border-zinc-800 pb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-zinc-600 dark:text-zinc-400 flex items-center gap-2">
                    <flux:icon.adjustments-horizontal class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                    <span>Filtros Avançados</span>
                </span>
                <button 
                    type="button" 
                    wire:click="toggleFiltros"
                    class="text-xs text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 cursor-pointer"
                >
                    Fechar &times;
                </button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                {{-- Bairro --}}
                <div class="space-y-1">
                    <label class="text-xs font-bold text-zinc-700 dark:text-zinc-300">Bairro</label>
                    <select 
                        wire:model.live="bairro" 
                        class="w-full text-xs font-semibold py-2 px-2.5 rounded-lg border border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-emerald-500 focus:outline-hidden"
                    >
                        <option value="">Todos os bairros</option>
                        @foreach ($bairrosDisponiveis as $b)
                            <option value="{{ $b }}">{{ $b }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Programa Social / Governo --}}
                <div class="space-y-1">
                    <label class="text-xs font-bold text-zinc-700 dark:text-zinc-300">Programa Social</label>
                    <select 
                        wire:model.live="programaGoverno" 
                        class="w-full text-xs font-semibold py-2 px-2.5 rounded-lg border border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-emerald-500 focus:outline-hidden"
                    >
                        <option value="">Todos</option>
                        <option value="sim">Inscrito em Programa Social</option>
                        <option value="nao">Não inscrito</option>
                    </select>
                </div>

                {{-- Estudo Bíblico --}}
                <div class="space-y-1">
                    <label class="text-xs font-bold text-zinc-700 dark:text-zinc-300">Estudo Bíblico</label>
                    <select 
                        wire:model.live="estudoBiblico" 
                        class="w-full text-xs font-semibold py-2 px-2.5 rounded-lg border border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-emerald-500 focus:outline-hidden"
                    >
                        <option value="">Todos</option>
                        <option value="sim">Recebe Estudo Bíblico</option>
                        <option value="nao">Não recebe</option>
                    </select>
                </div>

                {{-- Documentação Anexada --}}
                <div class="space-y-1">
                    <label class="text-xs font-bold text-zinc-700 dark:text-zinc-300">Documentação</label>
                    <select 
                        wire:model.live="documentos" 
                        class="w-full text-xs font-semibold py-2 px-2.5 rounded-lg border border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-emerald-500 focus:outline-hidden"
                    >
                        <option value="">Todas as situações</option>
                        <option value="completos">Completos (4/4 anexados)</option>
                        <option value="incompletos">Incompletos (com pendências)</option>
                        <option value="sem_documentos">Sem nenhum documento (0/4)</option>
                    </select>
                </div>

                {{-- Ordenação --}}
                <div class="space-y-1 sm:col-span-2 lg:col-span-4">
                    <label class="text-xs font-bold text-zinc-700 dark:text-zinc-300">Ordenação dos Resultados</label>
                    <div class="flex flex-wrap gap-2">
                        <button 
                            type="button" 
                            wire:click="$set('ordenacao', 'nome_asc')"
                            class="px-3 py-1.5 rounded-lg text-xs font-semibold cursor-pointer {{ $ordenacao === 'nome_asc' ? 'bg-emerald-700 text-white font-bold' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300' }}"
                        >
                            Nome (A-Z)
                        </button>
                        <button 
                            type="button" 
                            wire:click="$set('ordenacao', 'nome_desc')"
                            class="px-3 py-1.5 rounded-lg text-xs font-semibold cursor-pointer {{ $ordenacao === 'nome_desc' ? 'bg-emerald-700 text-white font-bold' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300' }}"
                        >
                            Nome (Z-A)
                        </button>
                        <button 
                            type="button" 
                            wire:click="$set('ordenacao', 'recentes')"
                            class="px-3 py-1.5 rounded-lg text-xs font-semibold cursor-pointer {{ $ordenacao === 'recentes' ? 'bg-emerald-700 text-white font-bold' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300' }}"
                        >
                            Mais Recentes Primeiro
                        </button>
                        <button 
                            type="button" 
                            wire:click="$set('ordenacao', 'antigos')"
                            class="px-3 py-1.5 rounded-lg text-xs font-semibold cursor-pointer {{ $ordenacao === 'antigos' ? 'bg-emerald-700 text-white font-bold' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300' }}"
                        >
                            Mais Antigos Primeiro
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- DESKTOP VIEW --}}
    <div wire:loading.class="opacity-60 pointer-events-none transition-opacity" class="hidden md:block overflow-x-auto rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 shadow-sm">
        <table class="w-full text-sm">
            <thead class="bg-zinc-100 dark:bg-zinc-800 text-left border-b border-zinc-200 dark:border-zinc-700">
                <tr>
                    <th class="px-4 py-3.5 font-bold text-zinc-900 dark:text-zinc-100">Nome</th>
                    <th class="px-4 py-3.5 font-bold text-zinc-900 dark:text-zinc-100">Telefone</th>
                    <th class="px-4 py-3.5 font-bold text-zinc-900 dark:text-zinc-100">Bairro</th>
                    <th class="px-4 py-3.5 font-bold text-zinc-900 dark:text-zinc-100">Família</th>
                    <th class="px-4 py-3.5 font-bold text-zinc-900 dark:text-zinc-100">Prog. Governo</th>
                    <th class="px-4 py-3.5 font-bold text-zinc-900 dark:text-zinc-100">Est. Bíblico</th>
                    <th class="px-4 py-3.5 font-bold text-zinc-900 dark:text-zinc-100">Documentos</th>
                    <th class="px-4 py-3.5 font-bold text-zinc-900 dark:text-zinc-100 text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                @forelse ($beneficiarios as $b)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/60 transition-colors">
                        <td class="px-4 py-3.5 font-bold text-zinc-900 dark:text-zinc-100">{{ $b->nome }}</td>
                        <td class="px-4 py-3.5 font-semibold text-zinc-700 dark:text-zinc-300">{{ $b->telefone ?? '—' }}</td>
                        <td class="px-4 py-3.5 font-semibold text-zinc-700 dark:text-zinc-300">{{ $b->bairro ?? '—' }}</td>
                        <td class="px-4 py-3.5 font-bold text-zinc-900 dark:text-zinc-100">{{ $b->num_pessoas_familia }} pessoa(s)</td>
                        <td class="px-4 py-3.5">
                            @if ($b->inscrito_programa_governo)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold bg-emerald-100 text-emerald-900 dark:bg-emerald-950 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-800">Sim</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-semibold bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 border border-zinc-300 dark:border-zinc-700">Não</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5">
                            @if ($b->recebe_estudo_biblico)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold bg-blue-100 text-blue-900 dark:bg-blue-950 dark:text-blue-300 border border-blue-300 dark:border-blue-800">Sim</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-semibold bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 border border-zinc-300 dark:border-zinc-700">Não</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5">
                            @php
                                $docsCount = ($b->foto_documento ? 1 : 0) + ($b->foto_documento_verso ? 1 : 0) + ($b->foto_documento_consentimento ? 1 : 0) + ($b->foto_documento_comprovante_residencia ? 1 : 0);
                            @endphp
                            @if ($docsCount === 4)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-900 dark:bg-emerald-950 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-800">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span> Completo (4/4)
                                </span>
                            @elseif ($docsCount > 0)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-900 dark:bg-amber-950 dark:text-amber-300 border border-amber-300 dark:border-amber-800">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span> Parcial ({{ $docsCount }}/4)
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-900 dark:bg-rose-950 dark:text-rose-300 border border-rose-300 dark:border-rose-800">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span> Pendente (0/4)
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-right">
                            <div class="flex justify-end gap-2">
                                <flux:button wire:click="show({{ $b->id }})" x-on:click="Flux.modal('show-beneficiario').show()" size="sm" variant="ghost" icon="eye" class="text-zinc-700 dark:text-zinc-300 hover:text-zinc-900 dark:hover:text-white" />
                                <flux:button href="{{ route('beneficiarios.edit', $b) }}" size="sm" variant="ghost" icon="pencil" class="text-zinc-700 dark:text-zinc-300 hover:text-zinc-900 dark:hover:text-white" wire:navigate />
                                <flux:button wire:click="confirmDelete({{ $b->id }})" x-on:click="Flux.modal('confirm-delete').show()" size="sm" variant="ghost" icon="trash" class="text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40" />
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-zinc-500 font-medium">
                            Nenhum beneficiário encontrado.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- MOBILE VIEW (Cards Empilhados) --}}
    <div wire:loading.class="opacity-60 pointer-events-none transition-opacity" class="md:hidden space-y-4">
        @forelse ($beneficiarios as $b)
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 p-4 space-y-3 bg-white dark:bg-zinc-900 shadow-sm">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-3">
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-base text-zinc-900 dark:text-zinc-100 truncate">{{ $b->nome }}</p>
                        <p class="text-xs font-semibold text-zinc-600 dark:text-zinc-400 uppercase mt-0.5">{{ $b->bairro ?? 'Bairro não inf.' }} • {{ $b->telefone ?? 'Sem tel' }}</p>
                    </div>
                    <div class="flex gap-1.5 self-end sm:self-start">
                        <flux:button wire:click="show({{ $b->id }})" x-on:click="Flux.modal('show-beneficiario').show()" size="sm" variant="ghost" icon="eye" class="text-zinc-700 dark:text-zinc-300" />
                        <flux:button href="{{ route('beneficiarios.edit', $b) }}" size="sm" variant="ghost" icon="pencil" class="text-zinc-700 dark:text-zinc-300" wire:navigate />
                        <flux:button wire:click="confirmDelete({{ $b->id }})" x-on:click="Flux.modal('confirm-delete').show()" size="sm" variant="ghost" icon="trash" class="text-rose-600 dark:text-rose-400" />
                    </div>
                </div>
                <div class="flex flex-wrap gap-2 text-sm pt-2 border-t border-zinc-100 dark:border-zinc-800">
                    <div class="w-full flex justify-between">
                        <span class="text-zinc-600 dark:text-zinc-400 font-medium">Família:</span>
                        <span class="font-bold text-zinc-900 dark:text-zinc-100">{{ $b->num_pessoas_familia }} pessoa(s)</span>
                    </div>
                    <div class="w-full flex justify-between items-center">
                        <span class="text-zinc-600 dark:text-zinc-400 font-medium">Prog. Governo:</span>
                        @if ($b->inscrito_programa_governo)
                            <span class="px-2 py-0.5 rounded text-xs font-bold bg-emerald-100 text-emerald-900 dark:bg-emerald-950 dark:text-emerald-300">Sim</span>
                        @else
                            <span class="px-2 py-0.5 rounded text-xs font-semibold bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">Não</span>
                        @endif
                    </div>
                    <div class="w-full flex justify-between items-center">
                        <span class="text-zinc-600 dark:text-zinc-400 font-medium">Estudo Bíblico:</span>
                        @if ($b->recebe_estudo_biblico)
                            <span class="px-2 py-0.5 rounded text-xs font-bold bg-blue-100 text-blue-900 dark:bg-blue-950 dark:text-blue-300">Sim</span>
                        @else
                            <span class="px-2 py-0.5 rounded text-xs font-semibold bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">Não</span>
                        @endif
                    </div>
                    <div class="w-full flex justify-between items-center">
                        <span class="text-zinc-600 dark:text-zinc-400 font-medium">Documentos:</span>
                        @php
                            $docsCount = ($b->foto_documento ? 1 : 0) + ($b->foto_documento_verso ? 1 : 0) + ($b->foto_documento_consentimento ? 1 : 0) + ($b->foto_documento_comprovante_residencia ? 1 : 0);
                        @endphp
                        @if ($docsCount === 4)
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-900 dark:bg-emerald-950 dark:text-emerald-300">Completo (4/4)</span>
                        @elseif ($docsCount > 0)
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-900 dark:bg-amber-950 dark:text-amber-300">Parcial ({{ $docsCount }}/4)</span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-900 dark:bg-rose-950 dark:text-rose-300">Pendente (0/4)</span>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 p-8 text-center text-zinc-500 font-medium bg-white dark:bg-zinc-900">
                Nenhum beneficiário encontrado.
            </div>
        @endforelse
    </div>

    <div>{{ $beneficiarios->links() }}</div>

    {{-- Modal de Detalhes do Beneficiário --}}
    <flux:modal name="show-beneficiario" class="md:min-w-[700px]">
        <div class="space-y-6">
            {{-- Estado de carregamento --}}
            <div wire:loading wire:target="show" class="w-full">
                <div class="flex flex-col items-center justify-center py-20 gap-3">
                    <flux:icon.loading class="h-8 w-8 text-emerald-600" />
                    <flux:text class="text-zinc-600 dark:text-zinc-400 text-sm font-bold">Carregando dados do beneficiário...</flux:text>
                </div>
            </div>

            {{-- Conteúdo do Beneficiário --}}
            <div wire:loading.remove wire:target="show" class="w-full space-y-6">
                @if ($selectedBeneficiario)
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-zinc-200 dark:border-zinc-800">
                        <div>
                            <flux:heading size="lg" class="break-words font-bold text-zinc-900 dark:text-zinc-100">{{ $selectedBeneficiario->nome }}</flux:heading>
                            <flux:text size="sm" class="block mt-1 font-semibold text-zinc-700 dark:text-zinc-300">{{ $selectedBeneficiario->cpf ?? 'Sem CPF' }} | {{ $selectedBeneficiario->rg ?? 'Sem RG' }}</flux:text>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 rounded-md text-xs font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 border border-zinc-300 dark:border-zinc-700 self-start sm:self-center">
                            Código: #{{ $selectedBeneficiario->id }}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        {{-- Endereço --}}
                        <div>
                            <span class="text-xs font-bold text-zinc-500 uppercase block mb-1">Endereço</span>
                            <span class="block font-bold text-zinc-900 dark:text-zinc-100 text-sm">{{ $selectedBeneficiario->rua }}, {{ $selectedBeneficiario->numero }}</span>
                            <span class="block text-xs font-semibold text-zinc-600 dark:text-zinc-400 mt-0.5">{{ $selectedBeneficiario->bairro }} - {{ $selectedBeneficiario->cidade }}</span>
                        </div>
                        {{-- Contato --}}
                        <div>
                            <span class="text-xs font-bold text-zinc-500 uppercase block mb-1">Contato</span>
                            <span class="block font-bold text-zinc-900 dark:text-zinc-100 text-sm">{{ $selectedBeneficiario->telefone ?? 'Não informado' }}</span>
                        </div>
                        {{-- Composição Familiar --}}
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <span class="text-xs font-bold text-zinc-500 uppercase block mb-1">Família</span>
                                <span class="block font-bold text-zinc-900 dark:text-zinc-100 text-sm">{{ $selectedBeneficiario->num_pessoas_familia }} pessoas</span>
                            </div>
                            <div>
                                <span class="text-xs font-bold text-zinc-500 uppercase block mb-1">Filhos</span>
                                <span class="block font-bold text-zinc-900 dark:text-zinc-100 text-sm">{{ count($selectedBeneficiario->filhos ?? []) }} filho(s)</span>
                            </div>
                        </div>
                    </div>

                    {{-- Seção de Documentos --}}
                    @if ($selectedBeneficiario->foto_documento || $selectedBeneficiario->foto_documento_verso || $selectedBeneficiario->foto_documento_consentimento || $selectedBeneficiario->foto_documento_comprovante_residencia)
                        <div class="border-t border-zinc-200 dark:border-zinc-800 pt-5">
                            <span class="text-xs font-bold text-zinc-500 uppercase mb-3 block">Documentos Cadastrados</span>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                                @if ($selectedBeneficiario->foto_documento)
                                    <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 p-2.5 bg-zinc-50 dark:bg-zinc-800/40">
                                        <span class="text-xs font-bold mb-1.5 block text-zinc-800 dark:text-zinc-200">Frente do Documento</span>
                                        <a href="{{ asset('storage/' . $selectedBeneficiario->foto_documento) }}" target="_blank" class="block group">
                                            <img src="{{ asset('storage/' . $selectedBeneficiario->foto_documento) }}" loading="lazy" decoding="async" class="h-32 w-full object-cover rounded-lg border border-zinc-300 dark:border-zinc-700 shadow-sm group-hover:opacity-90 transition-opacity">
                                            <span class="text-xs font-bold text-emerald-700 dark:text-emerald-400 mt-1.5 block text-center">Clique para ampliar</span>
                                        </a>
                                    </div>
                                @endif

                                @if ($selectedBeneficiario->foto_documento_verso)
                                    <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 p-2.5 bg-zinc-50 dark:bg-zinc-800/40">
                                        <span class="text-xs font-bold mb-1.5 block text-zinc-800 dark:text-zinc-200">Verso do Documento</span>
                                        <a href="{{ asset('storage/' . $selectedBeneficiario->foto_documento_verso) }}" target="_blank" class="block group">
                                            <img src="{{ asset('storage/' . $selectedBeneficiario->foto_documento_verso) }}" loading="lazy" decoding="async" class="h-32 w-full object-cover rounded-lg border border-zinc-300 dark:border-zinc-700 shadow-sm group-hover:opacity-90 transition-opacity">
                                            <span class="text-xs font-bold text-emerald-700 dark:text-emerald-400 mt-1.5 block text-center">Clique para ampliar</span>
                                        </a>
                                    </div>
                                @endif

                                @if ($selectedBeneficiario->foto_documento_consentimento)
                                    <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 p-2.5 bg-zinc-50 dark:bg-zinc-800/40">
                                        <span class="text-xs font-bold mb-1.5 block text-zinc-800 dark:text-zinc-200">Termo de Consentimento</span>
                                        <a href="{{ asset('storage/' . $selectedBeneficiario->foto_documento_consentimento) }}" target="_blank" class="block group">
                                            <img src="{{ asset('storage/' . $selectedBeneficiario->foto_documento_consentimento) }}" loading="lazy" decoding="async" class="h-32 w-full object-cover rounded-lg border border-zinc-300 dark:border-zinc-700 shadow-sm group-hover:opacity-90 transition-opacity">
                                            <span class="text-xs font-bold text-emerald-700 dark:text-emerald-400 mt-1.5 block text-center">Clique para ampliar</span>
                                        </a>
                                    </div>
                                @endif

                                @if ($selectedBeneficiario->foto_documento_comprovante_residencia)
                                    <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 p-2.5 bg-zinc-50 dark:bg-zinc-800/40">
                                        <span class="text-xs font-bold mb-1.5 block text-zinc-800 dark:text-zinc-200">Comprovante de Endereço</span>
                                        <a href="{{ asset('storage/' . $selectedBeneficiario->foto_documento_comprovante_residencia) }}" target="_blank" class="block group">
                                            <img src="{{ asset('storage/' . $selectedBeneficiario->foto_documento_comprovante_residencia) }}" loading="lazy" decoding="async" class="h-32 w-full object-cover rounded-lg border border-zinc-300 dark:border-zinc-700 shadow-sm group-hover:opacity-90 transition-opacity">
                                            <span class="text-xs font-bold text-emerald-700 dark:text-emerald-400 mt-1.5 block text-center">Clique para ampliar</span>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <div class="border-t border-zinc-200 dark:border-zinc-800 pt-5">
                        <div class="flex items-center justify-between mb-4">
                            <flux:heading size="md" class="font-bold text-zinc-900 dark:text-zinc-100">Histórico de Retiradas de Doações</flux:heading>
                            <span class="px-3 py-1 rounded-md text-xs font-bold bg-blue-100 text-blue-900 dark:bg-blue-950 dark:text-blue-300">
                                {{ $selectedBeneficiario->retiradas->count() }} retirada(s)
                            </span>
                        </div>
                        
                        <div class="max-h-[300px] overflow-y-auto space-y-3 pr-2 custom-scrollbar">
                            @forelse ($selectedBeneficiario->retiradas as $retirada)
                                <div class="p-3.5 bg-zinc-50 dark:bg-zinc-800/60 rounded-xl border border-zinc-200 dark:border-zinc-700">
                                    <div class="flex justify-between items-center mb-2">
                                        <div class="flex items-center gap-2">
                                            <flux:icon icon="calendar" variant="micro" class="text-emerald-600" />
                                            <span class="font-bold text-sm text-zinc-900 dark:text-zinc-100">{{ $retirada->data->format('d/m/Y') }}</span>
                                        </div>
                                        <span class="text-xs font-bold text-zinc-700 dark:text-zinc-300 bg-zinc-200 dark:bg-zinc-700 px-2 py-0.5 rounded">{{ $retirada->items->count() }} item(ns)</span>
                                    </div>
                                    <div class="text-xs font-semibold text-zinc-700 dark:text-zinc-300 leading-relaxed">
                                        {{ $retirada->items->map(fn($i) => $i->produto->nome . " (" . $i->quantidade . ")")->join(', ') }}
                                    </div>
                                    @if ($retirada->observacoes)
                                        <div class="mt-2 flex items-start gap-1">
                                            <flux:icon icon="chat-bubble-bottom-center-text" variant="micro" class="text-zinc-400 mt-0.5" />
                                            <div class="text-xs italic text-zinc-600 dark:text-zinc-400">{{ $retirada->observacoes }}</div>
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <div class="text-center py-10">
                                    <flux:icon icon="archive-box" class="mx-auto text-zinc-400 mb-2" />
                                    <flux:text class="text-zinc-500 text-sm font-medium">Nenhuma retirada registrada para esta família ainda.</flux:text>
                                </div>
                            @endforelse
                        </div>
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center py-20 gap-3">
                        <flux:icon.loading class="h-8 w-8 text-emerald-600" />
                        <flux:text class="text-zinc-500 text-sm font-bold">Carregando dados...</flux:text>
                    </div>
                @endif
            </div>

            <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4 border-t border-zinc-200 dark:border-zinc-800">
                <flux:button x-on:click="Flux.modal('show-beneficiario').close()" variant="ghost" class="w-full sm:w-auto font-bold">Fechar</flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- Modal de confirmação de exclusão --}}
    <flux:modal name="confirm-delete">
        <div class="space-y-4">
            <flux:heading class="font-bold text-zinc-900 dark:text-zinc-100">Confirmar exclusão</flux:heading>
            <flux:text class="text-zinc-700 dark:text-zinc-300">Tem certeza que deseja remover este beneficiário? Esta ação não pode ser desfeita.</flux:text>
            <div class="flex justify-end gap-2 pt-2">
                <flux:button wire:click="$set('deletingId', null)" x-on:click="Flux.modal('confirm-delete').close()" variant="ghost">Cancelar</flux:button>
                <flux:button wire:click="delete" x-on:click="Flux.modal('confirm-delete').close()" variant="danger" class="font-bold">Excluir Beneficiário</flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- FAB MOBILE (NOVO BENEFICIÁRIO) --}}
    <div class="md:hidden fixed bottom-20 right-4 z-30">
        <a 
            id="tour-mobile-fab-beneficiario"
            href="{{ route('beneficiarios.create') }}" 
            wire:navigate
            class="flex items-center justify-center w-14 h-14 rounded-full bg-emerald-700 active:bg-emerald-800 text-white shadow-2xl hover:scale-105 active:scale-95 transition-all duration-150 border-2 border-white/40"
            title="Novo Beneficiário"
        >
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
            </svg>
        </a>
    </div>
</div>
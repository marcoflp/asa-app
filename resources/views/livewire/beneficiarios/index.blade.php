<div class="flex h-full w-full flex-1 flex-col gap-4">

        @if (session('success'))
            <flux:callout variant="success" icon="check-circle">
                {{ session('success') }}
            </flux:callout>
        @endif

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <flux:heading size="xl">Beneficiários</flux:heading>
            <flux:button href="{{ route('beneficiarios.create') }}" variant="primary" icon="plus" wire:navigate class="w-full sm:w-auto">
                Novo Beneficiário
            </flux:button>
        </div>

        <div class="w-full sm:max-w-md">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="Buscar por nome, CPF ou bairro..." icon="magnifying-glass" class="w-full" />
        </div>

        {{-- DESKTOP VIEW (Mantém o layout original 100% intacto) --}}
        <div class="hidden md:block overflow-x-auto rounded-xl border border-neutral-200 dark:border-neutral-700">
            <table class="w-full text-sm">
                <thead class="bg-neutral-50 dark:bg-zinc-800 text-left">
                    <tr>
                        <th class="px-4 py-3 font-medium">Nome</th>
                        <th class="px-4 py-3 font-medium">Telefone</th>
                        <th class="px-4 py-3 font-medium">Bairro</th>
                        <th class="px-4 py-3 font-medium">Família</th>
                        <th class="px-4 py-3 font-medium">Prog. Governo</th>
                        <th class="px-4 py-3 font-medium">Est. Bíblico</th>
                        <th class="px-4 py-3 font-medium text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                    @forelse ($beneficiarios as $b)
                        <tr class="hover:bg-neutral-50 dark:hover:bg-zinc-800/50">
                            <td class="px-4 py-3 font-medium">{{ $b->nome }}</td>
                            <td class="px-4 py-3 text-neutral-600 dark:text-neutral-400">{{ $b->telefone ?? '—' }}</td>
                            <td class="px-4 py-3 text-neutral-600 dark:text-neutral-400">{{ $b->bairro ?? '—' }}</td>
                            <td class="px-4 py-3">{{ $b->num_pessoas_familia }} pessoa(s)</td>
                            <td class="px-4 py-3">
                                @if ($b->inscrito_programa_governo)
                                    <flux:badge color="green" size="sm">Sim</flux:badge>
                                @else
                                    <flux:badge color="zinc" size="sm">Não</flux:badge>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if ($b->recebe_estudo_biblico)
                                    <flux:badge color="blue" size="sm">Sim</flux:badge>
                                @else
                                    <flux:badge color="zinc" size="sm">Não</flux:badge>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex justify-end gap-2">
                                    <flux:button wire:click="show({{ $b->id }})" x-on:click="Flux.modal('show-beneficiario').show()" size="sm" variant="ghost" icon="eye" />
                                    <flux:button href="{{ route('beneficiarios.edit', $b) }}" size="sm" variant="ghost" icon="pencil" wire:navigate />
                                    <flux:button wire:click="confirmDelete({{ $b->id }})" x-on:click="Flux.modal('confirm-delete').show()" size="sm" variant="ghost" icon="trash" class="text-red-500" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-neutral-500">
                                Nenhum beneficiário encontrado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- MOBILE VIEW (Cards Empilhados) --}}
        <div class="md:hidden space-y-4">
            @forelse ($beneficiarios as $b)
                <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 p-4 space-y-3 bg-white dark:bg-zinc-900 shadow-sm">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="font-bold text-base">{{ $b->nome }}</p>
                            <p class="text-xs text-neutral-500 uppercase">{{ $b->bairro ?? 'Bairro não inf.' }} - {{ $b->telefone ?? 'Sem tel' }}</p>
                        </div>
                        <div class="flex gap-2">
                            <flux:button wire:click="show({{ $b->id }})" x-on:click="Flux.modal('show-beneficiario').show()" size="sm" variant="ghost" icon="eye" />
                            <flux:button href="{{ route('beneficiarios.edit', $b) }}" size="sm" variant="ghost" icon="pencil" wire:navigate />
                            <flux:button wire:click="confirmDelete({{ $b->id }})" x-on:click="Flux.modal('confirm-delete').show()" size="sm" variant="ghost" icon="trash" class="text-red-500" />
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2 text-sm pt-2 border-t border-neutral-100 dark:border-neutral-800">
                        <div class="w-full flex justify-between">
                            <span class="text-neutral-500">Família:</span>
                            <span>{{ $b->num_pessoas_familia }} pessoa(s)</span>
                        </div>
                        <div class="w-full flex justify-between items-center">
                            <span class="text-neutral-500">Prog. Governo:</span>
                            @if ($b->inscrito_programa_governo)
                                <flux:badge color="green" size="sm">Sim</flux:badge>
                            @else
                                <flux:badge color="zinc" size="sm">Não</flux:badge>
                            @endif
                        </div>
                        <div class="w-full flex justify-between items-center">
                            <span class="text-neutral-500">Estudo Bíblico:</span>
                            @if ($b->recebe_estudo_biblico)
                                <flux:badge color="blue" size="sm">Sim</flux:badge>
                            @else
                                <flux:badge color="zinc" size="sm">Não</flux:badge>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 p-8 text-center text-neutral-500">
                    Nenhum beneficiário encontrado.
                </div>
            @endforelse
        </div>

        <div>{{ $beneficiarios->links() }}</div>

        {{-- Modal de Detalhes do Beneficiário --}}
        <flux:modal name="show-beneficiario" class="md:min-w-[700px]">
            <div class="space-y-6">
                @if ($selectedBeneficiario)
                    <div class="flex justify-between items-start">
                        <div>
                            <flux:heading size="lg">{{ $selectedBeneficiario->nome }}</flux:heading>
                            <flux:text size="sm">{{ $selectedBeneficiario->cpf ?? 'Sem CPF' }} | {{ $selectedBeneficiario->rg ?? 'Sem RG' }}</flux:text>
                        </div>
                        <flux:badge color="zinc" size="sm">ID: #{{ $selectedBeneficiario->id }}</flux:badge>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div>
                                <flux:label class="text-xs text-neutral-500 uppercase">Endereço</flux:label>
                                <flux:text class="block font-medium">{{ $selectedBeneficiario->rua }}, {{ $selectedBeneficiario->numero }}</flux:text>
                                <flux:text class="block text-sm text-neutral-500">{{ $selectedBeneficiario->bairro }} - {{ $selectedBeneficiario->cidade }}</flux:text>
                            </div>
                            <div>
                                <flux:label class="text-xs text-neutral-500 uppercase">Contato</flux:label>
                                <flux:text class="block font-medium">{{ $selectedBeneficiario->telefone ?? 'Não informado' }}</flux:text>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <flux:label class="text-xs text-neutral-500 uppercase">Família</flux:label>
                                    <flux:text class="block">{{ $selectedBeneficiario->num_pessoas_familia }} pessoas</flux:text>
                                </div>
                                <div>
                                    <flux:label class="text-xs text-neutral-500 uppercase">Filhos</flux:label>
                                    <flux:text class="block">{{ count($selectedBeneficiario->filhos ?? []) }} filho(s)</flux:text>
                                </div>
                            </div>
                        </div>

                        @if ($selectedBeneficiario->foto_documento)
                            <div>
                                <flux:label class="text-xs text-neutral-500 uppercase mb-1 block">Documento Identificado</flux:label>
                                <a href="{{ asset('storage/' . $selectedBeneficiario->foto_documento) }}" target="_blank" class="block group">
                                    <img src="{{ asset('storage/' . $selectedBeneficiario->foto_documento) }}" class="h-40 w-full object-cover rounded-lg border border-neutral-200 shadow-sm group-hover:opacity-90 transition-opacity">
                                    <span class="text-[10px] text-neutral-400 mt-1 block text-center">Clique para ampliar</span>
                                </a>
                            </div>
                        @endif
                    </div>

                    <div class="border-t border-neutral-100 dark:border-neutral-800 pt-5">
                        <div class="flex items-center justify-between mb-4">
                            <flux:heading size="md">Histórico de Retiradas</flux:heading>
                            <flux:badge color="blue">{{ $selectedBeneficiario->retiradas->count() }} registradas</flux:badge>
                        </div>
                        
                        <div class="max-h-[300px] overflow-y-auto space-y-3 pr-2 custom-scrollbar">
                            @forelse ($selectedBeneficiario->retiradas as $retirada)
                                <div class="p-3 bg-neutral-50 dark:bg-zinc-800/50 rounded-lg border border-neutral-100 dark:border-neutral-800 transition-colors hover:border-blue-200">
                                    <div class="flex justify-between items-center mb-2">
                                        <div class="flex items-center gap-2">
                                            <flux:icon icon="calendar" variant="micro" class="text-neutral-400" />
                                            <span class="font-bold text-sm text-neutral-700 dark:text-neutral-200">{{ $retirada->data->format('d/m/Y') }}</span>
                                        </div>
                                        <flux:badge size="sm" variant="ghost">{{ $retirada->items->count() }} itens</flux:badge>
                                    </div>
                                    <div class="text-xs text-neutral-600 dark:text-neutral-400 leading-relaxed">
                                        {{ $retirada->items->map(fn($i) => $i->produto->nome . " (" . $i->quantidade . ")")->join(', ') }}
                                    </div>
                                    @if ($retirada->observacoes)
                                        <div class="mt-2 flex items-start gap-1">
                                            <flux:icon icon="chat-bubble-bottom-center-text" variant="micro" class="text-neutral-300 mt-0.5" />
                                            <div class="text-[10px] italic text-neutral-500">{{ $retirada->observacoes }}</div>
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <div class="text-center py-10">
                                    <flux:icon icon="archive-box" class="mx-auto text-neutral-200 mb-2" />
                                    <flux:text class="text-neutral-400 text-sm">Nenhuma retirada registrada.</flux:text>
                                </div>
                            @endforelse
                        </div>
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center py-20">
                        <flux:spacer />
                        <flux:text>Carregando dados...</flux:text>
                    </div>
                @endif

                <div class="flex justify-end gap-3 pt-4 border-t border-neutral-100 dark:border-neutral-800">
                    <flux:button x-on:click="Flux.modal('show-beneficiario').close()" variant="ghost">Fechar</flux:button>
                </div>
            </div>
        </flux:modal>

        {{-- Modal de confirmação de exclusão --}}
        <flux:modal name="confirm-delete">
            <div class="space-y-4">
                <flux:heading>Confirmar exclusão</flux:heading>
                <flux:text>Tem certeza que deseja remover este beneficiário? Esta ação não pode ser desfeita.</flux:text>
                <div class="flex justify-end gap-2">
                    <flux:button wire:click="$set('deletingId', null)" x-on:click="Flux.modal('confirm-delete').close()" variant="ghost">Cancelar</flux:button>
                    <flux:button wire:click="delete" x-on:click="Flux.modal('confirm-delete').close()" variant="danger">Excluir</flux:button>
                </div>
            </div>
        </flux:modal>

    </div>

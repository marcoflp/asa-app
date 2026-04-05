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

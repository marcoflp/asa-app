<div class="flex h-full w-full flex-1 flex-col gap-4">

        @if (session('success'))
            <flux:callout variant="success" icon="check-circle">
                {{ session('success') }}
            </flux:callout>
        @endif

        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <flux:heading size="xl">Beneficiários</flux:heading>
            <flux:button href="{{ route('beneficiarios.create') }}" variant="primary" icon="plus" wire:navigate class="w-full sm:w-auto">
                Novo Beneficiário
            </flux:button>
        </div>

        <flux:input wire:model.live.debounce.300ms="search" placeholder="Buscar por nome, CPF ou bairro..." icon="magnifying-glass" class="w-full" />

        <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 overflow-hidden">
            <table class="w-full text-sm block md:table">
                <thead class="bg-neutral-50 dark:bg-zinc-800 text-left hidden md:table-header-group">
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
                <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700 block md:table-row-group">
                    @forelse ($beneficiarios as $b)
                        <tr class="hover:bg-neutral-50 dark:hover:bg-zinc-800/50 block md:table-row p-4 md:p-0">
                            <td class="px-0 py-2 md:px-4 md:py-3 font-medium flex justify-between items-center md:table-cell">
                                <span class="md:hidden text-neutral-500 font-medium text-xs uppercase">Nome</span>
                                <span>{{ $b->nome }}</span>
                            </td>
                            <td class="px-0 py-2 md:px-4 md:py-3 text-neutral-600 dark:text-neutral-400 flex justify-between items-center md:table-cell">
                                <span class="md:hidden text-neutral-500 font-medium text-xs uppercase">Telefone</span>
                                <span>{{ $b->telefone ?? '—' }}</span>
                            </td>
                            <td class="px-0 py-2 md:px-4 md:py-3 text-neutral-600 dark:text-neutral-400 flex justify-between items-center md:table-cell">
                                <span class="md:hidden text-neutral-500 font-medium text-xs uppercase">Bairro</span>
                                <span>{{ $b->bairro ?? '—' }}</span>
                            </td>
                            <td class="px-0 py-2 md:px-4 md:py-3 flex justify-between items-center md:table-cell">
                                <span class="md:hidden text-neutral-500 font-medium text-xs uppercase">Família</span>
                                <span>{{ $b->num_pessoas_familia }} pessoa(s)</span>
                            </td>
                            <td class="px-0 py-2 md:px-4 md:py-3 flex justify-between items-center md:table-cell">
                                <span class="md:hidden text-neutral-500 font-medium text-xs uppercase">Prog. Governo</span>
                                <span>
                                    @if ($b->inscrito_programa_governo)
                                        <flux:badge color="green" size="sm">Sim</flux:badge>
                                    @else
                                        <flux:badge color="zinc" size="sm">Não</flux:badge>
                                    @endif
                                </span>
                            </td>
                            <td class="px-0 py-2 md:px-4 md:py-3 flex justify-between items-center md:table-cell">
                                <span class="md:hidden text-neutral-500 font-medium text-xs uppercase">Est. Bíblico</span>
                                <span>
                                    @if ($b->recebe_estudo_biblico)
                                        <flux:badge color="blue" size="sm">Sim</flux:badge>
                                    @else
                                        <flux:badge color="zinc" size="sm">Não</flux:badge>
                                    @endif
                                </span>
                            </td>
                            <td class="px-0 py-3 md:px-4 md:py-3 text-right flex justify-between md:justify-end items-center md:table-cell mt-2 md:mt-0 border-t border-neutral-100 dark:border-neutral-700/50 md:border-0">
                                <span class="md:hidden text-neutral-500 font-medium text-xs uppercase">Ações</span>
                                <div class="flex justify-end gap-2">
                                    <flux:button href="{{ route('beneficiarios.edit', $b) }}" size="sm" variant="ghost" icon="pencil" wire:navigate />
                                    <flux:button wire:click="confirmDelete({{ $b->id }})" x-on:click="Flux.modal('confirm-delete').show()" size="sm" variant="ghost" icon="trash" class="text-red-500" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr class="block md:table-row">
                            <td colspan="7" class="px-4 py-8 text-center text-neutral-500 block md:table-cell">
                                Nenhum beneficiário encontrado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
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

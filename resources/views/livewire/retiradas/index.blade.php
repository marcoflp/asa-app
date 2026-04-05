<div class="flex h-full w-full flex-1 flex-col gap-4">

    @if (session('success'))
        <flux:callout variant="success" icon="check-circle">{{ session('success') }}</flux:callout>
    @endif

    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <flux:heading size="xl">Retiradas</flux:heading>
        <flux:button href="{{ route('retiradas.create') }}" variant="primary" icon="plus" wire:navigate class="w-full sm:w-auto">
            Nova Retirada
        </flux:button>
    </div>

    <div class="flex flex-col sm:flex-row gap-3">
        <flux:input wire:model.live.debounce.300ms="search" placeholder="Buscar beneficiário..." icon="magnifying-glass" class="w-full sm:flex-1" />
        <div class="flex gap-3 w-full sm:w-auto">
            <flux:input type="date" wire:model.live="dataInicio" class="flex-1 sm:w-40" />
            <flux:input type="date" wire:model.live="dataFim" class="flex-1 sm:w-40" />
        </div>
    </div>

    <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 overflow-hidden">
        <table class="w-full text-sm block md:table">
            <thead class="bg-neutral-50 dark:bg-zinc-800 text-left hidden md:table-header-group">
                <tr>
                    <th class="px-4 py-3 font-medium">Data</th>
                    <th class="px-4 py-3 font-medium">Beneficiário</th>
                    <th class="px-4 py-3 font-medium">Itens</th>
                    <th class="px-4 py-3 font-medium">Observações</th>
                    <th class="px-4 py-3 font-medium text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700 block md:table-row-group">
                @forelse ($retiradas as $r)
                    <tr class="hover:bg-neutral-50 dark:hover:bg-zinc-800/50 block md:table-row p-4 md:p-0">
                        <td class="px-0 py-2 md:px-4 md:py-3 whitespace-nowrap flex justify-between items-center md:table-cell">
                            <span class="md:hidden text-neutral-500 font-medium text-xs uppercase">Data</span>
                            <span>{{ $r->data->format('d/m/Y') }}</span>
                        </td>
                        <td class="px-0 py-2 md:px-4 md:py-3 font-medium flex justify-between items-center md:table-cell">
                            <span class="md:hidden text-neutral-500 font-medium text-xs uppercase">Beneficiário</span>
                            <span>{{ $r->beneficiario->nome }}</span>
                        </td>
                        <td class="px-0 py-2 md:px-4 md:py-3 flex flex-col md:table-cell">
                            <span class="md:hidden text-neutral-500 font-medium text-xs uppercase mb-1">Itens</span>
                            <div class="flex flex-wrap gap-1 justify-end md:justify-start">
                                @foreach ($r->items as $item)
                                    <flux:badge size="sm" color="zinc">
                                        {{ $item->produto->nome }} &times; {{ $item->quantidade }} {{ $item->produto->unidade }}
                                    </flux:badge>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-0 py-2 md:px-4 md:py-3 text-neutral-500 max-w-none md:max-w-xs truncate flex flex-col md:table-cell">
                            <span class="md:hidden text-neutral-500 font-medium text-xs uppercase mb-1">Observações</span>
                            <span class="text-right md:text-left">{{ $r->observacoes ?? '—' }}</span>
                        </td>
                        <td class="px-0 py-3 md:px-4 md:py-3 text-right flex justify-between md:justify-end items-center md:table-cell mt-2 md:mt-0 border-t border-neutral-100 dark:border-neutral-700/50 md:border-0">
                            <span class="md:hidden text-neutral-500 font-medium text-xs uppercase">Ações</span>
                            <div class="flex justify-end gap-2">
                                <flux:button href="{{ route('retiradas.edit', $r) }}" size="sm" variant="ghost" icon="pencil" wire:navigate />
                                <flux:button wire:click="confirmDelete({{ $r->id }})" x-on:click="Flux.modal('confirm-delete').show()" size="sm" variant="ghost" icon="trash" class="text-red-500" />
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr class="block md:table-row">
                        <td colspan="5" class="px-4 py-8 text-center text-neutral-500 block md:table-cell">Nenhuma retirada encontrada.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $retiradas->links() }}</div>

    <flux:modal name="confirm-delete">
        <div class="space-y-4">
            <flux:heading>Confirmar exclusão</flux:heading>
            <flux:text>Tem certeza que deseja remover esta retirada?</flux:text>
            <div class="flex justify-end gap-2">
                <flux:button wire:click="$set('deletingId', null)" x-on:click="Flux.modal('confirm-delete').close()" variant="ghost">Cancelar</flux:button>
                <flux:button wire:click="delete" x-on:click="Flux.modal('confirm-delete').close()" variant="danger">Excluir</flux:button>
            </div>
        </div>
    </flux:modal>

</div>

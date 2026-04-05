<div class="flex h-full w-full flex-1 flex-col gap-4">

    @if (session('success'))
        <flux:callout variant="success" icon="check-circle">{{ session('success') }}</flux:callout>
    @endif

    <div class="flex items-center justify-between">
        <flux:heading size="xl">Retiradas</flux:heading>
        <flux:button href="{{ route('retiradas.create') }}" variant="primary" icon="plus" wire:navigate>
            Nova Retirada
        </flux:button>
    </div>

    <div class="flex flex-wrap gap-3">
        <flux:input wire:model.live.debounce.300ms="search" placeholder="Buscar beneficiário..." icon="magnifying-glass" class="flex-1 min-w-48" />
        <flux:input type="date" wire:model.live="dataInicio" class="w-40" />
        <flux:input type="date" wire:model.live="dataFim" class="w-40" />
    </div>

    <div class="overflow-x-auto rounded-xl border border-neutral-200 dark:border-neutral-700">
        <table class="w-full text-sm">
            <thead class="bg-neutral-50 dark:bg-zinc-800 text-left">
                <tr>
                    <th class="px-4 py-3 font-medium">Data</th>
                    <th class="px-4 py-3 font-medium">Beneficiário</th>
                    <th class="px-4 py-3 font-medium">Itens</th>
                    <th class="px-4 py-3 font-medium">Observações</th>
                    <th class="px-4 py-3 font-medium text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                @forelse ($retiradas as $r)
                    <tr class="hover:bg-neutral-50 dark:hover:bg-zinc-800/50">
                        <td class="px-4 py-3 whitespace-nowrap">{{ $r->data->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 font-medium">{{ $r->beneficiario->nome }}</td>
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap gap-1">
                                @foreach ($r->items as $item)
                                    <flux:badge size="sm" color="zinc">
                                        {{ $item->produto->nome }} &times; {{ $item->quantidade }} {{ $item->produto->unidade }}
                                    </flux:badge>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-4 py-3 text-neutral-500 max-w-xs truncate">{{ $r->observacoes ?? '—' }}</td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex justify-end gap-2">
                                <flux:button href="{{ route('retiradas.edit', $r) }}" size="sm" variant="ghost" icon="pencil" wire:navigate />
                                <flux:button wire:click="confirmDelete({{ $r->id }})" x-on:click="Flux.modal('confirm-delete').show()" size="sm" variant="ghost" icon="trash" class="text-red-500" />
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-neutral-500">Nenhuma retirada encontrada.</td>
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

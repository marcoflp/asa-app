<div class="flex h-full w-full flex-1 flex-col gap-4">

    @if (session('success'))
        <flux:callout variant="success" icon="check-circle">{{ session('success') }}</flux:callout>
    @endif

    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <flux:heading size="xl">Produtos</flux:heading>
        <flux:button href="{{ route('produtos.create') }}" variant="primary" icon="plus" wire:navigate class="w-full sm:w-auto">
            Novo Produto
        </flux:button>
    </div>

    <div class="flex flex-col sm:flex-row gap-3">
        <flux:input wire:model.live.debounce.300ms="search" placeholder="Buscar produto..." icon="magnifying-glass" class="w-full sm:flex-1" />
        <flux:select wire:model.live="categoria" class="w-full sm:w-48">
            <flux:select.option value="">Todas as categorias</flux:select.option>
            @foreach ($categorias as $cat)
                <flux:select.option value="{{ $cat }}">{{ $cat }}</flux:select.option>
            @endforeach
        </flux:select>
    </div>

    <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 overflow-hidden">
        <table class="w-full text-sm block md:table">
            <thead class="bg-neutral-50 dark:bg-zinc-800 text-left hidden md:table-header-group">
                <tr>
                    <th class="px-4 py-3 font-medium">Nome</th>
                    <th class="px-4 py-3 font-medium">Categoria</th>
                    <th class="px-4 py-3 font-medium">Unidade</th>
                    <th class="px-4 py-3 font-medium">Descrição</th>
                    <th class="px-4 py-3 font-medium">Status</th>
                    <th class="px-4 py-3 font-medium text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700 block md:table-row-group">
                @forelse ($produtos as $p)
                    <tr class="hover:bg-neutral-50 dark:hover:bg-zinc-800/50 block md:table-row p-4 md:p-0 {{ !$p->ativo ? 'opacity-50' : '' }}">
                        <td class="px-0 py-2 md:px-4 md:py-3 font-medium flex justify-between items-center md:table-cell">
                            <span class="md:hidden text-neutral-500 font-medium text-xs uppercase">Nome</span>
                            <span>{{ $p->nome }}</span>
                        </td>
                        <td class="px-0 py-2 md:px-4 md:py-3 flex justify-between items-center md:table-cell">
                            <span class="md:hidden text-neutral-500 font-medium text-xs uppercase">Categoria</span>
                            <flux:badge color="zinc" size="sm">{{ $p->categoria }}</flux:badge>
                        </td>
                        <td class="px-0 py-2 md:px-4 md:py-3 text-neutral-500 flex justify-between items-center md:table-cell">
                            <span class="md:hidden text-neutral-500 font-medium text-xs uppercase">Unidade</span>
                            <span>{{ $p->unidade }}</span>
                        </td>
                        <td class="px-0 py-2 md:px-4 md:py-3 text-neutral-500 max-w-none md:max-w-xs truncate flex flex-col md:flex-row md:items-center md:table-cell">
                            <span class="md:hidden text-neutral-500 font-medium text-xs uppercase mb-1">Descrição</span>
                            <span class="text-right md:text-left">{{ $p->descricao ?? '—' }}</span>
                        </td>
                        <td class="px-0 py-2 md:px-4 md:py-3 flex justify-between items-center md:table-cell">
                            <span class="md:hidden text-neutral-500 font-medium text-xs uppercase">Status</span>
                            <button wire:click="toggleAtivo({{ $p->id }})" class="cursor-pointer">
                                @if ($p->ativo)
                                    <flux:badge color="green" size="sm">Ativo</flux:badge>
                                @else
                                    <flux:badge color="red" size="sm">Inativo</flux:badge>
                                @endif
                            </button>
                        </td>
                        <td class="px-0 py-3 md:px-4 md:py-3 text-right flex justify-between md:justify-end items-center md:table-cell mt-2 md:mt-0 border-t border-neutral-100 dark:border-neutral-700/50 md:border-0">
                            <span class="md:hidden text-neutral-500 font-medium text-xs uppercase">Ações</span>
                            <div class="flex justify-end gap-2">
                                <flux:button href="{{ route('produtos.edit', $p) }}" size="sm" variant="ghost" icon="pencil" wire:navigate />
                                <flux:button wire:click="confirmDelete({{ $p->id }})" x-on:click="Flux.modal('confirm-delete').show()" size="sm" variant="ghost" icon="trash" class="text-red-500" />
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr class="block md:table-row">
                        <td colspan="6" class="px-4 py-8 text-center text-neutral-500 block md:table-cell">Nenhum produto encontrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $produtos->links() }}</div>

    <flux:modal name="confirm-delete">
        <div class="space-y-4">
            <flux:heading>Confirmar exclusão</flux:heading>
            <flux:text>Tem certeza que deseja remover este produto? Retiradas já registradas não serão afetadas.</flux:text>
            <div class="flex justify-end gap-2">
                <flux:button wire:click="$set('deletingId', null)" x-on:click="Flux.modal('confirm-delete').close()" variant="ghost">Cancelar</flux:button>
                <flux:button wire:click="delete" x-on:click="Flux.modal('confirm-delete').close()" variant="danger">Excluir</flux:button>
            </div>
        </div>
    </flux:modal>

</div>

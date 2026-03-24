<div class="flex h-full w-full flex-1 flex-col gap-4">

    @if (session('success'))
        <flux:callout variant="success" icon="check-circle">{{ session('success') }}</flux:callout>
    @endif

    <div class="flex items-center justify-between">
        <flux:heading size="xl">Produtos</flux:heading>
        <flux:button href="{{ route('produtos.create') }}" variant="primary" icon="plus" wire:navigate>
            Novo Produto
        </flux:button>
    </div>

    <div class="flex flex-wrap gap-3">
        <flux:input wire:model.live.debounce.300ms="search" placeholder="Buscar produto..." icon="magnifying-glass" class="flex-1 min-w-48" />
        <flux:select wire:model.live="categoria" class="w-48">
            <flux:select.option value="">Todas as categorias</flux:select.option>
            @foreach ($categorias as $cat)
                <flux:select.option value="{{ $cat }}">{{ $cat }}</flux:select.option>
            @endforeach
        </flux:select>
    </div>

    <div class="overflow-x-auto rounded-xl border border-neutral-200 dark:border-neutral-700">
        <table class="w-full text-sm">
            <thead class="bg-neutral-50 dark:bg-zinc-800 text-left">
                <tr>
                    <th class="px-4 py-3 font-medium">Nome</th>
                    <th class="px-4 py-3 font-medium">Categoria</th>
                    <th class="px-4 py-3 font-medium">Unidade</th>
                    <th class="px-4 py-3 font-medium">Descrição</th>
                    <th class="px-4 py-3 font-medium">Status</th>
                    <th class="px-4 py-3 font-medium text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                @forelse ($produtos as $p)
                    <tr class="hover:bg-neutral-50 dark:hover:bg-zinc-800/50 {{ !$p->ativo ? 'opacity-50' : '' }}">
                        <td class="px-4 py-3 font-medium">{{ $p->nome }}</td>
                        <td class="px-4 py-3">
                            <flux:badge color="zinc" size="sm">{{ $p->categoria }}</flux:badge>
                        </td>
                        <td class="px-4 py-3 text-neutral-500">{{ $p->unidade }}</td>
                        <td class="px-4 py-3 text-neutral-500 max-w-xs truncate">{{ $p->descricao ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <button wire:click="toggleAtivo({{ $p->id }})" class="cursor-pointer">
                                @if ($p->ativo)
                                    <flux:badge color="green" size="sm">Ativo</flux:badge>
                                @else
                                    <flux:badge color="red" size="sm">Inativo</flux:badge>
                                @endif
                            </button>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex justify-end gap-2">
                                <flux:button href="{{ route('produtos.edit', $p) }}" size="sm" variant="ghost" icon="pencil" wire:navigate />
                                <flux:button wire:click="confirmDelete({{ $p->id }})" size="sm" variant="ghost" icon="trash" class="text-red-500" />
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-neutral-500">Nenhum produto encontrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $produtos->links() }}</div>

    @if ($deletingId)
        <flux:modal name="confirm-delete" :show="true" wire:close="$set('deletingId', null)">
            <div class="space-y-4">
                <flux:heading>Confirmar exclusão</flux:heading>
                <flux:text>Tem certeza que deseja remover este produto? Retiradas já registradas não serão afetadas.</flux:text>
                <div class="flex justify-end gap-2">
                    <flux:button wire:click="$set('deletingId', null)" variant="ghost">Cancelar</flux:button>
                    <flux:button wire:click="delete" variant="danger">Excluir</flux:button>
                </div>
            </div>
        </flux:modal>
    @endif

</div>

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
        <div class="w-full sm:max-w-md">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="Buscar produto..." icon="magnifying-glass" class="w-full" />
        </div>
        <flux:select wire:model.live="categoria" class="w-full sm:w-48">
            <flux:select.option value="">Todas as categorias</flux:select.option>
            @foreach ($categorias as $cat)
                <flux:select.option value="{{ $cat }}">{{ $cat }}</flux:select.option>
            @endforeach
        </flux:select>
    </div>

    <div class="hidden md:block overflow-x-auto rounded-xl border border-neutral-200 dark:border-neutral-700">
        <table class="w-full text-sm">
            <thead class="bg-neutral-50 dark:bg-zinc-800 text-left">
                <tr>
                    <th class="px-4 py-3 font-medium">Nome</th>
                    <th class="px-4 py-3 font-medium">Categoria</th>
                    <th class="px-4 py-3 font-medium">Unidade</th>
                    <th class="px-4 py-3 font-medium">Estoque</th>
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
                        <td class="px-4 py-3 text-neutral-500">{{ $p->estoque }} {{ $p->unidade }}(s)</td>
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
                                <flux:button wire:click="confirmDelete({{ $p->id }})" x-on:click="Flux.modal('confirm-delete').show()" size="sm" variant="ghost" icon="trash" class="text-red-500" />
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

    {{-- MOBILE VIEW --}}
    <div class="block md:hidden space-y-4">
        @forelse ($produtos as $p)
            <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 p-4 space-y-3 bg-white dark:bg-zinc-900 shadow-sm {{ !$p->ativo ? 'opacity-50' : '' }}">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="font-bold text-base">{{ $p->nome }}</p>
                        <p class="text-xs text-neutral-500 capitalize">{{ $p->categoria }}</p>
                    </div>
                    <div class="flex gap-2">
                        <flux:button href="{{ route('produtos.edit', $p) }}" size="sm" variant="ghost" icon="pencil" wire:navigate />
                        <flux:button wire:click="confirmDelete({{ $p->id }})" x-on:click="Flux.modal('confirm-delete').show()" size="sm" variant="ghost" icon="trash" class="text-red-500" />
                    </div>
                </div>
                <div class="flex flex-col gap-2 text-sm pt-2 border-t border-neutral-100 dark:border-neutral-800">
                    <div class="w-full flex justify-between">
                        <span class="text-neutral-500">Unidade:</span>
                        <span>{{ $p->unidade }}</span>
                    </div>
                    <div class="w-full flex justify-between items-center">
                        <span class="text-neutral-500">Status:</span>
                        <button wire:click="toggleAtivo({{ $p->id }})" class="cursor-pointer">
                            @if ($p->ativo)
                                <flux:badge color="green" size="sm">Ativo</flux:badge>
                            @else
                                <flux:badge color="red" size="sm">Inativo</flux:badge>
                            @endif
                        </button>
                    </div>
                    @if($p->descricao)
                    <div class="w-full flex flex-col pt-1">
                        <span class="text-neutral-500 text-xs uppercase">Descrição</span>
                        <span class="italic text-neutral-600 dark:text-neutral-400 mt-1">{{ $p->descricao }}</span>
                    </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 p-8 text-center text-neutral-500">
                Nenhum produto encontrado.
            </div>
        @endforelse
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

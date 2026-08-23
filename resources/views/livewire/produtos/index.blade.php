<div class="flex h-full w-full flex-1 flex-col gap-4">

    @if (session('success'))
        <flux:callout variant="success" icon="check-circle">{{ session('success') }}</flux:callout>
    @endif

    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <flux:heading size="xl" class="text-zinc-900 dark:text-zinc-50 font-bold">Produtos & Estoque</flux:heading>
        <flux:button id="tour-btn-novo-produto" href="{{ route('produtos.create') }}" variant="primary" icon="plus" wire:navigate class="w-full sm:w-auto font-bold shadow-sm">
            Novo Produto
        </flux:button>
    </div>

    <div class="flex flex-col sm:flex-row gap-3">
        <div class="w-full sm:max-w-md">
            <flux:input id="tour-search-produto" wire:model.live.debounce.300ms="search" placeholder="Buscar produto por nome..." icon="magnifying-glass" class="w-full" />
        </div>
        <flux:select id="tour-filter-categoria" wire:model.live="categoria" class="w-full sm:w-56">
            <flux:select.option value="">Todas as categorias</flux:select.option>
            @foreach ($categorias as $cat)
                <flux:select.option value="{{ $cat }}">{{ $cat }}</flux:select.option>
            @endforeach
        </flux:select>
    </div>

    <div wire:loading.class="opacity-60 pointer-events-none transition-opacity" class="hidden md:block overflow-x-auto rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 shadow-sm">
        <table class="w-full text-sm">
            <thead class="bg-zinc-100 dark:bg-zinc-800 text-left border-b border-zinc-200 dark:border-zinc-700">
                <tr>
                    <th class="px-4 py-3.5 font-bold text-zinc-900 dark:text-zinc-100">Nome</th>
                    <th class="px-4 py-3.5 font-bold text-zinc-900 dark:text-zinc-100">Categoria</th>
                    <th class="px-4 py-3.5 font-bold text-zinc-900 dark:text-zinc-100">Unidade</th>
                    <th class="px-4 py-3.5 font-bold text-zinc-900 dark:text-zinc-100">Estoque</th>
                    <th class="px-4 py-3.5 font-bold text-zinc-900 dark:text-zinc-100">Descrição</th>
                    <th class="px-4 py-3.5 font-bold text-zinc-900 dark:text-zinc-100">Status</th>
                    <th class="px-4 py-3.5 font-bold text-zinc-900 dark:text-zinc-100 text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                @forelse ($produtos as $p)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/60 transition-colors {{ !$p->ativo ? 'opacity-50' : '' }}">
                        <td class="px-4 py-3.5 font-bold text-zinc-900 dark:text-zinc-100">{{ $p->nome }}</td>
                        <td class="px-4 py-3.5">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 border border-zinc-300 dark:border-zinc-700">
                                {{ $p->categoria }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5 font-semibold text-zinc-700 dark:text-zinc-300">{{ $p->unidade }}</td>
                        <td class="px-4 py-3.5 font-bold text-zinc-900 dark:text-zinc-100">
                            @if($p->estoque !== null)
                                <span class="{{ $p->estoque < 10 ? 'text-amber-600 dark:text-amber-400' : 'text-zinc-900 dark:text-zinc-100' }}">
                                    {{ $p->estoque }} {{ $p->unidade }}(s)
                                </span>
                            @else
                                <span class="text-zinc-400 font-normal italic">Sem controle</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-zinc-600 dark:text-zinc-400 max-w-xs truncate">{{ $p->descricao ?? '—' }}</td>
                        <td class="px-4 py-3.5">
                            <button wire:click="toggleAtivo({{ $p->id }})" class="cursor-pointer">
                                @if ($p->ativo)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-900 dark:bg-emerald-950 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span> Ativo
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-900 dark:bg-rose-950 dark:text-rose-300 border border-rose-300 dark:border-rose-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span> Inativo
                                    </span>
                                @endif
                            </button>
                        </td>
                        <td class="px-4 py-3.5 text-right">
                            <div class="flex justify-end gap-2">
                                <flux:button href="{{ route('produtos.edit', $p) }}" size="sm" variant="ghost" icon="pencil" class="text-zinc-700 dark:text-zinc-300 hover:text-zinc-900 dark:hover:text-white" wire:navigate />
                                <flux:button wire:click="confirmDelete({{ $p->id }})" x-on:click="Flux.modal('confirm-delete').show()" size="sm" variant="ghost" icon="trash" class="text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40" />
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-zinc-500 font-medium">Nenhum produto cadastrado no momento.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- MOBILE VIEW --}}
    <div wire:loading.class="opacity-60 pointer-events-none transition-opacity" class="block md:hidden space-y-4">
        @forelse ($produtos as $p)
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 p-4 space-y-3 bg-white dark:bg-zinc-900 shadow-sm {{ !$p->ativo ? 'opacity-50' : '' }}">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-3">
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-base text-zinc-900 dark:text-zinc-100 truncate">{{ $p->nome }}</p>
                        <span class="inline-block text-xs font-bold text-zinc-700 dark:text-zinc-300 bg-zinc-100 dark:bg-zinc-800 px-2 py-0.5 rounded mt-1 capitalize">{{ $p->categoria }}</span>
                    </div>
                    <div class="flex gap-1.5 self-end sm:self-start">
                        <flux:button href="{{ route('produtos.edit', $p) }}" size="sm" variant="ghost" icon="pencil" class="text-zinc-700 dark:text-zinc-300" wire:navigate />
                        <flux:button wire:click="confirmDelete({{ $p->id }})" x-on:click="Flux.modal('confirm-delete').show()" size="sm" variant="ghost" icon="trash" class="text-rose-600 dark:text-rose-400" />
                    </div>
                </div>
                <div class="flex flex-col gap-2 text-sm pt-2 border-t border-zinc-100 dark:border-zinc-800">
                    <div class="w-full flex justify-between">
                        <span class="text-zinc-600 dark:text-zinc-400 font-medium">Unidade:</span>
                        <span class="font-bold text-zinc-900 dark:text-zinc-100">{{ $p->unidade }}</span>
                    </div>
                    <div class="w-full flex justify-between">
                        <span class="text-zinc-600 dark:text-zinc-400 font-medium">Estoque:</span>
                        <span class="font-bold text-zinc-900 dark:text-zinc-100">{{ $p->estoque ?? 'Sem controle' }}</span>
                    </div>
                    <div class="w-full flex justify-between items-center">
                        <span class="text-zinc-600 dark:text-zinc-400 font-medium">Status:</span>
                        <button wire:click="toggleAtivo({{ $p->id }})" class="cursor-pointer">
                            @if ($p->ativo)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-900 dark:bg-emerald-950 dark:text-emerald-300">
                                    Ativo
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-900 dark:bg-rose-950 dark:text-rose-300">
                                    Inativo
                                </span>
                            @endif
                        </button>
                    </div>
                    @if($p->descricao)
                    <div class="w-full flex flex-col pt-1">
                        <span class="text-zinc-500 text-xs font-bold uppercase">Descrição</span>
                        <span class="italic text-zinc-700 dark:text-zinc-300 mt-1">{{ $p->descricao }}</span>
                    </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 p-8 text-center text-zinc-500 font-medium bg-white dark:bg-zinc-900">
                Nenhum produto cadastrado.
            </div>
        @endforelse
    </div>

    <div>{{ $produtos->links() }}</div>

    <flux:modal name="confirm-delete">
        <div class="space-y-4">
            <flux:heading class="font-bold text-zinc-900 dark:text-zinc-100">Confirmar exclusão</flux:heading>
            <flux:text class="text-zinc-700 dark:text-zinc-300">Tem certeza que deseja remover este produto? Retiradas já registradas não serão afetadas.</flux:text>
            <div class="flex justify-end gap-2 pt-2">
                <flux:button wire:click="$set('deletingId', null)" x-on:click="Flux.modal('confirm-delete').close()" variant="ghost">Cancelar</flux:button>
                <flux:button wire:click="delete" x-on:click="Flux.modal('confirm-delete').close()" variant="danger" class="font-bold">Excluir Produto</flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- FAB MOBILE (NOVO PRODUTO) --}}
    <div class="md:hidden fixed bottom-20 right-4 z-30">
        <a 
            id="tour-mobile-fab-produto"
            href="{{ route('produtos.create') }}" 
            wire:navigate
            class="flex items-center justify-center w-14 h-14 rounded-full bg-emerald-700 active:bg-emerald-800 text-white shadow-2xl hover:scale-105 active:scale-95 transition-all duration-150 border-2 border-white/40"
            title="Novo Produto"
        >
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
            </svg>
        </a>
    </div>

</div>

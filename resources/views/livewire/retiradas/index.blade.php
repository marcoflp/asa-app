<div class="flex h-full w-full flex-1 flex-col gap-4">

    @if (session('success'))
        <flux:callout variant="success" icon="check-circle">{{ session('success') }}</flux:callout>
    @endif

    <div class="flex items-center justify-between gap-4">
        <flux:heading size="xl" class="text-zinc-900 dark:text-zinc-50 font-bold">Retiradas de Doações</flux:heading>
        <flux:button id="tour-btn-nova-retirada" href="{{ route('retiradas.create') }}" variant="primary" icon="plus" wire:navigate class="hidden sm:inline-flex font-bold shadow-xs">
            Nova Retirada
        </flux:button>
    </div>

    <div class="flex flex-col sm:flex-row gap-2.5">
        <flux:input id="tour-search-retirada" wire:model.live.debounce.300ms="search" placeholder="Buscar por beneficiário..." icon="magnifying-glass" class="w-full sm:flex-1" />
        
        <div id="tour-filtros-data" class="grid grid-cols-2 gap-2 w-full sm:w-auto sm:flex sm:items-center bg-zinc-50 dark:bg-zinc-800/60 p-1.5 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-2xs">
            <div class="flex items-center gap-1.5 min-w-0">
                <span class="text-xs font-bold text-zinc-600 dark:text-zinc-400 pl-1 shrink-0">De:</span>
                <input 
                    type="date" 
                    wire:model.live="dataInicio" 
                    class="w-full sm:w-36 text-xs font-bold py-1.5 px-2 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-emerald-500 focus:outline-hidden"
                />
            </div>
            <div class="flex items-center gap-1.5 min-w-0">
                <span class="text-xs font-bold text-zinc-600 dark:text-zinc-400 shrink-0">Até:</span>
                <input 
                    type="date" 
                    wire:model.live="dataFim" 
                    class="w-full sm:w-36 text-xs font-bold py-1.5 px-2 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-emerald-500 focus:outline-hidden"
                />
            </div>
        </div>
    </div>

    {{-- DESKTOP VIEW --}}
    <div wire:loading.class="opacity-60 pointer-events-none transition-opacity" class="hidden md:block overflow-x-auto rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 shadow-sm">
        <table class="w-full text-sm">
            <thead class="bg-zinc-100 dark:bg-zinc-800 text-left border-b border-zinc-200 dark:border-zinc-700">
                <tr>
                    <th class="px-4 py-3.5 font-bold text-zinc-900 dark:text-zinc-100">Data</th>
                    <th class="px-4 py-3.5 font-bold text-zinc-900 dark:text-zinc-100">Beneficiário</th>
                    <th class="px-4 py-3.5 font-bold text-zinc-900 dark:text-zinc-100">Itens Entregues</th>
                    <th class="px-4 py-3.5 font-bold text-zinc-900 dark:text-zinc-100">Observações</th>
                    <th class="px-4 py-3.5 font-bold text-zinc-900 dark:text-zinc-100 text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                @forelse ($retiradas as $r)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/60 transition-colors">
                        <td class="px-4 py-3.5 whitespace-nowrap font-bold text-zinc-900 dark:text-zinc-100">{{ $r->data->format('d/m/Y') }}</td>
                        <td class="px-4 py-3.5 font-bold text-zinc-900 dark:text-zinc-100">{{ $r->beneficiario->nome }}</td>
                        <td class="px-4 py-3.5">
                            <div class="flex flex-wrap gap-1.5">
                                @foreach ($r->items as $item)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 border border-zinc-300 dark:border-zinc-700">
                                        {{ $item->produto->nome }} &times; {{ $item->quantidade }} {{ $item->produto->unidade }}
                                    </span>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-4 py-3.5 text-zinc-600 dark:text-zinc-400 max-w-xs truncate">{{ $r->observacoes ?? '—' }}</td>
                        <td class="px-4 py-3.5 text-right">
                            <div class="flex justify-end gap-2">
                                <flux:button href="{{ route('retiradas.edit', $r) }}" size="sm" variant="ghost" icon="pencil" class="text-zinc-700 dark:text-zinc-300 hover:text-zinc-900 dark:hover:text-white" wire:navigate />
                                <flux:button wire:click="confirmDelete({{ $r->id }})" x-on:click="Flux.modal('confirm-delete').show()" size="sm" variant="ghost" icon="trash" class="text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40" />
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-zinc-500 font-medium">Nenhuma retirada registrada.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- MOBILE VIEW --}}
    <div wire:loading.class="opacity-60 pointer-events-none transition-opacity" class="block md:hidden space-y-4">
        @forelse ($retiradas as $r)
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 p-4 space-y-3 bg-white dark:bg-zinc-900 shadow-sm">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs text-emerald-700 dark:text-emerald-400 font-bold mb-0.5">{{ $r->data->format('d/m/Y') }}</p>
                        <p class="font-bold text-base text-zinc-900 dark:text-zinc-100">{{ $r->beneficiario->nome }}</p>
                    </div>
                    <div class="flex gap-2">
                        <flux:button href="{{ route('retiradas.edit', $r) }}" size="sm" variant="ghost" icon="pencil" class="text-zinc-700 dark:text-zinc-300" wire:navigate />
                        <flux:button wire:click="confirmDelete({{ $r->id }})" x-on:click="Flux.modal('confirm-delete').show()" size="sm" variant="ghost" icon="trash" class="text-rose-600 dark:text-rose-400" />
                    </div>
                </div>
                <div class="pt-2 border-t border-zinc-100 dark:border-zinc-800">
                    <p class="text-xs text-zinc-500 uppercase font-bold mb-2">Itens retirados</p>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach ($r->items as $item)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700">
                                {{ $item->quantidade }}x {{ $item->produto->nome }}
                            </span>
                        @endforeach
                    </div>
                </div>
                @if($r->observacoes)
                <div class="pt-2 border-t border-zinc-100 dark:border-zinc-800 mt-2">
                    <p class="text-xs text-zinc-500 uppercase font-bold mb-1">Observações</p>
                    <p class="text-sm italic text-zinc-700 dark:text-zinc-300">{{ $r->observacoes }}</p>
                </div>
                @endif
            </div>
        @empty
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 p-8 text-center text-zinc-500 font-medium bg-white dark:bg-zinc-900">
                Nenhuma retirada encontrada.
            </div>
        @endforelse
    </div>

    <div>{{ $retiradas->links() }}</div>

    <flux:modal name="confirm-delete">
        <div class="space-y-4">
            <flux:heading class="font-bold text-zinc-900 dark:text-zinc-100">Confirmar exclusão</flux:heading>
            <flux:text class="text-zinc-700 dark:text-zinc-300">Tem certeza que deseja remover este registro de retirada?</flux:text>
            <div class="flex justify-end gap-2 pt-2">
                <flux:button wire:click="$set('deletingId', null)" x-on:click="Flux.modal('confirm-delete').close()" variant="ghost">Cancelar</flux:button>
                <flux:button wire:click="delete" x-on:click="Flux.modal('confirm-delete').close()" variant="danger" class="font-bold">Excluir Retirada</flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- FAB MOBILE (NOVA RETIRADA) --}}
    <div class="md:hidden fixed bottom-20 right-4 z-30">
        <a 
            id="tour-mobile-fab-retirada"
            href="{{ route('retiradas.create') }}" 
            wire:navigate
            class="flex items-center justify-center w-14 h-14 rounded-full bg-emerald-700 active:bg-emerald-800 text-white shadow-2xl hover:scale-105 active:scale-95 transition-all duration-150 border-2 border-white/40"
            title="Nova Retirada"
        >
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
            </svg>
        </a>
    </div>

</div>

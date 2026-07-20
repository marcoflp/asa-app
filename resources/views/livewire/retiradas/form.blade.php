<div class="flex h-full w-full flex-1 flex-col gap-6 max-w-3xl mx-auto">

    <div class="flex items-center gap-3">
        <flux:button href="{{ route('retiradas.index') }}" variant="ghost" icon="arrow-left" wire:navigate />
        <flux:heading size="xl">
            {{ $retirada?->exists ? 'Editar Retirada' : 'Nova Retirada' }}
        </flux:heading>
    </div>

    <form wire:submit="save" class="space-y-5">
        <flux:error name="geral" />

        {{-- Beneficiário e Data --}}
        <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 p-5 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:field class="md:col-span-1">
                    <flux:label>Beneficiário *</flux:label>
                    <flux:input wire:model.live.debounce.300ms="searchBeneficiario" placeholder="Digite para buscar..." icon="magnifying-glass" size="sm" class="mb-2" />
                    <flux:select wire:model="beneficiario_id">
                        <flux:select.option value="0" disabled>Selecione ({{ $beneficiarios->count() }} encontrados)</flux:select.option>
                        @foreach ($beneficiarios as $b)
                            <flux:select.option value="{{ $b->id }}">{{ $b->nome }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="beneficiario_id" />
                </flux:field>

                <flux:field>
                    <flux:label>Data *</flux:label>
                    <flux:input type="date" wire:model="data" />
                    <flux:error name="data" />
                </flux:field>
            </div>
        </div>

        {{-- Itens --}}
        <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 p-5 space-y-5">
            <flux:heading size="lg">Itens retirados</flux:heading>

            {{-- Autocomplete de Produtos --}}
            <div x-data="{ open: false }" class="relative w-full" @click.away="open = false">
                <flux:field>
                    <flux:input 
                        wire:model.live.debounce.300ms="searchProduto" 
                        @focus="open = true"
                        @keydown.escape="open = false"
                        placeholder="Buscar produto para adicionar..." 
                        icon="magnifying-glass" 
                        class="w-full text-base"
                    />
                </flux:field>

                {{-- Dropdown de resultados --}}
                @if(strlen($searchProduto) > 0 && count($produtos) > 0)
                    <div x-show="open" x-transition 
                         class="absolute z-50 w-full mt-1 bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg shadow-xl max-h-72 overflow-y-auto">
                        @php $categoriaAtual = null; @endphp
                        @foreach($produtos as $p)
                            @if ($p->categoria !== $categoriaAtual)
                                @php $categoriaAtual = $p->categoria; @endphp
                                <div class="px-3 py-2 text-xs font-bold text-neutral-500 bg-neutral-100 dark:bg-neutral-900 sticky top-0 uppercase tracking-widest border-b border-neutral-200 dark:border-neutral-700 z-10">
                                    {{ $p->categoria }}
                                </div>
                            @endif
                            <button type="button" 
                                    wire:click="adicionarProduto({{ $p->id }})"
                                    @click="open = false"
                                    class="w-full text-left px-4 py-3 hover:bg-blue-50 dark:hover:bg-neutral-700 transition-colors flex justify-between items-center border-b border-neutral-100 dark:border-neutral-800 last:border-0">
                                <span class="font-medium text-neutral-800 dark:text-neutral-200 text-base sm:text-sm">{{ $p->nome }}</span>
                                <span class="text-xs font-semibold text-neutral-500 bg-neutral-100 dark:bg-neutral-800 px-2 py-1 rounded-md">Estoque: {{ $p->estoque }} {{ $p->unidade }}</span>
                            </button>
                        @endforeach
                    </div>
                @elseif(strlen($searchProduto) > 0)
                    <div x-show="open" class="absolute z-50 w-full mt-1 bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg shadow-xl p-6 text-center text-neutral-500">
                        Nenhum produto encontrado.
                    </div>
                @endif
            </div>

            <flux:error name="items" />

            {{-- Lista de itens adicionados (Carrinho) --}}
            @if (count($items) > 0)
                <div class="space-y-3 mt-4">
                    @foreach ($items as $i => $item)
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between p-4 bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl shadow-sm gap-4 transition-all">
                            <div class="flex-1">
                                <h4 class="font-medium text-neutral-900 dark:text-neutral-100 text-lg sm:text-base">{{ $item['produto_nome'] }}</h4>
                            </div>
                            
                            <div class="flex items-center justify-between sm:justify-end w-full sm:w-auto gap-4">
                                {{-- Controle de Quantidade (Steppers) --}}
                                <div class="flex items-center border border-neutral-200 dark:border-neutral-600 rounded-lg bg-neutral-50 dark:bg-neutral-900 overflow-hidden shadow-inner">
                                    <button type="button" wire:click="decrementarItem({{ $i }})" class="px-4 py-3 sm:px-3 sm:py-2 text-neutral-600 dark:text-neutral-400 hover:bg-neutral-200 dark:hover:bg-neutral-700 transition-colors active:bg-neutral-300">
                                        <svg class="w-5 h-5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                                    </button>
                                    
                                    <div class="w-16 sm:w-14 text-center font-bold text-base">
                                        {{ $item['quantidade'] }} <span class="text-xs font-medium text-neutral-500">{{ $item['produto_unidade'] ?? '' }}</span>
                                    </div>
                                    
                                    <button type="button" wire:click="incrementarItem({{ $i }})" class="px-4 py-3 sm:px-3 sm:py-2 text-neutral-600 dark:text-neutral-400 hover:bg-neutral-200 dark:hover:bg-neutral-700 transition-colors active:bg-neutral-300">
                                        <svg class="w-5 h-5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    </button>
                                </div>

                                {{-- Botão Remover --}}
                                <button type="button" wire:click="removeItem({{ $i }})" class="p-3 sm:p-2 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors active:bg-red-100">
                                    <svg class="w-6 h-6 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center p-8 sm:p-12 border-2 border-dashed border-neutral-200 dark:border-neutral-700 rounded-xl text-neutral-400 bg-neutral-50/50 dark:bg-neutral-800/50 mt-4">
                    <svg class="w-12 h-12 mx-auto mb-3 text-neutral-300 dark:text-neutral-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    Nenhum item selecionado.<br/>Use a busca acima para adicionar produtos.
                </div>
            @endif
        </div>

        {{-- Observações --}}
        <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 p-5">
            <flux:field>
                <flux:label>Observações</flux:label>
                <flux:textarea wire:model="observacoes" rows="2" placeholder="Informações adicionais..." />
            </flux:field>
        </div>

        <div class="flex flex-col-reverse sm:flex-row justify-end gap-3">
            <flux:button href="{{ route('retiradas.index') }}" variant="ghost" wire:navigate class="w-full sm:w-auto">Cancelar</flux:button>
            <flux:button type="submit" variant="primary" class="w-full sm:w-auto">
                {{ $retirada?->exists ? 'Salvar alterações' : 'Registrar retirada' }}
            </flux:button>
        </div>

    </form>
</div>

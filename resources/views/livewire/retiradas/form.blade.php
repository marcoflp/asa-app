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
                <flux:field>
                    <flux:label>Beneficiário *</flux:label>

                    @if ($beneficiarioSelecionado)
                        @php
                            $ultima = $beneficiarioSelecionado->retiradas_max_data;
                            $dias = $ultima ? (int) abs(\Carbon\Carbon::parse($ultima)->startOfDay()->diffInDays(today())) : null;
                            $quando = $dias === null ? null : ($dias === 0 ? 'hoje' : ($dias === 1 ? 'ontem' : "há {$dias} dias"));
                        @endphp
                        <div wire:key="beneficiario-escolhido" class="flex items-center gap-3 rounded-lg border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/60 px-3 py-2.5">
                            <span class="shrink-0 w-9 h-9 rounded-full bg-accent text-accent-foreground flex items-center justify-center font-semibold text-sm" aria-hidden="true">
                                {{ mb_strtoupper(mb_substr($beneficiarioSelecionado->nome, 0, 1)) }}
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="font-medium text-neutral-900 dark:text-white truncate">{{ $beneficiarioSelecionado->nome }}</p>
                                <p class="text-xs text-neutral-500 dark:text-neutral-400 truncate">
                                    {{ $beneficiarioSelecionado->bairro ?: 'Bairro não informado' }}
                                    · {{ $quando ? 'Última retirada '.$quando : 'Primeira retirada' }}
                                </p>
                            </div>
                            <flux:button size="sm" variant="ghost" wire:click="trocarBeneficiario">Trocar</flux:button>
                        </div>

                        @if ($dias !== null && $dias < 7 && ! $retirada?->exists)
                            <flux:callout wire:key="aviso-recente" variant="warning" icon="exclamation-triangle" heading="Esta família já retirou {{ $quando }}." />
                        @endif
                    @else
                        <div wire:key="beneficiario-busca" class="relative"
                             x-data="{ open: false, active: 0,
                                 opcoes() { return [...$refs.lista?.querySelectorAll('[data-opcao]') ?? []] },
                                 mover(d) { const n = this.opcoes().length; if (n) { this.open = true; this.active = (this.active + d + n) % n; this.opcoes()[this.active].scrollIntoView({ block: 'nearest' }) } },
                                 escolher() { this.opcoes()[this.active]?.click() } }"
                             x-init="$nextTick(() => open = $el.contains(document.activeElement))"
                             @focusin="open = true"
                             @click="open = true"
                             @click.outside="open = false">
                            <flux:input
                                wire:model.live.debounce.250ms="searchBeneficiario"
                                @input="open = true; active = 0"
                                @keydown.arrow-down.prevent="mover(1)"
                                @keydown.arrow-up.prevent="mover(-1)"
                                @keydown.enter.prevent="escolher()"
                                @keydown.escape="open = false"
                                :autofocus="! $retirada?->exists"
                                placeholder="Nome, CPF ou telefone"
                                icon="magnifying-glass"
                                autocomplete="off"
                                role="combobox"
                                aria-autocomplete="list"
                            />

                            <div x-show="open" x-cloak x-transition.opacity.duration.100ms x-ref="lista" role="listbox"
                                 class="absolute z-50 w-full mt-1 bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg shadow-xl max-h-80 overflow-y-auto">
                                @if (trim($searchBeneficiario) === '' && $beneficiarios->isNotEmpty())
                                    <p class="px-3 pt-2.5 pb-1 text-xs font-medium text-neutral-500">Atendidos recentemente</p>
                                @endif

                                @forelse ($beneficiarios as $b)
                                    @php
                                        $dias = $b->retiradas_max_data ? (int) abs(\Carbon\Carbon::parse($b->retiradas_max_data)->startOfDay()->diffInDays(today())) : null;
                                    @endphp
                                    <button type="button" data-opcao role="option" wire:key="benef-{{ $b->id }}"
                                            wire:click="selecionarBeneficiario({{ $b->id }})"
                                            @mouseenter="active = {{ $loop->index }}"
                                            :class="active === {{ $loop->index }} ? 'bg-neutral-100 dark:bg-neutral-700' : ''"
                                            class="w-full text-left px-3 py-2.5 flex items-center justify-between gap-3">
                                        <span class="min-w-0">
                                            <span class="block font-medium text-neutral-900 dark:text-white truncate">{{ $b->nome }}</span>
                                            <span class="block text-xs text-neutral-500 dark:text-neutral-400 truncate">{{ $b->bairro ?: 'Bairro não informado' }}</span>
                                        </span>
                                        <span class="shrink-0 text-xs {{ $dias !== null && $dias < 7 ? 'text-amber-600 dark:text-amber-400 font-medium' : 'text-neutral-400' }}">
                                            {{ $dias === null ? 'Nunca retirou' : ($dias === 0 ? 'Retirou hoje' : ($dias === 1 ? 'Retirou ontem' : "Há {$dias} dias")) }}
                                        </span>
                                    </button>
                                @empty
                                    <div class="px-4 py-5 text-center text-sm text-neutral-500">
                                        Ninguém encontrado para "{{ $searchBeneficiario }}".
                                        <a href="{{ route('beneficiarios.create') }}" wire:navigate class="block mt-1 font-medium text-accent-content underline underline-offset-2">Cadastrar novo beneficiário</a>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    @endif
                    <flux:error name="beneficiario_id" />
                </flux:field>

                <flux:field class="self-start">
                    <flux:label>Data *</flux:label>
                    <flux:input type="date" wire:model="data" max="{{ now()->toDateString() }}" />
                    <flux:error name="data" />
                </flux:field>
            </div>
        </div>

        {{-- Itens --}}
        <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 p-5 space-y-5">
            <div class="flex items-baseline justify-between gap-3">
                <flux:heading size="lg">Itens retirados</flux:heading>
                @if (count($items))
                    <span class="text-sm text-neutral-500">{{ count($items) }} {{ count($items) === 1 ? 'produto' : 'produtos' }}</span>
                @endif
            </div>

            {{-- Busca de produtos: Enter adiciona o destacado e já deixa pronto para o próximo --}}
            <div class="relative w-full"
                 x-data="{ open: false, active: 0,
                     opcoes() { return [...$refs.lista?.querySelectorAll('[data-opcao]') ?? []] },
                     mover(d) { const n = this.opcoes().length; if (n) { this.open = true; this.active = (this.active + d + n) % n; this.opcoes()[this.active].scrollIntoView({ block: 'nearest' }) } },
                     escolher() { this.opcoes()[this.active]?.click() },
                     focar() { this.$nextTick(() => this.$root.querySelector('input')?.focus()) } }"
                 @focusin="open = true"
                 @click="open = true"
                 @click.outside="open = false">
                <flux:input
                    wire:model.live.debounce.250ms="searchProduto"
                    @input="open = true; active = 0"
                    @keydown.arrow-down.prevent="mover(1)"
                    @keydown.arrow-up.prevent="mover(-1)"
                    @keydown.enter.prevent="escolher()"
                    @keydown.escape="open = false"
                    placeholder="Buscar produto ou categoria"
                    icon="magnifying-glass"
                    autocomplete="off"
                    role="combobox"
                    aria-autocomplete="list"
                />

                @if (trim($searchProduto) !== '')
                    <div x-show="open" x-cloak x-transition.opacity.duration.100ms x-ref="lista" role="listbox"
                         class="absolute z-50 w-full mt-1 bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg shadow-xl max-h-80 overflow-y-auto">
                        @forelse ($produtos as $p)
                            @php $noCarrinho = collect($items)->firstWhere('produto_id', $p->id); @endphp
                            <button type="button" data-opcao role="option" wire:key="prod-{{ $p->id }}"
                                    wire:click="adicionarProduto({{ $p->id }})"
                                    @click="active = 0; focar()"
                                    @mouseenter="active = {{ $loop->index }}"
                                    :class="active === {{ $loop->index }} ? 'bg-neutral-100 dark:bg-neutral-700' : ''"
                                    class="w-full text-left px-3 py-2.5 flex items-center justify-between gap-3">
                                <span class="min-w-0">
                                    <span class="block font-medium text-neutral-900 dark:text-white truncate">{{ $p->nome }}</span>
                                    <span class="block text-xs text-neutral-500 dark:text-neutral-400">
                                        {{ $p->categoria }}@if ($noCarrinho) · já na lista ({{ $noCarrinho['quantidade'] }})@endif
                                    </span>
                                </span>
                                @if ($p->estoque === null)
                                    <span class="shrink-0 text-xs text-neutral-400">Sem controle</span>
                                @elseif ($p->estoque <= 0)
                                    <span class="shrink-0 text-xs font-medium text-amber-600 dark:text-amber-400">Sem estoque</span>
                                @else
                                    <span class="shrink-0 text-xs text-neutral-500">{{ (int) $p->estoque }} {{ $p->unidade }}</span>
                                @endif
                            </button>
                        @empty
                            <p class="px-4 py-5 text-center text-sm text-neutral-500">Nenhum produto encontrado para "{{ $searchProduto }}".</p>
                        @endforelse
                    </div>
                @endif
            </div>

            <flux:error name="items" />

            {{-- Lista de itens adicionados (Carrinho) --}}
            @if (count($items) > 0)
                <div class="space-y-3 mt-4">
                    @foreach ($items as $i => $item)
                        <div wire:key="item-{{ $item['produto_id'] }}" class="flex flex-col sm:flex-row sm:items-center justify-between p-4 bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl shadow-sm gap-4 transition-all">
                            <div class="flex-1">
                                <h4 class="font-medium text-neutral-900 dark:text-neutral-100 text-lg sm:text-base">{{ $item['produto_nome'] }}</h4>
                            </div>
                            
                            <div class="flex items-center justify-between sm:justify-end w-full sm:w-auto gap-4">
                                {{-- Controle de Quantidade (Steppers) --}}
                                <div class="flex items-center border border-neutral-200 dark:border-neutral-600 rounded-lg bg-neutral-50 dark:bg-neutral-900 overflow-hidden shadow-inner">
                                    <button type="button" wire:click="decrementarItem({{ $i }})" aria-label="Diminuir {{ $item['produto_nome'] }}" class="px-4 py-3 sm:px-3 sm:py-2 text-neutral-600 dark:text-neutral-400 hover:bg-neutral-200 dark:hover:bg-neutral-700 transition-colors active:bg-neutral-300">
                                        <svg class="w-5 h-5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                                    </button>
                                    
                                    <label class="flex items-center gap-1 px-1">
                                        <span class="sr-only">Quantidade de {{ $item['produto_nome'] }}</span>
                                        <input type="number" min="1" inputmode="numeric"
                                               wire:model.live.debounce.400ms="items.{{ $i }}.quantidade"
                                               class="w-12 bg-transparent text-center font-bold text-base outline-none [appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none focus:ring-2 focus:ring-accent rounded" />
                                        <span class="text-xs font-medium text-neutral-500">{{ $item['produto_unidade'] ?? '' }}</span>
                                    </label>
                                    
                                    <button type="button" wire:click="incrementarItem({{ $i }})" aria-label="Aumentar {{ $item['produto_nome'] }}" class="px-4 py-3 sm:px-3 sm:py-2 text-neutral-600 dark:text-neutral-400 hover:bg-neutral-200 dark:hover:bg-neutral-700 transition-colors active:bg-neutral-300">
                                        <svg class="w-5 h-5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    </button>
                                </div>

                                {{-- Botão Remover --}}
                                <button type="button" wire:click="removeItem({{ $i }})" aria-label="Remover {{ $item['produto_nome'] }}" class="p-3 sm:p-2 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors active:bg-red-100">
                                    <svg class="w-6 h-6 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center p-8 sm:p-12 border-2 border-dashed border-neutral-200 dark:border-neutral-700 rounded-xl text-neutral-400 bg-neutral-50/50 dark:bg-neutral-800/50 mt-4">
                    <svg class="w-12 h-12 mx-auto mb-3 text-neutral-300 dark:text-neutral-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    Nenhum item adicionado ainda.<br/>Busque um produto acima e aperte Enter para adicionar.
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

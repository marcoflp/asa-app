<div class="flex h-full w-full flex-1 flex-col gap-5 sm:gap-6">

    {{-- 1. CABEÇALHO & FILTRO DE PERÍODO --}}
    <div class="bg-white dark:bg-zinc-900 rounded-2xl p-4 sm:p-6 border border-zinc-200 dark:border-zinc-800 shadow-xs space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <div class="flex items-center gap-2.5">
                    <flux:heading size="xl" class="text-zinc-900 dark:text-zinc-100 font-bold tracking-tight">Visão Geral</flux:heading>
                </div>
                <p class="text-xs sm:text-sm text-zinc-500 dark:text-zinc-400 mt-1 flex items-center gap-1.5 font-medium">
                    <svg class="w-3.5 h-3.5 text-zinc-400 dark:text-zinc-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span>Período: <strong class="text-zinc-800 dark:text-zinc-200">{{ $inicio->format('d/m/Y') }}</strong> até <strong class="text-zinc-800 dark:text-zinc-200">{{ $fim->format('d/m/Y') }}</strong></span>
                </p>
            </div>

            {{-- Botão de Exportar no Cabeçalho --}}
            <div class="flex items-center gap-2 shrink-0">
                <flux:button id="tour-dashboard-exportar" wire:click="gerarRelatorio" icon="document-arrow-down" variant="primary" size="sm" class="w-full sm:w-auto shadow-xs font-semibold text-xs py-2">
                    Exportar PDF
                </flux:button>
            </div>
        </div>

        {{-- Seletor de Período Responsivo (Sem scroll horizontal) --}}
        <div id="tour-dashboard-periodo" class="pt-2 border-t border-zinc-100 dark:border-zinc-800">
            <div class="grid grid-cols-3 sm:flex sm:flex-wrap sm:items-center gap-1.5 w-full">
                @php
                    $periodos = [
                        'hoje' => 'Hoje',
                        'semanal' => '7 Dias',
                        'mensal' => 'Este Mês',
                        'trimestral' => 'Trimestre',
                        'semestral' => 'Semestre',
                        'personalizado' => 'Personalizado',
                    ];
                @endphp

                @foreach($periodos as $key => $label)
                    <button 
                        type="button"
                        wire:click="$set('periodo', '{{ $key }}')"
                        class="px-2.5 py-2 sm:px-3 sm:py-1.5 rounded-lg text-xs font-semibold text-center transition-colors cursor-pointer {{ $periodo === $key ? 'bg-emerald-700 text-white font-bold shadow-xs' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-700' }}"
                    >
                        {{ $label }}
                    </button>
                @endforeach
            </div>

            {{-- Seleção de Datas Personalizadas --}}
            @if($periodo === 'personalizado')
                <div class="mt-3 p-3 bg-zinc-50 dark:bg-zinc-800/40 rounded-xl border border-zinc-200 dark:border-zinc-700 flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5">
                    <div class="flex items-center gap-2 flex-1">
                        <span class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 w-8 shrink-0">De:</span>
                        <input 
                            type="date" 
                            wire:model.live="dataInicio" 
                            class="w-full text-xs font-semibold py-1.5 px-2.5 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-emerald-500 focus:outline-hidden"
                        />
                    </div>
                    <div class="flex items-center gap-2 flex-1">
                        <span class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 w-8 shrink-0">Até:</span>
                        <input 
                            type="date" 
                            wire:model.live="dataFim" 
                            class="w-full text-xs font-semibold py-1.5 px-2.5 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-emerald-500 focus:outline-hidden"
                        />
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- 2. CARDS KPI PRINCIPAIS (Uniformizados, Elegantes e Profissionais) --}}
    <div id="tour-dashboard-kpis" class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        
        {{-- Card 1: Retiradas --}}
        <div class="rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-4 sm:p-5 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] sm:text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Retiradas</span>
                <div class="p-2 bg-zinc-100 dark:bg-zinc-800 rounded-lg text-zinc-600 dark:text-zinc-300 shrink-0">
                    <flux:icon.arrow-trending-up class="w-4 h-4" />
                </div>
            </div>
            <div>
                <p class="text-2xl sm:text-3xl font-extrabold text-zinc-900 dark:text-zinc-100 tracking-tight">{{ $totalRetiradas }}</p>
                <span class="text-[11px] font-medium text-zinc-500 dark:text-zinc-400 mt-0.5 block">no período</span>
            </div>
        </div>

        {{-- Card 2: Beneficiários Atendidos --}}
        <div class="rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-4 sm:p-5 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] sm:text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Atendidos</span>
                <div class="p-2 bg-zinc-100 dark:bg-zinc-800 rounded-lg text-zinc-600 dark:text-zinc-300 shrink-0">
                    <flux:icon.users class="w-4 h-4" />
                </div>
            </div>
            <div>
                <p class="text-2xl sm:text-3xl font-extrabold text-zinc-900 dark:text-zinc-100 tracking-tight">{{ $totalBeneficiarios }}</p>
                <span class="text-[11px] font-medium text-zinc-500 dark:text-zinc-400 mt-0.5 block">beneficiários ativos</span>
            </div>
        </div>

        {{-- Card 3: Itens Entregues --}}
        <div class="rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-4 sm:p-5 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] sm:text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Itens Entregues</span>
                <div class="p-2 bg-zinc-100 dark:bg-zinc-800 rounded-lg text-zinc-600 dark:text-zinc-300 shrink-0">
                    <flux:icon.cube class="w-4 h-4" />
                </div>
            </div>
            <div>
                <p class="text-2xl sm:text-3xl font-extrabold text-zinc-900 dark:text-zinc-100 tracking-tight">{{ $totalItens }}</p>
                <span class="text-[11px] font-medium text-zinc-500 dark:text-zinc-400 mt-0.5 block">produtos totais</span>
            </div>
        </div>

        {{-- Card 4: Base Geral de Beneficiários --}}
        <a href="{{ route('beneficiarios.index') }}" wire:navigate class="rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-4 sm:p-5 shadow-xs hover:border-zinc-300 dark:hover:border-zinc-700 transition-colors flex flex-col justify-between cursor-pointer group">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] sm:text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Beneficiários</span>
                <div class="p-2 bg-zinc-100 dark:bg-zinc-800 group-hover:bg-emerald-100 dark:group-hover:bg-emerald-950/60 rounded-lg text-zinc-600 dark:text-zinc-300 group-hover:text-emerald-700 dark:group-hover:text-emerald-400 transition-colors shrink-0">
                    <flux:icon.users class="w-4 h-4" />
                </div>
            </div>
            <div>
                <p class="text-2xl sm:text-3xl font-extrabold text-zinc-900 dark:text-zinc-100 tracking-tight">{{ $totalBeneficiariosGeral }}</p>
                <span class="text-[11px] font-semibold text-emerald-700 dark:text-emerald-400 mt-0.5 flex items-center gap-1">
                    Ver todos &rarr;
                </span>
            </div>
        </a>
    </div>

    {{-- 3. ATALHOS OPERACIONAIS (Elegante e discreto) --}}
    <div class="rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-4 sm:p-5 shadow-xs space-y-3">
        <div class="flex items-center justify-between">
            <span class="text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Atalhos Operacionais</span>
            <span class="text-xs text-zinc-400 dark:text-zinc-500 font-medium">Acesso rápido</span>
        </div>

        <div class="grid grid-cols-3 gap-2.5">
            <a 
                href="{{ route('retiradas.create') }}" 
                wire:navigate 
                class="rounded-xl p-2.5 sm:p-3 flex flex-col items-center justify-center text-center gap-1.5 border border-zinc-200 dark:border-zinc-700/80 bg-zinc-50 dark:bg-zinc-800/50 hover:bg-zinc-100 dark:hover:bg-zinc-800 hover:border-emerald-500/50 transition-colors cursor-pointer group"
            >
                <div class="p-1.5 rounded-lg bg-white dark:bg-zinc-700 text-zinc-700 dark:text-zinc-200 group-hover:text-emerald-700 dark:group-hover:text-emerald-400 transition-colors">
                    <flux:icon.plus class="w-4 h-4" />
                </div>
                <span class="text-xs font-semibold text-zinc-800 dark:text-zinc-200 leading-tight">Nova Retirada</span>
            </a>

            <a 
                href="{{ route('beneficiarios.create') }}" 
                wire:navigate 
                class="rounded-xl p-2.5 sm:p-3 flex flex-col items-center justify-center text-center gap-1.5 border border-zinc-200 dark:border-zinc-700/80 bg-zinc-50 dark:bg-zinc-800/50 hover:bg-zinc-100 dark:hover:bg-zinc-800 hover:border-emerald-500/50 transition-colors cursor-pointer group"
            >
                <div class="p-1.5 rounded-lg bg-white dark:bg-zinc-700 text-zinc-700 dark:text-zinc-200 group-hover:text-emerald-700 dark:group-hover:text-emerald-400 transition-colors">
                    <flux:icon.user-plus class="w-4 h-4" />
                </div>
                <span class="text-xs font-semibold text-zinc-800 dark:text-zinc-200 leading-tight">Novo Beneficiário</span>
            </a>

            <a 
                href="{{ route('produtos.create') }}" 
                wire:navigate 
                class="rounded-xl p-2.5 sm:p-3 flex flex-col items-center justify-center text-center gap-1.5 border border-zinc-200 dark:border-zinc-700/80 bg-zinc-50 dark:bg-zinc-800/50 hover:bg-zinc-100 dark:hover:bg-zinc-800 hover:border-emerald-500/50 transition-colors cursor-pointer group"
            >
                <div class="p-1.5 rounded-lg bg-white dark:bg-zinc-700 text-zinc-700 dark:text-zinc-200 group-hover:text-emerald-700 dark:group-hover:text-emerald-400 transition-colors">
                    <flux:icon.archive-box-arrow-down class="w-4 h-4" />
                </div>
                <span class="text-xs font-semibold text-zinc-800 dark:text-zinc-200 leading-tight">Novo Produto</span>
            </a>
        </div>
    </div>

    {{-- 4. GRÁFICOS & RANKINGS --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 sm:gap-6">
        
        {{-- Top Produtos --}}
        <div class="rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-4 sm:p-6 shadow-xs flex flex-col gap-4">
            <div class="flex items-center justify-between border-b border-zinc-100 dark:border-zinc-800 pb-3">
                <div class="flex items-center gap-2">
                    <div class="p-1.5 bg-zinc-100 dark:bg-zinc-800 rounded-lg text-zinc-700 dark:text-zinc-300">
                        <flux:icon.chart-bar class="w-4 h-4 sm:w-5 sm:h-5" />
                    </div>
                    <flux:heading size="lg" class="text-zinc-900 dark:text-zinc-100 font-bold text-sm sm:text-base">Produtos Mais Retirados</flux:heading>
                </div>
                <span class="text-[11px] font-semibold text-zinc-500 dark:text-zinc-400 bg-zinc-100 dark:bg-zinc-800 px-2 py-0.5 rounded-md">Ranking</span>
            </div>

            <div class="flex flex-col gap-3.5 mt-1">
                @forelse ($topProdutos as $index => $tp)
                    <div class="flex flex-col gap-1.5">
                        <div class="flex justify-between items-center text-xs sm:text-sm">
                            <div class="flex items-center gap-2">
                                <span class="w-4.5 h-4.5 rounded-md bg-zinc-100 dark:bg-zinc-800 text-[10px] font-bold text-zinc-600 dark:text-zinc-400 flex items-center justify-center shrink-0">
                                    {{ $index + 1 }}
                                </span>
                                <span class="font-semibold text-zinc-900 dark:text-zinc-100 truncate max-w-[170px] sm:max-w-xs">{{ $tp->produto->nome }}</span>
                            </div>
                            <span class="text-xs font-semibold text-zinc-700 dark:text-zinc-300 bg-zinc-100 dark:bg-zinc-800 px-2 py-0.5 rounded-md shrink-0">
                                {{ $tp->total }} {{ $tp->produto->unidade }}(s)
                            </span>
                        </div>
                        @php $max = $topProdutos->first()->total ?: 1; @endphp
                        <div class="h-2 rounded-full bg-zinc-100 dark:bg-zinc-800 overflow-hidden">
                            <div class="h-full rounded-full bg-emerald-600 dark:bg-emerald-500 transition-all duration-300" style="width: {{ round(($tp->total / $max) * 100) }}%"></div>
                        </div>
                    </div>
                @empty
                    <div class="py-8 flex flex-col items-center justify-center text-center gap-2">
                        <flux:icon.inbox class="w-8 h-8 text-zinc-400 dark:text-zinc-600" />
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 font-medium">Nenhuma retirada registrada neste período.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Retiradas por Dia --}}
        <div class="rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-4 sm:p-6 shadow-xs flex flex-col gap-4">
            <div class="flex items-center justify-between border-b border-zinc-100 dark:border-zinc-800 pb-3">
                <div class="flex items-center gap-2">
                    <div class="p-1.5 bg-zinc-100 dark:bg-zinc-800 rounded-lg text-zinc-700 dark:text-zinc-300">
                        <flux:icon.calendar-days class="w-4 h-4 sm:w-5 sm:h-5" />
                    </div>
                    <flux:heading size="lg" class="text-zinc-900 dark:text-zinc-100 font-bold text-sm sm:text-base">Retiradas por Dia</flux:heading>
                </div>
                <span class="text-[11px] font-semibold text-zinc-500 dark:text-zinc-400 bg-zinc-100 dark:bg-zinc-800 px-2 py-0.5 rounded-md">Atividade</span>
            </div>

            <div class="flex flex-col gap-3 mt-1 max-h-[320px] overflow-y-auto pr-1">
                @forelse ($retiradasPorDia as $dia => $total)
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-semibold text-zinc-600 dark:text-zinc-400 w-14 shrink-0">{{ \Carbon\Carbon::parse($dia)->format('d/m') }}</span>
                        <div class="flex-1">
                            @php $maxDia = $retiradasPorDia->max() ?: 1; @endphp
                            <div class="h-2.5 rounded-full bg-zinc-100 dark:bg-zinc-800 overflow-hidden flex items-center">
                                <div class="h-full rounded-full bg-emerald-600 dark:bg-emerald-500 transition-all duration-300" style="width: {{ round(($total / $maxDia) * 100) }}%"></div>
                            </div>
                        </div>
                        <span class="text-xs font-bold text-zinc-900 dark:text-zinc-100 w-7 text-right">{{ $total }}</span>
                    </div>
                @empty
                    <div class="py-8 flex flex-col items-center justify-center text-center gap-2">
                        <flux:icon.inbox class="w-8 h-8 text-zinc-400 dark:text-zinc-600" />
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 font-medium">Nenhuma retirada registrada neste período.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- 5. ÚLTIMAS RETIRADAS REGISTRADAS --}}
    <div class="rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-4 sm:p-6 shadow-xs flex flex-col gap-4">
        <div class="flex items-center justify-between border-b border-zinc-100 dark:border-zinc-800 pb-3">
            <div class="flex items-center gap-2">
                <div class="p-1.5 bg-zinc-100 dark:bg-zinc-800 rounded-lg text-zinc-700 dark:text-zinc-300">
                    <flux:icon.clock class="w-4 h-4 sm:w-5 sm:h-5" />
                </div>
                <flux:heading size="lg" class="text-zinc-900 dark:text-zinc-100 font-bold text-sm sm:text-base">Últimas Retiradas Registradas</flux:heading>
            </div>
            <a href="{{ route('retiradas.index') }}" class="text-xs font-semibold text-emerald-700 dark:text-emerald-400 hover:underline flex items-center gap-1" wire:navigate>
                <span>Ver todas</span>
                <span>&rarr;</span>
            </a>
        </div>

        <div class="divide-y divide-zinc-100 dark:divide-zinc-800/80">
            @forelse ($ultimasRetiradas as $r)
                <a 
                    href="{{ route('retiradas.edit', $r) }}" 
                    wire:navigate
                    class="flex items-center justify-between py-3 px-2 rounded-xl hover:bg-zinc-50 dark:hover:bg-zinc-800/60 active:bg-zinc-100 dark:active:bg-zinc-800 transition-colors group cursor-pointer"
                >
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 flex items-center justify-center font-bold shrink-0 text-sm border border-zinc-200 dark:border-zinc-700">
                            {{ mb_substr($r->beneficiario->nome, 0, 1) }}
                        </div>
                        <div class="min-w-0">
                            <p class="font-semibold text-xs sm:text-sm text-zinc-900 dark:text-zinc-100 truncate max-w-[180px] sm:max-w-md group-hover:text-emerald-700 dark:group-hover:text-emerald-400 transition-colors">
                                {{ $r->beneficiario->nome }}
                            </p>
                            <p class="text-[11px] font-medium text-zinc-500 dark:text-zinc-400 flex items-center gap-1.5 mt-0.5">
                                <span>{{ $r->data->format('d/m/Y') }}</span>
                                <span>&bull;</span>
                                <span class="text-zinc-700 dark:text-zinc-300 font-semibold">{{ $r->items_count ?? $r->items->count() }} item(ns)</span>
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-1 text-zinc-400 group-hover:text-zinc-700 dark:group-hover:text-zinc-200">
                        <span class="text-xs font-medium hidden sm:inline">Detalhes</span>
                        <flux:icon.chevron-right class="w-4 h-4" />
                    </div>
                </a>
            @empty
                <div class="py-8 flex flex-col items-center justify-center text-center gap-2">
                    <flux:icon.inbox class="w-8 h-8 text-zinc-400 dark:text-zinc-600" />
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 font-medium">Nenhuma retirada recente registrada.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

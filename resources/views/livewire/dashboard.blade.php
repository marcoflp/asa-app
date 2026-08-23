<div class="flex h-full w-full flex-1 flex-col gap-8">

    {{-- Cabeçalho + filtro de período --}}
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 bg-white dark:bg-zinc-900 rounded-2xl p-6 shadow-sm border border-zinc-200 dark:border-zinc-800">
        <div>
            <flux:heading size="xl" class="mb-1 text-zinc-900 dark:text-zinc-50 font-bold">Visão Geral</flux:heading>
            <flux:text class="text-zinc-700 dark:text-zinc-300 text-sm font-medium">
                Acompanhe o desempenho do período de <span class="font-bold text-emerald-700 dark:text-emerald-400">{{ $inicio->format('d/m/Y') }}</span> a <span class="font-bold text-emerald-700 dark:text-emerald-400">{{ $fim->format('d/m/Y') }}</span>
            </flux:text>
        </div>

        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4">
            <div id="tour-dashboard-periodo" class="overflow-x-auto pb-2 sm:pb-0">
                <flux:radio.group wire:model.live="periodo" variant="segmented" size="sm" class="flex-nowrap min-w-max shadow-sm">
                    <flux:radio value="hoje" label="Hoje" />
                    <flux:radio value="semanal" label="7 dias" />
                    <flux:radio value="mensal" label="Mês" />
                    <flux:radio value="trimestral" label="Trimestre" />
                    <flux:radio value="semestral" label="Semestre" />
                    <flux:radio value="personalizado" label="Personalizado" />
                </flux:radio.group>
            </div>

            @if($periodo === 'personalizado')
                <div class="flex items-center gap-2 bg-zinc-50 dark:bg-zinc-800/80 p-1 rounded-xl border border-zinc-200 dark:border-zinc-700" x-data="{
                    init() {
                        if (window.flatpickr) {
                            flatpickr($refs.start, { dateFormat: 'Y-m-d' });
                            flatpickr($refs.end, { dateFormat: 'Y-m-d' });
                        }
                    }
                }">
                    <flux:input x-ref="start" wire:model.live="dataInicio" type="text" placeholder="Início" size="sm" class="w-28 cursor-pointer font-bold text-zinc-900 dark:text-zinc-100 bg-white dark:bg-zinc-800" />
                    <span class="text-zinc-600 dark:text-zinc-400 text-xs font-bold px-0.5">até</span>
                    <flux:input x-ref="end" wire:model.live="dataFim" type="text" placeholder="Fim" size="sm" class="w-28 cursor-pointer font-bold text-zinc-900 dark:text-zinc-100 bg-white dark:bg-zinc-800" />
                </div>
            @endif

            <flux:button id="tour-dashboard-exportar" wire:click="gerarRelatorio" icon="document-text" variant="primary" size="sm" class="w-full sm:w-auto shadow-sm">
                Exportar Relatório
            </flux:button>
        </div>
    </div>

    {{-- Cards do período --}}
    <div id="tour-dashboard-kpis" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="group rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-6 shadow-sm transition-all hover:shadow-md hover:border-blue-300 dark:hover:border-blue-700 flex flex-col gap-4">
            <div class="flex items-center justify-between">
                <flux:text class="text-xs text-zinc-700 dark:text-zinc-300 uppercase tracking-wider font-bold">Retiradas no período</flux:text>
                <div class="p-2.5 bg-blue-100 dark:bg-blue-950/80 rounded-xl text-blue-700 dark:text-blue-300 group-hover:scale-110 transition-transform">
                    <flux:icon.arrow-trending-up class="w-5 h-5" />
                </div>
            </div>
            <p class="text-4xl font-black text-zinc-900 dark:text-zinc-50 tracking-tight">{{ $totalRetiradas }}</p>
        </div>

        <div class="group rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-6 shadow-sm transition-all hover:shadow-md hover:border-emerald-300 dark:hover:border-emerald-700 flex flex-col gap-4">
            <div class="flex items-center justify-between">
                <flux:text class="text-xs text-zinc-700 dark:text-zinc-300 uppercase tracking-wider font-bold">Beneficiários Atendidos</flux:text>
                <div class="p-2.5 bg-emerald-100 dark:bg-emerald-950/80 rounded-xl text-emerald-700 dark:text-emerald-300 group-hover:scale-110 transition-transform">
                    <flux:icon.users class="w-5 h-5" />
                </div>
            </div>
            <p class="text-4xl font-black text-zinc-900 dark:text-zinc-50 tracking-tight">{{ $totalBeneficiarios }}</p>
        </div>

        <div class="group rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-6 shadow-sm transition-all hover:shadow-md hover:border-amber-300 dark:hover:border-amber-700 flex flex-col gap-4">
            <div class="flex items-center justify-between">
                <flux:text class="text-xs text-zinc-700 dark:text-zinc-300 uppercase tracking-wider font-bold">Total de Itens Entregues</flux:text>
                <div class="p-2.5 bg-amber-100 dark:bg-amber-950/80 rounded-xl text-amber-800 dark:text-amber-300 group-hover:scale-110 transition-transform">
                    <flux:icon.cube class="w-5 h-5" />
                </div>
            </div>
            <p class="text-4xl font-black text-zinc-900 dark:text-zinc-50 tracking-tight">{{ $totalItens }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Top 5 produtos --}}
        <div class="rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-6 shadow-sm flex flex-col gap-5">
            <div class="flex items-center gap-2 border-b border-zinc-100 dark:border-zinc-800 pb-4">
                <flux:icon.chart-bar class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
                <flux:heading size="lg" class="text-zinc-900 dark:text-zinc-100 font-bold">Produtos mais retirados</flux:heading>
            </div>
            <div class="flex flex-col gap-4 mt-2">
                @forelse ($topProdutos as $tp)
                    <div class="flex flex-col gap-1.5">
                        <div class="flex justify-between items-center text-sm">
                            <span class="font-bold text-zinc-900 dark:text-zinc-100">{{ $tp->produto->nome }}</span>
                            <span class="text-xs font-bold text-zinc-800 dark:text-zinc-200 bg-zinc-100 dark:bg-zinc-800 px-2.5 py-1 rounded-md">{{ $tp->total }} {{ $tp->produto->unidade }}(s)</span>
                        </div>
                        @php $max = $topProdutos->first()->total ?: 1; @endphp
                        <div class="h-2.5 rounded-full bg-zinc-100 dark:bg-zinc-800 overflow-hidden">
                            <div class="h-full rounded-full bg-gradient-to-r from-indigo-600 to-indigo-400 transition-all duration-500" style="width: {{ round(($tp->total / $max) * 100) }}%"></div>
                        </div>
                    </div>
                @empty
                    <div class="py-8 flex flex-col items-center justify-center text-center gap-3">
                        <flux:icon.inbox class="w-10 h-10 text-zinc-400 dark:text-zinc-600" />
                        <flux:text class="text-zinc-600 dark:text-zinc-400 font-medium">Nenhuma retirada registrada neste período.</flux:text>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Retiradas por dia --}}
        <div class="rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-6 shadow-sm flex flex-col gap-5">
            <div class="flex items-center gap-2 border-b border-zinc-100 dark:border-zinc-800 pb-4">
                <flux:icon.calendar-days class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
                <flux:heading size="lg" class="text-zinc-900 dark:text-zinc-100 font-bold">Retiradas por dia</flux:heading>
            </div>
            <div class="flex flex-col gap-3 mt-2">
                @forelse ($retiradasPorDia as $dia => $total)
                    <div class="flex items-center gap-4">
                        <span class="text-sm font-bold text-zinc-700 dark:text-zinc-300 w-16 shrink-0">{{ \Carbon\Carbon::parse($dia)->format('d/m') }}</span>
                        <div class="flex-1">
                            @php $maxDia = $retiradasPorDia->max() ?: 1; @endphp
                            <div class="h-2.5 rounded-full bg-zinc-100 dark:bg-zinc-800 overflow-hidden flex items-center">
                                <div class="h-full rounded-full bg-gradient-to-r from-emerald-600 to-teal-400 transition-all duration-500" style="width: {{ round(($total / $maxDia) * 100) }}%"></div>
                            </div>
                        </div>
                        <span class="text-sm font-extrabold text-zinc-900 dark:text-zinc-100 w-8 text-right">{{ $total }}</span>
                    </div>
                @empty
                    <div class="py-8 flex flex-col items-center justify-center text-center gap-3">
                        <flux:icon.inbox class="w-10 h-10 text-zinc-400 dark:text-zinc-600" />
                        <flux:text class="text-zinc-600 dark:text-zinc-400 font-medium">Nenhuma retirada registrada neste período.</flux:text>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Últimas retiradas + totais gerais --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2 rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-6 shadow-sm flex flex-col gap-5">
            <div class="flex items-center justify-between border-b border-zinc-100 dark:border-zinc-800 pb-4">
                <div class="flex items-center gap-2">
                    <flux:icon.clock class="w-5 h-5 text-rose-600 dark:text-rose-400" />
                    <flux:heading size="lg" class="text-zinc-900 dark:text-zinc-100 font-bold">Últimas retiradas</flux:heading>
                </div>
                <flux:button href="{{ route('retiradas.index') }}" size="xs" variant="ghost" class="font-bold text-emerald-700 dark:text-emerald-400 hover:text-emerald-800" wire:navigate>
                    Ver histórico completo &rarr;
                </flux:button>
            </div>
            <div class="flex flex-col gap-2">
                @forelse ($ultimasRetiradas as $r)
                    <div class="flex items-center justify-between p-3.5 rounded-xl hover:bg-zinc-50 dark:hover:bg-zinc-800/70 transition-colors border border-transparent hover:border-zinc-200 dark:hover:border-zinc-700">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-300 flex items-center justify-center font-black shrink-0 shadow-inner text-base">
                                {{ mb_substr($r->beneficiario->nome, 0, 1) }}
                            </div>
                            <div>
                                <p class="font-bold text-sm text-zinc-900 dark:text-zinc-100">{{ $r->beneficiario->nome }}</p>
                                <p class="text-xs font-semibold text-zinc-600 dark:text-zinc-400 mt-0.5 flex items-center gap-1.5">
                                    <flux:icon.calendar class="w-3.5 h-3.5 text-zinc-500" />
                                    {{ $r->data->format('d/m/Y') }} &bull; {{ $r->items_count ?? $r->items->count() }} item(ns)
                                </p>
                            </div>
                        </div>
                        <flux:button href="{{ route('retiradas.edit', $r) }}" size="sm" variant="ghost" icon="chevron-right" class="text-zinc-500 hover:text-zinc-900 dark:hover:text-white" wire:navigate />
                    </div>
                @empty
                    <div class="py-6 flex flex-col items-center justify-center text-center gap-3">
                        <flux:text class="text-zinc-600 dark:text-zinc-400 font-medium">Nenhuma retirada no período.</flux:text>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="space-y-6">
            <div class="group rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-6 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden flex flex-col justify-between h-full min-h-[160px]">
                <div class="absolute -right-6 -top-6 text-zinc-100 dark:text-zinc-800/40 transition-transform group-hover:rotate-12 group-hover:scale-110 duration-500">
                    <flux:icon.users class="w-32 h-32" />
                </div>
                <div class="relative z-10 space-y-2">
                    <flux:text class="text-xs font-bold text-zinc-600 dark:text-zinc-400 uppercase tracking-widest">Base de Famílias Atendidas</flux:text>
                    <p class="text-5xl font-black text-zinc-900 dark:text-zinc-50">{{ $totalBeneficiariosGeral }}</p>
                </div>
                <div class="relative z-10 pt-4">
                    <flux:button href="{{ route('beneficiarios.index') }}" size="sm" variant="outline" class="w-full font-bold border-zinc-300 dark:border-zinc-700 bg-white/80 dark:bg-zinc-800/80" wire:navigate>
                        Gerenciar Famílias
                    </flux:button>
                </div>
            </div>
        </div>
    </div>
</div>

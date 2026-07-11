<div class="flex h-full w-full flex-1 flex-col gap-8">

    {{-- Cabeçalho + filtro de período --}}
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 bg-white dark:bg-neutral-900 rounded-2xl p-6 shadow-sm border border-neutral-100 dark:border-neutral-800">
        <div>
            <flux:heading size="xl" class="mb-1 text-neutral-900 dark:text-white">Visão Geral</flux:heading>
            <flux:text class="text-neutral-500 text-sm">
                Acompanhe o desempenho do período de <span class="font-semibold text-blue-600 dark:text-blue-400">{{ $inicio->format('d/m/Y') }}</span> a <span class="font-semibold text-blue-600 dark:text-blue-400">{{ $fim->format('d/m/Y') }}</span>
            </flux:text>
        </div>

        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4">
            <div class="overflow-x-auto pb-2 sm:pb-0">
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
                <div class="flex items-center gap-2" x-data="{
                    init() {
                        if (window.flatpickr) {
                            flatpickr($refs.start, { dateFormat: 'Y-m-d' });
                            flatpickr($refs.end, { dateFormat: 'Y-m-d' });
                        }
                    }
                }">
                    <flux:input x-ref="start" wire:model.live="dataInicio" type="text" placeholder="Início" size="sm" class="w-28 cursor-pointer bg-white" />
                    <span class="text-neutral-400 text-sm">até</span>
                    <flux:input x-ref="end" wire:model.live="dataFim" type="text" placeholder="Fim" size="sm" class="w-28 cursor-pointer bg-white" />
                </div>
            @endif

            <flux:button wire:click="gerarRelatorio" icon="document-text" variant="primary" size="sm" class="w-full sm:w-auto shadow-sm">
                Exportar Relatório
            </flux:button>
        </div>
    </div>

    {{-- Cards do período --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="group rounded-2xl border border-neutral-100 dark:border-neutral-800 bg-white dark:bg-neutral-900 p-6 shadow-sm transition-all hover:shadow-md hover:border-blue-100 dark:hover:border-blue-900/50 flex flex-col gap-4">
            <div class="flex items-center justify-between">
                <flux:text class="text-xs text-neutral-500 uppercase tracking-wider font-semibold">Retiradas no período</flux:text>
                <div class="p-2 bg-blue-50 dark:bg-blue-500/10 rounded-lg text-blue-600 dark:text-blue-400 group-hover:scale-110 transition-transform">
                    <flux:icon.arrow-trending-up class="w-5 h-5" />
                </div>
            </div>
            <p class="text-4xl font-black text-neutral-800 dark:text-neutral-100 tracking-tight">{{ $totalRetiradas }}</p>
        </div>

        <div class="group rounded-2xl border border-neutral-100 dark:border-neutral-800 bg-white dark:bg-neutral-900 p-6 shadow-sm transition-all hover:shadow-md hover:border-emerald-100 dark:hover:border-emerald-900/50 flex flex-col gap-4">
            <div class="flex items-center justify-between">
                <flux:text class="text-xs text-neutral-500 uppercase tracking-wider font-semibold">Beneficiários Atendidos</flux:text>
                <div class="p-2 bg-emerald-50 dark:bg-emerald-500/10 rounded-lg text-emerald-600 dark:text-emerald-400 group-hover:scale-110 transition-transform">
                    <flux:icon.users class="w-5 h-5" />
                </div>
            </div>
            <p class="text-4xl font-black text-neutral-800 dark:text-neutral-100 tracking-tight">{{ $totalBeneficiarios }}</p>
        </div>

        <div class="group rounded-2xl border border-neutral-100 dark:border-neutral-800 bg-white dark:bg-neutral-900 p-6 shadow-sm transition-all hover:shadow-md hover:border-amber-100 dark:hover:border-amber-900/50 flex flex-col gap-4">
            <div class="flex items-center justify-between">
                <flux:text class="text-xs text-neutral-500 uppercase tracking-wider font-semibold">Total de Itens Entregues</flux:text>
                <div class="p-2 bg-amber-50 dark:bg-amber-500/10 rounded-lg text-amber-600 dark:text-amber-400 group-hover:scale-110 transition-transform">
                    <flux:icon.cube class="w-5 h-5" />
                </div>
            </div>
            <p class="text-4xl font-black text-neutral-800 dark:text-neutral-100 tracking-tight">{{ $totalItens }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Top 5 produtos --}}
        <div class="rounded-2xl border border-neutral-100 dark:border-neutral-800 bg-white dark:bg-neutral-900 p-6 shadow-sm flex flex-col gap-5">
            <div class="flex items-center gap-2 border-b border-neutral-100 dark:border-neutral-800 pb-4">
                <flux:icon.chart-bar class="w-5 h-5 text-indigo-500" />
                <flux:heading size="lg" class="text-neutral-800 dark:text-neutral-200">Produtos mais retirados</flux:heading>
            </div>
            <div class="flex flex-col gap-4 mt-2">
                @forelse ($topProdutos as $tp)
                    <div class="flex flex-col gap-1.5">
                        <div class="flex justify-between items-center text-sm">
                            <span class="font-semibold text-neutral-700 dark:text-neutral-300">{{ $tp->produto->nome }}</span>
                            <span class="text-xs font-medium text-neutral-500 bg-neutral-100 dark:bg-neutral-800 px-2 py-0.5 rounded-md">{{ $tp->total }} {{ $tp->produto->unidade }}(s)</span>
                        </div>
                        @php $max = $topProdutos->first()->total ?: 1; @endphp
                        <div class="h-2.5 rounded-full bg-neutral-100 dark:bg-neutral-800 overflow-hidden">
                            <div class="h-full rounded-full bg-gradient-to-r from-indigo-500 to-indigo-400 transition-all duration-500" style="width: {{ round(($tp->total / $max) * 100) }}%"></div>
                        </div>
                    </div>
                @empty
                    <div class="py-8 flex flex-col items-center justify-center text-center gap-3">
                        <flux:icon.inbox class="w-10 h-10 text-neutral-300 dark:text-neutral-600" />
                        <flux:text class="text-neutral-500">Nenhuma retirada registrada neste período.</flux:text>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Retiradas por dia --}}
        <div class="rounded-2xl border border-neutral-100 dark:border-neutral-800 bg-white dark:bg-neutral-900 p-6 shadow-sm flex flex-col gap-5">
            <div class="flex items-center gap-2 border-b border-neutral-100 dark:border-neutral-800 pb-4">
                <flux:icon.calendar-days class="w-5 h-5 text-emerald-500" />
                <flux:heading size="lg" class="text-neutral-800 dark:text-neutral-200">Retiradas por dia</flux:heading>
            </div>
            <div class="flex flex-col gap-3 mt-2">
                @forelse ($retiradasPorDia as $dia => $total)
                    <div class="flex items-center gap-4">
                        <span class="text-sm font-medium text-neutral-500 w-16 shrink-0">{{ \Carbon\Carbon::parse($dia)->format('d/m') }}</span>
                        <div class="flex-1">
                            @php $maxDia = $retiradasPorDia->max() ?: 1; @endphp
                            <div class="h-2.5 rounded-full bg-neutral-100 dark:bg-neutral-800 overflow-hidden flex items-center">
                                <div class="h-full rounded-full bg-gradient-to-r from-emerald-500 to-teal-400 transition-all duration-500" style="width: {{ round(($total / $maxDia) * 100) }}%"></div>
                            </div>
                        </div>
                        <span class="text-sm font-bold text-neutral-700 dark:text-neutral-300 w-8 text-right">{{ $total }}</span>
                    </div>
                @empty
                    <div class="py-8 flex flex-col items-center justify-center text-center gap-3">
                        <flux:icon.inbox class="w-10 h-10 text-neutral-300 dark:text-neutral-600" />
                        <flux:text class="text-neutral-500">Nenhuma retirada registrada neste período.</flux:text>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Últimas retiradas + totais gerais --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2 rounded-2xl border border-neutral-100 dark:border-neutral-800 bg-white dark:bg-neutral-900 p-6 shadow-sm flex flex-col gap-5">
            <div class="flex items-center justify-between border-b border-neutral-100 dark:border-neutral-800 pb-4">
                <div class="flex items-center gap-2">
                    <flux:icon.clock class="w-5 h-5 text-rose-500" />
                    <flux:heading size="lg" class="text-neutral-800 dark:text-neutral-200">Últimas retiradas</flux:heading>
                </div>
                <flux:button href="{{ route('retiradas.index') }}" size="xs" variant="ghost" wire:navigate>
                    Ver histórico
                </flux:button>
            </div>
            <div class="flex flex-col gap-2">
                @forelse ($ultimasRetiradas as $r)
                    <div class="flex items-center justify-between p-3 rounded-xl hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition-colors border border-transparent hover:border-neutral-100 dark:hover:border-neutral-800">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-neutral-100 to-neutral-200 dark:from-neutral-800 dark:to-neutral-700 flex items-center justify-center text-neutral-600 dark:text-neutral-300 font-bold shrink-0 shadow-inner">
                                {{ mb_substr($r->beneficiario->nome, 0, 1) }}
                            </div>
                            <div>
                                <p class="font-semibold text-sm text-neutral-800 dark:text-neutral-200">{{ $r->beneficiario->nome }}</p>
                                <p class="text-xs font-medium text-neutral-500 mt-0.5 flex items-center gap-1">
                                    <flux:icon.calendar class="w-3 h-3" />
                                    {{ $r->data->format('d/m/Y') }} &bull; {{ $r->items_count ?? $r->items->count() }} item(ns)
                                </p>
                            </div>
                        </div>
                        <flux:button href="{{ route('retiradas.edit', $r) }}" size="sm" variant="ghost" icon="chevron-right" class="text-neutral-400 hover:text-neutral-800 dark:hover:text-neutral-200" wire:navigate />
                    </div>
                @empty
                    <div class="py-6 flex flex-col items-center justify-center text-center gap-3">
                        <flux:text class="text-neutral-500">Nenhuma retirada no período.</flux:text>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="space-y-6">
            <div class="group rounded-2xl border border-neutral-100 dark:border-neutral-800 bg-white dark:bg-neutral-900 p-6 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden flex flex-col justify-between h-full min-h-[160px]">
                <div class="absolute -right-6 -top-6 text-neutral-100 dark:text-neutral-800/50 transition-transform group-hover:rotate-12 group-hover:scale-110 duration-500">
                    <flux:icon.users class="w-32 h-32" />
                </div>
                <div class="relative z-10 space-y-2">
                    <flux:text class="text-xs font-bold text-neutral-500 uppercase tracking-widest">Base de Beneficiários</flux:text>
                    <p class="text-5xl font-black text-neutral-800 dark:text-neutral-100">{{ $totalBeneficiariosGeral }}</p>
                </div>
                <div class="relative z-10 pt-4">
                    <flux:button href="{{ route('beneficiarios.index') }}" size="sm" variant="outline" class="w-full bg-white/50 backdrop-blur-sm" wire:navigate>
                        Gerenciar Beneficiários
                    </flux:button>
                </div>
            </div>

            
        </div>
    </div>
</div>

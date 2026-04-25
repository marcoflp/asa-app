<div class="flex h-full w-full flex-1 flex-col gap-6">

    {{-- Cabeçalho + filtro de período --}}
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <flux:heading size="xl">Dashboard</flux:heading>
            <flux:text class="text-neutral-500 text-sm">
                {{ $inicio->format('d/m/Y') }} — {{ $fim->format('d/m/Y') }}
            </flux:text>
        </div>

        <div class="flex items-center gap-3">
            <flux:radio.group wire:model.live="periodo" variant="segmented" size="sm">
                <flux:radio value="hoje" label="Hoje" />
                <flux:radio value="semanal" label="Uma semana" />
                <flux:radio value="mensal" label="Um mês" />
                <flux:radio value="trimestral" label="Trimestral" />
                <flux:radio value="semestral" label="Semestral" />
            </flux:radio.group>

            <flux:button wire:click="gerarRelatorio" icon="document-text" variant="ghost" size="sm">
                Relatório PDF
            </flux:button>
        </div>
    </div>

    {{-- Cards do período --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 p-5 space-y-1">
            <flux:text class="text-sm text-neutral-500">Retiradas no período</flux:text>
            <p class="text-3xl font-bold">{{ $totalRetiradas }}</p>
        </div>
        <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 p-5 space-y-1">
            <flux:text class="text-sm text-neutral-500">Beneficiários atendidos</flux:text>
            <p class="text-3xl font-bold">{{ $totalBeneficiarios }}</p>
        </div>
        <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 p-5 space-y-1">
            <flux:text class="text-sm text-neutral-500">Total de itens entregues</flux:text>
            <p class="text-3xl font-bold">{{ $totalItens }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- Top 5 produtos --}}
        <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 p-5 space-y-3">
            <flux:heading size="lg">Produtos mais retirados</flux:heading>
            @forelse ($topProdutos as $tp)
                <div class="flex items-center gap-3">
                    <div class="flex-1">
                        <div class="flex justify-between text-sm mb-1">
                            <span class="font-medium">{{ $tp->produto->nome }}</span>
                            <span class="text-neutral-500">{{ $tp->total }} {{ $tp->produto->unidade }}</span>
                        </div>
                        @php $max = $topProdutos->first()->total ?: 1; @endphp
                        <div class="h-2 rounded-full bg-neutral-100 dark:bg-neutral-700">
                            <div class="h-2 rounded-full bg-blue-500" style="width: {{ round(($tp->total / $max) * 100) }}%"></div>
                        </div>
                    </div>
                </div>
            @empty
                <flux:text class="text-neutral-500">Nenhuma retirada no período.</flux:text>
            @endforelse
        </div>

        {{-- Retiradas por dia --}}
        <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 p-5 space-y-3">
            <flux:heading size="lg">Retiradas por dia</flux:heading>
            @forelse ($retiradasPorDia as $dia => $total)
                <div class="flex items-center gap-3">
                    <span class="text-sm text-neutral-500 w-24 shrink-0">{{ \Carbon\Carbon::parse($dia)->format('d/m') }}</span>
                    <div class="flex-1">
                        @php $maxDia = $retiradasPorDia->max() ?: 1; @endphp
                        <div class="h-2 rounded-full bg-neutral-100 dark:bg-neutral-700">
                            <div class="h-2 rounded-full bg-emerald-500" style="width: {{ round(($total / $maxDia) * 100) }}%"></div>
                        </div>
                    </div>
                    <span class="text-sm font-medium w-6 text-right">{{ $total }}</span>
                </div>
            @empty
                <flux:text class="text-neutral-500">Nenhuma retirada no período.</flux:text>
            @endforelse
        </div>

    </div>

    {{-- Últimas retiradas + totais gerais --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <div class="md:col-span-2 rounded-xl border border-neutral-200 dark:border-neutral-700 p-5 space-y-3">
            <flux:heading size="lg">Últimas retiradas</flux:heading>
            <div class="divide-y divide-neutral-100 dark:divide-neutral-700">
                @forelse ($ultimasRetiradas as $r)
                    <div class="flex items-center justify-between py-2.5">
                        <div>
                            <p class="font-medium text-sm">{{ $r->beneficiario->nome }}</p>
                            <p class="text-xs text-neutral-500">{{ $r->data->format('d/m/Y') }}</p>
                        </div>
                        <flux:button href="{{ route('retiradas.edit', $r) }}" size="sm" variant="ghost" icon="arrow-right" wire:navigate />
                    </div>
                @empty
                    <flux:text class="text-neutral-500">Nenhuma retirada no período.</flux:text>
                @endforelse
            </div>
        </div>

        <div class="space-y-4">
            <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 p-5 space-y-1">
                <flux:text class="text-sm text-neutral-500">Total de beneficiários</flux:text>
                <p class="text-3xl font-bold">{{ $totalBeneficiariosGeral }}</p>
                <flux:button href="{{ route('beneficiarios.index') }}" size="sm" variant="ghost" wire:navigate class="mt-1">
                    Ver todos
                </flux:button>
            </div>
            <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 p-5 space-y-1">
                <flux:text class="text-sm text-neutral-500">Produtos cadastrados</flux:text>
                <p class="text-3xl font-bold">{{ $totalProdutosGeral }}</p>
            </div>
        </div>

    </div>

</div>

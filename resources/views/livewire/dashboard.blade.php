@php
    $hora = now()->hour;
    $saudacao = $hora < 12 ? 'Bom dia' : ($hora < 18 ? 'Boa tarde' : 'Boa noite');
    $primeiroNome = explode(' ', trim(auth()->user()->name))[0];
    $ehSabado = now()->isSaturday();

    $nomesPeriodo = [
        'hoje' => 'Hoje',
        'semanal' => '7 dias',
        'mensal' => '30 dias',
        'trimestral' => '3 meses',
        'semestral' => '6 meses',
        'personalizado' => 'Escolher datas',
    ];
    $comparacao = $periodo === 'hoje' ? 'ontem' : 'período anterior';

    $kpis = [
        ['chave' => 'familias', 'rotulo' => 'Famílias atendidas', 'icone' => 'home'],
        ['chave' => 'pessoas', 'rotulo' => 'Pessoas alcançadas', 'icone' => 'users'],
        ['chave' => 'retiradas', 'rotulo' => 'Retiradas', 'icone' => 'clipboard-document-check'],
        ['chave' => 'itens', 'rotulo' => 'Itens entregues', 'icone' => 'cube'],
    ];

    $pontos = $serie['pontos'];
    $maxSerie = max(1, collect($pontos)->max('total'));
    $passoRotulo = max(1, (int) ceil(count($pontos) / 10));
    $maxCategoria = max(1, (int) $itensPorCategoria->max('total'));
    $maxProduto = max(1, (int) $topProdutos->max('total'));
    $maxBairro = max(1, (int) collect($bairros)->max());

    $quando = fn (int $dias) => $dias === 0 ? 'hoje' : ($dias === 1 ? 'ontem' : "há {$dias} dias");
@endphp

<div class="painel flex h-full w-full flex-1 flex-col gap-8">
    <style>
        .painel { --viz-barra: #16774f; --viz-apagada: #d4d4d8; }
        .dark .painel { --viz-barra: #2fa271; --viz-apagada: #3f3f46; }
    </style>

    {{-- ===================== CABEÇALHO ===================== --}}
    <header class="flex flex-col lg:flex-row lg:items-end justify-between gap-4">
        <div>
            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400 first-letter:uppercase">{{ now()->translatedFormat('l, d \d\e F') }}</p>
            <h1 class="mt-1 text-2xl sm:text-3xl font-bold tracking-tight text-zinc-900 dark:text-white">{{ $saudacao }}, {{ $primeiroNome }}</h1>
            @if ($ehSabado)
                <p class="mt-2 inline-flex items-center gap-2 rounded-full bg-emerald-50 dark:bg-emerald-950/50 px-3 py-1 text-sm font-semibold text-emerald-800 dark:text-emerald-300">
                    <flux:icon.clock variant="micro" /> Hoje tem atendimento, das 15h às 16h
                </p>
            @endif
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 sm:w-auto">
            <flux:button href="{{ route('retiradas.create') }}" wire:navigate variant="primary" icon="plus" class="h-12 px-5 text-base">Registrar retirada</flux:button>
            <flux:button href="{{ route('beneficiarios.create') }}" wire:navigate icon="user-plus" class="h-12 px-5 text-base">Cadastrar família</flux:button>
        </div>
    </header>

    {{-- ===================== HOJE + PRECISA DE ATENÇÃO ===================== --}}
    <section aria-labelledby="titulo-atencao" class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        {{-- Atendidas hoje --}}
        <div class="rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-5 flex flex-col">
            <div class="flex items-baseline justify-between gap-3">
                <h2 class="text-base font-semibold text-zinc-900 dark:text-white">Atendidas hoje</h2>
                <a href="{{ route('retiradas.index') }}" wire:navigate class="text-sm font-medium text-emerald-700 dark:text-emerald-400 hover:underline">Ver retiradas</a>
            </div>
            <p class="mt-3 text-5xl font-bold tracking-tight text-zinc-900 dark:text-white tabular-nums">{{ $hoje->count() }}</p>
            <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $hoje->count() === 1 ? 'família' : 'famílias' }}</p>

            @if ($hoje->isEmpty())
                <p class="mt-4 text-sm text-zinc-500 dark:text-zinc-400">Nenhuma retirada registrada hoje ainda.</p>
            @else
                <ul class="mt-4 divide-y divide-zinc-100 dark:divide-zinc-800 -mx-1" x-data="{ todas: false }">
                    @foreach ($hoje as $r)
                        <li @if ($loop->index >= 5) x-show="todas" x-cloak @endif>
                            <a href="{{ route('retiradas.edit', $r) }}" wire:navigate class="flex items-center justify-between gap-3 rounded-lg px-1 py-2 hover:bg-zinc-50 dark:hover:bg-zinc-800">
                                <span class="truncate text-sm font-medium text-zinc-800 dark:text-zinc-200">{{ $r->beneficiario?->nome ?? 'Beneficiário removido' }}</span>
                                <span class="shrink-0 text-xs text-zinc-500 tabular-nums">{{ $r->items_count }} {{ $r->items_count === 1 ? 'item' : 'itens' }}</span>
                            </a>
                        </li>
                    @endforeach
                    @if ($hoje->count() > 5)
                        <li class="pt-2"><button type="button" @click="todas = !todas" class="text-sm font-medium text-emerald-700 dark:text-emerald-400 hover:underline" x-text="todas ? 'Mostrar menos' : 'Mostrar todas ({{ $hoje->count() }})'"></button></li>
                    @endif
                </ul>
            @endif
        </div>

        {{-- Precisa de atenção --}}
        <div class="lg:col-span-2 rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-5">
            <h2 id="titulo-atencao" class="text-base font-semibold text-zinc-900 dark:text-white">Precisa de atenção</h2>

            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-6">
                {{-- Estoque acabando --}}
                <div>
                    <div class="flex items-center gap-2">
                        <flux:icon.archive-box class="size-5 {{ $estoqueBaixo->isNotEmpty() ? 'text-amber-600 dark:text-amber-400' : 'text-zinc-400' }}" />
                        <h3 class="text-sm font-semibold text-zinc-800 dark:text-zinc-200">Estoque acabando</h3>
                    </div>
                    @if ($estoqueBaixo->isEmpty())
                        <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">Nenhum produto controlado com menos de 10.</p>
                    @else
                        <ul class="mt-2 space-y-1.5">
                            @foreach ($estoqueBaixo->take(4) as $p)
                                <li class="flex items-center justify-between gap-3 text-sm">
                                    <a href="{{ route('produtos.edit', $p) }}" wire:navigate class="truncate text-zinc-700 dark:text-zinc-300 hover:underline">{{ $p->nome }}</a>
                                    <span class="shrink-0 font-semibold tabular-nums {{ $p->estoque <= 0 ? 'text-red-700 dark:text-red-400' : 'text-zinc-700 dark:text-zinc-300' }}">
                                        {{ $p->estoque <= 0 ? 'acabou' : $p->estoque.' '.\App\Livewire\Dashboard::unidade($p->estoque, $p->unidade) }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                        <a href="{{ route('produtos.index', ['statusEstoque' => 'baixo_estoque']) }}" wire:navigate class="mt-2 inline-block text-sm font-medium text-emerald-700 dark:text-emerald-400 hover:underline">
                            {{ $estoqueBaixo->count() > 4 ? 'Ver os '.$estoqueBaixo->count().' produtos' : 'Ver produtos' }}
                        </a>
                    @endif
                </div>

                {{-- Famílias que não vêm há tempo --}}
                <div x-data="{ todas: false }">
                    <div class="flex items-center gap-2">
                        <flux:icon.user-minus class="size-5 {{ $sumidas->isNotEmpty() ? 'text-amber-600 dark:text-amber-400' : 'text-zinc-400' }}" />
                        <h3 class="text-sm font-semibold text-zinc-800 dark:text-zinc-200">Não vêm há mais de 2 meses</h3>
                    </div>
                    @if ($sumidas->isEmpty())
                        <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">Todas as famílias vieram nos últimos 2 meses.</p>
                    @else
                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Vale ligar e saber se está tudo bem.</p>
                        <ul class="mt-2 space-y-1.5">
                            @foreach ($sumidas as $b)
                                <li @if ($loop->index >= 4) x-show="todas" x-cloak @endif class="flex items-center justify-between gap-3 text-sm">
                                    <a href="{{ route('beneficiarios.edit', $b) }}" wire:navigate class="truncate text-zinc-700 dark:text-zinc-300 hover:underline">{{ $b->nome }}</a>
                                    <span class="shrink-0 text-zinc-500 tabular-nums">{{ $quando($b->dias) }}</span>
                                </li>
                            @endforeach
                        </ul>
                        @if ($sumidas->count() > 4)
                            <button type="button" @click="todas = !todas" class="mt-2 text-sm font-medium text-emerald-700 dark:text-emerald-400 hover:underline"
                                x-text="todas ? 'Mostrar menos' : 'Ver as {{ $sumidas->count() }} famílias'"></button>
                        @endif
                    @endif
                </div>

                {{-- Cadastros sem documentos --}}
                <div>
                    <div class="flex items-center gap-2">
                        <flux:icon.identification class="size-5 {{ $semDocumentos > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-zinc-400' }}" />
                        <h3 class="text-sm font-semibold text-zinc-800 dark:text-zinc-200">Cadastros sem nenhum documento</h3>
                    </div>
                    @if ($semDocumentos === 0)
                        <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">Todos os cadastros têm ao menos uma foto de documento.</p>
                    @else
                        <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
                            <span class="text-2xl font-bold text-zinc-900 dark:text-white tabular-nums">{{ $semDocumentos }}</span>
                            {{ $semDocumentos === 1 ? 'família ainda sem' : 'famílias ainda sem' }} foto de documento. Aproveite quando vierem buscar a próxima retirada.
                        </p>
                        <a href="{{ route('beneficiarios.index', ['documentos' => 'sem_documentos']) }}" wire:navigate class="mt-1 inline-block text-sm font-medium text-emerald-700 dark:text-emerald-400 hover:underline">Ver quais são</a>
                    @endif
                </div>

                {{-- Urgentes no site --}}
                <div>
                    <div class="flex items-center gap-2">
                        <flux:icon.globe-alt class="size-5 text-zinc-400" />
                        <h3 class="text-sm font-semibold text-zinc-800 dark:text-zinc-200">Pedindo doação no site</h3>
                    </div>
                    @if ($urgentes->isEmpty())
                        <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">Nenhum produto marcado como urgente. O site mostra “Toda doação é bem-vinda”.</p>
                    @else
                        <p class="mt-2 text-sm text-zinc-700 dark:text-zinc-300">{{ $urgentes->join(', ', ' e ') }}.</p>
                    @endif
                    <a href="{{ route('produtos.index') }}" wire:navigate class="mt-1 inline-block text-sm font-medium text-emerald-700 dark:text-emerald-400 hover:underline">Escolher produtos urgentes</a>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== IMPACTO ===================== --}}
    <section aria-labelledby="titulo-impacto" class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-3">
            <div>
                <h2 id="titulo-impacto" class="text-xl font-bold tracking-tight text-zinc-900 dark:text-white">Impacto da ASA</h2>
                <p class="text-sm text-zinc-500 dark:text-zinc-400 tabular-nums">De {{ $inicio->format('d/m/Y') }} a {{ $fim->format('d/m/Y') }}</p>
            </div>
            <flux:button id="tour-dashboard-exportar" wire:click="gerarRelatorio" icon="document-arrow-down" size="sm">Baixar relatório em PDF</flux:button>
        </div>

        {{-- Filtro de período: uma linha, acima dos gráficos --}}
        <div id="tour-dashboard-periodo" class="flex flex-wrap items-center gap-2" role="group" aria-label="Período">
            @foreach ($nomesPeriodo as $chave => $rotulo)
                <button type="button" wire:click="$set('periodo', '{{ $chave }}')" aria-pressed="{{ $periodo === $chave ? 'true' : 'false' }}"
                    class="h-9 px-3.5 rounded-full text-sm font-medium border transition-colors {{ $periodo === $chave ? 'bg-zinc-900 text-white border-zinc-900 dark:bg-white dark:text-zinc-900 dark:border-white' : 'bg-white dark:bg-zinc-900 text-zinc-700 dark:text-zinc-300 border-zinc-200 dark:border-zinc-700 hover:border-zinc-400' }}">
                    {{ $rotulo }}
                </button>
            @endforeach
            @if ($periodo === 'personalizado')
                <div class="flex items-center gap-2 w-full sm:w-auto sm:ml-2">
                    <label class="sr-only" for="dash-de">De</label>
                    <input id="dash-de" type="date" wire:model.live="dataInicio" max="{{ now()->toDateString() }}" class="h-9 rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-2 text-sm text-zinc-900 dark:text-zinc-100" />
                    <span class="text-sm text-zinc-500">até</span>
                    <label class="sr-only" for="dash-ate">Até</label>
                    <input id="dash-ate" type="date" wire:model.live="dataFim" max="{{ now()->toDateString() }}" class="h-9 rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-2 text-sm text-zinc-900 dark:text-zinc-100" />
                </div>
            @endif
        </div>

        <div wire:loading.class="opacity-60" wire:target="periodo, dataInicio, dataFim, filtrarCategoria" class="space-y-4 transition-opacity">
            {{-- KPIs com comparação --}}
            <div id="tour-dashboard-kpis" class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                @foreach ($kpis as $k)
                    @php $v = $variacoes[$k['chave']]; @endphp
                    <div class="rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-4 sm:p-5">
                        <div class="flex items-center gap-2 text-sm font-medium text-zinc-600 dark:text-zinc-400">
                            <flux:icon :icon="$k['icone']" class="size-4" /> {{ $k['rotulo'] }}
                        </div>
                        <p class="mt-2 text-3xl sm:text-4xl font-bold tracking-tight text-zinc-900 dark:text-white tabular-nums">{{ number_format($resumo[$k['chave']], 0, ',', '.') }}</p>
                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                            @if ($v === null)
                                sem dados do {{ $comparacao }}
                            @elseif ($v === 0)
                                igual ao {{ $comparacao }}
                            @else
                                <span class="inline-flex items-center gap-0.5 font-semibold text-zinc-800 dark:text-zinc-200">
                                    <flux:icon :icon="$v > 0 ? 'arrow-up' : 'arrow-down'" variant="micro" class="size-3.5" />{{ abs($v) }}%
                                </span>
                                {{ $v > 0 ? 'a mais' : 'a menos' }} que no {{ $comparacao }}
                            @endif
                        </p>
                    </div>
                @endforeach
            </div>

            {{-- Retiradas ao longo do tempo --}}
            <div class="rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-4 sm:p-6" x-data="{ dica: null, tabela: false }">
                <div class="flex items-baseline justify-between gap-3">
                    <h3 class="text-base font-semibold text-zinc-900 dark:text-white">Retiradas {{ $serie['semanal'] ? 'por semana' : 'por dia' }}</h3>
                    <button type="button" @click="tabela = !tabela" class="text-sm font-medium text-zinc-600 dark:text-zinc-400 hover:underline" x-text="tabela ? 'Ver gráfico' : 'Ver como tabela'"></button>
                </div>

                @if (collect($pontos)->sum('total') === 0)
                    <p class="py-12 text-center text-sm text-zinc-500 dark:text-zinc-400">Nenhuma retirada neste período.</p>
                @else
                    <div x-show="!tabela" class="mt-4">
                        <div class="relative h-48 sm:h-56">
                            {{-- linha de referência do maior valor --}}
                            <div class="absolute inset-x-0 top-0 border-t border-dashed border-zinc-200 dark:border-zinc-700"></div>
                            <span class="absolute -top-2.5 right-0 bg-white dark:bg-zinc-900 pl-1 text-[11px] text-zinc-500 tabular-nums">{{ $maxSerie }}</span>
                            <div class="absolute inset-x-0 bottom-0 border-t border-zinc-300 dark:border-zinc-600"></div>

                            <div class="absolute inset-0 flex items-end gap-[2px]">
                                @foreach ($pontos as $i => $pt)
                                    <button type="button" class="group relative flex-1 h-full flex items-end focus:outline-none"
                                        @mouseenter="dica = {{ $i }}" @mouseleave="dica = null" @focus="dica = {{ $i }}" @blur="dica = null"
                                        aria-label="{{ $pt['dica'] }}: {{ $pt['total'] }} {{ $pt['total'] === 1 ? 'retirada' : 'retiradas' }}">
                                        <span class="block w-full rounded-t-[4px] transition-opacity"
                                            :class="dica !== null && dica !== {{ $i }} ? 'opacity-40' : ''"
                                            style="height: {{ $pt['total'] ? max(2, round($pt['total'] / $maxSerie * 100)) : 0 }}%; background: var(--viz-barra)"></span>
                                        <span x-show="dica === {{ $i }}" x-cloak
                                            class="pointer-events-none absolute bottom-full mb-2 z-10 whitespace-nowrap rounded-lg bg-zinc-900 dark:bg-white px-2.5 py-1.5 text-xs text-white dark:text-zinc-900 shadow-lg
                                                   {{ $i < count($pontos) / 3 ? 'left-0' : ($i > count($pontos) * 2 / 3 ? 'right-0' : 'left-1/2 -translate-x-1/2') }}">
                                            <span class="block font-semibold first-letter:uppercase">{{ $pt['dica'] }}</span>
                                            <span class="tabular-nums">{{ $pt['total'] }} {{ $pt['total'] === 1 ? 'retirada' : 'retiradas' }}</span>
                                        </span>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                        <div class="mt-2 flex gap-[2px]" aria-hidden="true">
                            @foreach ($pontos as $i => $pt)
                                <span class="flex-1 text-center text-[11px] text-zinc-500 tabular-nums {{ $i % $passoRotulo === 0 ? '' : 'invisible' }}">{{ $pt['rotulo'] }}</span>
                            @endforeach
                        </div>
                    </div>

                    <div x-show="tabela" x-cloak class="mt-4 max-h-72 overflow-y-auto">
                        <table class="w-full text-sm">
                            <thead class="text-left text-zinc-500"><tr><th class="py-1.5 font-medium">{{ $serie['semanal'] ? 'Semana' : 'Dia' }}</th><th class="py-1.5 font-medium text-right">Retiradas</th></tr></thead>
                            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                                @foreach ($pontos as $pt)
                                    <tr><td class="py-1.5 text-zinc-700 dark:text-zinc-300 first-letter:uppercase">{{ $pt['dica'] }}</td><td class="py-1.5 text-right font-semibold tabular-nums text-zinc-900 dark:text-white">{{ $pt['total'] }}</td></tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                {{-- Itens por categoria (clicável) --}}
                <div class="rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-4 sm:p-6">
                    <h3 class="text-base font-semibold text-zinc-900 dark:text-white">Itens entregues por categoria</h3>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Toque numa categoria para ver os produtos dela.</p>
                    @if ($itensPorCategoria->isEmpty())
                        <p class="py-10 text-center text-sm text-zinc-500 dark:text-zinc-400">Nenhum item entregue neste período.</p>
                    @else
                        <ul class="mt-4 space-y-1">
                            @foreach ($itensPorCategoria as $c)
                                @php $ativa = $categoria === $c->categoria; $apagada = $categoria !== '' && ! $ativa; @endphp
                                <li>
                                    <button type="button" wire:click="filtrarCategoria(@js($c->categoria))" aria-pressed="{{ $ativa ? 'true' : 'false' }}"
                                        class="w-full text-left rounded-lg px-2 py-2 -mx-2 hover:bg-zinc-50 dark:hover:bg-zinc-800 {{ $ativa ? 'bg-zinc-50 dark:bg-zinc-800' : '' }}">
                                        <span class="flex items-baseline justify-between gap-3 text-sm">
                                            <span class="font-medium {{ $apagada ? 'text-zinc-400 dark:text-zinc-500' : 'text-zinc-800 dark:text-zinc-200' }}">{{ $c->categoria }}</span>
                                            <span class="tabular-nums {{ $apagada ? 'text-zinc-400 dark:text-zinc-500' : 'font-semibold text-zinc-900 dark:text-white' }}">{{ number_format($c->total, 0, ',', '.') }}</span>
                                        </span>
                                        <span class="mt-1.5 block h-2.5 rounded-full bg-zinc-100 dark:bg-zinc-800">
                                            <span class="block h-full rounded-full" style="width: {{ round($c->total / $maxCategoria * 100) }}%; background: var({{ $apagada ? '--viz-apagada' : '--viz-barra' }})"></span>
                                        </span>
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                {{-- Produtos mais retirados --}}
                <div class="rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-4 sm:p-6">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h3 class="text-base font-semibold text-zinc-900 dark:text-white">Produtos mais retirados</h3>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $categoria ? 'Só da categoria '.$categoria : 'Todas as categorias' }}</p>
                        </div>
                        @if ($categoria)
                            <flux:button size="xs" variant="ghost" icon="x-mark" wire:click="filtrarCategoria(@js($categoria))">Ver todas</flux:button>
                        @endif
                    </div>
                    @if ($topProdutos->isEmpty())
                        <p class="py-10 text-center text-sm text-zinc-500 dark:text-zinc-400">Nenhum produto retirado neste período.</p>
                    @else
                        <ol class="mt-4 space-y-3">
                            @foreach ($topProdutos as $tp)
                                <li>
                                    <div class="flex items-baseline justify-between gap-3 text-sm">
                                        <span class="truncate font-medium text-zinc-800 dark:text-zinc-200">{{ $tp->produto?->nome ?? 'Produto removido' }}</span>
                                        <span class="shrink-0 font-semibold tabular-nums text-zinc-900 dark:text-white">{{ number_format($tp->total, 0, ',', '.') }} <span class="font-normal text-zinc-500">{{ \App\Livewire\Dashboard::unidade($tp->total, $tp->produto?->unidade) }}</span></span>
                                    </div>
                                    <div class="mt-1.5 h-2.5 rounded-full bg-zinc-100 dark:bg-zinc-800">
                                        <div class="h-full rounded-full" style="width: {{ round($tp->total / $maxProduto * 100) }}%; background: var(--viz-barra)"></div>
                                    </div>
                                </li>
                            @endforeach
                        </ol>
                    @endif
                </div>
            </div>

            {{-- Bairros --}}
            <div class="rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-4 sm:p-6">
                <h3 class="text-base font-semibold text-zinc-900 dark:text-white">De onde vêm as famílias atendidas</h3>
                <p class="text-xs text-zinc-500 dark:text-zinc-400">Famílias atendidas no período, por bairro.</p>
                @if (collect($bairros)->isEmpty())
                    <p class="py-10 text-center text-sm text-zinc-500 dark:text-zinc-400">Nenhuma família atendida neste período.</p>
                @else
                    <ul class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-3">
                        @foreach ($bairros as $nomeBairro => $total)
                            <li>
                                <div class="flex items-baseline justify-between gap-3 text-sm">
                                    <span class="truncate font-medium text-zinc-800 dark:text-zinc-200">{{ $nomeBairro }}</span>
                                    <span class="shrink-0 font-semibold tabular-nums text-zinc-900 dark:text-white">{{ $total }} <span class="font-normal text-zinc-500">{{ $total === 1 ? 'família' : 'famílias' }}</span></span>
                                </div>
                                <div class="mt-1.5 h-2.5 rounded-full bg-zinc-100 dark:bg-zinc-800">
                                    <div class="h-full rounded-full" style="width: {{ round($total / $maxBairro * 100) }}%; background: var({{ str_starts_with($nomeBairro, 'Outros') || $nomeBairro === 'Não informado' ? '--viz-apagada' : '--viz-barra' }})"></div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </section>
</div>

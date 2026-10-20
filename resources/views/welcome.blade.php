<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="#1a2f26">

        <title>ASA Passo Fundo – O que estamos precisando</title>
        <meta name="description" content="Veja os itens que a Ação Solidária Adventista de Passo Fundo mais precisa agora e onde entregar sua doação.">

        <link rel="icon" href="/logo.png" type="image/png">
        <link rel="apple-touch-icon" href="/logo.png">

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,700;12..96,800&family=Figtree:wght@400;500;600&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            .font-display { font-family: 'Bricolage Grotesque', ui-sans-serif, system-ui, sans-serif; }
            .font-body { font-family: 'Figtree', ui-sans-serif, system-ui, sans-serif; }

            @keyframes item-in {
                from { opacity: 0; transform: translateY(12px); }
                to { opacity: 1; transform: none; }
            }
            @media (prefers-reduced-motion: no-preference) {
                .item-in { opacity: 0; animation: item-in .5s cubic-bezier(.2,.7,.2,1) forwards; }
            }
        </style>
    </head>
    <body class="font-body bg-[#f5f7f3] text-[#14211b] dark:bg-[#0e1813] dark:text-[#e8eee9] min-h-screen antialiased selection:bg-asa-gold selection:text-asa-green">

        <a href="#necessidades" class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-50 focus:bg-white focus:text-asa-green focus:px-4 focus:py-2 focus:rounded-lg">Pular para a lista</a>

        <!-- Cabeçalho -->
        <header class="max-w-3xl mx-auto px-5 sm:px-8 pt-5 flex items-center justify-between">
            <a href="/" class="flex items-center gap-3 rounded-lg focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-asa-gold">
                <img src="/logo.png" alt="" width="40" height="40" class="w-10 h-10 object-contain">
                <span class="leading-tight">
                    <span class="block font-display font-bold text-lg text-asa-green dark:text-white">ASA</span>
                    <span class="block text-xs text-[#5b6b63] dark:text-[#9fb0a6]">Passo Fundo</span>
                </span>
            </a>

            <nav class="flex items-center gap-2 text-sm font-semibold">
                @auth
                    <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded-full bg-asa-green text-white hover:bg-[#24402f] transition-colors focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-asa-gold">
                        Abrir painel
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 rounded-full text-asa-green dark:text-white hover:bg-asa-green/5 dark:hover:bg-white/5 transition-colors focus-visible:outline-2 focus-visible:outline-asa-gold">
                        Entrar
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="px-4 py-2 rounded-full bg-asa-green text-white hover:bg-[#24402f] transition-colors focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-asa-gold">
                            Criar conta
                        </a>
                    @endif
                @endauth
            </nav>
        </header>

        <main>
            <!-- Necessidades: o destaque da página -->
            <section id="necessidades" class="max-w-3xl mx-auto px-5 sm:px-8 pt-14 sm:pt-20 pb-16 sm:pb-24 scroll-mt-6">
                @if($produtosEmFalta->count() > 0)
                    <h1 class="font-display font-bold text-[1.75rem] sm:text-4xl leading-tight tracking-tight text-[#5b6b63] dark:text-[#9fb0a6] max-w-[18ch]">
                        Hoje, a ASA está precisando de
                    </h1>

                    <ul class="mt-6 sm:mt-8 border-t-2 border-asa-green dark:border-asa-gold">
                        @foreach($produtosEmFalta as $produto)
                            <li class="item-in flex items-center gap-4 py-4 sm:py-5 border-b border-[#dbe2dc] dark:border-white/10" style="animation-delay: {{ $loop->index * 70 }}ms">
                                <span class="shrink-0 w-11 h-11 rounded-full bg-asa-green text-asa-gold flex items-center justify-center" aria-hidden="true">
                                    @switch($produto->categoria)
                                        @case('Alimento')
                                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 11-1 9"/><path d="m19 11-4-7"/><path d="M2 11h20"/><path d="m3.5 11 1.6 7.4a2 2 0 0 0 2 1.6h9.8a2 2 0 0 0 2-1.6l1.7-7.4"/><path d="M4.5 15.5h15"/><path d="m5 11 4-7"/><path d="m9 11 1 9"/></svg>
                                            @break
                                        @case('Higiene')
                                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22a7 7 0 0 0 7-7c0-2-1-3.9-3-5.5s-3.5-4-4-6.5c-.5 2.5-2 4.9-4 6.5C6 11.1 5 13 5 15a7 7 0 0 0 7 7z"/></svg>
                                            @break
                                        @case('Limpeza')
                                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.937 15.5A2 2 0 0 0 8.5 14.063l-6.135-1.582a.5.5 0 0 1 0-.962L8.5 9.936A2 2 0 0 0 9.937 8.5l1.582-6.135a.5.5 0 0 1 .963 0L14.063 8.5A2 2 0 0 0 15.5 9.937l6.135 1.581a.5.5 0 0 1 0 .964L15.5 14.063a2 2 0 0 0-1.437 1.437l-1.582 6.135a.5.5 0 0 1-.963 0z"/></svg>
                                            @break
                                        @case('Vestuário')
                                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.38 3.46 16 2a4 4 0 0 1-8 0L3.62 3.46a2 2 0 0 0-1.34 2.23l.58 3.47a1 1 0 0 0 .99.84H6v10c0 1.1.9 2 2 2h8a2 2 0 0 0 2-2V10h2.15a1 1 0 0 0 .99-.84l.58-3.47a2 2 0 0 0-1.34-2.23z"/></svg>
                                            @break
                                        @default
                                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 21.73a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73z"/><path d="M12 22V12"/><path d="m3.3 7 8.7 5 8.7-5"/></svg>
                                    @endswitch
                                </span>

                                <span class="min-w-0 flex-1">
                                    <span class="block font-display font-extrabold text-[1.75rem] sm:text-[2.5rem] leading-none tracking-tight text-asa-green dark:text-white truncate">{{ $produto->nome }}</span>
                                    <span class="block mt-1.5 text-sm text-[#5b6b63] dark:text-[#9fb0a6]">{{ $produto->categoria }}</span>
                                </span>

                                <span class="shrink-0 px-3 py-1 rounded-full text-xs font-semibold bg-asa-gold/20 text-[#7a5a0f] dark:bg-asa-gold/15 dark:text-asa-gold">
                                    Urgente
                                </span>
                            </li>
                        @endforeach
                    </ul>

                    <p class="mt-8 text-lg leading-relaxed max-w-[52ch] text-[#33443b] dark:text-[#c5d1ca]">
                        Qualquer quantidade já ajuda. Sua doação chega direto às famílias que atendemos aqui em Passo Fundo.
                    </p>
                @else
                    <h1 class="font-display font-extrabold text-4xl sm:text-5xl leading-tight tracking-tight text-asa-green dark:text-white max-w-[16ch]">
                        Nosso estoque está em dia
                    </h1>
                    <p class="mt-5 text-lg leading-relaxed max-w-[52ch] text-[#33443b] dark:text-[#c5d1ca]">
                        Graças às doações recentes, não há itens em falta agora. Alimentos e produtos de higiene continuam sendo bem-vindos.
                    </p>
                @endif

                <a href="#onde-entregar" class="mt-8 inline-flex items-center justify-center w-full sm:w-auto px-7 py-4 rounded-2xl bg-asa-green text-white dark:bg-asa-gold dark:text-asa-green font-semibold text-base hover:bg-[#24402f] dark:hover:bg-[#e3b84d] transition-colors focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-asa-gold">
                    Ver onde entregar
                </a>
            </section>

            <!-- Onde entregar -->
            <section id="onde-entregar" class="bg-asa-green text-white scroll-mt-0">
                <div class="max-w-3xl mx-auto px-5 sm:px-8 py-16 sm:py-24">
                    <h2 class="font-display font-bold text-lg text-asa-gold">Onde entregar</h2>
                    <p class="mt-3 font-display font-extrabold text-[2.25rem] sm:text-6xl leading-[1.05] tracking-tight">
                        Rua Capitão Eleutério, 293
                    </p>
                    <p class="mt-3 text-lg text-white/75">Centro, Passo Fundo – RS</p>

                    <div class="mt-10 flex flex-col sm:flex-row gap-3">
                        <a href="https://www.google.com/maps/search/?api=1&query=Rua+Capit%C3%A3o+Eleut%C3%A9rio%2C+293%2C+Passo+Fundo+-+RS" target="_blank" rel="noopener" class="inline-flex items-center justify-center gap-2 px-7 py-4 rounded-2xl bg-asa-gold text-asa-green font-semibold hover:bg-[#e3b84d] transition-colors focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/><circle cx="12" cy="10" r="3"/></svg>
                            Abrir no mapa
                        </a>
                        <a href="https://www.adventistas.org/pt/asa/" target="_blank" rel="noopener" class="inline-flex items-center justify-center px-7 py-4 rounded-2xl border border-white/25 font-semibold hover:bg-white/10 transition-colors focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white">
                            Conhecer a ASA
                        </a>
                    </div>

                    <p class="mt-14 pt-8 border-t border-white/15 font-display text-2xl sm:text-3xl font-medium leading-snug max-w-[24ch] text-white/90">
                        Nossa missão é amar e servir ao próximo.
                    </p>
                </div>
            </section>
        </main>

        <footer class="bg-asa-green text-white/55 border-t border-white/10">
            <div class="max-w-3xl mx-auto px-5 sm:px-8 py-8 flex items-center gap-3 text-sm">
                <img src="/logo.png" alt="" width="28" height="28" class="w-7 h-7 object-contain opacity-70">
                <span>&copy; {{ date('Y') }} Ação Solidária Adventista de Passo Fundo</span>
            </div>
        </footer>

    </body>
</html>

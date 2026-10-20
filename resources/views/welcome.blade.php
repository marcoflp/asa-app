<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>ASA - Ação Solidária Adventista</title>

        <link rel="icon" href="/logo.png" type="image/png">
        <link rel="apple-touch-icon" href="/logo.png">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">

        <!-- Tailwind CSS & Vite -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="mesh-bg text-slate-900 dark:text-slate-100 font-sans min-h-screen selection:bg-asa-gold selection:text-white">
        
        <!-- Header -->
        <header class="fixed top-0 left-0 right-0 z-50 glass scroll-py-4 transition-all duration-300">
            <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <img src="/logo.png" alt="ASA Logo" class="w-12 h-12 object-contain drop-shadow-sm">
                    <span class="font-outfit text-2xl font-bold tracking-tight text-asa-green dark:text-white">ASA</span>
                </div>

                <nav class="flex items-center gap-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="px-5 py-2.5 bg-asa-green text-white rounded-xl text-sm font-medium hover:scale-105 transition-all active:scale-95 shadow-lg shadow-asa-green/20">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-5 py-2.5 text-sm font-medium hover:text-asa-gold transition-colors">
                            Login
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="px-6 py-2.5 bg-asa-gold text-white rounded-xl text-sm font-bold shadow-lg shadow-asa-gold/25 hover:scale-105 transition-all active:scale-95">
                                Registrar
                            </a>
                        @endif
                    @endauth
                </nav>
            </div>
        </header>

        <main class="pt-28 md:pt-36 pb-20 px-4 sm:px-6 max-w-7xl mx-auto">
            
            <!-- Hero Section -->
            <section class="text-center mb-16 md:mb-24 animate-fade-in">
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-asa-green/5 dark:bg-white/5 rounded-full mb-8 border border-asa-green/10">
                    <span class="w-2 h-2 bg-asa-gold rounded-full animate-pulse"></span>
                    <span class="text-xs font-bold text-asa-green dark:text-asa-gold uppercase tracking-widest">Ação Solidária Adventista</span>
                </div>
                
                <h1 class="font-outfit text-4xl sm:text-5xl md:text-7xl font-extrabold tracking-tight mb-8">
                    <span class="text-asa-green dark:text-white">Juntos, fazemos</span> <br>
                    <span class="bg-gradient-to-r from-asa-green to-asa-gold bg-clip-text text-transparent">a diferença na ASA</span>
                </h1>
                
                <p class="text-lg md:text-xl text-slate-600 dark:text-slate-400 max-w-2xl mx-auto mb-12 leading-relaxed font-medium">
                    Nossa missão é amar e servir ao próximo. Sua doação é essencial para garantirmos o sustento das famílias que atendemos.
                </p>
                
                <div class="flex flex-col sm:flex-row justify-center gap-3 sm:gap-4 max-w-sm sm:max-w-none mx-auto">
                    <a href="#necessidades" class="px-8 sm:px-10 py-4 bg-asa-green text-white rounded-2xl font-bold shadow-xl shadow-asa-green/30 hover:scale-[1.03] transition-all active:scale-95">
                        Ver Itens em Falta
                    </a>
                    <a href="#sobre" class="px-10 py-4 glass rounded-2xl font-bold hover:bg-slate-50 dark:hover:bg-slate-800 transition-all border border-slate-200 dark:border-white/10 group">
                        Sobre a ASA
                        <svg class="inline-block ml-2 w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </a>
                </div>
            </section>

            <!-- Shortage Section -->
            <section id="necessidades" class="animate-slide-up bg-white/50 dark:bg-white/5 rounded-[2rem] md:rounded-[3rem] p-5 sm:p-10 md:p-16 border border-slate-100 dark:border-white/5" style="animation-delay: 0.2s">
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10 md:mb-16">
                    <div class="max-w-xl">
                        <h2 class="font-outfit text-3xl md:text-5xl font-bold mb-4 text-asa-green dark:text-white line-clamp-2">O que estamos precisando</h2>
                        <p class="text-slate-500 dark:text-slate-400">Estes são os itens com maior urgência em nosso estoque. Se puder doar qualquer quantidade, será de grande ajuda.</p>
                    </div>
                </div>

                @if($produtosEmFalta->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-8">
                        @foreach($produtosEmFalta as $produto)
                            <div class="group relative bg-white dark:bg-slate-900/50 rounded-[2rem] p-6 md:p-8 border border-slate-100 dark:border-white/5 hover:border-asa-gold transition-all duration-500 shadow-sm hover:shadow-2xl hover:-translate-y-2 overflow-hidden">
                                <!-- Category Icon -->
                                <div class="w-16 h-16 bg-asa-green/5 dark:bg-asa-gold/10 rounded-2xl flex items-center justify-center mb-8 group-hover:bg-asa-green transition-colors duration-500 text-asa-green dark:text-asa-gold group-hover:text-white">
                                    @switch($produto->categoria)
                                        @case('Alimento')
                                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 11-1 9"/><path d="m19 11-4-7"/><path d="M2 11h20"/><path d="m3.5 11 1.6 7.4a2 2 0 0 0 2 1.6h9.8a2 2 0 0 0 2-1.6l1.7-7.4"/><path d="M4.5 15.5h15"/><path d="m5 11 4-7"/><path d="m9 11 1 9"/></svg>
                                            @break
                                        @case('Higiene')
                                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22a7 7 0 0 0 7-7c0-2-1-3.9-3-5.5s-3.5-4-4-6.5c-.5 2.5-2 4.9-4 6.5C6 11.1 5 13 5 15a7 7 0 0 0 7 7z"/></svg>
                                            @break
                                        @case('Limpeza')
                                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.937 15.5A2 2 0 0 0 8.5 14.063l-6.135-1.582a.5.5 0 0 1 0-.962L8.5 9.936A2 2 0 0 0 9.937 8.5l1.582-6.135a.5.5 0 0 1 .963 0L14.063 8.5A2 2 0 0 0 15.5 9.937l6.135 1.581a.5.5 0 0 1 0 .964L15.5 14.063a2 2 0 0 0-1.437 1.437l-1.582 6.135a.5.5 0 0 1-.963 0z"/></svg>
                                            @break
                                        @case('Vestuário')
                                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.38 3.46 16 2a4 4 0 0 1-8 0L3.62 3.46a2 2 0 0 0-1.34 2.23l.58 3.47a1 1 0 0 0 .99.84H6v10c0 1.1.9 2 2 2h8a2 2 0 0 0 2-2V10h2.15a1 1 0 0 0 .99-.84l.58-3.47a2 2 0 0 0-1.34-2.23z"/></svg>
                                            @break
                                        @default
                                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
                                    @endswitch
                                </div>

                                <h3 class="font-outfit text-2xl font-bold mb-3 text-slate-800 dark:text-white">{{ $produto->nome }}</h3>
                                <p class="text-slate-500 dark:text-slate-400 font-medium mb-8">
                                    {{ $produto->categoria }} <span class="mx-2 text-slate-300">•</span> {{ $produto->unidade }}
                                </p>

                                <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-widest bg-asa-gold/15 text-[#8a6210] dark:text-asa-gold">
                                    <span class="w-1.5 h-1.5 rounded-full bg-current animate-pulse motion-reduce:animate-none"></span>
                                    Urgente
                                </span>
                                
                                <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-asa-gold/5 to-transparent rounded-bl-full group-hover:scale-150 transition-transform duration-700"></div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-20 px-8 bg-asa-green/5 rounded-[2rem] border-2 border-dashed border-asa-green/10">
                        <div class="w-24 h-24 bg-asa-green text-white rounded-full flex items-center justify-center mx-auto mb-8 shadow-xl">
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        </div>
                        <h3 class="font-outfit text-3xl font-bold mb-4 text-asa-green">Estoque Abastecido!</h3>
                        <p class="text-slate-600 dark:text-slate-400 max-w-md mx-auto text-lg font-medium leading-relaxed">Graças às doações recentes, nosso estoque de itens básicos está completo. Continue nos apoiando!</p>
                    </div>
                @endif
            </section>

            <!-- O que é a ASA -->
            <section id="sobre" class="mt-16 md:mt-28 scroll-mt-28 relative overflow-hidden bg-asa-green text-white rounded-[2rem] md:rounded-[3rem] p-6 sm:p-10 md:p-16">
                <div class="absolute -right-24 -top-24 w-80 h-80 bg-asa-gold/20 rounded-full blur-[90px] pointer-events-none"></div>
                <div class="relative grid md:grid-cols-[1fr_1.4fr] gap-8 md:gap-14 items-center">
                    <div class="text-center md:text-left">
                        <div class="w-28 h-28 md:w-36 md:h-36 mx-auto md:mx-0 bg-white rounded-full flex items-center justify-center shadow-2xl shadow-black/30">
                            <img src="/logo.png" alt="Logo da ASA" class="w-20 h-20 md:w-24 md:h-24 object-contain">
                        </div>
                        <h2 class="mt-6 font-outfit text-3xl md:text-5xl font-bold">O que é a ASA?</h2>
                        <p class="mt-4 text-white/75 leading-relaxed max-w-md mx-auto md:mx-0">A Ação Solidária Adventista é o braço social da igreja local, feito por liderança e membros em favor dos seus semelhantes, com base nos princípios bíblicos.</p>
                    </div>
                    <ul class="grid gap-3">
                    <li class="flex gap-4 bg-white/[.06] border border-white/10 rounded-2xl p-5">
                        <span class="shrink-0 w-11 h-11 rounded-xl bg-asa-gold/15 text-asa-gold flex items-center justify-center"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M11 21.73a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73z"/><path d="M12 22V12"/><path d="m3.3 7 8.7 5 8.7-5"/></svg></span>
                        <span>
                            <span class="block font-outfit font-bold text-lg">Cestas básicas</span>
                            <span class="block mt-1 text-sm leading-relaxed text-white/70">Entrega de alimentos para as famílias cadastradas.</span>
                        </span>
                    </li>
                    <li class="flex gap-4 bg-white/[.06] border border-white/10 rounded-2xl p-5">
                        <span class="shrink-0 w-11 h-11 rounded-xl bg-asa-gold/15 text-asa-gold flex items-center justify-center"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg></span>
                        <span>
                            <span class="block font-outfit font-bold text-lg">Varal solidário</span>
                            <span class="block mt-1 text-sm leading-relaxed text-white/70">Roupas e calçados doados para quem precisa.</span>
                        </span>
                    </li>
                    <li class="flex gap-4 bg-white/[.06] border border-white/10 rounded-2xl p-5">
                        <span class="shrink-0 w-11 h-11 rounded-xl bg-asa-gold/15 text-asa-gold flex items-center justify-center"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></span>
                        <span>
                            <span class="block font-outfit font-bold text-lg">Visitas</span>
                            <span class="block mt-1 text-sm leading-relaxed text-white/70">Acompanhamento das pessoas cadastradas.</span>
                        </span>
                    </li>
                    <li class="flex gap-4 bg-white/[.06] border border-white/10 rounded-2xl p-5">
                        <span class="shrink-0 w-11 h-11 rounded-xl bg-asa-gold/15 text-asa-gold flex items-center justify-center"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 7v14"/><path d="M3 18a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h5a4 4 0 0 1 4 4 4 4 0 0 1 4-4h5a1 1 0 0 1 1 1v13a1 1 0 0 1-1 1h-6a3 3 0 0 0-3 3 3 3 0 0 0-3-3z"/></svg></span>
                        <span>
                            <span class="block font-outfit font-bold text-lg">Apoio à comunidade</span>
                            <span class="block mt-1 text-sm leading-relaxed text-white/70">Suporte psicológico, espiritual e social.</span>
                        </span>
                    </li>
                    </ul>
                </div>
            </section>

            <!-- Onde e quando entregar -->
            <section id="como-doar" class="mt-16 md:mt-28 grid md:grid-cols-2 gap-6 md:gap-12 items-center scroll-mt-28">
                <img src="/img/sede-asa.jpg" alt="Prédio da Igreja Adventista na Rua Capitão Eleutério, onde funciona a ASA" width="799" height="600" loading="lazy" class="w-full h-64 md:h-[28rem] object-cover rounded-[2rem] md:rounded-[2.5rem] bg-asa-green/10 shadow-xl shadow-asa-green/10">
                <div>
                    <h2 class="font-outfit text-3xl md:text-5xl font-bold text-asa-green dark:text-white">Onde e quando doar</h2>
                    <div class="mt-6 grid gap-3">
                        <div class="flex gap-4 items-start bg-white dark:bg-slate-900/50 rounded-2xl p-5 border border-slate-100 dark:border-white/5">
                            <span class="shrink-0 w-11 h-11 rounded-xl bg-asa-green text-asa-gold flex items-center justify-center"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></span>
                            <span>
                                <span class="block font-outfit font-bold text-lg text-slate-800 dark:text-white">Sábados, das 15h às 16h</span>
                                <span class="block mt-1 text-sm text-slate-500 dark:text-slate-400">Aberto todos os sábados no mesmo horário.</span>
                            </span>
                        </div>
                        <div class="flex gap-4 items-start bg-white dark:bg-slate-900/50 rounded-2xl p-5 border border-slate-100 dark:border-white/5">
                            <span class="shrink-0 w-11 h-11 rounded-xl bg-asa-green text-asa-gold flex items-center justify-center"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/><circle cx="12" cy="10" r="3"/></svg></span>
                            <span>
                                <span class="block font-outfit font-bold text-lg text-slate-800 dark:text-white">Rua Capitão Eleutério, 293</span>
                                <span class="block mt-1 text-sm text-slate-500 dark:text-slate-400">Centro, Passo Fundo – RS. No prédio da Igreja Adventista.</span>
                            </span>
                        </div>
                    </div>
                    <div class="mt-6 flex flex-col sm:flex-row gap-3">
                        <a href="https://www.google.com/maps/search/?api=1&query=Rua+Capit%C3%A3o+Eleut%C3%A9rio%2C+293%2C+Passo+Fundo+-+RS" target="_blank" rel="noopener" class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-asa-green text-white rounded-2xl font-bold shadow-xl shadow-asa-green/25 hover:scale-[1.03] transition-all active:scale-95">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/><circle cx="12" cy="10" r="3"/></svg>
                            Abrir no mapa
                        </a>
                        <a href="https://www.adventistas.org/pt/asa/" target="_blank" rel="noopener" class="inline-flex items-center justify-center px-8 py-4 glass rounded-2xl font-bold text-asa-green dark:text-white border border-slate-200 dark:border-white/10 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all">
                            Site oficial da ASA
                        </a>
                    </div>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer class="py-16 border-t border-slate-100 dark:border-white/5 text-center">
            <div class="max-w-7xl mx-auto px-6">
                <img src="/logo.png" alt="ASA Logo" class="w-10 h-10 object-contain mx-auto mb-6 grayscale opacity-50">
                <p class="text-sm font-bold text-slate-400 dark:text-slate-600 uppercase tracking-[0.2em]">&copy; {{ date('Y') }} ASA - Ação Solidária Adventista de Passo Fundo</p>
                <p class="text-sm font-bold text-slate-400 dark:text-slate-600 uppercase tracking-[0.2em]">Rua Capitão Eleutério, 293 - Centro</p>
                <p class="text-sm font-bold text-slate-400 dark:text-slate-600 uppercase tracking-[0.2em]">Passo Fundo - RS</p>
                <div class="mt-4 flex justify-center gap-6 text-slate-300 dark:text-slate-700">
                    <span class="w-1.5 h-1.5 bg-current rounded-full"></span>
                    <span class="w-1.5 h-1.5 bg-current rounded-full"></span>
                    <span class="w-1.5 h-1.5 bg-current rounded-full"></span>
                </div>
            </div>
        </footer>

    </body>
</html>

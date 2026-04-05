<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>ASA - Ação Solidária Adventista</title>

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

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
                            <!-- <a href="{{ route('register') }}" class="px-6 py-2.5 bg-asa-gold text-white rounded-xl text-sm font-bold shadow-lg shadow-asa-gold/25 hover:scale-105 transition-all active:scale-95">
                                Registrar
                            </a> -->
                        @endif
                    @endauth
                </nav>
            </div>
        </header>

        <main class="pt-36 pb-20 px-6 max-w-7xl mx-auto">
            
            <!-- Hero Section -->
            <section class="text-center mb-32 animate-fade-in">
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-asa-green/5 dark:bg-white/5 rounded-full mb-8 border border-asa-green/10">
                    <span class="w-2 h-2 bg-asa-gold rounded-full animate-pulse"></span>
                    <span class="text-xs font-bold text-asa-green dark:text-asa-gold uppercase tracking-widest">Ação Solidária Adventista</span>
                </div>
                
                <h1 class="font-outfit text-5xl md:text-7xl font-extrabold tracking-tight mb-8">
                    <span class="text-asa-green dark:text-white">Juntos, fazemos</span> <br>
                    <span class="bg-gradient-to-r from-asa-green to-asa-gold bg-clip-text text-transparent">a diferença na ASA</span>
                </h1>
                
                <p class="text-lg md:text-xl text-slate-600 dark:text-slate-400 max-w-2xl mx-auto mb-12 leading-relaxed font-medium">
                    Nossa missão é amar e servir ao próximo. Sua doação é essencial para garantirmos o sustento das famílias que atendemos.
                </p>
                
                <div class="flex flex-wrap justify-center gap-4">
                    <a href="#necessidades" class="px-10 py-4 bg-asa-green text-white rounded-2xl font-bold shadow-xl shadow-asa-green/30 hover:scale-[1.03] transition-all active:scale-95">
                        Ver Itens em Falta
                    </a>
                    <a href="https://www.adventistas.org/pt/asa/" target="_blank" class="px-10 py-4 glass rounded-2xl font-bold hover:bg-slate-50 dark:hover:bg-slate-800 transition-all border border-slate-200 dark:border-white/10 group">
                        Sobre a ASA
                        <svg class="inline-block ml-2 w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </a>
                </div>
            </section>

            <!-- Shortage Section -->
            <section id="necessidades" class="animate-slide-up bg-white/50 dark:bg-white/5 rounded-[3rem] p-10 md:p-16 border border-slate-100 dark:border-white/5" style="animation-delay: 0.2s">
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-16">
                    <div class="max-w-xl">
                        <h2 class="font-outfit text-4xl md:text-5xl font-bold mb-4 text-asa-green dark:text-white line-clamp-2">O que estamos precisando</h2>
                        <p class="text-slate-500 dark:text-slate-400">Estes são os itens com maior urgência em nosso estoque. Se puder doar qualquer quantidade, será de grande ajuda.</p>
                    </div>
                </div>

                @if($produtosEmFalta->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        @foreach($produtosEmFalta as $produto)
                            <div class="group relative bg-white dark:bg-slate-900/50 rounded-[2rem] p-8 border border-slate-100 dark:border-white/5 hover:border-asa-gold transition-all duration-500 shadow-sm hover:shadow-2xl hover:-translate-y-2 overflow-hidden">
                                <!-- Category Icon -->
                                <div class="w-16 h-16 bg-asa-green/5 dark:bg-asa-gold/10 rounded-2xl flex items-center justify-center mb-8 group-hover:bg-asa-green transition-colors duration-500 text-asa-green dark:text-asa-gold group-hover:text-white">
                                    @switch($produto->categoria)
                                        @case('Alimento')
                                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                                            @break
                                        @case('Higiene')
                                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 20H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h3.9a2 2 0 0 1 1.69.9l.81 1.2a2 2 0 0 0 1.69.9H20a2 2 0 0 1 2 2v3.5"/></svg>
                                            @break
                                        @case('Limpeza')
                                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 22h20"/><path d="M7 22v-5a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v5"/><path d="M9 15V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v10"/></svg>
                                            @break
                                        @default
                                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
                                    @endswitch
                                </div>

                                <h3 class="font-outfit text-2xl font-bold mb-3 text-slate-800 dark:text-white">{{ $produto->nome }}</h3>
                                <p class="text-slate-500 dark:text-slate-400 font-medium mb-8">
                                    {{ $produto->categoria }} <span class="mx-2 text-slate-300">•</span> {{ $produto->unidade }}
                                </p>

                                <div class="flex items-center gap-3">
                                    <div class="px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-widest {{ $produto->estoque == 0 ? 'bg-red-50 text-red-600 dark:bg-red-900/20' : 'bg-asa-gold/10 text-asa-gold' }}">
                                        @if($produto->estoque == 0)
                                            ESGOTADO
                                        @else
                                            Urgente ({{ (int)$produto->estoque }})
                                        @endif
                                    </div>
                                    <span class="text-[10px] font-bold text-slate-300 dark:text-slate-600 uppercase tracking-tighter">Estoque Atual</span>
                                </div>
                                
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

            <!-- <section class="mt-40 animate-slide-up" style="animation-delay: 0.4s">
                <div class="brand-gradient rounded-[3.5rem] p-12 md:p-24 relative overflow-hidden text-center text-white shadow-2xl shadow-asa-green/30">
                    <div class="absolute top-0 left-0 w-full h-full opacity-10 pointer-events-none" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 40px 40px;"></div>
                    
                    <div class="relative z-10">
                        <h2 class="font-outfit text-4xl md:text-6xl font-extrabold mb-8">Faça parte da nossa rede</h2>
                        <p class="text-white/80 max-w-2xl mx-auto mb-14 text-lg md:text-xl font-medium leading-relaxed">
                            Seja como voluntário local ou contribuindo com doações regulares, sua participação é fundamental para transformarmos a realidade da nossa comunidade.
                        </p>
                        <div class="flex flex-wrap justify-center gap-6">
                            <a href="{{ route('register') }}" class="px-12 py-5 bg-white text-asa-green rounded-2xl font-black text-lg hover:scale-[1.03] transition-all active:scale-95 shadow-xl">
                                Quero Ser Voluntário
                            </a>
                            <a href="{{ route('login') }}" class="px-12 py-5 border-2 border-white/30 rounded-2xl font-bold text-lg hover:bg-white/10 transition-all backdrop-blur-sm">
                                Acessar Painel
                            </a>
                        </div>
                    </div>

                    <div class="absolute -left-20 -bottom-20 w-80 h-80 bg-white opacity-[0.03] rounded-full"></div>
                    <div class="absolute -right-20 -top-20 w-80 h-80 bg-asa-gold opacity-[0.2] rounded-full blur-[100px]"></div>
                </div>
            </section> -->
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

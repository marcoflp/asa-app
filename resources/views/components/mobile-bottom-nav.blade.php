<div x-data="{ moreDrawerOpen: false }" class="lg:hidden">
    {{-- BOTTOM NAVIGATION BAR FIXA --}}
    <nav 
        class="fixed bottom-0 inset-x-0 z-40 bg-white/95 dark:bg-zinc-900/95 backdrop-blur-lg border-t border-zinc-200 dark:border-zinc-800 shadow-[0_-4px_20px_rgba(0,0,0,0.08)] pb-[max(env(safe-area-inset-bottom),8px)] pt-1.5 px-2"
        aria-label="Navegação inferior mobile"
    >
        <div class="grid grid-cols-5 items-center justify-around max-w-md mx-auto">
            
            {{-- 1. Início --}}
            <a 
                id="tour-mobile-nav-dashboard"
                href="{{ route('dashboard') }}" 
                wire:navigate
                class="flex flex-col items-center justify-center py-1 px-1 rounded-xl transition-all duration-150 active:scale-90 select-none {{ request()->routeIs('dashboard') ? 'text-emerald-700 dark:text-emerald-400 font-extrabold' : 'text-zinc-600 dark:text-zinc-400 font-semibold hover:text-zinc-900 dark:hover:text-white' }}"
                style="-webkit-tap-highlight-color: transparent;"
            >
                <div class="relative p-1 transition-colors {{ request()->routeIs('dashboard') ? 'bg-emerald-100/80 dark:bg-emerald-950/70 text-emerald-700 dark:text-emerald-400 rounded-xl' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                </div>
                <span class="text-[10px] mt-0.5 tracking-tight">Início</span>
            </a>

            {{-- 2. Beneficiários --}}
            <a 
                id="tour-mobile-nav-beneficiarios"
                href="{{ route('beneficiarios.index') }}" 
                wire:navigate
                class="flex flex-col items-center justify-center py-1 px-1 rounded-xl transition-all duration-150 active:scale-90 select-none {{ request()->routeIs('beneficiarios.*') ? 'text-emerald-700 dark:text-emerald-400 font-extrabold' : 'text-zinc-600 dark:text-zinc-400 font-semibold hover:text-zinc-900 dark:hover:text-white' }}"
                style="-webkit-tap-highlight-color: transparent;"
            >
                <div class="relative p-1 transition-colors {{ request()->routeIs('beneficiarios.*') ? 'bg-emerald-100/80 dark:bg-emerald-950/70 text-emerald-700 dark:text-emerald-400 rounded-xl' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <span class="text-[10px] mt-0.5 tracking-tight">Beneficiários</span>
            </a>

            {{-- 3. Estoque / Produtos --}}
            <a 
                id="tour-mobile-nav-produtos"
                href="{{ route('produtos.index') }}" 
                wire:navigate
                class="flex flex-col items-center justify-center py-1 px-1 rounded-xl transition-all duration-150 active:scale-90 select-none {{ request()->routeIs('produtos.*') ? 'text-emerald-700 dark:text-emerald-400 font-extrabold' : 'text-zinc-600 dark:text-zinc-400 font-semibold hover:text-zinc-900 dark:hover:text-white' }}"
                style="-webkit-tap-highlight-color: transparent;"
            >
                <div class="relative p-1 transition-colors {{ request()->routeIs('produtos.*') ? 'bg-emerald-100/80 dark:bg-emerald-950/70 text-emerald-700 dark:text-emerald-400 rounded-xl' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
                <span class="text-[10px] mt-0.5 tracking-tight">Estoque</span>
            </a>

            {{-- 4. Retiradas --}}
            <a 
                id="tour-mobile-nav-retiradas"
                href="{{ route('retiradas.index') }}" 
                wire:navigate
                class="flex flex-col items-center justify-center py-1 px-1 rounded-xl transition-all duration-150 active:scale-90 select-none {{ request()->routeIs('retiradas.*') ? 'text-emerald-700 dark:text-emerald-400 font-extrabold' : 'text-zinc-600 dark:text-zinc-400 font-semibold hover:text-zinc-900 dark:hover:text-white' }}"
                style="-webkit-tap-highlight-color: transparent;"
            >
                <div class="relative p-1 transition-colors {{ request()->routeIs('retiradas.*') ? 'bg-emerald-100/80 dark:bg-emerald-950/70 text-emerald-700 dark:text-emerald-400 rounded-xl' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                </div>
                <span class="text-[10px] mt-0.5 tracking-tight">Retiradas</span>
            </a>

            {{-- 5. Mais / Menu do Usuário --}}
            <button 
                id="tour-mobile-nav-mais"
                @click="moreDrawerOpen = true"
                type="button"
                class="flex flex-col items-center justify-center py-1 px-1 rounded-xl transition-all duration-150 active:scale-90 select-none text-zinc-600 dark:text-zinc-400 font-semibold hover:text-zinc-900 dark:hover:text-white cursor-pointer"
                style="-webkit-tap-highlight-color: transparent;"
            >
                <div class="relative p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
                    </svg>
                </div>
                <span class="text-[10px] mt-0.5 tracking-tight">Mais</span>
            </button>

        </div>
    </nav>

    {{-- DRAWER DESLIZANTE DE OPÇÕES (MENU "MAIS") --}}
    <div 
        x-show="moreDrawerOpen" 
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs"
        style="display: none;"
    >
        {{-- Backdrop click para fechar --}}
        <div @click="moreDrawerOpen = false" class="absolute inset-0"></div>

        {{-- Painel inferior deslizante --}}
        <div 
            x-show="moreDrawerOpen"
            x-transition:enter="transition ease-out duration-250 transform"
            x-transition:enter-start="translate-y-full"
            x-transition:enter-end="translate-y-0"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="translate-y-0"
            x-transition:leave-end="translate-y-full"
            class="absolute bottom-0 inset-x-0 bg-white dark:bg-zinc-900 rounded-t-3xl p-6 shadow-2xl border-t border-zinc-200 dark:border-zinc-800 pb-[max(env(safe-area-inset-bottom),24px)] space-y-5"
        >
            {{-- Barra de arrasto visual --}}
            <div class="w-12 h-1.5 bg-zinc-300 dark:bg-zinc-700 rounded-full mx-auto -mt-2"></div>

            {{-- Perfil do Usuário --}}
            <div class="flex items-center gap-3.5 pb-4 border-b border-zinc-100 dark:border-zinc-800">
                <div class="w-12 h-12 rounded-full bg-emerald-100 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-300 flex items-center justify-center font-extrabold text-base shrink-0 shadow-inner">
                    {{ auth()->user()->initials() }}
                </div>
                <div class="min-w-0 flex-1">
                    <p class="font-bold text-base text-zinc-900 dark:text-zinc-100 truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-zinc-500 truncate">{{ auth()->user()->email }}</p>
                </div>
                <button 
                    @click="moreDrawerOpen = false" 
                    type="button" 
                    class="p-2 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 rounded-lg cursor-pointer"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            {{-- Links do Menu --}}
            <div class="space-y-1.5">
                <a 
                    href="{{ route('profile.edit') }}" 
                    wire:navigate 
                    @click="moreDrawerOpen = false"
                    class="flex items-center gap-3.5 p-3 rounded-xl hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-800 dark:text-zinc-200 font-semibold text-sm transition-colors"
                >
                    <div class="p-2 rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <span>Configurações & Perfil</span>
                </a>

                <a 
                    href="{{ route('usuarios.index') }}" 
                    wire:navigate 
                    @click="moreDrawerOpen = false"
                    class="flex items-center gap-3.5 p-3 rounded-xl hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-800 dark:text-zinc-200 font-semibold text-sm transition-colors"
                >
                    <div class="p-2 rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                    <span>Gerenciar Usuários</span>
                </a>

                <button 
                    type="button" 
                    @click="moreDrawerOpen = false; $dispatch('open-help-modal')"
                    class="w-full flex items-center gap-3.5 p-3 rounded-xl hover:bg-zinc-100 dark:hover:bg-zinc-800 text-emerald-700 dark:text-emerald-400 font-semibold text-sm transition-colors cursor-pointer"
                >
                    <div class="p-2 rounded-lg bg-emerald-100 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <span>Ajuda & Tutorial Guiado</span>
                </button>
            </div>

            {{-- Botão Sair --}}
            <form method="POST" action="{{ route('logout') }}" class="pt-2 border-t border-zinc-100 dark:border-zinc-800">
                @csrf
                <button 
                    type="submit" 
                    class="w-full flex items-center justify-center gap-2 p-3 rounded-xl bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 font-bold text-sm hover:bg-rose-100 dark:hover:bg-rose-900/40 transition-colors cursor-pointer"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    <span>Sair da Conta</span>
                </button>
            </form>
        </div>
    </div>
</div>

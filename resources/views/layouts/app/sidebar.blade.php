@php
    $appearanceCookie = request()->cookie('flux_appearance') ?? ($_COOKIE['flux_appearance'] ?? null);
    $isDark = $appearanceCookie === 'dark';
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" @class(['dark' => $isDark])>
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-zinc-50 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 font-sans antialiased selection:bg-emerald-500 selection:text-white">
        {{-- Barra de Progresso Superior para Livewire Navigate (sem flashes escuros) --}}
        <div 
            x-data="{ show: false, timer: null }" 
            x-on:livewire:navigating.window="timer = setTimeout(() => { show = true }, 100)" 
            x-on:livewire:navigated.window="clearTimeout(timer); show = false"
            class="fixed top-0 inset-x-0 z-[9999] pointer-events-none h-1 overflow-hidden"
        >
            <div 
                x-show="show" 
                x-transition:enter="transition-all ease-out duration-300"
                x-transition:enter-start="opacity-0 w-0"
                x-transition:enter-end="opacity-100 w-full"
                class="h-full bg-emerald-600 shadow-sm shadow-emerald-500/50"
                style="width: 100%; display: none;"
            ></div>
        </div>

        <flux:sidebar sticky collapsible="mobile" class="border-e border-asa-green/20 bg-asa-green text-zinc-100 dark:border-zinc-800 dark:bg-asa-green dark">
            <flux:sidebar.header>
                <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:sidebar.group :heading="__('Plataforma')" class="grid">
                    <flux:sidebar.item id="tour-nav-dashboard" icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" class="text-zinc-200 hover:text-white font-medium" wire:navigate>
                        {{ __('Início') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item id="tour-nav-beneficiarios" icon="users" :href="route('beneficiarios.index')" :current="request()->routeIs('beneficiarios.*')" class="text-zinc-200 hover:text-white font-medium" wire:navigate>
                        {{ __('Beneficiários') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item id="tour-nav-produtos" icon="archive-box" :href="route('produtos.index')" :current="request()->routeIs('produtos.*')" class="text-zinc-200 hover:text-white font-medium" wire:navigate>
                        {{ __('Produtos') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item id="tour-nav-retiradas" icon="arrow-up-tray" :href="route('retiradas.index')" :current="request()->routeIs('retiradas.*')" class="text-zinc-200 hover:text-white font-medium" wire:navigate>
                        {{ __('Retiradas') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>
            </flux:sidebar.nav>

            <flux:spacer />

            <div id="tour-user-menu">
                <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
            </div>
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden bg-white dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-800">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <flux:avatar
                                    :name="auth()->user()->name"
                                    :initials="auth()->user()->initials()"
                                />

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <flux:heading class="truncate font-bold text-zinc-900 dark:text-zinc-100">{{ auth()->user()->name }}</flux:heading>
                                    <flux:text class="truncate text-xs text-zinc-600 dark:text-zinc-400">{{ auth()->user()->email }}</flux:text>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                            {{ __('Configurações') }}
                        </flux:menu.item>
                        <flux:menu.item :href="route('usuarios.index')" icon="users" wire:navigate>
                            {{ __('Gerenciar Usuários') }}
                        </flux:menu.item>
                        <flux:menu.item href="/log-viewer" icon="command-line" target="_blank">
                            {{ __('Logs do Sistema') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item
                            as="button"
                            type="submit"
                            icon="arrow-right-start-on-rectangle"
                            class="w-full cursor-pointer text-red-600 dark:text-red-400 font-semibold"
                            data-test="logout-button"
                        >
                            {{ __('Sair') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        {{-- Barra de Navegação Inferior Fixa para Smartphones --}}
        <x-mobile-bottom-nav />

        {{-- Componente de Tutorial Guiado e Central de Ajuda --}}
        <x-guided-tour />

        @fluxScripts
    </body>
</html>

<div 
    x-data="{
        showHelpModal: false,
        activeTab: 'geral',
        tourRunning: false,
        currentStep: 0,
        showFirstTimePrompt: false,
        spotlightStyle: {},
        cardPositionStyle: {},
        elevatedEl: null,
        steps: [
            {
                target: null,
                title: 'Sistema ASA: Apresentação',
                description: 'Plataforma para gestão de atendimentos a famílias, controle de estoque de doações e registro de entregas. Acompanhe a apresentação dos módulos principais.',
                badge: 'Introdução',
                url: '{{ route('dashboard') }}',
                screenName: 'Início'
            },
            {
                targetDesktop: '#tour-nav-dashboard',
                targetMobile: '#tour-mobile-nav-dashboard',
                inPageTarget: '#tour-dashboard-periodo',
                title: 'Painel Geral e Filtros de Período',
                description: 'Visão consolidada das operações. Utilize os botões de período para alternar entre Hoje, 7 Dias, Mês, Semestre ou intervalo personalizado, e gere relatórios em PDF.',
                badge: 'Dashboard',
                url: '{{ route('dashboard') }}',
                screenName: 'Início'
            },
            {
                targetDesktop: '#tour-nav-beneficiarios',
                targetMobile: '#tour-mobile-nav-beneficiarios',
                inPageTarget: '#tour-btn-novo-beneficiario',
                inPageTargetMobile: '#tour-mobile-fab-beneficiario',
                title: 'Cadastro de Beneficiários',
                description: 'Gerencie o cadastro das famílias atendidas com dados pessoais, endereço, composição familiar e upload dos documentos (frente, verso, comprovante e termo).',
                badge: 'Famílias',
                url: '{{ route('beneficiarios.index') }}',
                screenName: 'Beneficiários'
            },
            {
                targetDesktop: '#tour-nav-beneficiarios',
                targetMobile: '#tour-mobile-nav-beneficiarios',
                inPageTarget: '#tour-search-beneficiario',
                title: 'Consulta Rápida de Famílias',
                description: 'Localize cadastros por nome, CPF ou bairro. Acesse a ficha para visualizar documentos anexados e o histórico de entregas realizadas.',
                badge: 'Famílias',
                url: '{{ route('beneficiarios.index') }}',
                screenName: 'Beneficiários'
            },
            {
                targetDesktop: '#tour-nav-produtos',
                targetMobile: '#tour-mobile-nav-produtos',
                inPageTarget: '#tour-btn-novo-produto',
                inPageTargetMobile: '#tour-mobile-fab-produto',
                title: 'Controle de Estoque',
                description: 'Cadastre itens disponíveis para doação por categoria e unidade de medida. O sistema sinaliza automaticamente quando o saldo estiver abaixo do limite mínimo.',
                badge: 'Estoque',
                url: '{{ route('produtos.index') }}',
                screenName: 'Produtos'
            },
            {
                targetDesktop: '#tour-nav-retiradas',
                targetMobile: '#tour-mobile-nav-retiradas',
                inPageTarget: '#tour-btn-nova-retirada',
                inPageTargetMobile: '#tour-mobile-fab-retirada',
                title: 'Registro de Retiradas',
                description: 'Registre a entrega de doações vinculando o beneficiário e os itens entregues. A baixa no estoque de cada produto é realizada automaticamente.',
                badge: 'Retiradas',
                url: '{{ route('retiradas.index') }}',
                screenName: 'Retiradas'
            },
            {
                targetDesktop: '#tour-nav-retiradas',
                targetMobile: '#tour-mobile-nav-retiradas',
                inPageTarget: '#tour-filtros-data',
                title: 'Filtros de Retiradas por Data',
                description: 'Filtre as entregas por intervalo de datas para conferência de relatórios semanais, mensais ou por período específico.',
                badge: 'Retiradas',
                url: '{{ route('retiradas.index') }}',
                screenName: 'Retiradas'
            },
            {
                targetDesktop: '#tour-user-menu',
                targetMobile: '#tour-mobile-nav-mais',
                title: 'Configurações e Usuários',
                description: 'Gerencie preferências de aparência (Modo Claro/Escuro), credenciais de acesso, cadastro de operadores voluntários e acesse o suporte a qualquer momento.',
                badge: 'Configurações',
                url: '{{ route('dashboard') }}',
                screenName: 'Configurações'
            }
        ],

        init() {
            // Verifica se tem tour ativo salvo no sessionStorage (continuidade entre páginas)
            const savedTourStep = sessionStorage.getItem('asa_active_tour_step');
            if (savedTourStep !== null) {
                const stepNum = parseInt(savedTourStep, 10);
                if (!isNaN(stepNum) && stepNum >= 0 && stepNum < this.steps.length) {
                    this.currentStep = stepNum;
                    this.tourRunning = true;
                    setTimeout(() => {
                        this.updateSpotlight();
                    }, 400);
                }
            } else {
                // Se é primeira visita geral
                const completed = localStorage.getItem('asa_tour_completed');
                if (!completed) {
                    setTimeout(() => {
                        this.showFirstTimePrompt = true;
                    }, 1200);
                }
            }

            window.addEventListener('resize', () => {
                if (this.tourRunning) {
                    this.updateSpotlight();
                }
            });

            window.addEventListener('scroll', () => {
                if (this.tourRunning) {
                    this.updateSpotlight();
                }
            }, true);

            window.addEventListener('open-help-modal', () => {
                this.showHelpModal = true;
            });

            window.addEventListener('start-tour', () => {
                this.startTour();
            });

            document.addEventListener('livewire:navigated', () => {
                if (this.tourRunning) {
                    setTimeout(() => {
                        this.updateSpotlight();
                    }, 250);
                }
            });
        },

        startTour() {
            this.showHelpModal = false;
            this.showFirstTimePrompt = false;
            this.currentStep = 0;
            this.tourRunning = true;
            sessionStorage.setItem('asa_active_tour_step', '0');

            const step = this.steps[0];
            if (step && step.url) {
                const targetPath = new URL(step.url, window.location.origin).pathname;
                if (window.location.pathname !== targetPath) {
                    if (window.Livewire && window.Livewire.navigate) {
                        window.Livewire.navigate(step.url);
                        return;
                    } else {
                        window.location.href = step.url;
                        return;
                    }
                }
            }

            this.$nextTick(() => {
                this.updateSpotlight();
            });
        },

        nextStep() {
            if (this.currentStep < this.steps.length - 1) {
                this.currentStep++;
                sessionStorage.setItem('asa_active_tour_step', this.currentStep.toString());
                
                const step = this.steps[this.currentStep];
                if (step && step.url) {
                    const targetPath = new URL(step.url, window.location.origin).pathname;
                    if (window.location.pathname !== targetPath) {
                        if (window.Livewire && window.Livewire.navigate) {
                            window.Livewire.navigate(step.url);
                            return;
                        } else {
                            window.location.href = step.url;
                            return;
                        }
                    }
                }

                this.updateSpotlight();
            } else {
                this.finishTour();
            }
        },

        prevStep() {
            if (this.currentStep > 0) {
                this.currentStep--;
                sessionStorage.setItem('asa_active_tour_step', this.currentStep.toString());

                const step = this.steps[this.currentStep];
                if (step && step.url) {
                    const targetPath = new URL(step.url, window.location.origin).pathname;
                    if (window.location.pathname !== targetPath) {
                        if (window.Livewire && window.Livewire.navigate) {
                            window.Livewire.navigate(step.url);
                            return;
                        } else {
                            window.location.href = step.url;
                            return;
                        }
                    }
                }

                this.updateSpotlight();
            }
        },

        finishTour() {
            if (this.elevatedEl) {
                this.elevatedEl.style.zIndex = '';
                this.elevatedEl.style.position = '';
                this.elevatedEl = null;
            }
            this.tourRunning = false;
            sessionStorage.removeItem('asa_active_tour_step');
            localStorage.setItem('asa_tour_completed', 'true');
        },

        dismissFirstTime() {
            this.showFirstTimePrompt = false;
            localStorage.setItem('asa_tour_completed', 'true');
        },

        resolveTargetElement(step) {
            if (!step) return null;

            const isMobile = window.innerWidth < 1024;

            // 1. Tenta alvo específico mobile ou desktop
            if (isMobile && step.inPageTargetMobile) {
                const el = document.querySelector(step.inPageTargetMobile);
                if (el && el.offsetParent !== null) return el;
            }

            if (step.inPageTarget) {
                const el = document.querySelector(step.inPageTarget);
                if (el && el.offsetParent !== null) return el;
            }

            if (isMobile && step.targetMobile) {
                const el = document.querySelector(step.targetMobile);
                if (el && el.offsetParent !== null) return el;
            }

            if (!isMobile && step.targetDesktop) {
                const el = document.querySelector(step.targetDesktop);
                if (el && el.offsetParent !== null) return el;
            }

            if (step.target) {
                const el = document.querySelector(step.target);
                if (el && el.offsetParent !== null) return el;
            }

            return null;
        },

        updateSpotlight() {
            if (!this.tourRunning) return;

            const step = this.steps[this.currentStep];
            if (!step) return;

            if (this.elevatedEl) {
                this.elevatedEl.style.zIndex = '';
                this.elevatedEl.style.position = '';
                this.elevatedEl = null;
            }

            const targetEl = this.resolveTargetElement(step);

            if (!targetEl) {
                // Passo sem alvo físico ou tela diferente
                this.spotlightStyle = {
                    display: 'none'
                };
                this.cardPositionStyle = {
                    top: '50%',
                    left: '50%',
                    transform: 'translate(-50%, -50%)',
                    width: window.innerWidth < 640 ? 'calc(100vw - 32px)' : '460px',
                    position: 'fixed'
                };
                return;
            }

            targetEl.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'nearest' });

            const rect = targetEl.getBoundingClientRect();
            const padding = 8;

            this.spotlightStyle = {
                display: 'block',
                position: 'fixed',
                top: Math.max(0, rect.top - padding) + 'px',
                left: Math.max(0, rect.left - padding) + 'px',
                width: (rect.width + padding * 2) + 'px',
                height: (rect.height + padding * 2) + 'px',
                boxShadow: '0 0 0 9999px rgba(0, 0, 0, 0.72), 0 0 20px 2px rgba(16, 185, 129, 0.5)',
                borderRadius: '16px',
                border: '2px solid #10b981',
                zIndex: '9995',
                pointerEvents: 'none',
                transition: 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)'
            };

            const isMobile = window.innerWidth < 768;
            const cardWidth = isMobile ? Math.min(window.innerWidth - 32, 400) : 460;

            if (isMobile) {
                const isTargetAtBottom = rect.top > window.innerHeight / 2;

                if (isTargetAtBottom) {
                    this.cardPositionStyle = {
                        top: '16px',
                        left: '50%',
                        transform: 'translateX(-50%)',
                        width: cardWidth + 'px',
                        maxWidth: 'calc(100vw - 32px)',
                        position: 'fixed',
                        maxHeight: 'calc(100vh - 120px)',
                        overflowY: 'auto'
                    };
                } else {
                    this.cardPositionStyle = {
                        bottom: '76px',
                        left: '50%',
                        transform: 'translateX(-50%)',
                        width: cardWidth + 'px',
                        maxWidth: 'calc(100vw - 32px)',
                        position: 'fixed',
                        maxHeight: 'calc(100vh - 120px)',
                        overflowY: 'auto'
                    };
                }
            } else {
                let top = rect.top;
                let left = rect.right + 20;

                if (left + cardWidth > window.innerWidth) {
                    left = Math.max(20, rect.left - cardWidth - 20);
                }

                if (top + 320 > window.innerHeight) {
                    top = Math.max(20, window.innerHeight - 340);
                }

                this.cardPositionStyle = {
                    top: Math.max(20, top) + 'px',
                    left: left + 'px',
                    width: cardWidth + 'px',
                    position: 'fixed'
                };
            }
        }
    }"
    class="relative"
>

    {{-- BOTÃO FLUTUANTE DE AJUDA & TUTORIAL --}}
    <div id="tour-help-button" class="fixed bottom-20 lg:bottom-6 left-4 lg:left-auto lg:right-6 z-40">
        <button 
            @click="showHelpModal = true"
            type="button"
            class="flex items-center gap-2 px-3.5 py-2 lg:px-4 lg:py-2.5 rounded-full bg-emerald-700 hover:bg-emerald-800 active:bg-emerald-900 text-white font-semibold shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-200 border-2 border-white/20 focus:outline-hidden focus:ring-4 focus:ring-emerald-500/40 text-xs lg:text-sm cursor-pointer select-none"
            title="Manual e Instruções do Sistema"
            style="-webkit-tap-highlight-color: transparent;"
        >
            <span class="flex items-center justify-center w-5 h-5 lg:w-6 lg:h-6 rounded-full bg-white text-emerald-800 font-bold text-xs shadow-inner">
                ?
            </span>
            <span class="hidden sm:inline">Ajuda e Manual</span>
            <span class="sm:hidden">Ajuda</span>
        </button>
    </div>

    {{-- CONVITE DE APRESENTAÇÃO NA PRIMEIRA VISITA --}}
    <div 
        x-show="showFirstTimePrompt" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-8 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-8 scale-95"
        class="fixed bottom-24 lg:bottom-20 right-4 lg:right-6 z-50 max-w-sm w-[calc(100%-2rem)] p-5 bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl border-2 border-emerald-500/40 dark:border-emerald-500/50"
        style="display: none;"
    >
        <div class="flex items-start gap-3.5">
            <div class="p-2.5 bg-emerald-100 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300 rounded-xl shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="flex-1">
                <h4 class="font-bold text-base text-zinc-900 dark:text-zinc-50">Apresentação do Sistema ASA</h4>
                <p class="text-xs text-zinc-700 dark:text-zinc-300 mt-1 leading-relaxed">
                    Deseja visualizar um tour guiado de 1 minuto pelos módulos principais do sistema?
                </p>
                <div class="flex items-center gap-2 mt-4">
                    <button 
                        @click="startTour()" 
                        type="button" 
                        class="px-3.5 py-2 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold transition-colors cursor-pointer shadow-xs"
                    >
                        Iniciar Apresentação
                    </button>
                    <button 
                        @click="dismissFirstTime()" 
                        type="button" 
                        class="px-3 py-2 rounded-lg text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-xs font-semibold transition-colors cursor-pointer"
                    >
                        Depois
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- OVERLAY / HOLOFOTE (SPOTLIGHT) DO TOUR INTERATIVO --}}
    <div 
        x-show="tourRunning" 
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-[9990] pointer-events-auto"
        style="display: none;"
    >
        {{-- Fundo escuro quando for passo central (sem alvo) --}}
        <div 
            x-show="!steps[currentStep]?.target && !steps[currentStep]?.inPageTarget && !steps[currentStep]?.targetDesktop && !steps[currentStep]?.targetMobile" 
            class="absolute inset-0 bg-black/75 backdrop-blur-[2px] transition-all duration-300"
        ></div>

        {{-- Borda iluminada + Máscara Cutout no elemento focado --}}
        <div 
            :style="spotlightStyle"
            class="transition-all duration-300"
        ></div>

        {{-- CARD EXPLICATIVO DO PASSO ATUAL --}}
        <div 
            :style="cardPositionStyle"
            class="z-[9999] bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl border-2 border-emerald-600/50 dark:border-emerald-500/50 p-5 md:p-6 transition-all duration-300 text-zinc-900 dark:text-zinc-50"
        >
            {{-- Cabeçalho do Card com Badge e Fechar --}}
            <div class="flex items-center justify-between pb-3 border-b border-zinc-200 dark:border-zinc-800 gap-2">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-100 dark:bg-emerald-950/80 text-emerald-800 dark:text-emerald-300 text-xs font-bold shrink-0">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span x-text="steps[currentStep]?.badge"></span>
                    <span>&bull;</span>
                    <span x-text="`Passo ${currentStep + 1} de ${steps.length}`"></span>
                </span>

                <button 
                    @click="finishTour()" 
                    type="button" 
                    class="text-zinc-400 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-white p-1 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors cursor-pointer"
                    title="Fechar apresentação"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            {{-- Conteúdo Explicativo --}}
            <div class="py-3.5 space-y-2">
                <h3 class="text-base md:text-lg font-bold text-zinc-900 dark:text-zinc-100 flex items-center gap-2" x-text="steps[currentStep]?.title"></h3>
                <p class="text-xs md:text-sm text-zinc-700 dark:text-zinc-300 leading-relaxed" x-text="steps[currentStep]?.description"></p>
            </div>

            {{-- Barra de Progresso Verde ASA --}}
            <div class="w-full bg-zinc-200 dark:bg-zinc-800 h-1.5 rounded-full overflow-hidden my-3">
                <div 
                    class="bg-emerald-600 dark:bg-emerald-500 h-full rounded-full transition-all duration-300"
                    :style="`width: ${((currentStep + 1) / steps.length) * 100}%`"
                ></div>
            </div>

            {{-- Botões de Ação do Passo --}}
            <div class="flex items-center justify-between pt-1 gap-2">
                <button 
                    @click="prevStep()" 
                    x-show="currentStep > 0"
                    type="button" 
                    class="px-3.5 py-2 rounded-xl border border-zinc-300 dark:border-zinc-700 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-800 dark:text-zinc-200 text-xs md:text-sm font-semibold transition-colors cursor-pointer"
                >
                    &larr; Anterior
                </button>
                <div x-show="currentStep === 0"></div>

                <div class="flex items-center gap-2">
                    <button 
                        @click="finishTour()" 
                        type="button" 
                        class="px-2.5 py-2 text-zinc-500 hover:text-zinc-800 dark:text-zinc-400 dark:hover:text-zinc-200 text-xs font-semibold transition-colors cursor-pointer"
                    >
                        Encerrar
                    </button>

                    <button 
                        @click="nextStep()" 
                        type="button" 
                        class="px-4 py-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs md:text-sm font-bold shadow-md hover:shadow-lg transition-all cursor-pointer flex items-center gap-1"
                    >
                        <span x-text="currentStep === steps.length - 1 ? 'Concluir' : 'Próximo &rarr;'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL COMPLETO DA CENTRAL DE AJUDA & GUIA PRÁTICO --}}
    <div 
        x-show="showHelpModal" 
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6 bg-black/65 backdrop-blur-xs"
        style="display: none;"
    >
        <div 
            @click.outside="showHelpModal = false"
            class="bg-white dark:bg-zinc-900 w-full max-w-3xl rounded-2xl shadow-2xl border border-zinc-200 dark:border-zinc-800 max-h-[92vh] flex flex-col overflow-hidden"
        >
            {{-- Cabeçalho do Modal --}}
            <div class="flex items-center justify-between p-4 sm:p-5 border-b border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/90">
                <div class="flex items-center gap-3">
                    <div class="p-2 sm:p-2.5 rounded-xl bg-emerald-100 dark:bg-emerald-950/80 text-emerald-800 dark:text-emerald-300 font-bold shrink-0">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg sm:text-xl font-bold text-zinc-900 dark:text-zinc-100">Central de Ajuda e Manual de Operação</h2>
                        <p class="text-xs text-zinc-600 dark:text-zinc-400">Instruções e diretrizes para voluntários e operadores</p>
                    </div>
                </div>
                <button 
                    @click="showHelpModal = false" 
                    type="button" 
                    class="text-zinc-400 hover:text-zinc-700 dark:hover:text-white p-2 rounded-xl hover:bg-zinc-200 dark:hover:bg-zinc-800 transition-colors cursor-pointer"
                >
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            {{-- Destaque: Botão para Iniciar Apresentação Guiada --}}
            <div class="p-3.5 sm:p-4 bg-gradient-to-r from-emerald-800 to-teal-900 text-white flex flex-col sm:flex-row items-center justify-between gap-3 shadow-inner">
                <div class="space-y-0.5 text-center sm:text-left">
                    <span class="font-bold text-xs sm:text-sm">Apresentação Interativa em Tela</span>
                    <p class="text-[11px] sm:text-xs text-emerald-100">Destaque os elementos e atalhos na tela passo a passo.</p>
                </div>
                <button 
                    @click="startTour()" 
                    type="button" 
                    class="w-full sm:w-auto px-4 py-2 rounded-xl bg-white text-emerald-900 hover:bg-emerald-50 font-bold text-xs sm:text-sm shadow-md transition-all cursor-pointer shrink-0 text-center"
                >
                    Iniciar Apresentação
                </button>
            </div>

            {{-- Navegação por Abas do Manual --}}
            <div class="flex overflow-x-auto border-b border-zinc-200 dark:border-zinc-800 bg-zinc-100 dark:bg-zinc-950 px-2 pt-2 gap-1.5 text-xs sm:text-sm font-semibold select-none custom-scrollbar">
                <button 
                    @click="activeTab = 'geral'" 
                    :class="activeTab === 'geral' ? 'bg-white dark:bg-zinc-900 text-emerald-700 dark:text-emerald-400 border-b-2 border-emerald-600 shadow-xs' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200'"
                    class="px-3.5 py-2 rounded-t-xl transition-colors cursor-pointer whitespace-nowrap"
                >
                    Visão Geral
                </button>
                <button 
                    @click="activeTab = 'beneficiarios'" 
                    :class="activeTab === 'beneficiarios' ? 'bg-white dark:bg-zinc-900 text-emerald-700 dark:text-emerald-400 border-b-2 border-emerald-600 shadow-xs' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200'"
                    class="px-3.5 py-2 rounded-t-xl transition-colors cursor-pointer whitespace-nowrap"
                >
                    Famílias
                </button>
                <button 
                    @click="activeTab = 'produtos'" 
                    :class="activeTab === 'produtos' ? 'bg-white dark:bg-zinc-900 text-emerald-700 dark:text-emerald-400 border-b-2 border-emerald-600 shadow-xs' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200'"
                    class="px-3.5 py-2 rounded-t-xl transition-colors cursor-pointer whitespace-nowrap"
                >
                    Estoque
                </button>
                <button 
                    @click="activeTab = 'retiradas'" 
                    :class="activeTab === 'retiradas' ? 'bg-white dark:bg-zinc-900 text-emerald-700 dark:text-emerald-400 border-b-2 border-emerald-600 shadow-xs' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200'"
                    class="px-3.5 py-2 rounded-t-xl transition-colors cursor-pointer whitespace-nowrap"
                >
                    Retiradas
                </button>
                <button 
                    @click="activeTab = 'mobile'" 
                    :class="activeTab === 'mobile' ? 'bg-white dark:bg-zinc-900 text-emerald-700 dark:text-emerald-400 border-b-2 border-emerald-600 shadow-xs' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200'"
                    class="px-3.5 py-2 rounded-t-xl transition-colors cursor-pointer whitespace-nowrap"
                >
                    Dicas para Smartphones
                </button>
                <button 
                    @click="activeTab = 'faq'" 
                    :class="activeTab === 'faq' ? 'bg-white dark:bg-zinc-900 text-emerald-700 dark:text-emerald-400 border-b-2 border-emerald-600 shadow-xs' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200'"
                    class="px-3.5 py-2 rounded-t-xl transition-colors cursor-pointer whitespace-nowrap"
                >
                    Dúvidas Frequentes
                </button>
            </div>

            {{-- Conteúdo das Abas --}}
            <div class="p-4 sm:p-6 overflow-y-auto space-y-5 flex-1 text-zinc-800 dark:text-zinc-200 leading-relaxed text-xs sm:text-sm">

                {{-- ABA: GERAL --}}
                <div x-show="activeTab === 'geral'" class="space-y-4">
                    <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/60">
                        <h4 class="font-bold text-sm sm:text-base text-emerald-900 dark:text-emerald-200">Objetivo do Sistema ASA</h4>
                        <p class="text-zinc-700 dark:text-zinc-300 mt-1">
                            A plataforma ASA foi estruturada para centralizar o atendimento socioassistencial, controle rigoroso de estoques e rastreabilidade total das doações distribuídas.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="p-3.5 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-800/50 space-y-1.5">
                            <h5 class="font-bold text-zinc-900 dark:text-zinc-100">1. Famílias</h5>
                            <p class="text-xs text-zinc-600 dark:text-zinc-400">Cadastre os beneficiários, composição familiar e anexe documentos comprobatórios.</p>
                        </div>
                        <div class="p-3.5 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-800/50 space-y-1.5">
                            <h5 class="font-bold text-zinc-900 dark:text-zinc-100">2. Estoque</h5>
                            <p class="text-xs text-zinc-600 dark:text-zinc-400">Monitore as entradas, categorias e saldos mínimos de alimentos e mantimentos.</p>
                        </div>
                        <div class="p-3.5 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-800/50 space-y-1.5">
                            <h5 class="font-bold text-zinc-900 dark:text-zinc-100">3. Retiradas</h5>
                            <p class="text-xs text-zinc-600 dark:text-zinc-400">Registre entregas com baixa em tempo real e histórico auditável.</p>
                        </div>
                    </div>
                </div>

                {{-- ABA: BENEFICIARIOS --}}
                <div x-show="activeTab === 'beneficiarios'" class="space-y-4" style="display: none;">
                    <div class="flex items-center justify-between">
                        <h4 class="font-bold text-sm sm:text-base text-zinc-900 dark:text-zinc-100">Processo de Cadastro de Famílias</h4>
                        <a href="{{ route('beneficiarios.create') }}" wire:navigate @click="showHelpModal = false" class="text-xs font-bold text-emerald-700 dark:text-emerald-400 hover:underline">
                            Novo Cadastro &rarr;
                        </a>
                    </div>
                    <ol class="list-decimal list-inside space-y-2 text-zinc-700 dark:text-zinc-300">
                        <li>Acesse o menu <strong>Famílias</strong> e clique em <strong>Novo Beneficiário</strong>.</li>
                        <li>Preencha nome completo, telefone para contato, CPF, RG e endereço residencial.</li>
                        <li>Informe o número de pessoas no núcleo familiar e a idade dos dependentes.</li>
                        <li><strong>Anexos de Documentos:</strong> Capture fotos da frente do documento, verso, comprovante de endereço e termo assinado diretamente pela câmera do dispositivo ou galeria.</li>
                        <li>Revise os dados e clique em <strong>Salvar beneficiário</strong>.</li>
                    </ol>
                    <div class="p-3 rounded-lg bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300 text-xs">
                        <strong>Consulta e Histórico:</strong> Na listagem principal, utilize a busca por nome ou CPF para consultar fichas cadastrais e o histórico de retiradas anteriores.
                    </div>
                </div>

                {{-- ABA: PRODUTOS --}}
                <div x-show="activeTab === 'produtos'" class="space-y-4" style="display: none;">
                    <div class="flex items-center justify-between">
                        <h4 class="font-bold text-sm sm:text-base text-zinc-900 dark:text-zinc-100">Gerenciamento de Estoque</h4>
                        <a href="{{ route('produtos.create') }}" wire:navigate @click="showHelpModal = false" class="text-xs font-bold text-emerald-700 dark:text-emerald-400 hover:underline">
                            Novo Produto &rarr;
                        </a>
                    </div>
                    <ul class="list-disc list-inside space-y-2 text-zinc-700 dark:text-zinc-300">
                        <li>Acesse o menu <strong>Estoque</strong> e clique em <strong>Novo Produto</strong>.</li>
                        <li>Informe a descrição do produto (ex: <em>Arroz 5kg</em>, <em>Feijão 1kg</em>, <em>Leite 1L</em>, <em>Cobertor</em>).</li>
                        <li>Selecione a <strong>Categoria</strong> (Alimentos, Vestuário, Higiene, etc.) e a <strong>Unidade de Medida</strong> (kg, unidade, pacote).</li>
                        <li>Defina a quantidade inicial em estoque. Itens com quantidade inferior a 10 unidades receberão alerta de estoque baixo.</li>
                    </ul>
                </div>

                {{-- ABA: RETIRADAS --}}
                <div x-show="activeTab === 'retiradas'" class="space-y-4" style="display: none;">
                    <div class="flex items-center justify-between">
                        <h4 class="font-bold text-sm sm:text-base text-zinc-900 dark:text-zinc-100">Registro de Retiradas e Entregas</h4>
                        <a href="{{ route('retiradas.create') }}" wire:navigate @click="showHelpModal = false" class="text-xs font-bold text-emerald-700 dark:text-emerald-400 hover:underline">
                            Nova Retirada &rarr;
                        </a>
                    </div>
                    <ol class="list-decimal list-inside space-y-2 text-zinc-700 dark:text-zinc-300">
                        <li>Acesse o menu <strong>Retiradas</strong> e clique em <strong>Nova Retirada</strong>.</li>
                        <li>Selecione o beneficiário cadastrado.</li>
                        <li>Confirme a data do atendimento.</li>
                        <li>Adicione os itens e ajuste as quantidades através dos seletores.</li>
                        <li>Clique em <strong>Salvar retirada</strong> para efetivar a baixa imediata no estoque.</li>
                    </ol>
                </div>

                {{-- ABA: MOBILE --}}
                <div x-show="activeTab === 'mobile'" class="space-y-4" style="display: none;">
                    <h4 class="font-bold text-sm sm:text-base text-zinc-900 dark:text-zinc-100">Orientações para Dispositivos Móveis</h4>
                    <div class="space-y-3">
                        <div class="p-3 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-800/40">
                            <h5 class="font-bold text-zinc-900 dark:text-zinc-100">1. Botão Flutuante de Ação (+)</h5>
                            <p class="text-xs text-zinc-600 dark:text-zinc-400 mt-0.5">No smartphone, o botão circular no canto inferior direito permite iniciar novos cadastros em qualquer tela.</p>
                        </div>
                        <div class="p-3 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-800/40">
                            <h5 class="font-bold text-zinc-900 dark:text-zinc-100">2. Captura Direta pela Câmera</h5>
                            <p class="text-xs text-zinc-600 dark:text-zinc-400 mt-0.5">Nos formulários com fotos de documentos, o botão Câmera aciona a lente traseira do aparelho para foto instantânea.</p>
                        </div>
                        <div class="p-3 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-800/40">
                            <h5 class="font-bold text-zinc-900 dark:text-zinc-100">3. Barra de Navegação Inferior</h5>
                            <p class="text-xs text-zinc-600 dark:text-zinc-400 mt-0.5">Acesse os módulos operacionais através dos atalhos fixos na base da tela.</p>
                        </div>
                    </div>
                </div>

                {{-- ABA: FAQ --}}
                <div x-show="activeTab === 'faq'" class="space-y-3" style="display: none;">
                    <div class="border border-zinc-200 dark:border-zinc-800 rounded-xl p-3.5 space-y-1">
                        <h5 class="font-bold text-zinc-900 dark:text-zinc-100">Como alternar entre Modo Claro e Escuro?</h5>
                        <p class="text-xs text-zinc-600 dark:text-zinc-400">Acesse o menu <strong>Mais</strong> (ou perfil) &rarr; <strong>Configurações</strong> &rarr; <strong>Aparência</strong> e selecione o tema desejado.</p>
                    </div>

                    <div class="border border-zinc-200 dark:border-zinc-800 rounded-xl p-3.5 space-y-1">
                        <h5 class="font-bold text-zinc-900 dark:text-zinc-100">Como alterar a senha de acesso?</h5>
                        <p class="text-xs text-zinc-600 dark:text-zinc-400">Acesse <strong>Configurações</strong> &rarr; <strong>Segurança</strong>, insira a senha atual e defina a nova credencial.</p>
                    </div>

                    <div class="border border-zinc-200 dark:border-zinc-800 rounded-xl p-3.5 space-y-1">
                        <h5 class="font-bold text-zinc-900 dark:text-zinc-100">O que indica o status de Documentação (Pendente, Parcial, Completo)?</h5>
                        <p class="text-xs text-zinc-600 dark:text-zinc-400">Representa o preenchimento dos 4 comprovantes exigidos (frente do RG, verso do RG, termo de consentimento e comprovante de endereço). O status torna-se <strong>Completo (4/4)</strong> com o envio integral.</p>
                    </div>
                </div>

            </div>

            {{-- Rodapé do Modal --}}
            <div class="p-3.5 sm:p-4 border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/90 flex justify-end">
                <button 
                    @click="showHelpModal = false" 
                    type="button" 
                    class="px-5 py-2 rounded-xl bg-zinc-200 dark:bg-zinc-800 hover:bg-zinc-300 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 font-bold text-xs sm:text-sm transition-colors cursor-pointer"
                >
                    Fechar
                </button>
            </div>
        </div>
    </div>

</div>

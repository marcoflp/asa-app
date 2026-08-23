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
                title: '👋 Bem-vindo ao Sistema ASA!',
                description: 'Este sistema foi criado para organizar o atendimento a famílias, controle de doações e entregas com muita facilidade e clareza.',
                badge: 'Início',
                position: 'center'
            },
            {
                target: '#tour-nav-dashboard',
                title: '📊 Início & Relatórios',
                description: 'Aqui você acompanha o resumo rápido do mês: quantidade de atendimentos, total de doações entregues e produtos mais retirados.',
                badge: 'Navegação',
                position: 'right'
            },
            {
                target: '#tour-nav-beneficiarios',
                title: '👥 Beneficiários (Famílias)',
                description: 'Aqui você cadastra e consulta as famílias atendidas, incluindo telefone, endereço, número de pessoas na casa e fotos dos documentos (RG, CPF e comprovante).',
                badge: 'Módulo 1',
                position: 'right'
            },
            {
                target: '#tour-nav-produtos',
                title: '📦 Produtos & Estoque',
                description: 'Gerencie os itens disponíveis para doação (arroz, feijão, leite, roupas, cobertores, etc.), acompanhe as quantidades e receba alertas do que está acabando.',
                badge: 'Módulo 2',
                position: 'right'
            },
            {
                target: '#tour-nav-retiradas',
                title: '🤝 Retiradas de Doações',
                description: 'Sempre que uma família retirar uma cesta ou doação, registre aqui. O sistema dá baixa automática no estoque e guarda o histórico com a data da entrega.',
                badge: 'Módulo 3',
                position: 'right'
            },
            {
                target: '#tour-user-menu',
                title: '⚙️ Meu Perfil & Segurança',
                description: 'Neste menu você pode alterar sua senha, trocar entre modo Claro ou Escuro e gerenciar outros voluntários cadastrados no sistema.',
                badge: 'Configurações',
                position: 'top-right'
            },
            {
                target: '#tour-help-button',
                title: '💡 Central de Ajuda Sempre Disponível',
                description: 'Ficou com alguma dúvida? Você pode clicar neste botão a qualquer momento para abrir o manual explicativo ou refazer este tour guiado!',
                badge: 'Ajuda',
                position: 'top-left'
            }
        ],

        init() {
            // Verifica se é a primeira vez do usuário
            const completed = localStorage.getItem('asa_tour_completed');
            if (!completed) {
                setTimeout(() => {
                    this.showFirstTimePrompt = true;
                }, 1200);
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
        },

        startTour() {
            this.showHelpModal = false;
            this.showFirstTimePrompt = false;
            this.currentStep = 0;
            this.tourRunning = true;
            this.$nextTick(() => {
                this.updateSpotlight();
            });
        },

        nextStep() {
            if (this.currentStep < this.steps.length - 1) {
                this.currentStep++;
                this.updateSpotlight();
            } else {
                this.finishTour();
            }
        },

        prevStep() {
            if (this.currentStep > 0) {
                this.currentStep--;
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
            localStorage.setItem('asa_tour_completed', 'true');
        },

        dismissFirstTime() {
            this.showFirstTimePrompt = false;
            localStorage.setItem('asa_tour_completed', 'true');
        },

        updateSpotlight() {
            // Limpa elevação do elemento anterior
            if (this.elevatedEl) {
                this.elevatedEl.style.zIndex = '';
                this.elevatedEl.style.position = '';
                this.elevatedEl = null;
            }

            const step = this.steps[this.currentStep];
            if (!step || !step.target) {
                // Passo central sem alvo específico
                this.spotlightStyle = { display: 'none' };
                this.cardPositionStyle = {
                    top: '50%',
                    left: '50%',
                    transform: 'translate(-50%, -50%)',
                    position: 'fixed'
                };
                return;
            }

            const el = document.querySelector(step.target);
            if (!el) {
                // Se elemento não estiver visível (ex: mobile fechado), centraliza
                this.spotlightStyle = { display: 'none' };
                this.cardPositionStyle = {
                    top: '50%',
                    left: '50%',
                    transform: 'translate(-50%, -50%)',
                    position: 'fixed'
                };
                return;
            }

            // Eleva o elemento alvo para que fique 100% visível e nítido
            el.style.zIndex = '9995';
            el.style.position = 'relative';
            this.elevatedEl = el;

            // Garante visibilidade do elemento
            el.scrollIntoView({ behavior: 'smooth', block: 'center', inline: 'center' });

            const rect = el.getBoundingClientRect();
            const padding = 6;

            this.spotlightStyle = {
                display: 'block',
                top: (rect.top - padding) + 'px',
                left: (rect.left - padding) + 'px',
                width: (rect.width + padding * 2) + 'px',
                height: (rect.height + padding * 2) + 'px',
                position: 'fixed',
                boxShadow: '0 0 0 9999px rgba(0, 0, 0, 0.78), 0 0 20px rgba(245, 158, 11, 0.6)',
                zIndex: '9990',
                borderRadius: '12px',
                border: '3px solid #f59e0b',
                pointerEvents: 'none',
                backgroundColor: 'transparent'
            };

            const isMobile = window.innerWidth < 768;
            const cardWidth = Math.min(window.innerWidth - 32, 420);

            if (isMobile) {
                this.cardPositionStyle = {
                    bottom: '24px',
                    left: '50%',
                    transform: 'translateX(-50%)',
                    width: cardWidth + 'px',
                    position: 'fixed'
                };
            } else {
                // Posicionamento inteligente em desktop
                let top = rect.top;
                let left = rect.right + 20;

                if (step.position === 'top-right') {
                    top = rect.top - 20;
                    left = Math.max(20, rect.left - cardWidth - 20);
                } else if (step.position === 'top-left') {
                    top = Math.max(20, rect.top - 260);
                    left = Math.max(20, rect.right - cardWidth);
                } else if (left + cardWidth > window.innerWidth) {
                    left = Math.max(20, rect.left - cardWidth - 20);
                }

                // Garante que não saia da tela verticalmente
                if (top + 280 > window.innerHeight) {
                    top = Math.max(20, window.innerHeight - 300);
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
    <div id="tour-help-button" class="fixed bottom-20 lg:bottom-5 left-4 lg:left-auto lg:right-5 z-40">
        <button 
            @click="showHelpModal = true"
            type="button"
            class="flex items-center gap-2 px-3.5 py-2 lg:px-4 lg:py-2.5 rounded-full bg-emerald-700 hover:bg-emerald-800 text-white font-semibold shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-200 border-2 border-white/20 focus:outline-none focus:ring-4 focus:ring-emerald-500/40 text-xs lg:text-sm cursor-pointer"
            title="Clique para ver o Tutorial e Guia do Sistema"
        >
            <span class="flex items-center justify-center w-5 h-5 lg:w-6 lg:h-6 rounded-full bg-white text-emerald-800 font-bold text-xs shadow-inner">
                ?
            </span>
            <span class="hidden sm:inline">Ajuda & Tutorial</span>
            <span class="sm:hidden">Ajuda</span>
        </button>
    </div>

    {{-- CONVITE DE BOAS-VINDAS NA PRIMEIRA VISITA --}}
    <div 
        x-show="showFirstTimePrompt" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-8 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-8 scale-95"
        class="fixed bottom-24 lg:bottom-20 right-4 lg:right-5 z-50 max-w-sm w-[calc(100%-2rem)] p-5 bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl border-2 border-emerald-500/30 dark:border-emerald-500/40"
        style="display: none;"
    >
        <div class="flex items-start gap-3.5">
            <div class="p-2.5 bg-emerald-100 dark:bg-emerald-950/70 text-emerald-700 dark:text-emerald-300 rounded-xl shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="flex-1">
                <h4 class="font-bold text-base text-zinc-900 dark:text-zinc-50">Bem-vindo(a) ao sistema da ASA!</h4>
                <p class="text-xs text-zinc-700 dark:text-zinc-300 mt-1 leading-relaxed">
                    Gostaria de fazer um tour guiado de 1 minuto para conhecer onde fica cada ferramenta?
                </p>
                <div class="flex items-center gap-2 mt-4">
                    <button 
                        @click="startTour()" 
                        type="button" 
                        class="px-3.5 py-2 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold transition-colors cursor-pointer shadow-sm"
                    >
                        Iniciar Tour Agora
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
        {{-- Fundo escuro APENAS quando for um passo central (sem alvo específico) --}}
        <div 
            x-show="!steps[currentStep]?.target" 
            class="absolute inset-0 bg-black/75 backdrop-blur-[2px] transition-all duration-300"
        ></div>

        {{-- Borda iluminada + Máscara Cutout em volta do elemento com foco --}}
        <div 
            x-show="steps[currentStep]?.target"
            :style="spotlightStyle"
            class="transition-all duration-300"
        ></div>

        {{-- CARD EXPLICATIVO DO PASSO ATUAL --}}
        <div 
            :style="cardPositionStyle"
            class="z-[9999] bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl border-2 border-emerald-600/40 dark:border-amber-400/40 p-5 md:p-6 transition-all duration-300 text-zinc-900 dark:text-zinc-50"
        >
            {{-- Cabeçalho do Card com Badge e Fechar --}}
            <div class="flex items-center justify-between pb-3 border-b border-zinc-200 dark:border-zinc-800">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-100 dark:bg-emerald-950/80 text-emerald-800 dark:text-emerald-300 text-xs font-bold">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span x-text="steps[currentStep]?.badge"></span>
                    <span>&bull;</span>
                    <span x-text="`Passo ${currentStep + 1} de ${steps.length}`"></span>
                </span>

                <button 
                    @click="finishTour()" 
                    type="button" 
                    class="text-zinc-500 hover:text-zinc-800 dark:text-zinc-400 dark:hover:text-white p-1 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors cursor-pointer"
                    title="Pular / Fechar tutorial"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            {{-- Conteúdo Explicativo --}}
            <div class="py-4 space-y-2">
                <h3 class="text-lg md:text-xl font-bold text-zinc-900 dark:text-zinc-100" x-text="steps[currentStep]?.title"></h3>
                <p class="text-sm md:text-base text-zinc-700 dark:text-zinc-300 leading-relaxed" x-text="steps[currentStep]?.description"></p>
            </div>

            {{-- Barra de Progresso --}}
            <div class="w-full bg-zinc-200 dark:bg-zinc-800 h-2 rounded-full overflow-hidden my-3">
                <div 
                    class="bg-emerald-600 dark:bg-amber-400 h-full rounded-full transition-all duration-300"
                    :style="`width: ${((currentStep + 1) / steps.length) * 100}%`"
                ></div>
            </div>

            {{-- Ações do Tour: Anterior / Próximo / Pular --}}
            <div class="flex items-center justify-between pt-2 gap-2">
                <button 
                    @click="prevStep()" 
                    x-show="currentStep > 0"
                    type="button" 
                    class="px-4 py-2 rounded-xl border border-zinc-300 dark:border-zinc-700 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-800 dark:text-zinc-200 text-xs md:text-sm font-semibold transition-colors cursor-pointer"
                >
                    &larr; Anterior
                </button>
                <div x-show="currentStep === 0"></div>

                <div class="flex items-center gap-2">
                    <button 
                        @click="finishTour()" 
                        type="button" 
                        class="px-3 py-2 text-zinc-500 hover:text-zinc-800 dark:text-zinc-400 dark:hover:text-zinc-200 text-xs font-semibold transition-colors cursor-pointer"
                    >
                        Pular Tour
                    </button>

                    <button 
                        @click="nextStep()" 
                        type="button" 
                        class="px-5 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 dark:bg-amber-500 dark:hover:bg-amber-600 text-white dark:text-zinc-950 text-xs md:text-sm font-bold shadow-md hover:shadow-lg transition-all cursor-pointer flex items-center gap-1.5"
                    >
                        <span x-text="currentStep === steps.length - 1 ? 'Concluir 🎉' : 'Próximo &rarr;'"></span>
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
        class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 bg-black/60 backdrop-blur-sm"
        style="display: none;"
    >
        <div 
            @click.outside="showHelpModal = false"
            class="bg-white dark:bg-zinc-900 w-full max-w-3xl rounded-2xl shadow-2xl border border-zinc-200 dark:border-zinc-800 max-h-[90vh] flex flex-col overflow-hidden"
        >
            {{-- Cabeçalho do Modal --}}
            <div class="flex items-center justify-between p-5 border-b border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/90">
                <div class="flex items-center gap-3">
                    <div class="p-2.5 rounded-xl bg-emerald-100 dark:bg-emerald-950/70 text-emerald-800 dark:text-emerald-300 font-bold">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-zinc-900 dark:text-zinc-100">Central de Ajuda & Guia do Usuário</h2>
                        <p class="text-xs text-zinc-600 dark:text-zinc-400">Instruções simples e diretas para utilizar o sistema ASA</p>
                    </div>
                </div>
                <button 
                    @click="showHelpModal = false" 
                    type="button" 
                    class="text-zinc-500 hover:text-zinc-800 dark:text-zinc-400 dark:hover:text-white p-2 rounded-xl hover:bg-zinc-200 dark:hover:bg-zinc-800 transition-colors cursor-pointer"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            {{-- Destaque: Botão para Iniciar Tour Interativo --}}
            <div class="p-4 bg-gradient-to-r from-emerald-600 to-teal-700 text-white flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="space-y-0.5 text-center sm:text-left">
                    <span class="font-bold text-sm">Prefere ver na prática?</span>
                    <p class="text-xs text-emerald-100">Inicie o tour interativo para destacar cada botão e tela na sua frente.</p>
                </div>
                <button 
                    @click="startTour()" 
                    type="button" 
                    class="px-4 py-2 rounded-xl bg-white text-emerald-800 font-bold text-xs md:text-sm shadow hover:bg-emerald-50 transition-all cursor-pointer shrink-0"
                >
                    🚀 Iniciar Tour Interativo na Tela
                </button>
            </div>

            {{-- Navegação por Abas do Manual --}}
            <div class="flex overflow-x-auto border-b border-zinc-200 dark:border-zinc-800 bg-zinc-100 dark:bg-zinc-950 px-3 pt-2 gap-2 text-sm font-semibold">
                <button 
                    @click="activeTab = 'geral'" 
                    :class="activeTab === 'geral' ? 'bg-white dark:bg-zinc-900 text-emerald-700 dark:text-emerald-400 border-b-2 border-emerald-600' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200'"
                    class="px-4 py-2.5 rounded-t-xl transition-colors cursor-pointer whitespace-nowrap"
                >
                    🏠 Visão Geral
                </button>
                <button 
                    @click="activeTab = 'beneficiarios'" 
                    :class="activeTab === 'beneficiarios' ? 'bg-white dark:bg-zinc-900 text-emerald-700 dark:text-emerald-400 border-b-2 border-emerald-600' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200'"
                    class="px-4 py-2.5 rounded-t-xl transition-colors cursor-pointer whitespace-nowrap"
                >
                    👥 Beneficiários
                </button>
                <button 
                    @click="activeTab = 'produtos'" 
                    :class="activeTab === 'produtos' ? 'bg-white dark:bg-zinc-900 text-emerald-700 dark:text-emerald-400 border-b-2 border-emerald-600' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200'"
                    class="px-4 py-2.5 rounded-t-xl transition-colors cursor-pointer whitespace-nowrap"
                >
                    📦 Produtos & Estoque
                </button>
                <button 
                    @click="activeTab = 'retiradas'" 
                    :class="activeTab === 'retiradas' ? 'bg-white dark:bg-zinc-900 text-emerald-700 dark:text-emerald-400 border-b-2 border-emerald-600' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200'"
                    class="px-4 py-2.5 rounded-t-xl transition-colors cursor-pointer whitespace-nowrap"
                >
                    🤝 Retiradas
                </button>
                <button 
                    @click="activeTab = 'faq'" 
                    :class="activeTab === 'faq' ? 'bg-white dark:bg-zinc-900 text-emerald-700 dark:text-emerald-400 border-b-2 border-emerald-600' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200'"
                    class="px-4 py-2.5 rounded-t-xl transition-colors cursor-pointer whitespace-nowrap"
                >
                    ❓ Perguntas Frequentes
                </button>
            </div>

            {{-- Conteúdo das Abas --}}
            <div class="p-6 overflow-y-auto space-y-6 flex-1 text-zinc-800 dark:text-zinc-200 leading-relaxed text-sm">

                {{-- ABA: GERAL --}}
                <div x-show="activeTab === 'geral'" class="space-y-5">
                    <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/60">
                        <h4 class="font-bold text-base text-emerald-900 dark:text-emerald-200">Como o sistema funciona?</h4>
                        <p class="text-zinc-700 dark:text-zinc-300 mt-1">
                            O ASA foi desenvolvido para que qualquer pessoa consiga registrar as doações e famílias atendidas sem complicação. Ele é dividido em 3 pilares principais:
                        </p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="p-4 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-800/50 space-y-2">
                            <span class="text-2xl">👥</span>
                            <h5 class="font-bold text-zinc-900 dark:text-zinc-100">1. Famílias</h5>
                            <p class="text-xs text-zinc-600 dark:text-zinc-400">Cadastre quem recebe a ajuda e anexe fotos dos documentos para controle.</p>
                        </div>
                        <div class="p-4 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-800/50 space-y-2">
                            <span class="text-2xl">📦</span>
                            <h5 class="font-bold text-zinc-900 dark:text-zinc-100">2. Doações / Estoque</h5>
                            <p class="text-xs text-zinc-600 dark:text-zinc-400">Cadastre os alimentos, agasalhos e produtos que chegam para doação.</p>
                        </div>
                        <div class="p-4 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-800/50 space-y-2">
                            <span class="text-2xl">🤝</span>
                            <h5 class="font-bold text-zinc-900 dark:text-zinc-100">3. Entregas (Retiradas)</h5>
                            <p class="text-xs text-zinc-600 dark:text-zinc-400">Registre quando a família retira os itens. O estoque é atualizado na hora!</p>
                        </div>
                    </div>
                </div>

                {{-- ABA: BENEFICIARIOS --}}
                <div x-show="activeTab === 'beneficiarios'" class="space-y-4" style="display: none;">
                    <h4 class="font-bold text-base text-zinc-900 dark:text-zinc-100">Como cadastrar uma nova família:</h4>
                    <ol class="list-decimal list-inside space-y-2.5 text-zinc-700 dark:text-zinc-300">
                        <li>Clique em <strong>Beneficiários</strong> no menu lateral e depois no botão <strong>+ Novo Beneficiário</strong>.</li>
                        <li>Preencha o <strong>Nome completo</strong> (obrigatório), telefone, CPF, RG e endereço.</li>
                        <li>Informe quantas pessoas moram na casa e as idades dos filhos.</li>
                        <li><strong>Fotos de Documentos:</strong> Você pode tirar fotos direto pelo celular ou computador da frente/verso do RG, comprovante de residência e termo assinado.</li>
                        <li>Clique em <strong>Salvar beneficiário</strong> no final da página.</li>
                    </ol>
                    <div class="p-3 rounded-lg bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 text-amber-900 dark:text-amber-200 text-xs">
                        💡 <strong>Dica de busca:</strong> Na lista de beneficiários, você pode digitar o nome ou CPF para encontrar a pessoa em segundos!
                    </div>
                </div>

                {{-- ABA: PRODUTOS --}}
                <div x-show="activeTab === 'produtos'" class="space-y-4" style="display: none;">
                    <h4 class="font-bold text-base text-zinc-900 dark:text-zinc-100">Como gerenciar os produtos:</h4>
                    <ul class="list-disc list-inside space-y-2 text-zinc-700 dark:text-zinc-300">
                        <li>Acesse <strong>Produtos</strong> no menu lateral.</li>
                        <li>Clique em <strong>+ Novo Produto</strong> para adicionar um item (ex: Arroz, Feijão, Cobertor).</li>
                        <li>Escolha a <strong>Categoria</strong> (Alimentos, Roupas, Higiene, etc.) e a <strong>Unidade</strong> (kg, unidade, pacote).</li>
                        <li>Se quiser controlar quantidade em estoque, digite o número no campo <strong>Estoque atual</strong>. Caso não queira controlar, deixe vazio.</li>
                    </ul>
                </div>

                {{-- ABA: RETIRADAS --}}
                <div x-show="activeTab === 'retiradas'" class="space-y-4" style="display: none;">
                    <h4 class="font-bold text-base text-zinc-900 dark:text-zinc-100">Como registrar uma entrega de doação:</h4>
                    <ol class="list-decimal list-inside space-y-2.5 text-zinc-700 dark:text-zinc-300">
                        <li>Acesse <strong>Retiradas</strong> no menu lateral e clique em <strong>+ Nova Retirada</strong>.</li>
                        <li>Selecione o <strong>Beneficiário</strong> que está recebendo os itens.</li>
                        <li>Confira ou ajuste a <strong>Data da entrega</strong>.</li>
                        <li>Clique nos produtos doados para adicioná-los à lista e ajuste as quantidades com os botões <strong>+</strong> e <strong>-</strong>.</li>
                        <li>Clique em <strong>Salvar retirada</strong>. O sistema baixa o estoque automaticamente!</li>
                    </ol>
                </div>

                {{-- ABA: FAQ --}}
                <div x-show="activeTab === 'faq'" class="space-y-4" style="display: none;">
                    <div class="border border-zinc-200 dark:border-zinc-800 rounded-xl p-4 space-y-1">
                        <h5 class="font-bold text-zinc-900 dark:text-zinc-100">Como trocar entre Modo Claro e Escuro?</h5>
                        <p class="text-xs text-zinc-600 dark:text-zinc-400">Clique no seu nome no canto inferior do menu &rarr; <strong>Configurações</strong> &rarr; <strong>Aparência</strong> e escolha o tema que preferir.</p>
                    </div>

                    <div class="border border-zinc-200 dark:border-zinc-800 rounded-xl p-4 space-y-1">
                        <h5 class="font-bold text-zinc-900 dark:text-zinc-100">Como alterar minha senha de acesso?</h5>
                        <p class="text-xs text-zinc-600 dark:text-zinc-400">Vá em <strong>Configurações</strong> &rarr; <strong>Segurança</strong>, informe sua senha atual e digite a nova senha desejada.</p>
                    </div>

                    <div class="border border-zinc-200 dark:border-zinc-800 rounded-xl p-4 space-y-1">
                        <h5 class="font-bold text-zinc-900 dark:text-zinc-100">Como tirar fotos dos documentos pelo celular?</h5>
                        <p class="text-xs text-zinc-600 dark:text-zinc-400">Ao tocar no botão de envio da foto no cadastro do beneficiário, o próprio celular abrirá a câmera para você tirar a foto na hora.</p>
                    </div>
                </div>

            </div>

            {{-- Rodapé do Modal --}}
            <div class="p-4 border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/90 flex justify-end">
                <button 
                    @click="showHelpModal = false" 
                    type="button" 
                    class="px-5 py-2.5 rounded-xl bg-zinc-200 dark:bg-zinc-800 hover:bg-zinc-300 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 font-bold text-sm transition-colors cursor-pointer"
                >
                    Entendido / Fechar
                </button>
            </div>
        </div>
    </div>

</div>

<div class="flex h-full w-full flex-1 flex-col gap-6 max-w-4xl mx-auto">

        <div class="flex items-center gap-3">
            <flux:button href="{{ route('beneficiarios.index') }}" variant="ghost" icon="arrow-left" wire:navigate />
            <flux:heading size="xl">
                {{ $beneficiario?->exists ? 'Editar Beneficiário' : 'Novo Beneficiário' }}
            </flux:heading>
        </div>

        <form wire:submit="save" class="space-y-6">
            <flux:error name="geral" />

            {{-- Dados Pessoais --}}
            <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 p-5 space-y-4">
                <flux:heading size="lg">Dados Pessoais</flux:heading>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:field class="md:col-span-2">
                        <flux:label>Nome completo *</flux:label>
                        <flux:input wire:model="nome" placeholder="Nome do beneficiário" />
                        <flux:error name="nome" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Telefone</flux:label>
                        <flux:input wire:model="telefone" placeholder="(54) 99999-9999" type="tel" inputmode="tel" />
                        <flux:error name="telefone" />
                    </flux:field>

                    <flux:field>
                        <flux:label>CPF</flux:label>
                        <flux:input wire:model="cpf" placeholder="000.000.000-00" inputmode="numeric" />
                        <flux:error name="cpf" />
                    </flux:field>

                    <flux:field>
                        <flux:label>RG</flux:label>
                        <flux:input wire:model="rg" placeholder="0000000000" inputmode="numeric" />
                        <flux:error name="rg" />
                    </flux:field>

                    {{-- Documentos (Frente, Verso, Consentimento e Comprovante de Residência) --}}
                    <div class="md:col-span-2 border-t border-neutral-100 dark:border-neutral-800 pt-4">
                        <div class="mb-3">
                            <flux:heading size="md" class="text-zinc-900 dark:text-zinc-100 font-bold">Fotos de Documentos</flux:heading>
                            <p class="text-xs text-zinc-600 dark:text-zinc-400 mt-0.5">Tire fotos diretamente com a câmera do celular ou selecione fotos da galeria.</p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            {{-- 1. Frente --}}
                            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 p-3.5 bg-zinc-50/50 dark:bg-zinc-800/40 space-y-2.5 flex flex-col justify-between" x-data="{}">
                                <div>
                                    <span class="block font-bold text-xs text-zinc-900 dark:text-zinc-100 mb-1">1. Documento (Frente)</span>
                                    
                                    {{-- Inputs Ocultos --}}
                                    <input 
                                        type="file" 
                                        x-ref="cameraFrente" 
                                        wire:model="foto_documento" 
                                        accept="image/*" 
                                        capture="environment" 
                                        class="hidden" 
                                    />
                                    <input 
                                        type="file" 
                                        x-ref="galleryFrente" 
                                        wire:model="foto_documento" 
                                        accept="image/*,image/heic,image/heif,.heic,.heif,application/pdf" 
                                        class="hidden" 
                                    />

                                    {{-- Loading --}}
                                    <div wire:loading wire:target="foto_documento" class="flex items-center gap-1.5 text-xs text-emerald-600 dark:text-emerald-400 font-bold py-1">
                                        <svg class="animate-spin h-3.5 w-3.5" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        <span>Carregando...</span>
                                    </div>
                                    <flux:error name="foto_documento" />

                                    {{-- Preview --}}
                                    @if ($foto_documento)
                                        <div class="mt-1.5 relative group">
                                            @if (in_array(strtolower($foto_documento->getClientOriginalExtension() ?? ''), ['heic', 'heif']))
                                                <div class="h-28 w-full rounded-lg border border-emerald-300 dark:border-emerald-700 bg-emerald-50 dark:bg-emerald-950/40 flex flex-col items-center justify-center text-xs text-emerald-800 dark:text-emerald-300 gap-1 p-2">
                                                    <span class="font-bold">📱 Foto HEIC</span>
                                                    <span class="text-[10px] text-zinc-500">Pronta para salvar</span>
                                                </div>
                                            @else
                                                <img src="{{ $foto_documento->temporaryUrl() }}" class="h-28 w-full rounded-lg object-cover border-2 border-emerald-500/60 shadow-xs">
                                            @endif
                                            <span class="absolute top-1 right-1 bg-emerald-700 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-md shadow">Nova Foto</span>
                                        </div>
                                    @elseif ($foto_documento_path)
                                        <div class="mt-1.5 relative group">
                                            <a href="{{ asset('storage/' . $foto_documento_path) }}" target="_blank" class="block">
                                                <img src="{{ asset('storage/' . $foto_documento_path) }}" class="h-28 w-full rounded-lg object-cover border border-zinc-300 dark:border-zinc-700 shadow-xs group-hover:opacity-90 transition-opacity">
                                            </a>
                                            <span class="absolute top-1 right-1 bg-zinc-800/80 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-md backdrop-blur-xs">Salvo</span>
                                        </div>
                                    @else
                                        <div class="h-24 w-full rounded-lg border-2 border-dashed border-zinc-200 dark:border-zinc-700 flex flex-col items-center justify-center text-zinc-400 dark:text-zinc-500 text-xs gap-1">
                                            <svg class="w-6 h-6 text-zinc-300 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                            <span>Nenhuma foto</span>
                                        </div>
                                    @endif
                                </div>

                                {{-- Botões --}}
                                <div class="grid grid-cols-2 gap-1.5 pt-1 border-t border-zinc-100 dark:border-zinc-800">
                                    <button 
                                        type="button" 
                                        @click="$refs.cameraFrente.click()" 
                                        class="flex items-center justify-center gap-1 py-2 px-1.5 rounded-lg bg-emerald-700 hover:bg-emerald-800 active:bg-emerald-900 text-white font-bold text-xs shadow-xs transition-all cursor-pointer"
                                    >
                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        <span>Câmera</span>
                                    </button>

                                    <button 
                                        type="button" 
                                        @click="$refs.galleryFrente.click()" 
                                        class="flex items-center justify-center gap-1 py-2 px-1.5 rounded-lg bg-white dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 hover:bg-zinc-100 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-200 font-bold text-xs transition-all cursor-pointer"
                                    >
                                        <svg class="w-4 h-4 shrink-0 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        <span>Galeria</span>
                                    </button>
                                </div>
                            </div>

                            {{-- 2. Verso --}}
                            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 p-3.5 bg-zinc-50/50 dark:bg-zinc-800/40 space-y-2.5 flex flex-col justify-between" x-data="{}">
                                <div>
                                    <span class="block font-bold text-xs text-zinc-900 dark:text-zinc-100 mb-1">2. Documento (Verso)</span>
                                    
                                    {{-- Inputs Ocultos --}}
                                    <input 
                                        type="file" 
                                        x-ref="cameraVerso" 
                                        wire:model="foto_documento_verso" 
                                        accept="image/*" 
                                        capture="environment" 
                                        class="hidden" 
                                    />
                                    <input 
                                        type="file" 
                                        x-ref="galleryVerso" 
                                        wire:model="foto_documento_verso" 
                                        accept="image/*,image/heic,image/heif,.heic,.heif,application/pdf" 
                                        class="hidden" 
                                    />

                                    {{-- Loading --}}
                                    <div wire:loading wire:target="foto_documento_verso" class="flex items-center gap-1.5 text-xs text-emerald-600 dark:text-emerald-400 font-bold py-1">
                                        <svg class="animate-spin h-3.5 w-3.5" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        <span>Carregando...</span>
                                    </div>
                                    <flux:error name="foto_documento_verso" />

                                    {{-- Preview --}}
                                    @if ($foto_documento_verso)
                                        <div class="mt-1.5 relative group">
                                            @if (in_array(strtolower($foto_documento_verso->getClientOriginalExtension() ?? ''), ['heic', 'heif']))
                                                <div class="h-28 w-full rounded-lg border border-emerald-300 dark:border-emerald-700 bg-emerald-50 dark:bg-emerald-950/40 flex flex-col items-center justify-center text-xs text-emerald-800 dark:text-emerald-300 gap-1 p-2">
                                                    <span class="font-bold">📱 Foto HEIC</span>
                                                    <span class="text-[10px] text-zinc-500">Pronta para salvar</span>
                                                </div>
                                            @else
                                                <img src="{{ $foto_documento_verso->temporaryUrl() }}" class="h-28 w-full rounded-lg object-cover border-2 border-emerald-500/60 shadow-xs">
                                            @endif
                                            <span class="absolute top-1 right-1 bg-emerald-700 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-md shadow">Nova Foto</span>
                                        </div>
                                    @elseif ($foto_documento_verso_path)
                                        <div class="mt-1.5 relative group">
                                            <a href="{{ asset('storage/' . $foto_documento_verso_path) }}" target="_blank" class="block">
                                                <img src="{{ asset('storage/' . $foto_documento_verso_path) }}" class="h-28 w-full rounded-lg object-cover border border-zinc-300 dark:border-zinc-700 shadow-xs group-hover:opacity-90 transition-opacity">
                                            </a>
                                            <span class="absolute top-1 right-1 bg-zinc-800/80 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-md backdrop-blur-xs">Salvo</span>
                                        </div>
                                    @else
                                        <div class="h-24 w-full rounded-lg border-2 border-dashed border-zinc-200 dark:border-zinc-700 flex flex-col items-center justify-center text-zinc-400 dark:text-zinc-500 text-xs gap-1">
                                            <svg class="w-6 h-6 text-zinc-300 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                            <span>Nenhuma foto</span>
                                        </div>
                                    @endif
                                </div>

                                {{-- Botões --}}
                                <div class="grid grid-cols-2 gap-1.5 pt-1 border-t border-zinc-100 dark:border-zinc-800">
                                    <button 
                                        type="button" 
                                        @click="$refs.cameraVerso.click()" 
                                        class="flex items-center justify-center gap-1 py-2 px-1.5 rounded-lg bg-emerald-700 hover:bg-emerald-800 active:bg-emerald-900 text-white font-bold text-xs shadow-xs transition-all cursor-pointer"
                                    >
                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        <span>Câmera</span>
                                    </button>

                                    <button 
                                        type="button" 
                                        @click="$refs.galleryVerso.click()" 
                                        class="flex items-center justify-center gap-1 py-2 px-1.5 rounded-lg bg-white dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 hover:bg-zinc-100 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-200 font-bold text-xs transition-all cursor-pointer"
                                    >
                                        <svg class="w-4 h-4 shrink-0 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        <span>Galeria</span>
                                    </button>
                                </div>
                            </div>

                            {{-- 3. Consentimento --}}
                            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 p-3.5 bg-zinc-50/50 dark:bg-zinc-800/40 space-y-2.5 flex flex-col justify-between" x-data="{}">
                                <div>
                                    <span class="block font-bold text-xs text-zinc-900 dark:text-zinc-100 mb-1">3. Termo de Consentimento</span>
                                    
                                    {{-- Inputs Ocultos --}}
                                    <input 
                                        type="file" 
                                        x-ref="cameraConsentimento" 
                                        wire:model="foto_documento_consentimento" 
                                        accept="image/*" 
                                        capture="environment" 
                                        class="hidden" 
                                    />
                                    <input 
                                        type="file" 
                                        x-ref="galleryConsentimento" 
                                        wire:model="foto_documento_consentimento" 
                                        accept="image/*,image/heic,image/heif,.heic,.heif,application/pdf" 
                                        class="hidden" 
                                    />

                                    {{-- Loading --}}
                                    <div wire:loading wire:target="foto_documento_consentimento" class="flex items-center gap-1.5 text-xs text-emerald-600 dark:text-emerald-400 font-bold py-1">
                                        <svg class="animate-spin h-3.5 w-3.5" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        <span>Carregando...</span>
                                    </div>
                                    <flux:error name="foto_documento_consentimento" />

                                    {{-- Preview --}}
                                    @if ($foto_documento_consentimento)
                                        <div class="mt-1.5 relative group">
                                            @if (in_array(strtolower($foto_documento_consentimento->getClientOriginalExtension() ?? ''), ['heic', 'heif']))
                                                <div class="h-28 w-full rounded-lg border border-emerald-300 dark:border-emerald-700 bg-emerald-50 dark:bg-emerald-950/40 flex flex-col items-center justify-center text-xs text-emerald-800 dark:text-emerald-300 gap-1 p-2">
                                                    <span class="font-bold">📱 Foto HEIC</span>
                                                    <span class="text-[10px] text-zinc-500">Pronta para salvar</span>
                                                </div>
                                            @else
                                                <img src="{{ $foto_documento_consentimento->temporaryUrl() }}" class="h-28 w-full rounded-lg object-cover border-2 border-emerald-500/60 shadow-xs">
                                            @endif
                                            <span class="absolute top-1 right-1 bg-emerald-700 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-md shadow">Nova Foto</span>
                                        </div>
                                    @elseif ($foto_documento_consentimento_path)
                                        <div class="mt-1.5 relative group">
                                            <a href="{{ asset('storage/' . $foto_documento_consentimento_path) }}" target="_blank" class="block">
                                                <img src="{{ asset('storage/' . $foto_documento_consentimento_path) }}" class="h-28 w-full rounded-lg object-cover border border-zinc-300 dark:border-zinc-700 shadow-xs group-hover:opacity-90 transition-opacity">
                                            </a>
                                            <span class="absolute top-1 right-1 bg-zinc-800/80 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-md backdrop-blur-xs">Salvo</span>
                                        </div>
                                    @else
                                        <div class="h-24 w-full rounded-lg border-2 border-dashed border-zinc-200 dark:border-zinc-700 flex flex-col items-center justify-center text-zinc-400 dark:text-zinc-500 text-xs gap-1">
                                            <svg class="w-6 h-6 text-zinc-300 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                            <span>Nenhuma foto</span>
                                        </div>
                                    @endif
                                </div>

                                {{-- Botões --}}
                                <div class="grid grid-cols-2 gap-1.5 pt-1 border-t border-zinc-100 dark:border-zinc-800">
                                    <button 
                                        type="button" 
                                        @click="$refs.cameraConsentimento.click()" 
                                        class="flex items-center justify-center gap-1 py-2 px-1.5 rounded-lg bg-emerald-700 hover:bg-emerald-800 active:bg-emerald-900 text-white font-bold text-xs shadow-xs transition-all cursor-pointer"
                                    >
                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        <span>Câmera</span>
                                    </button>

                                    <button 
                                        type="button" 
                                        @click="$refs.galleryConsentimento.click()" 
                                        class="flex items-center justify-center gap-1 py-2 px-1.5 rounded-lg bg-white dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 hover:bg-zinc-100 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-200 font-bold text-xs transition-all cursor-pointer"
                                    >
                                        <svg class="w-4 h-4 shrink-0 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        <span>Galeria</span>
                                    </button>
                                </div>
                            </div>

                            {{-- 4. Comprovante de Residência --}}
                            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 p-3.5 bg-zinc-50/50 dark:bg-zinc-800/40 space-y-2.5 flex flex-col justify-between" x-data="{}">
                                <div>
                                    <span class="block font-bold text-xs text-zinc-900 dark:text-zinc-100 mb-1">4. Comprovante de Endereço</span>
                                    
                                    {{-- Inputs Ocultos --}}
                                    <input 
                                        type="file" 
                                        x-ref="cameraComprovante" 
                                        wire:model="foto_documento_comprovante_residencia" 
                                        accept="image/*" 
                                        capture="environment" 
                                        class="hidden" 
                                    />
                                    <input 
                                        type="file" 
                                        x-ref="galleryComprovante" 
                                        wire:model="foto_documento_comprovante_residencia" 
                                        accept="image/*,image/heic,image/heif,.heic,.heif,application/pdf" 
                                        class="hidden" 
                                    />

                                    {{-- Loading --}}
                                    <div wire:loading wire:target="foto_documento_comprovante_residencia" class="flex items-center gap-1.5 text-xs text-emerald-600 dark:text-emerald-400 font-bold py-1">
                                        <svg class="animate-spin h-3.5 w-3.5" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        <span>Carregando...</span>
                                    </div>
                                    <flux:error name="foto_documento_comprovante_residencia" />

                                    {{-- Preview --}}
                                    @if ($foto_documento_comprovante_residencia)
                                        <div class="mt-1.5 relative group">
                                            @if (in_array(strtolower($foto_documento_comprovante_residencia->getClientOriginalExtension() ?? ''), ['heic', 'heif']))
                                                <div class="h-28 w-full rounded-lg border border-emerald-300 dark:border-emerald-700 bg-emerald-50 dark:bg-emerald-950/40 flex flex-col items-center justify-center text-xs text-emerald-800 dark:text-emerald-300 gap-1 p-2">
                                                    <span class="font-bold">📱 Foto HEIC</span>
                                                    <span class="text-[10px] text-zinc-500">Pronta para salvar</span>
                                                </div>
                                            @else
                                                <img src="{{ $foto_documento_comprovante_residencia->temporaryUrl() }}" class="h-28 w-full rounded-lg object-cover border-2 border-emerald-500/60 shadow-xs">
                                            @endif
                                            <span class="absolute top-1 right-1 bg-emerald-700 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-md shadow">Nova Foto</span>
                                        </div>
                                    @elseif ($foto_documento_comprovante_residencia_path)
                                        <div class="mt-1.5 relative group">
                                            <a href="{{ asset('storage/' . $foto_documento_comprovante_residencia_path) }}" target="_blank" class="block">
                                                <img src="{{ asset('storage/' . $foto_documento_comprovante_residencia_path) }}" class="h-28 w-full rounded-lg object-cover border border-zinc-300 dark:border-zinc-700 shadow-xs group-hover:opacity-90 transition-opacity">
                                            </a>
                                            <span class="absolute top-1 right-1 bg-zinc-800/80 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-md backdrop-blur-xs">Salvo</span>
                                        </div>
                                    @else
                                        <div class="h-24 w-full rounded-lg border-2 border-dashed border-zinc-200 dark:border-zinc-700 flex flex-col items-center justify-center text-zinc-400 dark:text-zinc-500 text-xs gap-1">
                                            <svg class="w-6 h-6 text-zinc-300 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                            <span>Nenhuma foto</span>
                                        </div>
                                    @endif
                                </div>

                                {{-- Botões --}}
                                <div class="grid grid-cols-2 gap-1.5 pt-1 border-t border-zinc-100 dark:border-zinc-800">
                                    <button 
                                        type="button" 
                                        @click="$refs.cameraComprovante.click()" 
                                        class="flex items-center justify-center gap-1 py-2 px-1.5 rounded-lg bg-emerald-700 hover:bg-emerald-800 active:bg-emerald-900 text-white font-bold text-xs shadow-xs transition-all cursor-pointer"
                                    >
                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        <span>Câmera</span>
                                    </button>

                                    <button 
                                        type="button" 
                                        @click="$refs.galleryComprovante.click()" 
                                        class="flex items-center justify-center gap-1 py-2 px-1.5 rounded-lg bg-white dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 hover:bg-zinc-100 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-200 font-bold text-xs transition-all cursor-pointer"
                                    >
                                        <svg class="w-4 h-4 shrink-0 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        <span>Galeria</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Endereço --}}
            <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 p-5 space-y-4">
                <flux:heading size="lg">Endereço</flux:heading>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <flux:field class="md:col-span-2">
                        <flux:label>Rua</flux:label>
                        <flux:input wire:model="rua" placeholder="Nome da rua" />
                        <flux:error name="rua" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Número</flux:label>
                        <flux:input wire:model="numero" placeholder="123" inputmode="numeric" />
                        <flux:error name="numero" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Bairro</flux:label>
                        <flux:input wire:model="bairro" placeholder="Bairro" />
                        <flux:error name="bairro" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Cidade</flux:label>
                        <flux:input wire:model="cidade" />
                        <flux:error name="cidade" />
                    </flux:field>

                    <flux:field>
                        <flux:label>CEP</flux:label>
                        <flux:input wire:model="cep" placeholder="99000-000" inputmode="numeric" />
                        <flux:error name="cep" />
                    </flux:field>
                </div>
            </div>

            {{-- Composição Familiar --}}
            <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 p-5 space-y-4">
                <flux:heading size="lg">Composição Familiar</flux:heading>

                <flux:field>
                    <flux:label>Número de pessoas na família *</flux:label>
                    <flux:input type="number" wire:model="num_pessoas_familia" min="1" inputmode="numeric" class="w-32" />
                    <flux:error name="num_pessoas_familia" />
                </flux:field>

                <div>
                    <flux:label class="mb-2 block">Filhos</flux:label>

                    @if (count($filhos) > 0)
                        <div class="flex flex-wrap gap-2 mb-3">
                            @foreach ($filhos as $i => $filho)
                                <div class="flex items-center gap-1 rounded-full bg-blue-100 dark:bg-blue-900/30 px-3 py-1 text-sm">
                                    <span>{{ $filho['idade'] }} ano(s)</span>
                                    <button type="button" wire:click="removeFilho({{ $i }})" class="ml-1 text-red-500 hover:text-red-700">×</button>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="flex items-end gap-2">
                        <flux:field>
                            <flux:label>Idade do filho</flux:label>
                            <flux:input type="number" wire:model="filho_idade" min="0" max="17" class="w-28" placeholder="0" />
                        </flux:field>
                        <flux:button type="button" wire:click="addFilho" variant="ghost" icon="plus">
                            Adicionar filho
                        </flux:button>
                    </div>
                </div>
            </div>

            {{-- Programas Sociais --}}
            <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 p-5 space-y-4">
                <flux:heading size="lg">Programas Sociais</flux:heading>

                <flux:field>
                    <flux:checkbox wire:model.live="inscrito_programa_governo" label="Inscrito em programa do governo" />
                </flux:field>

                @if ($inscrito_programa_governo)
                    <flux:field>
                        <flux:label>Qual programa?</flux:label>
                        <flux:input wire:model="programa_governo" placeholder="Ex: Bolsa Família, BPC..." />
                        <flux:error name="programa_governo" />
                    </flux:field>
                @endif
            </div>

            {{-- Estudo Bíblico --}}
            <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 p-5 space-y-4">
                <flux:heading size="lg">Estudo Bíblico</flux:heading>

                <flux:field>
                    <flux:checkbox wire:model.live="recebe_estudo_biblico" label="Recebe estudo bíblico" />
                </flux:field>

                @if ($recebe_estudo_biblico)
                    <flux:field>
                        <flux:label>Instrutor</flux:label>
                        <flux:input wire:model="instrutor_biblico" placeholder="Nome do instrutor" />
                        <flux:error name="instrutor_biblico" />
                    </flux:field>
                @endif
            </div>

            {{-- Observações --}}
            <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 p-5 space-y-4">
                <flux:heading size="lg">Observações</flux:heading>
                <flux:field>
                    <flux:textarea wire:model="observacoes" rows="3" placeholder="Informações adicionais..." />
                </flux:field>
            </div>

            <div class="flex flex-col-reverse sm:flex-row justify-end gap-3">
                <flux:button href="{{ route('beneficiarios.index') }}" variant="ghost" wire:navigate class="w-full sm:w-auto">Cancelar</flux:button>
                <flux:button type="submit" variant="primary" class="w-full sm:w-auto" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="foto_documento, foto_documento_verso, foto_documento_consentimento, foto_documento_comprovante_residencia, save">
                        {{ $beneficiario?->exists ? 'Salvar alterações' : 'Cadastrar beneficiário' }}
                    </span>
                    <span wire:loading wire:target="foto_documento, foto_documento_verso, foto_documento_consentimento, foto_documento_comprovante_residencia">
                        <flux:icon name="photo" class="inline-block w-4 h-4 mr-2" />
                        Carregando imagens...
                    </span>
                    <span wire:loading wire:target="save">
                        Salvando...
                    </span>
                </flux:button>
            </div>

        </form>
    </div>

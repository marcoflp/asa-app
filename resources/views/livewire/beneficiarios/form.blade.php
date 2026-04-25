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
                        <flux:input wire:model="telefone" placeholder="(54) 99999-9999" />
                        <flux:error name="telefone" />
                    </flux:field>

                    <flux:field>
                        <flux:label>CPF</flux:label>
                        <flux:input wire:model="cpf" placeholder="000.000.000-00" />
                        <flux:error name="cpf" />
                    </flux:field>

                    <flux:field>
                        <flux:label>RG</flux:label>
                        <flux:input wire:model="rg" placeholder="0000000000" />
                        <flux:error name="rg" />
                    </flux:field>

                    <flux:field class="md:col-span-2">
                        <flux:label>Foto do documento</flux:label>
                        <flux:input type="file" wire:model="foto_documento" accept="image/*" />
                        <div wire:loading wire:target="foto_documento" class="text-xs text-blue-500 mt-1">Carregando imagem...</div>
                        <flux:error name="foto_documento" />
                        
                        @if ($foto_documento)
                            <div class="mt-2">
                                <flux:text size="sm" class="mb-1">Pré-visualização:</flux:text>
                                <img src="{{ $foto_documento->temporaryUrl() }}" class="h-32 rounded-lg object-cover border border-neutral-200">
                            </div>
                        @elseif ($foto_documento_path)
                            <div class="mt-2">
                                <flux:text size="sm" class="mb-1">Arquivo atual:</flux:text>
                                <a href="{{ asset('storage/' . $foto_documento_path) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $foto_documento_path) }}" class="h-32 rounded-lg object-cover border border-neutral-200 hover:opacity-80 transition-opacity">
                                </a>
                            </div>
                        @endif
                    </flux:field>
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
                        <flux:input wire:model="numero" placeholder="123" />
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
                        <flux:input wire:model="cep" placeholder="99000-000" />
                        <flux:error name="cep" />
                    </flux:field>
                </div>
            </div>

            {{-- Composição Familiar --}}
            <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 p-5 space-y-4">
                <flux:heading size="lg">Composição Familiar</flux:heading>

                <flux:field>
                    <flux:label>Número de pessoas na família *</flux:label>
                    <flux:input type="number" wire:model="num_pessoas_familia" min="1" class="w-32" />
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

            <div class="flex justify-end gap-3">
                <flux:button href="{{ route('beneficiarios.index') }}" variant="ghost" wire:navigate>Cancelar</flux:button>
                <flux:button type="submit" variant="primary">
                    {{ $beneficiario?->exists ? 'Salvar alterações' : 'Cadastrar beneficiário' }}
                </flux:button>
            </div>

        </form>
    </div>

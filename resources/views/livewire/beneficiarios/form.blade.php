<div class="flex h-full w-full flex-1 flex-col gap-6 max-w-4xl mx-auto"
     x-on:formulario-invalido.window="setTimeout(() => {
         const erro = [...document.querySelectorAll('[data-flux-error]')].find(e => !e.classList.contains('hidden'));
         erro?.closest('[data-secao]')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
         erro?.closest('[data-flux-field]')?.querySelector('input, select, textarea')?.focus({ preventScroll: true });
     }, 60)">

    <div class="flex items-center gap-3">
        <flux:button href="{{ route('beneficiarios.index') }}" variant="ghost" icon="arrow-left" wire:navigate aria-label="Voltar para beneficiários" />
        <div>
            <flux:heading size="xl">{{ $beneficiario?->exists ? 'Editar beneficiário' : 'Novo beneficiário' }}</flux:heading>
            <flux:text class="mt-0.5">Campos com * são obrigatórios. O resto pode ser preenchido depois.</flux:text>
        </div>
    </div>

    <form wire:submit="save" class="space-y-5" novalidate>
        @if ($errors->has('geral'))
            <flux:callout variant="danger" icon="x-circle" heading="{{ $errors->first('geral') }}" />
        @endif

        {{-- Dados pessoais --}}
        <section data-secao class="rounded-xl border border-neutral-200 dark:border-neutral-700 p-5 space-y-4 scroll-mt-6">
            <flux:heading size="lg">Dados pessoais</flux:heading>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-start">
                <flux:field class="md:col-span-2">
                    <flux:label>Nome completo *</flux:label>
                    <flux:input wire:model.live.blur="nome" autocomplete="off" autocapitalize="words" :autofocus="! $beneficiario?->exists" />
                    <flux:error name="nome" />
                </flux:field>

                @if ($this->parecidos->isNotEmpty())
                    <div class="md:col-span-2">
                        <flux:callout variant="warning" icon="user-group" heading="Já existe cadastro parecido">
                            <flux:callout.text>
                                Confira se não é a mesma pessoa antes de cadastrar de novo:
                                @foreach ($this->parecidos as $p)
                                    <a href="{{ route('beneficiarios.edit', $p) }}" wire:navigate class="font-semibold underline underline-offset-2">{{ $p->nome }}</a>@if ($p->bairro) ({{ $p->bairro }})@endif{{ $loop->last ? '' : ',' }}
                                @endforeach
                            </flux:callout.text>
                        </flux:callout>
                    </div>
                @endif

                <flux:field>
                    <flux:label>Telefone</flux:label>
                    <flux:input wire:model.live.blur="telefone" placeholder="(54) 99999-9999" type="tel" inputmode="tel" autocomplete="off"
                        x-mask:dynamic="$input.replace(/\D/g, '').length > 10 ? '(99) 99999-9999' : '(99) 9999-99999'" />
                    <flux:error name="telefone" />
                </flux:field>

                <flux:field>
                    <flux:label>CPF</flux:label>
                    <flux:input wire:model.live.blur="cpf" placeholder="000.000.000-00" inputmode="numeric" autocomplete="off" x-mask="999.999.999-99" />
                    <flux:error name="cpf" />
                </flux:field>

                <flux:field>
                    <flux:label>RG</flux:label>
                    <flux:input wire:model="rg" inputmode="numeric" autocomplete="off" x-mask="9999999999" />
                    <flux:error name="rg" />
                </flux:field>
            </div>
        </section>

        {{-- Documentos --}}
        <section data-secao class="rounded-xl border border-neutral-200 dark:border-neutral-700 p-5 space-y-4 scroll-mt-6">
            <div>
                <flux:heading size="lg">Fotos dos documentos</flux:heading>
                <flux:text class="mt-0.5">Tire a foto com a câmera do celular ou escolha da galeria.</flux:text>
            </div>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                <x-foto-documento campo="foto_documento" titulo="Documento (frente)" :arquivo="$foto_documento" :salvo="$foto_documento_path" />
                <x-foto-documento campo="foto_documento_verso" titulo="Documento (verso)" :arquivo="$foto_documento_verso" :salvo="$foto_documento_verso_path" />
                <x-foto-documento campo="foto_documento_consentimento" titulo="Termo de consentimento" :arquivo="$foto_documento_consentimento" :salvo="$foto_documento_consentimento_path" />
                <x-foto-documento campo="foto_documento_comprovante_residencia" titulo="Comprovante de endereço" :arquivo="$foto_documento_comprovante_residencia" :salvo="$foto_documento_comprovante_residencia_path" />
            </div>
        </section>

        {{-- Endereço --}}
        <section data-secao class="rounded-xl border border-neutral-200 dark:border-neutral-700 p-5 space-y-4 scroll-mt-6">
            <flux:heading size="lg">Endereço</flux:heading>

            <div class="grid grid-cols-1 md:grid-cols-6 gap-4 items-start">
                <flux:field class="md:col-span-2">
                    <flux:label>CEP</flux:label>
                    <flux:input wire:model.live.debounce.400ms="cep" placeholder="99000-000" inputmode="numeric" autocomplete="off" x-mask="99999-999" />
                    <div wire:loading wire:target="cep" class="mt-1.5 text-xs text-neutral-500">Buscando endereço...</div>
                    <div wire:loading.remove wire:target="cep">
                        @if ($cepStatus === 'ok')
                            <p class="mt-1.5 text-xs text-emerald-700 dark:text-emerald-400">Endereço preenchido pelo CEP. Confira abaixo.</p>
                        @elseif ($cepStatus === 'nao-encontrado')
                            <p class="mt-1.5 text-xs text-amber-700 dark:text-amber-400">CEP não encontrado. Preencha o endereço à mão.</p>
                        @elseif ($cepStatus === 'nao-consultado')
                            <p class="mt-1.5 text-xs text-neutral-500">Não deu para buscar o CEP agora. Preencha à mão.</p>
                        @else
                            <p class="mt-1.5 text-xs text-neutral-500">Digite o CEP para preencher o endereço.</p>
                        @endif
                    </div>
                    <flux:error name="cep" />
                </flux:field>

                <flux:field class="md:col-span-4">
                    <flux:label>Rua</flux:label>
                    <flux:input wire:model="rua" autocomplete="off" />
                    <flux:error name="rua" />
                </flux:field>

                <flux:field class="md:col-span-1">
                    <flux:label>Número</flux:label>
                    <flux:input wire:model="numero" autocomplete="off" />
                    <flux:error name="numero" />
                </flux:field>

                <flux:field class="md:col-span-3">
                    <flux:label>Bairro</flux:label>
                    <x-campo-sugestoes model="bairro" :opcoes="$this->bairrosDisponiveis" placeholder="Digite para buscar o bairro" novo="Cadastrar novo bairro" />
                    <flux:error name="bairro" />
                </flux:field>

                <flux:field class="md:col-span-2">
                    <flux:label>Cidade *</flux:label>
                    <flux:input wire:model="cidade" autocomplete="off" />
                    <flux:error name="cidade" />
                </flux:field>
            </div>
        </section>

        {{-- Família --}}
        <section data-secao class="rounded-xl border border-neutral-200 dark:border-neutral-700 p-5 space-y-5 scroll-mt-6">
            <flux:heading size="lg">Família</flux:heading>

            <flux:field>
                <flux:label>Quantas pessoas moram na casa? *</flux:label>
                <div class="flex items-center gap-2">
                    <flux:button type="button" icon="minus" aria-label="Menos uma pessoa"
                        wire:click="$set('num_pessoas_familia', {{ max(1, $num_pessoas_familia - 1) }})" :disabled="$num_pessoas_familia <= 1" />
                    <div class="w-20"><flux:input type="number" wire:model.live.blur="num_pessoas_familia" min="1" max="30" inputmode="numeric" class:input="text-center" aria-label="Pessoas na casa" /></div>
                    <flux:button type="button" icon="plus" aria-label="Mais uma pessoa"
                        wire:click="$set('num_pessoas_familia', {{ $num_pessoas_familia + 1 }})" />
                </div>
                <flux:error name="num_pessoas_familia" />
            </flux:field>

            <div>
                <flux:label>Filhos menores de idade</flux:label>

                @if (count($filhos) > 0)
                    <ul class="flex flex-wrap gap-2 mt-2">
                        @foreach ($filhos as $i => $filho)
                            <li wire:key="filho-{{ $i }}" class="flex items-center gap-1 rounded-full bg-accent/10 text-accent-content pl-3 pr-1 py-1 text-sm font-medium">
                                {{ $filho['idade'] == 0 ? 'Menos de 1 ano' : $filho['idade'].' '.($filho['idade'] == 1 ? 'ano' : 'anos') }}
                                <button type="button" wire:click="removeFilho({{ $i }})" aria-label="Remover filho de {{ $filho['idade'] }} anos"
                                    class="w-6 h-6 rounded-full flex items-center justify-center hover:bg-red-100 hover:text-red-600 dark:hover:bg-red-900/40 transition-colors">×</button>
                            </li>
                        @endforeach
                    </ul>
                @endif

                <div class="mt-2 flex items-start gap-2">
                    <flux:field class="w-32">
                        <flux:input type="number" wire:model="filho_idade" min="0" max="17" inputmode="numeric" aria-label="Idade do filho"
                            placeholder="Idade" x-on:keydown.enter.prevent="$wire.addFilho()" />
                    </flux:field>
                    <flux:button type="button" wire:click="addFilho" icon="plus">Adicionar filho</flux:button>
                </div>
                <flux:error name="filho_idade" />

                @if (count($filhos) >= $num_pessoas_familia)
                    <p class="mt-2 text-sm text-amber-700 dark:text-amber-400">
                        Há {{ count($filhos) }} {{ count($filhos) === 1 ? 'filho' : 'filhos' }} para {{ $num_pessoas_familia }} {{ $num_pessoas_familia === 1 ? 'pessoa' : 'pessoas' }} na casa. Confira o número de pessoas.
                    </p>
                @endif
            </div>
        </section>

        {{-- Acompanhamento --}}
        <section data-secao class="rounded-xl border border-neutral-200 dark:border-neutral-700 p-5 space-y-5 scroll-mt-6">
            <flux:heading size="lg">Programas e acompanhamento</flux:heading>

            <div class="space-y-3">
                <flux:switch wire:model.live="inscrito_programa_governo" label="Participa de programa do governo" align="left" />
                @if ($inscrito_programa_governo)
                    <flux:field>
                        <flux:label>Qual programa?</flux:label>
                        <flux:input wire:model="programa_governo" placeholder="Ex: Bolsa Família, BPC" />
                        <flux:error name="programa_governo" />
                    </flux:field>
                @endif
            </div>

            <flux:separator variant="subtle" />

            <div class="space-y-3">
                <flux:switch wire:model.live="recebe_estudo_biblico" label="Recebe estudo bíblico" align="left" />
                @if ($recebe_estudo_biblico)
                    <flux:field>
                        <flux:label>Quem dá o estudo?</flux:label>
                        <flux:input wire:model="instrutor_biblico" />
                        <flux:error name="instrutor_biblico" />
                    </flux:field>
                @endif
            </div>
        </section>

        {{-- Observações --}}
        <section data-secao class="rounded-xl border border-neutral-200 dark:border-neutral-700 p-5 scroll-mt-6">
            <flux:field>
                <flux:label>Observações</flux:label>
                <flux:textarea wire:model="observacoes" rows="3" placeholder="Algo importante sobre a família" />
            </flux:field>
        </section>

        <div class="flex flex-col-reverse sm:flex-row justify-end gap-3">
            <flux:button href="{{ route('beneficiarios.index') }}" variant="ghost" wire:navigate class="w-full sm:w-auto">Cancelar</flux:button>
            <flux:button type="submit" variant="primary" class="w-full sm:w-auto"
                wire:loading.attr="disabled" wire:target="save, foto_documento, foto_documento_verso, foto_documento_consentimento, foto_documento_comprovante_residencia">
                <span wire:loading.remove wire:target="save, foto_documento, foto_documento_verso, foto_documento_consentimento, foto_documento_comprovante_residencia">
                    {{ $beneficiario?->exists ? 'Salvar alterações' : 'Cadastrar beneficiário' }}
                </span>
                <span wire:loading wire:target="foto_documento, foto_documento_verso, foto_documento_consentimento, foto_documento_comprovante_residencia">Enviando fotos...</span>
                <span wire:loading wire:target="save">Salvando...</span>
            </flux:button>
        </div>
    </form>
</div>

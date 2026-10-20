<div class="flex h-full w-full flex-1 flex-col gap-6 max-w-xl mx-auto"
     x-on:formulario-invalido.window="setTimeout(() => {
         const erro = [...document.querySelectorAll('[data-flux-error]')].find(e => !e.classList.contains('hidden'));
         erro?.closest('[data-flux-field]')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
         erro?.closest('[data-flux-field]')?.querySelector('input, textarea')?.focus({ preventScroll: true });
     }, 60)">

    <div class="flex items-center gap-3">
        <flux:button href="{{ route('produtos.index') }}" variant="ghost" icon="arrow-left" wire:navigate aria-label="Voltar para produtos" />
        <flux:heading size="xl">{{ $produto?->exists ? 'Editar produto' : 'Novo produto' }}</flux:heading>
    </div>

    <form wire:submit="save" class="space-y-5" novalidate>
        @if ($errors->has('geral'))
            <flux:callout variant="danger" icon="x-circle" heading="{{ $errors->first('geral') }}" />
        @endif

        <section class="rounded-xl border border-neutral-200 dark:border-neutral-700 p-5 space-y-4">
            <flux:field>
                <flux:label>Nome *</flux:label>
                <flux:input wire:model.live.blur="nome" placeholder="Ex: Arroz, Cobertor, Sabonete" autocomplete="off" :autofocus="! $produto?->exists" />
                <flux:error name="nome" />
            </flux:field>

            @if ($this->parecidos->isNotEmpty())
                <flux:callout variant="warning" icon="exclamation-triangle" heading="Já existe um produto com esse nome">
                    <flux:callout.text>
                        @foreach ($this->parecidos as $p)
                            <a href="{{ route('produtos.edit', $p) }}" wire:navigate class="font-semibold underline underline-offset-2">{{ $p->nome }}</a>
                            ({{ $p->categoria }}{{ $p->ativo ? '' : ', inativo' }}){{ $loop->last ? '.' : ',' }}
                        @endforeach
                        Se for o mesmo, edite o cadastro existente em vez de criar outro.
                    </flux:callout.text>
                </flux:callout>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-start">
                <flux:field>
                    <flux:label>Categoria *</flux:label>
                    <x-campo-sugestoes model="categoria" :opcoes="$this->categorias" placeholder="Ex: Alimento" novo="Criar categoria" />
                    <flux:error name="categoria" />
                </flux:field>

                <flux:field>
                    <flux:label>Unidade *</flux:label>
                    <x-campo-sugestoes model="unidade" :opcoes="$this->unidades" placeholder="Ex: kg, unidade" novo="Usar unidade" />
                    <flux:error name="unidade" />
                </flux:field>
            </div>

            <flux:field>
                <flux:label>Descrição</flux:label>
                <flux:textarea wire:model="descricao" rows="2" placeholder="Opcional" />
            </flux:field>
        </section>

        <section class="rounded-xl border border-neutral-200 dark:border-neutral-700 p-5 space-y-4">
            <flux:switch wire:model.live="controlarEstoque" align="left"
                label="Controlar estoque"
                description="A cada retirada o sistema desconta a quantidade. Com menos de 10, o item aparece como urgente na página inicial." />

            @if ($controlarEstoque)
                <flux:field>
                    <flux:label>Quantidade em estoque{{ $unidade ? ' ('.$unidade.')' : '' }}</flux:label>
                    <div class="flex items-center gap-2">
                        <flux:button type="button" icon="minus" aria-label="Diminuir estoque" wire:click="$set('estoque', {{ (int) $estoque - 1 }})" />
                        <div class="w-28"><flux:input type="number" wire:model.live.blur="estoque" inputmode="numeric" class:input="text-center" aria-label="Quantidade em estoque" /></div>
                        <flux:button type="button" icon="plus" aria-label="Aumentar estoque" wire:click="$set('estoque', {{ (int) $estoque + 1 }})" />
                    </div>
                    <flux:error name="estoque" />
                    @if ($estoque !== null && $estoque < 0)
                        <p class="mt-2 text-sm text-amber-700 dark:text-amber-400">
                            O estoque está negativo: saiu mais do que foi registrado na entrada. Conte o que tem na prateleira e coloque o número real.
                        </p>
                    @endif
                </flux:field>
            @endif

            <flux:separator variant="subtle" />

            <flux:switch wire:model="ativo" align="left"
                label="Produto ativo"
                description="Produtos inativos não aparecem na busca das retiradas nem na página inicial." />
        </section>

        <div class="flex flex-col-reverse sm:flex-row justify-end gap-3">
            <flux:button href="{{ route('produtos.index') }}" variant="ghost" wire:navigate class="w-full sm:w-auto">Cancelar</flux:button>
            <flux:button type="submit" variant="primary" class="w-full sm:w-auto">
                {{ $produto?->exists ? 'Salvar alterações' : 'Cadastrar produto' }}
            </flux:button>
        </div>
    </form>
</div>

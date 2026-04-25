<div class="flex h-full w-full flex-1 flex-col gap-6 max-w-xl mx-auto">

    <div class="flex items-center gap-3">
        <flux:button href="{{ route('produtos.index') }}" variant="ghost" icon="arrow-left" wire:navigate />
        <flux:heading size="xl">
            {{ $produto?->exists ? 'Editar Produto' : 'Novo Produto' }}
        </flux:heading>
    </div>

    <form wire:submit="save" class="space-y-5">
        <flux:error name="geral" />

        <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 p-5 space-y-4">

            <flux:field>
                <flux:label>Nome *</flux:label>
                <flux:input wire:model="nome" placeholder="Ex: Arroz, Cobertor, Sabonete..." />
                <flux:error name="nome" />
            </flux:field>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                <div class="space-y-2">
                    <flux:field>
                        <flux:label>Categoria *</flux:label>
                        <flux:select wire:model.live="categoria">
                            <flux:select.option value="" disabled>Selecione...</flux:select.option>
                            @foreach ($categorias as $cat)
                                <flux:select.option value="{{ $cat }}">{{ $cat }}</flux:select.option>
                            @endforeach
                            <flux:select.option value="outra">+ Nova categoria</flux:select.option>
                        </flux:select>
                        <flux:error name="categoria" />
                    </flux:field>
                    @if ($categoria === 'outra')
                        <flux:input wire:model="novaCategoria" placeholder="Nome da nova categoria" />
                    @endif
                </div>

                <div class="space-y-2">
                    <flux:field>
                        <flux:label>Unidade *</flux:label>
                        <flux:select wire:model.live="unidade">
                            <flux:select.option value="" disabled>Selecione...</flux:select.option>
                            @foreach (\App\Livewire\Produtos\Form::UNIDADES as $u)
                                <flux:select.option value="{{ $u }}">{{ $u }}</flux:select.option>
                            @endforeach
                            <flux:select.option value="outro">+ Nova unidade</flux:select.option>
                        </flux:select>
                        <flux:error name="unidade" />
                    </flux:field>
                    @if ($unidade === 'outro')
                        <flux:input wire:model="novaUnidade" placeholder="Ex: cesto, muda, dose..." />
                    @endif
                </div>

            </div>

            <flux:field>
                <flux:label>Descrição</flux:label>
                <flux:textarea wire:model="descricao" rows="2" placeholder="Informações adicionais sobre o produto..." />
            </flux:field>

            <flux:field>
                <flux:label>Estoque atual (deixe vazio se não quiser controlar)</flux:label>
                <flux:input type="number" wire:model="estoque" placeholder="0" />
                <flux:error name="estoque" />
            </flux:field>

            <flux:field>
                <flux:checkbox wire:model="ativo" label="Produto ativo (aparece nas retiradas)" />
            </flux:field>

        </div>

        <div class="flex justify-end gap-3">
            <flux:button href="{{ route('produtos.index') }}" variant="ghost" wire:navigate>Cancelar</flux:button>
            <flux:button type="submit" variant="primary">Salvar produto</flux:button>
        </div>

    </form>
</div>

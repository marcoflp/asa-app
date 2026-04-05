<div class="flex h-full w-full flex-1 flex-col gap-6 max-w-3xl mx-auto">

    <div class="flex items-center gap-3">
        <flux:button href="{{ route('retiradas.index') }}" variant="ghost" icon="arrow-left" wire:navigate />
        <flux:heading size="xl">
            {{ $retirada?->exists ? 'Editar Retirada' : 'Nova Retirada' }}
        </flux:heading>
    </div>

    <form wire:submit="save" class="space-y-5">

        {{-- Beneficiário e Data --}}
        <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 p-5 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:field class="md:col-span-1">
                    <flux:label>Beneficiário *</flux:label>
                    <flux:select wire:model="beneficiario_id">
                        <flux:select.option value="0" disabled>Selecione...</flux:select.option>
                        @foreach ($beneficiarios as $b)
                            <flux:select.option value="{{ $b->id }}">{{ $b->nome }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="beneficiario_id" />
                </flux:field>

                <flux:field>
                    <flux:label>Data *</flux:label>
                    <flux:input type="date" wire:model="data" />
                    <flux:error name="data" />
                </flux:field>
            </div>
        </div>

        {{-- Itens --}}
        <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 p-5 space-y-4">
            <flux:heading size="lg">Itens retirados</flux:heading>

            {{-- Lista de itens adicionados --}}
            @if (count($items) > 0)
                <div class="divide-y divide-neutral-100 dark:divide-neutral-700 rounded-lg border border-neutral-200 dark:border-neutral-700">
                    @foreach ($items as $i => $item)
                        <div class="flex items-center justify-between px-4 py-2.5">
                            <span class="font-medium">{{ $item['produto_nome'] }}</span>
                            <div class="flex items-center gap-3">
                                <span class="text-sm text-neutral-500">{{ $item['quantidade'] }} {{ collect($produtos)->firstWhere('id', $item['produto_id'])?->unidade ?? '' }}</span>
                                <flux:button type="button" wire:click="removeItem({{ $i }})" size="sm" variant="ghost" icon="trash" class="text-red-500" />
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <flux:error name="items" />

            {{-- Adicionar item --}}
            <div class="grid grid-cols-1 sm:grid-cols-12 items-end gap-3 pt-1">
                <flux:field class="sm:col-span-8">
                    <flux:label>Produto</flux:label>
                    <flux:select wire:model="item_produto_id">
                        <flux:select.option value="0" disabled>Selecione...</flux:select.option>
                        @php $categoriaAtual = null; @endphp
                        @foreach ($produtos as $p)
                            @if ($p->categoria !== $categoriaAtual)
                                @php $categoriaAtual = $p->categoria; @endphp
                                <flux:select.option disabled>── {{ $p->categoria }} ──</flux:select.option>
                            @endif
                            <flux:select.option value="{{ $p->id }}">{{ $p->nome }} ({{ $p->unidade }})</flux:select.option>
                        @endforeach
                    </flux:select>
                </flux:field>

                <flux:field class="sm:col-span-2">
                    <flux:label>Quantidade</flux:label>
                    <flux:input type="number" wire:model="item_quantidade" min="1" />
                </flux:field>

                <div class="sm:col-span-2">
                    <flux:button type="button" wire:click="addItem" variant="ghost" icon="plus" class="w-full">
                        Adicionar
                    </flux:button>
                </div>
            </div>
        </div>

        {{-- Observações --}}
        <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 p-5">
            <flux:field>
                <flux:label>Observações</flux:label>
                <flux:textarea wire:model="observacoes" rows="2" placeholder="Informações adicionais..." />
            </flux:field>
        </div>

        <div class="flex justify-end gap-3">
            <flux:button href="{{ route('retiradas.index') }}" variant="ghost" wire:navigate>Cancelar</flux:button>
            <flux:button type="submit" variant="primary">
                {{ $retirada?->exists ? 'Salvar alterações' : 'Registrar retirada' }}
            </flux:button>
        </div>

    </form>
</div>

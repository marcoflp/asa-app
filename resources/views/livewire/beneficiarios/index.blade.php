<div class="flex h-full w-full flex-1 flex-col gap-4">

        @if (session('success'))
            <flux:callout variant="success" icon="check-circle">
                {{ session('success') }}
            </flux:callout>
        @endif

        <div class="flex items-center justify-between">
            <flux:heading size="xl">Beneficiários</flux:heading>
            <flux:button href="{{ route('beneficiarios.create') }}" variant="primary" icon="plus" wire:navigate>
                Novo Beneficiário
            </flux:button>
        </div>

        <flux:input wire:model.live.debounce.300ms="search" placeholder="Buscar por nome, CPF ou bairro..." icon="magnifying-glass" />

        <div class="overflow-x-auto rounded-xl border border-neutral-200 dark:border-neutral-700">
            <table class="w-full text-sm">
                <thead class="bg-neutral-50 dark:bg-zinc-800 text-left">
                    <tr>
                        <th class="px-4 py-3 font-medium">Nome</th>
                        <th class="px-4 py-3 font-medium">Telefone</th>
                        <th class="px-4 py-3 font-medium">Bairro</th>
                        <th class="px-4 py-3 font-medium">Família</th>
                        <th class="px-4 py-3 font-medium">Prog. Governo</th>
                        <th class="px-4 py-3 font-medium">Est. Bíblico</th>
                        <th class="px-4 py-3 font-medium text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                    @forelse ($beneficiarios as $b)
                        <tr class="hover:bg-neutral-50 dark:hover:bg-zinc-800/50">
                            <td class="px-4 py-3 font-medium">{{ $b->nome }}</td>
                            <td class="px-4 py-3 text-neutral-600 dark:text-neutral-400">{{ $b->telefone ?? '—' }}</td>
                            <td class="px-4 py-3 text-neutral-600 dark:text-neutral-400">{{ $b->bairro ?? '—' }}</td>
                            <td class="px-4 py-3">{{ $b->num_pessoas_familia }} pessoa(s)</td>
                            <td class="px-4 py-3">
                                @if ($b->inscrito_programa_governo)
                                    <flux:badge color="green" size="sm">Sim</flux:badge>
                                @else
                                    <flux:badge color="zinc" size="sm">Não</flux:badge>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if ($b->recebe_estudo_biblico)
                                    <flux:badge color="blue" size="sm">Sim</flux:badge>
                                @else
                                    <flux:badge color="zinc" size="sm">Não</flux:badge>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex justify-end gap-2">
                                    <flux:button href="{{ route('beneficiarios.edit', $b) }}" size="sm" variant="ghost" icon="pencil" wire:navigate />
                                    <flux:button wire:click="confirmDelete({{ $b->id }})" size="sm" variant="ghost" icon="trash" class="text-red-500" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-neutral-500">
                                Nenhum beneficiário encontrado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div>{{ $beneficiarios->links() }}</div>

        {{-- Modal de confirmação de exclusão --}}
        <flux:modal name="confirm-delete" :show="$deletingId !== null" wire:close="$set('deletingId', null)">
            <div class="space-y-4">
                <flux:heading>Confirmar exclusão</flux:heading>
                <flux:text>Tem certeza que deseja remover este beneficiário? Esta ação não pode ser desfeita.</flux:text>
                <div class="flex justify-end gap-2">
                    <flux:button wire:click="$set('deletingId', null)" variant="ghost">Cancelar</flux:button>
                    <flux:button wire:click="delete" variant="danger">Excluir</flux:button>
                </div>
            </div>
        </flux:modal>

    </div>

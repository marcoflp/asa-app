<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex items-center justify-between">
        <flux:heading size="xl">Usuários</flux:heading>
        <flux:button href="{{ route('usuarios.create') }}" variant="primary" icon="plus" wire:navigate>Novo Usuário</flux:button>
    </div>

    <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-neutral-50 dark:bg-neutral-800 text-sm text-neutral-500 uppercase">
                <tr>
                    <th class="px-5 py-3">Nome</th>
                    <th class="px-5 py-3">E-mail</th>
                    <th class="px-5 py-3 text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-100 dark:divide-neutral-700">
                @foreach($users as $user)
                    <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/50">
                        <td class="px-5 py-3">{{ $user->name }}</td>
                        <td class="px-5 py-3">{{ $user->email }}</td>
                        <td class="px-5 py-3 text-right">
                            <flux:button href="{{ route('usuarios.edit', $user) }}" size="sm" variant="ghost" icon="pencil" wire:navigate />
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

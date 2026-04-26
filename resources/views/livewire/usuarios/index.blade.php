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
                    <th class="px-5 py-3">Último Acesso</th>
                    <th class="px-5 py-3 text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-100 dark:divide-neutral-700">
                @foreach($users as $user)
                    <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/50">
                        <td class="px-5 py-3">
                            <div class="font-medium text-neutral-800 dark:text-neutral-200">{{ $user->name }}</div>
                        </td>
                        <td class="px-5 py-3 text-sm text-neutral-500">{{ $user->email }}</td>
                        <td class="px-5 py-3">
                            @if($user->last_seen_at)
                                <div class="flex items-center gap-2 text-xs">
                                    <div class="h-2 w-2 rounded-full {{ $user->last_seen_at->diffInMinutes(now()) < 5 ? 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]' : 'bg-neutral-300' }}"></div>
                                    <span class="text-neutral-600 dark:text-neutral-400">
                                        {{ $user->last_seen_at->diffForHumans() }}
                                    </span>
                                </div>
                            @else
                                <span class="text-xs text-neutral-400 italic">N/A</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right">
                            <flux:button href="{{ route('usuarios.edit', $user) }}" size="sm" variant="ghost" icon="pencil-square" wire:navigate />
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

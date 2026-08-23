<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <flux:heading size="xl" class="text-zinc-900 dark:text-zinc-50 font-bold">Gerenciar Usuários</flux:heading>
        <flux:button href="{{ route('usuarios.create') }}" variant="primary" icon="plus" wire:navigate class="w-full sm:w-auto font-bold shadow-sm">+ Novo Usuário</flux:button>
    </div>

    {{-- DESKTOP VIEW --}}
    <div wire:loading.class="opacity-60 pointer-events-none transition-opacity" class="hidden md:block rounded-xl border border-zinc-200 dark:border-zinc-700 overflow-hidden bg-white dark:bg-zinc-900 shadow-sm">
        <table class="w-full text-left">
            <thead class="bg-zinc-100 dark:bg-zinc-800 text-sm text-zinc-900 dark:text-zinc-100 font-bold border-b border-zinc-200 dark:border-zinc-700 uppercase">
                <tr>
                    <th class="px-5 py-3.5">Nome</th>
                    <th class="px-5 py-3.5">E-mail</th>
                    <th class="px-5 py-3.5">Último Acesso</th>
                    <th class="px-5 py-3.5 text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                @foreach($users as $user)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/60 transition-colors">
                        <td class="px-5 py-3.5">
                            <div class="font-bold text-zinc-900 dark:text-zinc-100">{{ $user->name }}</div>
                        </td>
                        <td class="px-5 py-3.5 text-sm font-semibold text-zinc-700 dark:text-zinc-300">{{ $user->email }}</td>
                        <td class="px-5 py-3.5">
                            @if($user->last_seen_at)
                                <div class="flex items-center gap-2 text-xs font-semibold">
                                    <div class="h-2.5 w-2.5 rounded-full {{ $user->last_seen_at->diffInMinutes(now()) < 5 ? 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.7)]' : 'bg-zinc-400' }}"></div>
                                    <span class="text-zinc-700 dark:text-zinc-300">
                                        {{ $user->last_seen_at->diffForHumans() }}
                                    </span>
                                </div>
                            @else
                                <span class="text-xs text-zinc-400 font-medium italic">Nunca acessou</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            <flux:button href="{{ route('usuarios.edit', $user) }}" size="sm" variant="ghost" icon="pencil-square" class="text-zinc-700 dark:text-zinc-300 hover:text-zinc-900 dark:hover:text-white" wire:navigate />
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- MOBILE VIEW --}}
    <div wire:loading.class="opacity-60 pointer-events-none transition-opacity" class="block md:hidden space-y-4">
        @foreach($users as $user)
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 p-4 space-y-3 bg-white dark:bg-zinc-900 shadow-sm">
                <div class="flex justify-between items-start">
                    <div>
                        <div class="font-bold text-base text-zinc-900 dark:text-zinc-100">{{ $user->name }}</div>
                        <div class="text-xs font-semibold text-zinc-600 dark:text-zinc-400 mt-1">{{ $user->email }}</div>
                    </div>
                    <flux:button href="{{ route('usuarios.edit', $user) }}" size="sm" variant="ghost" icon="pencil-square" class="text-zinc-700 dark:text-zinc-300" wire:navigate />
                </div>
                <div class="pt-2 border-t border-zinc-100 dark:border-zinc-800 flex justify-between items-center text-xs">
                    <span class="text-zinc-600 dark:text-zinc-400 font-medium">Último Acesso:</span>
                    @if($user->last_seen_at)
                        <div class="flex items-center gap-2">
                            <div class="h-2 w-2 rounded-full {{ $user->last_seen_at->diffInMinutes(now()) < 5 ? 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]' : 'bg-zinc-400' }}"></div>
                            <span class="text-zinc-700 dark:text-zinc-300 font-semibold">
                                {{ $user->last_seen_at->diffForHumans() }}
                            </span>
                        </div>
                    @else
                        <span class="text-zinc-400 italic">Nunca acessou</span>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>

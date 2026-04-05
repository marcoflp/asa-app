<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <flux:heading size="xl">{{ $user?->exists ? 'Editar Usuário' : 'Novo Usuário' }}</flux:heading>
        <flux:button variant="ghost" :href="route('dashboard')" wire:navigate class="w-full sm:w-auto">Voltar ao Início</flux:button>
    </div>

    <form wire:submit.prevent="save" class="space-y-6">
        <flux:card>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>Nome Completo</flux:label>
                    <flux:input wire:model="name" placeholder="Ex: João da Silva" />
                    <flux:error name="name" />
                </flux:field>

                <flux:field>
                    <flux:label>E-mail (Login)</flux:label>
                    <flux:input wire:model="email" type="email" placeholder="Ex: joao@email.com" />
                    <flux:error name="email" />
                </flux:field>

                <flux:field>
                    <flux:label>Senha {{ $user?->exists ? '(Deixe em branco para manter)' : '' }}</flux:label>
                    <flux:input wire:model="password" type="password" viewable />
                    <flux:error name="password" />
                </flux:field>

                <flux:field>
                    <flux:label>Confirmar Senha</flux:label>
                    <flux:input wire:model="password_confirmation" type="password" viewable />
                </flux:field>
            </div>
        </flux:card>

        <div class="flex justify-end gap-3">
            <flux:button variant="ghost" :href="route('dashboard')" wire:navigate>Cancelar</flux:button>
            <flux:button type="submit" variant="primary">
                {{ $user?->exists ? 'Salvar Alterações' : 'Criar Usuário' }}
            </flux:button>
        </div>
    </form>
</div>

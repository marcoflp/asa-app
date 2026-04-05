<x-layouts::auth :title="__('Criar conta')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Criar conta')" :description="__('Insira seus dados abaixo para criar sua conta')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-6">
            @csrf
            <!-- Name -->
            <flux:field>
                <flux:label>{{ __('Nome') }}</flux:label>
                <flux:input
                    name="name"
                    :value="old('name')"
                    type="text"
                    required
                    autofocus
                    autocomplete="name"
                    :placeholder="__('Nome completo')"
                />
                <flux:text variant="subtle" size="sm">Mínimo de 3 caracteres.</flux:text>
                <flux:error name="name" />
            </flux:field>

            <!-- Email Address -->
            <flux:field>
                <flux:label>{{ __('Endereço de e-mail') }}</flux:label>
                <flux:input
                    name="email"
                    :value="old('email')"
                    type="email"
                    required
                    autocomplete="email"
                    placeholder="email@example.com"
                />
                <flux:text variant="subtle" size="sm">Insira um e-mail válido (ex: joao@email.com).</flux:text>
                <flux:error name="email" />
            </flux:field>

            <!-- Password -->
            <flux:field>
                <flux:label>{{ __('Senha') }}</flux:label>
                <flux:input
                    name="password"
                    type="password"
                    required
                    autocomplete="new-password"
                    :placeholder="__('Senha')"
                    viewable
                />
                <flux:text variant="subtle" size="sm">Mínimo de 8 caracteres.</flux:text>
                <flux:error name="password" />
            </flux:field>

            <!-- Confirm Password -->
            <flux:field>
                <flux:label>{{ __('Confirmar senha') }}</flux:label>
                <flux:input
                    name="password_confirmation"
                    type="password"
                    required
                    autocomplete="new-password"
                    :placeholder="__('Confirmar senha')"
                    viewable
                />
                <flux:text variant="subtle" size="sm">Repita a senha informada acima.</flux:text>
            </flux:field>

            <div class="flex items-center justify-end">
                <flux:button type="submit" variant="primary" class="w-full" data-test="register-user-button">
                    {{ __('Criar conta') }}
                </flux:button>
            </div>
        </form>

        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
            <span>{{ __('Já tem uma conta?') }}</span>
            <flux:link :href="route('login')" wire:navigate>{{ __('Entrar') }}</flux:link>
        </div>
    </div>
</x-layouts::auth>

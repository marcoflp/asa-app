<x-layouts::auth :title="__('Login')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Entre na sua conta')" :description="__('')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-6">
            @csrf

            <!-- Email Address -->
            <flux:field>
                <flux:label>{{ __('Endereço de e-mail') }}</flux:label>
                <flux:input
                    name="email"
                    :value="old('email')"
                    type="email"
                    required
                    autofocus
                    autocomplete="email"
                    placeholder="email@example.com"
                />
                <flux:text variant="subtle" size="sm">Seu e-mail cadastrado.</flux:text>
                <flux:error name="email" />
            </flux:field>

            <!-- Password -->
            <div class="relative">
                <flux:field>
                    <flux:label>{{ __('Senha') }}</flux:label>
                    <flux:input
                        name="password"
                        type="password"
                        required
                        autocomplete="current-password"
                        :placeholder="__('Senha')"
                        viewable
                    />
                    <flux:text variant="subtle" size="sm">Sua senha de acesso.</flux:text>
                    <flux:error name="password" />
                </flux:field>

                @if (Route::has('password.request'))
                    <flux:link class="absolute top-0 text-sm end-0" :href="route('password.request')" wire:navigate>
                        {{ __('Esqueceu sua senha?') }}
                    </flux:link>
                @endif
            </div>

            <!-- Remember Me -->
            <flux:checkbox name="remember" :label="__('Lembre de mim')" :checked="old('remember')" />

            <div class="flex items-center justify-end">
                <flux:button variant="primary" type="submit" class="w-full" data-test="login-button">
                    {{ __('Entrar') }}
                </flux:button>
            </div>
        </form>

        @if (Route::has('register'))
            <!-- <div class="space-x-1 text-sm text-center rtl:space-x-reverse text-zinc-600 dark:text-zinc-400">
                <span>{{ __('Não tem uma conta?') }}</span>
                <flux:link :href="route('register')" wire:navigate>{{ __('Registre-se') }}</flux:link>
            </div> -->
        @endif
    </div>
</x-layouts::auth>

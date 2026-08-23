<div class="flex items-start max-md:flex-col">
    <div class="w-full pb-4 md:me-10 md:w-[220px]">
        <flux:navlist aria-label="{{ __('Configurações') }}" class="max-md:grid max-md:grid-cols-3 max-md:gap-1 max-md:pb-2 max-md:border-b max-md:border-neutral-200 max-md:dark:border-neutral-700">
            <flux:navlist.item :href="route('profile.edit')" wire:navigate class="text-center justify-center">{{ __('Perfil') }}</flux:navlist.item>
            <flux:navlist.item :href="route('security.edit')" wire:navigate class="text-center justify-center">{{ __('Segurança') }}</flux:navlist.item>
            <flux:navlist.item :href="route('appearance.edit')" wire:navigate class="text-center justify-center">{{ __('Aparência') }}</flux:navlist.item>
        </flux:navlist>
    </div>

    <flux:separator class="md:hidden" />

    <div class="flex-1 self-stretch max-md:pt-6">
        <flux:heading>{{ $heading ?? '' }}</flux:heading>
        <flux:subheading>{{ $subheading ?? '' }}</flux:subheading>

        <div class="mt-5 w-full max-w-lg">
            {{ $slot }}
        </div>
    </div>
</div>

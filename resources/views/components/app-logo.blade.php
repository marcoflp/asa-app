@props([
    'sidebar' => false,
])

@if($sidebar)
    <flux:sidebar.brand name="ASA" {{ $attributes }}>
        <x-slot name="logo">
            <img src="/logo.png" alt="ASA" class="size-8 rounded-md object-contain" />
        </x-slot>
    </flux:sidebar.brand>
@else
    <flux:brand name="ASA" {{ $attributes }}>
        <x-slot name="logo">
            <img src="/logo.png" alt="ASA" class="size-8 rounded-md object-contain" />
        </x-slot>
    </flux:brand>
@endif

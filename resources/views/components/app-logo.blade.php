@props([
    'sidebar' => false,
])

@if($sidebar)
    <flux:sidebar.brand name="ASA" {{ $attributes }}>
        <x-slot name="logo" class="size-9 rounded-full bg-white shadow-sm ring-1 ring-black/5">
            <img src="/logo.png" alt="" class="size-7 object-contain" />
        </x-slot>
    </flux:sidebar.brand>
@else
    <flux:brand name="ASA" {{ $attributes }}>
        <x-slot name="logo" class="size-9 rounded-full bg-white shadow-sm ring-1 ring-black/5">
            <img src="/logo.png" alt="" class="size-7 object-contain" />
        </x-slot>
    </flux:brand>
@endif

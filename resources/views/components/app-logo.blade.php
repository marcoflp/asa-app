@props([
    'sidebar' => false,
])

@if($sidebar)
    <flux:sidebar.brand name="ASA" {{ $attributes }}>
        <x-slot name="logo">
            <img src="/logoescrita.jpg" alt="ASA" class="h-8 w-auto rounded-md bg-white px-2 py-1 object-contain" />
        </x-slot>
    </flux:sidebar.brand>
@else
    <flux:brand name="ASA" {{ $attributes }}>
        <x-slot name="logo">
            <img src="/logoescrita.jpg" alt="ASA" class="h-8 w-auto rounded-md bg-white px-2 py-1 object-contain" />
        </x-slot>
    </flux:brand>
@endif

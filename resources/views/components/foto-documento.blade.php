{{--
    Bloco de foto de documento: tirar com a câmera ou escolher da galeria, com prévia.
    Uso: <x-foto-documento campo="foto_documento" titulo="Documento (frente)" :arquivo="$foto_documento" :salvo="$foto_documento_path" />
--}}
@props([
    'campo',
    'titulo',
    'arquivo' => null,
    'salvo' => null,
])

@php
    $ehHeic = $arquivo && in_array(strtolower($arquivo->getClientOriginalExtension() ?? ''), ['heic', 'heif']);
@endphp

<div x-data class="rounded-xl border border-zinc-200 dark:border-zinc-700 p-3 bg-zinc-50/60 dark:bg-zinc-800/40 flex flex-col gap-2.5">
    <div class="flex items-center justify-between gap-2">
        <span class="font-semibold text-sm text-zinc-900 dark:text-zinc-100">{{ $titulo }}</span>
        @if ($arquivo)
            <span class="text-[11px] font-semibold text-emerald-700 dark:text-emerald-400">Nova foto</span>
        @elseif ($salvo)
            <span class="text-[11px] font-semibold text-zinc-500">Salva</span>
        @endif
    </div>

    <input type="file" x-ref="camera" wire:model="{{ $campo }}" accept="image/*" capture="environment" class="hidden" />
    <input type="file" x-ref="galeria" wire:model="{{ $campo }}" accept="image/*,.heic,.heif" class="hidden" />

    <div wire:loading.flex wire:target="{{ $campo }}" class="h-24 sm:h-28 w-full rounded-lg border border-dashed border-emerald-300 dark:border-emerald-800 items-center justify-center gap-2 text-xs font-semibold text-emerald-700 dark:text-emerald-400">
        <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
        Enviando foto...
    </div>

    <div wire:loading.remove wire:target="{{ $campo }}">
        @if ($arquivo && $ehHeic)
            <div class="h-24 sm:h-28 w-full rounded-lg border border-emerald-300 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-950/40 flex flex-col items-center justify-center text-xs text-emerald-800 dark:text-emerald-300">
                <span class="font-semibold">Foto do iPhone (HEIC)</span>
                <span class="text-zinc-500">Será convertida ao salvar</span>
            </div>
        @elseif ($arquivo)
            <img src="{{ $arquivo->temporaryUrl() }}" alt="Prévia: {{ $titulo }}" class="h-24 sm:h-28 w-full rounded-lg object-cover ring-2 ring-emerald-500/60">
        @elseif ($salvo)
            <a href="{{ asset('storage/'.$salvo) }}" target="_blank" class="block" title="Abrir foto em tamanho real">
                <img src="{{ asset('storage/'.$salvo) }}" alt="{{ $titulo }}" class="h-24 sm:h-28 w-full rounded-lg object-cover border border-zinc-300 dark:border-zinc-700 hover:opacity-90 transition-opacity">
            </a>
        @else
            <div class="h-24 sm:h-28 w-full rounded-lg border-2 border-dashed border-zinc-200 dark:border-zinc-700 flex items-center justify-center text-xs text-zinc-400">
                Nenhuma foto
            </div>
        @endif
    </div>

    <flux:error name="{{ $campo }}" class="!mt-0" />

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-1.5">
        <button type="button" @click="$refs.camera.click()"
                class="flex items-center justify-center gap-1.5 h-9 rounded-lg bg-accent text-accent-foreground font-semibold text-xs hover:opacity-90 transition-opacity focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            {{ $arquivo || $salvo ? 'Refazer' : 'Câmera' }}
        </button>
        <button type="button" @click="$refs.galeria.click()"
                class="flex items-center justify-center gap-1.5 h-9 rounded-lg bg-white dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 text-zinc-700 dark:text-zinc-200 font-semibold text-xs hover:bg-zinc-100 dark:hover:bg-zinc-700 transition-colors focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent">
            <svg class="w-4 h-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            Galeria
        </button>
    </div>
</div>

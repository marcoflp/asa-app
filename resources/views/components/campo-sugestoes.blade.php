{{--
    Campo de texto com sugestões (autocompletar) que também aceita um valor novo.
    Busca sem diferenciar acentos e maiúsculas. Funciona com teclado (setas, Enter, Esc).

    Uso: <x-campo-sugestoes model="bairro" :opcoes="$lista" placeholder="..." novo="Usar como novo bairro" />
--}}
@props([
    'model',
    'opcoes' => [],
    'placeholder' => '',
    'novo' => 'Usar este valor',
])

<div class="relative"
     x-data="{
         valor: $wire.entangle(@js($model)),
         opcoes: @js(array_values($opcoes)),
         aberto: false,
         ativo: 0,
         norm(t) { return (t ?? '').toString().normalize('NFD').replace(/[̀-ͯ]/g, '').toLowerCase().trim() },
         get filtradas() {
             const q = this.norm(this.valor);
             if (!q) return this.opcoes.slice(0, 50);
             const termos = q.split(/\s+/);
             return this.opcoes
                 .filter(o => termos.every(t => this.norm(o).includes(t)))
                 .sort((a, b) => (this.norm(a).startsWith(q) ? 0 : 1) - (this.norm(b).startsWith(q) ? 0 : 1))
                 .slice(0, 50);
         },
         get exato() { return this.opcoes.some(o => this.norm(o) === this.norm(this.valor)) },
         get mostrarNovo() { return this.norm(this.valor) !== '' && !this.exato },
         get total() { return this.filtradas.length + (this.mostrarNovo ? 1 : 0) },
         escolher(o) { this.valor = o; this.aberto = false },
         mover(d) { if (!this.total) return; this.aberto = true; this.ativo = (this.ativo + d + this.total) % this.total;
             this.$nextTick(() => this.$refs.lista?.querySelector('[data-ativo=true]')?.scrollIntoView({ block: 'nearest' })) },
         confirmar() {
             if (!this.aberto) return;
             if (this.ativo < this.filtradas.length) this.escolher(this.filtradas[this.ativo]);
             else this.aberto = false;
         },
     }"
     @click.outside="aberto = false"
     @focusin="aberto = true">
    <input type="text"
           x-model="valor"
           @input="aberto = true; ativo = 0"
           @keydown.arrow-down.prevent="mover(1)"
           @keydown.arrow-up.prevent="mover(-1)"
           @keydown.enter="if (aberto && total) { $event.preventDefault(); confirmar() }"
           @keydown.escape="aberto = false"
           @keydown.tab="aberto = false"
           placeholder="{{ $placeholder }}"
           autocomplete="off"
           role="combobox"
           aria-autocomplete="list"
           :aria-expanded="aberto"
           {{ $attributes->class('w-full h-10 px-3 text-base sm:text-sm rounded-lg border border-zinc-200 border-b-zinc-300/80 dark:border-white/10 bg-white dark:bg-white/10 text-zinc-700 dark:text-zinc-300 placeholder-zinc-400 dark:placeholder-zinc-400 shadow-xs outline-none focus:ring-2 focus:ring-accent focus:border-transparent') }} />

    <div x-show="aberto && total" x-cloak x-transition.opacity.duration.100ms x-ref="lista" role="listbox"
         class="absolute z-50 w-full mt-1 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg shadow-xl max-h-64 overflow-y-auto py-1">
        <template x-for="(o, i) in filtradas" :key="o">
            <button type="button" role="option" @click="escolher(o)" @mouseenter="ativo = i"
                    :data-ativo="ativo === i" :aria-selected="ativo === i"
                    :class="ativo === i ? 'bg-zinc-100 dark:bg-zinc-700' : ''"
                    class="w-full text-left px-3 py-2 text-sm text-zinc-800 dark:text-zinc-100" x-text="o"></button>
        </template>
        <button type="button" x-show="mostrarNovo" @click="aberto = false" @mouseenter="ativo = filtradas.length"
                :data-ativo="ativo === filtradas.length"
                :class="ativo === filtradas.length ? 'bg-zinc-100 dark:bg-zinc-700' : ''"
                class="w-full text-left px-3 py-2 text-sm border-t border-zinc-100 dark:border-zinc-700 text-accent-content font-medium">
            <span>{{ $novo }}:</span> <span x-text="'“' + (valor ?? '').trim() + '”'"></span>
        </button>
    </div>
</div>

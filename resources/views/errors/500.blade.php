<x-layouts::auth title="Erro Inesperado">
    <div class="flex flex-col items-center justify-center text-center px-4">
        <div class="bg-red-100 dark:bg-red-950/50 text-red-600 dark:text-red-400 p-4 rounded-full mb-6">
            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
        </div>
        
        <h1 class="text-2xl font-bold text-zinc-800 dark:text-zinc-100 mb-2">Ops! Algo deu errado.</h1>
        <p class="text-zinc-600 dark:text-zinc-400 text-sm max-w-sm mb-6">
            Desculpe, ocorreu um erro inesperado no sistema. O erro já foi reportado e em breve será corrigido.
        </p>

        @if(isset($errorRef))
            <div class="bg-zinc-100 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-lg p-3 mb-6 w-full text-center">
                <p class="text-xs text-zinc-500 dark:text-zinc-400 font-medium mb-1">Código do Erro (Referência):</p>
                <code class="text-sm font-mono text-zinc-800 dark:text-zinc-200 select-all">{{ $errorRef }}</code>
            </div>
        @endif

        <div class="flex gap-3 w-full">
            <a href="{{ url()->previous() }}" class="flex-1 px-4 py-2 bg-zinc-200 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-medium rounded-lg hover:bg-zinc-300 dark:hover:bg-zinc-700 transition-colors text-center text-sm">
                Voltar
            </a>
            <a href="{{ url('/') }}" class="flex-1 px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white font-medium rounded-lg transition-colors text-center text-sm">
                Início
            </a>
        </div>
    </div>
</x-layouts::auth>

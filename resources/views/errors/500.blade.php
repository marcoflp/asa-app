<x-layouts.app title="Erro Inesperado">
    <div class="flex flex-col items-center justify-center min-h-[60vh] text-center px-4">
        <div class="bg-red-100 text-red-600 p-4 rounded-full mb-6">
            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
        </div>
        
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Ops! Algo deu errado.</h1>
        <p class="text-gray-600 max-w-lg mb-8">
            Desculpe, ocorreu um erro inesperado no sistema. O erro já foi reportado e em breve será corrigido.
        </p>

        @if(isset($errorRef))
            <div class="bg-gray-100 border border-gray-300 rounded-lg p-4 mb-8">
                <p class="text-sm text-gray-500 font-medium mb-1">Código do Erro (Referência):</p>
                <code class="text-lg font-mono text-gray-800 select-all">{{ $errorRef }}</code>
            </div>
        @endif

        <div class="flex gap-4">
            <a href="{{ url()->previous() }}" class="px-6 py-2 bg-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-300 transition-colors">
                Voltar
            </a>
            <a href="{{ url('/') }}" class="px-6 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                Ir para o Início
            </a>
        </div>
    </div>
</x-layouts.app>

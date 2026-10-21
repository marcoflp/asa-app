@component('layouts.app', ['title' => __('Início')])
    <asa-painel configuracao="{{ json_encode($configuracao) }}"></asa-painel>

    @foreach (['styles.css', 'main.js'] as $arquivo)
        @continue (! file_exists(public_path("painel/{$arquivo}")))
        @php($endereco = asset("painel/{$arquivo}").'?v='.filemtime(public_path("painel/{$arquivo}")))
        @if (str_ends_with($arquivo, '.css'))
            <link rel="stylesheet" href="{{ $endereco }}" data-navigate-track>
        @else
            <script type="module" src="{{ $endereco }}" data-navigate-once></script>
        @endif
    @endforeach
@endcomponent

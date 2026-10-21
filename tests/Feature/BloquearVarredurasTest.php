<?php

use App\Http\Middleware\LogUserActions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

test('varreduras automáticas recebem 404 e não entram no log', function (string $endereco) {
    Log::shouldReceive('channel')->never();

    $this->get($endereco)->assertNotFound()->assertContent('');
})->with([
    'pearcmd' => ['/index.php?lang=../../../../usr/local/lib/php/pearcmd&+config-create+/&/<?echo(md5("hi"));?>+/tmp/index1.php'],
    'thinkphp' => ['/index.php?s=/index/\think\app/invokefunction&function=call_user_func_array&vars[0]=md5&vars[1][]=Hello'],
    'travessia codificada' => ['/?file=..%2F..%2Fetc%2Fpasswd'],
    'arquivo .env' => ['/.env'],
    'git' => ['/.git/config'],
    'wordpress' => ['/wp-login.php'],
    'phpunit' => ['/vendor/phpunit/phpunit/src/Util/PHP/eval-stdin.php'],
]);

test('páginas normais continuam funcionando', function () {
    $this->get('/')->assertOk();
    $this->get('/login')->assertOk();
});

test('o log de ações não guarda os dados pessoais enviados pelos formulários', function () {
    $contextos = [];
    Log::shouldReceive('channel')->with('user-requests')->andReturnSelf();
    Log::shouldReceive('info')->andReturnUsing(function (string $mensagem, array $contexto = []) use (&$contextos) {
        $contextos[] = $mensagem.json_encode($contexto);
    });

    $requisicao = Request::create('/livewire-1234/update', 'POST', [
        'components' => [[
            'snapshot' => json_encode(['data' => ['cpf' => '529.982.247-25'], 'memo' => ['name' => 'beneficiarios.form']]),
            'updates' => ['cpf' => '529.982.247-25'],
            'calls' => [],
        ]],
    ]);

    (new LogUserActions)->handle($requisicao, fn () => response('ok'));

    expect($contextos)->toHaveCount(1)
        ->and($contextos[0])->toContain('beneficiarios.form (updates: cpf)')
        ->and($contextos[0])->not->toContain('529.982.247-25');
});

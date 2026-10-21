<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BloquearVarredurasAutomaticas
{
    private const ASSINATURAS = [
        '../',
        '..\\',
        'pearcmd',
        'invokefunction',
        'call_user_func',
        'think\\app',
        'allow_url_include',
        'auto_prepend_file',
        'php://',
        'phpunit',
        'eval-stdin',
        '<?php',
        '<?=',
        '<?echo',
        'base64_decode(',
        '/.env',
        '/.git',
        'wp-admin',
        'wp-login',
        'wp-content',
        'xmlrpc.php',
        'phpmyadmin',
        'cgi-bin',
        '/boaform',
        '/actuator',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if ($this->ehVarredura($request)) {
            return response('', Response::HTTP_NOT_FOUND);
        }

        return $next($request);
    }

    private function ehVarredura(Request $request): bool
    {
        $endereco = mb_strtolower(rawurldecode(rawurldecode($request->getRequestUri())));

        return collect(self::ASSINATURAS)->contains(fn (string $assinatura) => str_contains($endereco, $assinatura));
    }
}

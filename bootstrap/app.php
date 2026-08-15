<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo('/login');
        $middleware->redirectUsersTo('/dashboard');

        $middleware->web(append: [
            \App\Http\Middleware\SecurityHeaders::class,
            \App\Http\Middleware\UpdateLastSeenAt::class,
            \App\Http\Middleware\LogUserActions::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (Throwable $e, \Illuminate\Http\Request $request) {
            // Permitir que o Laravel lide normalmente com autenticação, validação e erros HTTP do cliente (< 500)
            if (
                $e instanceof \Illuminate\Auth\AuthenticationException ||
                $e instanceof \Illuminate\Validation\ValidationException ||
                $e instanceof \Illuminate\Auth\Access\AuthorizationException ||
                $e instanceof \Illuminate\Session\TokenMismatchException ||
                ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface && $e->getStatusCode() < 500)
            ) {
                return null;
            }

            if (!config('app.debug')) {
                $errorRef = (string) \Illuminate\Support\Str::uuid();
                
                \Illuminate\Support\Facades\Log::error('Erro [' . $errorRef . ']: ' . $e->getMessage(), [
                    'url' => $request->fullUrl(),
                    'input' => $request->except(['password', 'password_confirmation']),
                    'trace' => $e->getTraceAsString(),
                ]);

                if ($request->expectsJson() || $request->is('livewire/*')) {
                    return response()->json([
                        'message' => 'Ocorreu um erro interno. Nossa equipe já foi notificada.',
                        'error_ref' => $errorRef
                    ], 500);
                }

                return response()->view('errors.500', ['errorRef' => $errorRef], 500);
            }
        });
    })->create();

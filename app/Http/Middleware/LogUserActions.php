<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogUserActions
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($this->shouldExclude($request)) {
            return $response;
        }

        $user = Auth::check() ? Auth::user()->name : 'Guest';
        $method = $request->method();
        $path = $request->getRequestUri();
        $status = $response->getStatusCode();

        $livewireDetails = '';
        if ($method === 'POST' && str_contains($path, 'livewire') && str_contains($path, '/update') && $request->has('components')) {
            $componentDescriptions = [];
            foreach ($request->input('components', []) as $component) {
                $snapshot = $component['snapshot'] ?? null;
                if (is_string($snapshot)) {
                    $snapshotData = json_decode($snapshot, true);
                } else {
                    $snapshotData = $snapshot;
                }
                $name = $snapshotData['memo']['name'] ?? 'unknown-component';

                $updates = $component['updates'] ?? [];
                $updatedKeys = array_keys($updates);

                $calls = $component['calls'] ?? [];
                $calledMethods = [];
                foreach ($calls as $call) {
                    $calledMethods[] = $call['method'] ?? 'unknown-method';
                }

                $desc = $name;
                if (!empty($calledMethods) || !empty($updatedKeys)) {
                    $actions = [];
                    if (!empty($calledMethods)) {
                        $actions[] = "methods: " . implode(', ', $calledMethods);
                    }
                    if (!empty($updatedKeys)) {
                        $actions[] = "updates: " . implode(', ', $updatedKeys);
                    }
                    $desc .= " (" . implode('; ', $actions) . ")";
                }
                $componentDescriptions[] = $desc;
            }
            if (!empty($componentDescriptions)) {
                $livewireDetails = ' [Livewire: ' . implode(' | ', $componentDescriptions) . ']';
            }
        }

        $message = "{$method} {$path}{$livewireDetails} por user {$user} [Status: {$status}]";

        $body = $this->sanitizeData($request->all());

        if (empty($body)) {
            Log::channel('user-requests')->info($message);
        } else {
            Log::channel('user-requests')->info($message, ['body' => $body]);
        }

        return $response;
    }

    /**
     * Determine if the request should be excluded from logs.
     */
    protected function shouldExclude(Request $request): bool
    {
        // Exclude common debug, asset, and loop-causing routes
        $excludePatterns = [
            'log-viewer*',
            '_debugbar*',
            '_telescope*',
            'up', // Health check
            'livewire/livewire.js',
            'livewire/livewire.min.js',
            'livewire/livewire.js.map',
        ];

        foreach ($excludePatterns as $pattern) {
            if ($request->is($pattern)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Recursively sanitize sensitive keys in the data.
     */
    protected function sanitizeData(mixed $data): mixed
    {
        if (is_array($data)) {
            $sanitized = [];
            foreach ($data as $key => $value) {
                if (is_string($key) && $this->isSensitiveKey($key)) {
                    $sanitized[$key] = '********';
                } else {
                    $sanitized[$key] = $this->sanitizeData($value);
                }
            }
            return $sanitized;
        }

        return $data;
    }

    /**
     * Check if a key is sensitive.
     */
    protected function isSensitiveKey(string $key): bool
    {
        $sensitiveKeys = [
            'password',
            'password_confirmation',
            'current_password',
            'token',
            '_token',
            'secret',
            'key',
            'senha',
            'card',
        ];

        foreach ($sensitiveKeys as $sensitive) {
            if (stripos($key, $sensitive) !== false) {
                return true;
            }
        }

        return false;
    }
}

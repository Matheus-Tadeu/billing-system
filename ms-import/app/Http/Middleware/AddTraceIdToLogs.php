<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AddTraceIdToLogs
{
    public function handle($request, Closure $next)
    {
        $traceId = $request->header('X-Trace-ID', Str::uuid()->toString());

        // Adicionar o trace ID ao contexto de log
        Log::withContext(['trace_id' => $traceId]);

        // Disponibilizar o trace ID na requisição
        $request->headers->set('X-Trace-ID', $traceId);

        return $next($request);
    }
}

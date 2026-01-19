<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyMiddleware
{
    /**
     * Handle an incoming request.
     * ตรวจสอบ API Key สำหรับ External API Access
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('X-API-KEY') ?? $request->query('api_key');
        
        if (!$apiKey || $apiKey !== config('app.report_api_key')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Invalid API Key.',
            ], 401);
        }

        return $next($request);
    }
}

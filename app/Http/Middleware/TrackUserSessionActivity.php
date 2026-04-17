<?php

namespace App\Http\Middleware;

use App\Services\UserSessionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackUserSessionActivity
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->user()) {
            try {
                UserSessionService::touchCurrentSession($request->user());
            } catch (\Throwable $e) {
                // Activity tracking must never block normal user requests.
            }
        }

        return $response;
    }
}

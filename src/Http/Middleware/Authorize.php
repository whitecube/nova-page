<?php

namespace Whitecube\NovaPage\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Whitecube\NovaPage\NovaPageTool;
use Symfony\Component\HttpFoundation\Response;

class Authorize
{

    public function handle(Request $request, Closure $next) : Response
    {
        return app(NovaPageTool::class)->authorize($request)
            ? $next($request)
            : abort(403);
    }
    
}
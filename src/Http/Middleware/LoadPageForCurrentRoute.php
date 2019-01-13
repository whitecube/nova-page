<?php

namespace Whitecube\NovaPage\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Whitecube\NovaPage\Pages\Manager;

class LoadPageForCurrentRoute
{

    /**
     * The Nova Page Manager singleton instance
     *
     * @var \Whitecube\NovaPage\Pages\Manager
     */
    protected $page;

    /**
     * Create the Middleware Instance
     *
     * @return void
     */
    public function __construct(Manager $page)
    {
        $this->page = $page;
    }

    /**
     * Handle an incoming request
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $this->page->loadForRoute($request->route());

        return $next($request);
    }

}
<?php

namespace Whitecube\NovaPage\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Whitecube\NovaPage\Pages\Manager;

class LoadPageFromRouteName
{

    /**
     * The Nova Page Manager singleton instance
     *
     * @var \Whitecube\NovaPage\Pages\Manager
     */
    protected $page;

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
        $this->page->load($request->route()->getName());

        return $next($request);
    }

}
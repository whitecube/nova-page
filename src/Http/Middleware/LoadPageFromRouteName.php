<?php

namespace Whitecube\NovaPage\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Whitecube\NovaPage\Page\Manager;

class LoadPageFromRouteName
{

    /**
     * The Nova Page Manager singleton instance
     *
     * @var \Whitecube\NovaPage\Page\Manager
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
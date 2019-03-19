<?php

namespace Whitecube\NovaPage;

use Laravel\Nova\Nova;
use Laravel\Nova\Tool;

class NovaPageTool extends Tool
{
    /**
     * Perform any tasks that need to happen when the tool is booted.
     *
     * @return void
     */
    public function boot()
    {
        Nova::resources([
            \Whitecube\NovaPage\Pages\PageResource::class,
            \Whitecube\NovaPage\Pages\OptionResource::class,
        ]);
    }

    /**
     * Build the view that renders the navigation links for the tool.
     *
     * @return \Illuminate\View\View
     */
    public function renderNavigation()
    {
        return view('nova-page::navigation');
    }
}

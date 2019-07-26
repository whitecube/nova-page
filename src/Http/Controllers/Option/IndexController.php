<?php

namespace Whitecube\NovaPage\Http\Controllers\Option;

use Whitecube\NovaPage\Pages\Manager;
use Laravel\Nova\Http\Requests\ResourceIndexRequest;
use Whitecube\NovaPage\Http\Controllers\ResourceIndexController;

class IndexController extends ResourceIndexController
{
    /**
     * Resource label callback
     * 
     * @return string
     */
    protected function resourceLabel() {
        return config('novapage.labels.options');
    }

    /**
     * Callback to retrieve the resource index items
     * 
     * @param  \Laravel\Nova\Http\Requests\ResourceIndexRequest $request
     * @param  \Whitecube\NovaPage\Pages\Manager $manager
     * @return \Illuminate\Support\Collection 
     */
    protected function resourceIndexItems(ResourceIndexRequest $request, Manager $manager) {
        return $manager->queryIndexResources($request, 'option');
    }
}

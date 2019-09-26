<?php

namespace Whitecube\NovaPage\Pages\Concerns;

use Route;
use Whitecube\NovaPage\Pages\Template;
use Whitecube\NovaPage\Pages\OptionResource;
use Laravel\Nova\Http\Requests\ResourceIndexRequest;

trait QueriesResources
{
    /**
     * Retrieves registered static resource for given request and type
     *
     * @param  \Laravel\Nova\Http\Requests\ResourceIndexRequest  $request
     * @param  string $type
     * @return \Illuminate\Support\Collection
     */
    public function queryIndexResources(ResourceIndexRequest $request, $type) {
        $query = $this->newQueryWithoutScopes();
        return $query->whereType($type)->get(false)->map(function($template) use ($type) {
            return $this->getResourceForType($type, $template);
        });
    }

    /**
     * Retrieves registered pages with a route type count for request
     *
     * @param  \Laravel\Nova\Http\Requests\ResourceIndexRequest  $request
     * @param  string $type
     * @return \Illuminate\Support\Collection
     */
    public function queryResourcesCount(ResourceIndexRequest $request, $type)
    {
        return $this->newQueryWithoutScopes()->whereType($type)->get(false)->count();
    }

    /**
     * Creates a new Nova Resource for given type and Template
     *
     * @param  string $type
     * @param  \Whitecube\NovaPage\Pages\Template $resource
     * @return \Laravel\Nova\Resource
     */
    protected function getResourceForType($type, Template $resource) {
        $page_resource_class = config('novapage.default_page_resource');
        $option_resource_class = config('novapage.default_option_resource');
        switch ($type) {
            case 'route': return new $page_resource_class($resource);
            case 'option': return new $option_resource_class($resource);
        }
    }
}

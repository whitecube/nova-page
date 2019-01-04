<?php

namespace Whitecube\NovaPage\Pages;

use Route;
use Laravel\Nova\Http\Requests\ResourceIndexRequest;

trait QueriesPages
{
    /**
     * Retrieves registered pages for request
     *
     * @param  \Laravel\Nova\Http\Requests\ResourceIndexRequest  $request
     * @return \Illuminate\Support\Collection
     */
    public function queryIndex(ResourceIndexRequest $request)
    {
        return $this->getAllTemplateRoutes()->map(function($route) {
            return new Resource($this->loadForRoute($route));
        });
    }

    /**
     * Retrieves registered pages count for request
     *
     * @param  \Laravel\Nova\Http\Requests\ResourceIndexRequest  $request
     * @return \Illuminate\Support\Collection
     */
    public function queryCount(ResourceIndexRequest $request)
    {
        return $this->getAllTemplateRoutes()->count();
    }

    /**
     * Retrieves all Routes having a template assigned
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getAllTemplateRoutes()
    {
        return collect(Route::getRoutes()->getRoutes())->filter(function($route) {
            return !is_null($route->template());
        });
    }
}

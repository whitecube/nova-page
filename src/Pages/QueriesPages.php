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
        $query = $this->newQueryWithoutScopes();
        return $query->get(false)->map(function($template) {
            return new Resource($template);
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
        return $this->newQueryWithoutScopes()->get(false)->count();
    }
}

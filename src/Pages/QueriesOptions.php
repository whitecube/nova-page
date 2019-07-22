<?php

namespace Whitecube\NovaPage\Pages;

use Route;
use Laravel\Nova\Http\Requests\ResourceIndexRequest;

trait QueriesOptions
{
    /**
     * Retrieves registered pages with a option type for request
     *
     * @param  \Laravel\Nova\Http\Requests\ResourceIndexRequest  $request
     * @return \Illuminate\Support\Collection
     */
    public function queryOptionsIndex(ResourceIndexRequest $request) {
        $query = $this->newQueryWithoutScopes();
        return $query->whereType('option')->get(false)->map(function($template) {
            return new OptionResource($template);
        });
    }

    /**
     * Retrieves registered pages with a option type count for request
     *
     * @param  \Laravel\Nova\Http\Requests\ResourceIndexRequest  $request
     * @return \Illuminate\Support\Collection
     */
    public function queryOptionsCount(ResourceIndexRequest $request)
    {
        return $this->newQueryWithoutScopes()->whereType('option')->get(false)->count();
    }
}

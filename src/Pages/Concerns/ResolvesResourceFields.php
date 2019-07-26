<?php

namespace Whitecube\NovaPage\Pages\Concerns;

use Laravel\Nova\Fields\FieldCollection;
use Laravel\Nova\Http\Requests\NovaRequest;
use Whitecube\NovaPage\Http\Controllers\Page\IndexController as PageResourceIndexController;
use Whitecube\NovaPage\Http\Controllers\Option\IndexController as OptionResourceIndexController;

trait ResolvesResourceFields
{
    /**
     * Get the fields that are available for the given request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return \Illuminate\Support\Collection
     */
    public function availableFields(NovaRequest $request)
    {
        if($this->isDisplayingIndexFields($request)) {
            return new FieldCollection($this->getIndexTableFields($request));
        }

        return new FieldCollection(array_values($this->filter($this->fields($request))));
    }

    /**
     * Check if incoming request displays an index page
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return bool
     */
    protected function isDisplayingIndexFields(NovaRequest $request)
    {
        $indexActions = [
            PageResourceIndexController::class . '@handle',
            OptionResourceIndexController::class . '@handle'
        ];

        return in_array(
            $request->route()->getAction()['controller'],
            $indexActions
        );
    }
}
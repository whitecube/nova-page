<?php

namespace Whitecube\NovaPage\Pages;

use Laravel\Nova\Fields\FieldCollection;
use Laravel\Nova\Http\Requests\NovaRequest;
use Whitecube\NovaPage\Http\Controllers\PageResourceIndexController;
use Whitecube\NovaPage\Http\Controllers\OptionResourceIndexController;

trait ResolvesPageFields
{

    /**
     * Get the fields that are available for the given request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return \Illuminate\Support\Collection
     */
    public function availableFields(NovaRequest $request)
    {
        $action = $request->route()->getAction()['controller'];

        if($action === PageResourceIndexController::class . '@handle' || $action === OptionResourceIndexController::class . '@handle') {
            return new FieldCollection($this->getIndexTableFields($request));
        }

        return new FieldCollection(array_values($this->filter($this->fields($request))));
    }
}
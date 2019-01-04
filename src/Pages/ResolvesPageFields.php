<?php

namespace Whitecube\NovaPage\Pages;

use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\FieldCollection;
use Laravel\Nova\Http\Requests\NovaRequest;
use Whitecube\NovaPage\Http\Controllers\ResourceIndexController;

use Laravel\Nova\Fields\Avatar;
use Laravel\Nova\Fields\MorphTo;
use Illuminate\Support\Collection;
use Laravel\Nova\Fields\MorphMany;
use Laravel\Nova\Actions\Actionable;
use Laravel\Nova\Fields\MorphToMany;
use Laravel\Nova\Contracts\Resolvable;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Actions\ActionResource;
use Laravel\Nova\Contracts\ListableField;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Laravel\Nova\Http\Requests\ResourceDetailRequest;

trait ResolvesPageFields
{
    /**
     * Resolve the index fields.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return \Illuminate\Support\Collection
     */
    public function indexFields(NovaRequest $request)
    {
        return new FieldCollection([

            Text::make('Name', function() {
                return $this->getName();
            })->sortable(),

            Text::make('Title', function() {
                return $this->getTitle();
            })->sortable(),
            
        ]);
    }

    /**
     * Get the fields that are available for the given request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return \Illuminate\Support\Collection
     */
    public function availableFields(NovaRequest $request)
    {
        $action = $request->route()->getAction()['controller'];

        if($action === ResourceIndexController::class . '@handle') {
            return $this->indexFields($request);
        }

        return new FieldCollection(array_values($this->filter($this->fields($request))));
    }
}
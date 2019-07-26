<?php

namespace Whitecube\NovaPage\Pages;

use Laravel\Nova\Resource;
use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Laravel\Nova\Http\Requests\NovaRequest;

abstract class StaticResource extends Resource
{
    use Concerns\ResolvesResourceFields;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'title';

    /**
     * Indicates if the resoruce should be globally searchable.
     * Disabled for now until there is a fix for issue #15
     *
     * @var bool
     */
    public static $globallySearchable = false;

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'title', 'name'
    ];

    /**
     * Indicates if the resource should be displayed in the sidebar.
     *
     * @var bool
     */
    public static $displayInNavigation = false;

    /**
     * Create a new resource instance.
     *
     * @param \Whitecube\NovaPage\Pages\Template $resource
     * @return void
     */
    public function __construct(Template $resource)
    {
        parent::__construct($resource);
    }

    /**
     * Determine if this resource uses soft deletes.
     *
     * @return bool
     */
    public static function softDeletes()
    {
        return false;
    }

    /**
     * Get the search result subtitle for the resource.
     *
     * @return string
     */
    public function subtitle()
    {
        return $this->resource->getName();
    }

    /**
     * Get a fresh instance of the model represented by the resource.
     *
     * @return mixed
     */
    public static function newModel()
    {
        if(request()->resourceId) {
            return resolve(Manager::class)
                ->newQueryWithoutScopes()
                ->whereKey(request()->resourceId)
                ->firstOrFail();
        }
        return resolve(Manager::class);
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function fields(Request $request)
    {
        return array_merge(
            $this->getFormIntroductionFields(),
            $this->getTemplateAttributesFields($request)
        );
    }

    /**
     * Get the base fields displayed at the top of the resource's form.
     *
     * @return array
     */
    abstract protected function getFormIntroductionFields();

    /**
     * Get the base attributes Nova Panel
     *
     * @return array
     */
    abstract protected function getIndexTableFields();

    /**
     * Get the fields displayed by the template.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    protected function getTemplateAttributesFields(Request $request)
    {
        return $this->resource->fields($request);
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function cards(Request $request)
    {
        return $this->resource->cards($request);
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }

    /**
     * Determine if the current user can create new resources.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public static function authorizedToCreate(Request $request)
    {
        return false;
    }

    /**
     * Determine if the current user can delete the given resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public function authorizedToDelete(Request $request)
    {
        return false;
    }

    /**
     * Prepare the resource for JSON serialization.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->serializeWithId($this->resolveFields(resolve(NovaRequest::class)));
    }

    /**
     * Prepare the resource for JSON serialization using the given fields.
     *
     * @param  \Illuminate\Support\Collection  $fields
     * @return array
     */
    protected function serializeWithId(Collection $fields)
    {
        return [
            'id' => tap(ID::make('id', function() {
                        return $this->getKey();
                    }))->resolve($this->resource),
            'fields' => $fields->all(),
        ];
    }

}
<?php

namespace Whitecube\NovaPage\Pages;

use Laravel\Nova\Nova;
use Laravel\Nova\Resource as BaseResource;
use Laravel\Nova\Panel;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Laravel\Nova\Http\Requests\NovaRequest;

class PageResource extends BaseResource
{

    use ResolvesPageFields;

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
     * Get the displayable label of the resource.
     *
     * @return string
     */
    public static function label()
    {
        return 'nova-pages';
    }

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     */
    public static function singularLabel()
    {
        return 'nova-page';
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
     * Get the URI key for the resource.
     *
     * @return string
     */
    public static function uriKey()
    {
        return 'nova-page';
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function fields(Request $request)
    {
        return array_prepend(
            $this->getTemplateAttributesFields($request),
            new Panel('Base page attributes', $this->getBaseAttributeFields())
        );
    }

    /**
     * Get the base common attributes
     *
     * @return array
     */
    protected function getBaseAttributeFields()
    {
        return [
            Text::make('Page title', 'nova_page_title')
                ->rules(['required', 'string', 'max:255']),

            DateTime::make('Page creation date', 'nova_page_created_at')
                ->format('DD-MM-YYYY HH:mm:ss')
                ->rules(['required', 'string', 'max:255']),
        ];
    }

    /**
     * Get the base attributes Nova Panel
     *
     * @return array
     */
    protected function getIndexTableFields()
    {
        return [
            Text::make('Name', function() {
                return $this->getName();
            })->sortable(),

            Text::make('Title', function() {
                return $this->getTitle();
            })->sortable(),

            DateTime::make('Last updated on', function() {
                $updated_at = $this->getDate('updated_at');
                return $updated_at ? $updated_at->toDateTimeString() : null;
            })->format(config('novapage.date_format'))->sortable()
        ];
    }

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
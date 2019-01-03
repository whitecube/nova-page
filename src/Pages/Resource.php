<?php

namespace Whitecube\NovaPage\Pages;

use Laravel\Nova\Nova;
use Laravel\Nova\Resource as BaseResource;
use Laravel\Nova\Panel;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\DateTime;
use Illuminate\Http\Request;

class Resource extends BaseResource
{

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'title';

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
     * Get the displayable label of the resource.
     *
     * @return string
     */
    public static function label()
    {
        // TODO: add lang translations for resource label
        return 'nova-pages';
    }

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     */
    public static function singularLabel()
    {
        // TODO: add lang translations for resource singular label
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
        return;
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
            $this->getBaseAttributesPanel(),
            $this->getTemplateAttributesFields($request)
        );
    }

    /**
     * Get the base attributes Nova Panel
     *
     * @return Laravel\Nova\Panel
     */
    protected function getBaseAttributesPanel()
    {
        // TODO: add lang translations for base field labels 
        return new Panel('Base page attributes', [
            Text::make('Page title', '_nova_page_title')
                ->rules(['required', 'string', 'max:255']),

            DateTime::make('Page creation date', '_nova_page_created_at')
                ->format('DD-MM-YYYY HH:mm:ss')
                ->rules(['required', 'string', 'max:255']),
        ]);
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

}
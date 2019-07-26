<?php

namespace Whitecube\NovaPage\Pages;

use Laravel\Nova\Panel;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\DateTime;

class PageResource extends StaticResource
{
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
     * Get the displayable label of the resource.
     *
     * @return string
     */
    public static function label()
    {
        return config('novapage.labels.pages');
    }

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     */
    public static function singularLabel()
    {
        return config('novapage.labels.page');
    }

    /**
     * Get the base fields displayed at the top of the resource's form.
     *
     * @return array
     */
    protected function getFormIntroductionFields()
    {
        return [
            new Panel('Base page attributes', $this->getBaseAttributeFields()),
        ];
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

}
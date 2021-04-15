<?php

namespace Whitecube\NovaPage\Pages;

use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Panel;

class PageResource extends StaticResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'nova-page';

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
            (new Panel(__('Base page attributes'), $this->getBaseAttributeFields()))->withToolbar(),
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
            Text::make(__('Page title'), 'nova_page_title')
                ->rules(['required', 'string', 'max:255']),

            DateTime::make(__('Page creation date'), 'nova_page_created_at')
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
            Text::make(__('Name'), 'name', function () {
                return $this->getName();
            })->sortable(),

            Text::make(__('Title'), 'title', function () {
                return $this->getTitle();
            })->sortable(),

            DateTime::make(__('Last updated on'), 'last_updated_on', function () {
                $updated_at = $this->getDate('updated_at');
                return $updated_at ? $updated_at->toDateTimeString() : null;
            })->format(config('novapage.date_format'))->sortable(),
        ];
    }

}

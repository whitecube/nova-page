<?php

namespace Whitecube\NovaPage\Pages;

use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\Text;

class OptionResource extends StaticResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'nova-option';
    
    /**
     * Get the URI key for the resource.
     *
     * @return string
     */
    public static function uriKey()
    {
        return 'nova-option';
    }

    /**
     * Get the displayable label of the resource.
     *
     * @return string
     */
    public static function label()
    {
        return config('novapage.labels.options');
    }

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     */
    public static function singularLabel()
    {
        return config('novapage.labels.option');
    }

    /**
     * Get the base fields displayed at the top of the resource's form.
     *
     * @return array
     */
    protected function getFormIntroductionFields()
    {
        return [
            Heading::make($this->getFormattedName()),
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
                return $this->getFormattedName();
            })->sortable(),

            DateTime::make(__('Last updated on'), 'last_updated_on', function () {
                $updated_at = $this->getDate('updated_at');
                return $updated_at ? $updated_at->toDateTimeString() : null;
            })->format(config('novapage.date_format'))->sortable(),
        ];
    }

    /**
     * Format template class name for display
     *
     * @return string
     */
    public function getFormattedName()
    {
        return ucfirst(preg_replace('/(?<!\ )[A-Z]/', ' $0', $this->getName()));
    }

}

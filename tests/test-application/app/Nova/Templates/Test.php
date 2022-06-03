<?php

namespace FakeTestApp\Nova\Templates;

use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\Text;
use Whitecube\NovaPage\Pages\Template;

class Test extends Template {

    protected $jsonAttributes = [
        'foo_json'
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            Text::make('Test', 'test_field')
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function cards(NovaRequest $request)
    {
        return [];
    }

    /**
     * Return a test value
     *
     * @return string
     */
    public function foo() {
        return 'bar';
    }

    /**
     * Return a value from an undefined computed attribute
     *
     * @return string
     */
    public function getComputedAttribute() {
        return 'foo';
    }
}

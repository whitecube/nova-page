<?php

namespace FakeTestApp\Nova\Templates;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\Text;
use Whitecube\NovaPage\Pages\Template;

class Test extends Template {

    protected $jsonAttributes = [
        'foo_json'
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            Text::make('Test', 'test_field')
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function cards(Request $request)
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
<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Admin pages tool label
    |--------------------------------------------------------------------------
    |
    | How should we name the link in Laravel Nova's sidebar?
    |
    */
    'label' => 'Pages',

    /*
    |--------------------------------------------------------------------------
    | Admin options tool label
    |--------------------------------------------------------------------------
    |
    | How should we name the link in Laravel Nova's sidebar?
    |
    */
    'options_label' => 'Options',

    /*
    |--------------------------------------------------------------------------
    | Admin tool date formating
    |--------------------------------------------------------------------------
    |
    | How should we format (for display only) dates in the page resource views?
    |
    */
    'date_format' => 'DD/MM/YYYY · HH:mm',

    /*
    |--------------------------------------------------------------------------
    | Options Templates Routes
    |--------------------------------------------------------------------------
    |
    | Options Templates provide reusable content that can be shared accross
    | multiple routes.
    |
    */
    'options' => [
        'App\Nova\Options\FooterBlocks' => '*',
        'App\Nova\Options\ContactInfo' => ['home', 'contact']
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Source
    |--------------------------------------------------------------------------
    |
    | This option controls the default source driver, needed to access and
    | write static page data. It is possible to write your own as long as it
    | implements the Whitecube\NovaPage\Sources\SourceInterface.
    |
    */
    'default_source' => \Whitecube\NovaPage\Sources\Filesystem::class,
    
    /*
    |--------------------------------------------------------------------------
    | Sources configuration
    |--------------------------------------------------------------------------
    |
    | Here are each of the available source configurations. The source
    | configuration array matching the source's name will automatically be
    | provided when instanciated. This means you can add your own source's
    | configuration here too.
    |
    | Path configurations can contain the following variables: {locale}
    |
    */
    'sources' => [

        'filesystem' => [
            'path' => resource_path('lang/{type}/{key}.json')
        ],
        'database' => [
            'table_name' => 'static_pages'
        ]

    ]

];
<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Admin pages tool labels
    |--------------------------------------------------------------------------
    |
    | How should we name the links in Laravel Nova's sidebar?
    |
    */
    'labels' => [
        'pages' => 'Pages',
        'page' => 'Page',
        'options' => 'Options',
        'option' => 'Option'
    ],

    /*
    |--------------------------------------------------------------------------
    | Resource label
    |--------------------------------------------------------------------------
    |
    | Set the displayable label of the resource.
    |
    */
    'resource_label' => 'nova-pages',

    /*
    |--------------------------------------------------------------------------
    | Resource singular label
    |--------------------------------------------------------------------------
    |
    | Set the displayable singular label of the resource.
    |
    */
    'resource_singular_label' => 'nova-page',

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
    | Path configurations can contain the following variables: 
    | {type}, {key}, {locale}
    |
    */
    'sources' => [
        'filesystem' => [
            'path' => resource_path('lang/{type}/{key}.json')
        ],
        'database' => [
            'table_name' => 'static_pages'
        ],
    ]

];
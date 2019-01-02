<?php

return [

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
    */
    'sources' => [

        'filesystem' => [
            'directory' => resource_path('lang/en/static')
        ]

    ]

];
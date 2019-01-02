# Nova Page

Static pages content management system for Laravel Nova

## Installation

In your terminal type : `composer require whitecube/nova-page` and provide "dev-master" as the version of the package. Or open up composer.json and add the following line under `require`:

```json
    {
        "require": {
            "whitecube/nova-page": "dev-master"
        }
    }
```

Register the package in the `providers` section of the app config file in `app/config/app.php`:

```php
    'providers' => [
        // ...
        
        /*
         * Package Service Providers...
         */
        Whitecube\NovaPage\NovaPageServiceProvider::class,
        // ...
    ],
```

Next, add the `Page` facade:

```php
    'aliases' => [
        // ...
        'Page' => Whitecube\NovaPage\NovaPageFacade::class,
        // ...
    ],
```

Now you can publish the package's configuration file with the `php artisan vendor:publish` command. This will add a `app/config/novapage.php` file containing the package's default configuration.

## Middleware registration

It is possible to load the page's static content automatically using the `LoadPageFromRouteName` middleware. This way, the application will fetch the page's data using the current route's name as identifier. Of course, this means you'll need to name the routes in order to get it to work.

For instance, it is possible to autoload the page's static content on each "web" request by adding `\Whitecube\NovaPage\Middleware\LoadPageFromRouteName::class` to the `web` middleware group array located in the `App\Http\Kernel` file:

```php
    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            // ...
            \Whitecube\NovaPage\Middleware\LoadPageFromRouteName::class,
            // ...
        ],

        // ...
    ];
```
